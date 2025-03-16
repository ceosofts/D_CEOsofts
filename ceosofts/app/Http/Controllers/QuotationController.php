<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\PaymentStatus;
use App\Models\JobStatus;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\Invoice;

class QuotationController extends Controller
{
    /**
     * แสดงรายการใบเสนอราคา (Quotation) ทั้งหมด
     */
    public function index()
    {
        $quotations = Quotation::with(['status'])->latest()->paginate(10);
        return view('quotations.index', compact('quotations'));
    }

    /**
     * แสดงฟอร์มสร้างใบเสนอราคาใหม่
     */
    public function create()
    {
        // ดึงข้อมูลสำหรับ dropdown ต่าง ๆ
        $companies       = Company::all();
        $customers       = Customer::all();
        $products        = Product::all();
        $sales_employees = Employee::where('department_id', 1)->get();
        $payment_statuses = PaymentStatus::all();
        $jobStatuses = JobStatus::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('quotations.create', compact(
            'companies',
            'customers',
            'products',
            'sales_employees',
            'payment_statuses',
            'jobStatuses'
        ));
    }

    /**
     * บันทึกใบเสนอราคาใหม่
     */
    public function store(Request $request)
    {
        // ตรวจสอบความถูกต้องของข้อมูลหลัก
        $request->validate([
            'seller_company' => 'required|string|max:255',
            'quotation_date' => 'required|date',
            'customer_id'    => 'required|exists:customers,id',
            'payment'        => 'required|string|max:255',
            'status_id' => 'nullable|exists:job_statuses,id',
        ]);

        DB::transaction(function () use ($request) {
            // สร้างเลข Quotation Number ใหม่ (เช่น Q-000001)
            $lastQ   = Quotation::orderBy('id', 'desc')->first();
            $nextNum = $lastQ ? intval(substr($lastQ->quotation_number, 2)) + 1 : 1;
            $quotNo  = 'Q-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);

            // ดึงข้อมูลลูกค้าจาก customer_id
            $customer = Customer::findOrFail($request->customer_id);

            // สร้างใบเสนอราคาใหม่ โดยบันทึกข้อมูล Seller และข้อมูล Customer จาก customer
            $quotation = Quotation::create([
                // Seller Info
                'seller_company'   => $request->seller_company,
                'seller_address'   => $request->seller_address,
                'seller_phone'     => $request->seller_phone,
                'seller_fax'       => $request->seller_fax,
                'seller_line'      => $request->seller_line,
                'seller_email'     => $request->seller_email,
                // Quotation Info
                'quotation_number' => $quotNo,
                'quotation_date'   => $request->quotation_date,
                // Customer Info (ดึงจาก customer)
                'customer_id'             => $request->customer_id,
                'customer_company'        => $customer->companyname,
                'customer_contact_name'   => $customer->contact_name,
                'customer_address'        => $customer->address,
                'customer_phone'          => $customer->phone,
                'customer_fax'            => $customer->fax ?? '',
                'customer_email'          => $customer->email,
                // Ref
                'your_ref' => $request->your_ref,
                'our_ref'  => $request->our_ref,
                // Conditions
                'delivery' => $request->delivery,
                'warranty' => $request->warranty,
                'validity' => $request->validity,
                'payment'  => $request->payment,
                // เพิ่ม status_id
                'status_id' => $request->status_id,
                // Signature
                'prepared_by'    => $request->prepared_by,
                'sales_engineer' => $request->sales_engineer,
                // Default values
                'total_amount'    => 0,
                'amount_in_words' => '',
            ]);

            // เพิ่มรายการสินค้า (QuotationItem) และคำนวณยอดรวม
            $sum = 0;
            if ($request->has('items')) {
                foreach ($request->items as $i => $row) {
                    $qty  = $row['quantity'] ?? 1;
                    $unit = $row['unit_price'] ?? 0;
                    $net  = $qty * $unit;
                    $itemNo = $row['item_no'] ?? ($i + 1);

                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'item_no'      => $itemNo,
                        'product_id'   => !empty($row['product_id']) ? $row['product_id'] : null,
                        'description'  => $row['description'] ?? '',
                        'quantity'     => $qty,
                        'unit_price'   => $unit,
                        'net_price'    => $net,
                    ]);
                    $sum += $net;
                }
            }

            // อัปเดตยอดรวมใบเสนอราคา และแปลงยอดเป็นคำ (amount in words)
            $words = $this->numToWords($sum);
            $quotation->update([
                'total_amount'    => $sum,
                'amount_in_words' => $words,
            ]);

            // Log for debugging
            \Log::info('Creating quotation with status:', [
                'quotation_id' => $quotation->id,
                'status_id' => $request->status_id
            ]);
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation created');
    }

    /**
     * แสดงรายละเอียดใบเสนอราคา
     */
    public function show($id)
    {
        $quotation = Quotation::with(['items.product', 'customer'])->findOrFail($id);
        $seller = Company::where('company_name', $quotation->seller_company)->first();
        $customer = Customer::find($quotation->customer_id);

        return view('quotations.show', compact('quotation', 'seller', 'customer'));
    }

    /**
     * แสดงฟอร์มแก้ไขใบเสนอราคา
     */
    public function edit($id)
    {
        // โหลด Quotation ตาม id
        $quotation = Quotation::with('items')->findOrFail($id);

        // โหลดข้อมูลอื่น ๆ
        $companies       = Company::all();
        $customers       = Customer::all();
        $products        = Product::all();
        $sales_employees = Employee::where('department_id', 1)->get();
        $payment_statuses = PaymentStatus::all();
        $jobStatuses = JobStatus::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // ส่งทุกตัวแปรไปยัง Blade
        return view('quotations.edit', compact(
            'quotation',
            'companies',
            'customers',
            'products',
            'sales_employees',
            'payment_statuses',
            'jobStatuses'
        ));
    }

    /**
     * อัปเดตใบเสนอราคา
     */
    public function update(Request $request, Quotation $quotation)
    {
        try {
            $validated = $request->validate([
                'seller_company' => 'required|string|max:255',
                'quotation_date' => 'required|date',
                'customer_id' => 'required|exists:customers,id',
                'status_id' => 'nullable|exists:job_statuses,id',
                'payment' => 'required|string|max:255',
                // Add other validation rules as needed
            ]);

            DB::beginTransaction();

            // Update basic quotation information
            $quotation->update($validated);

            // Update items if present
            if ($request->has('items')) {
                // Delete existing items
                $quotation->items()->delete();

                $sum = 0;
                foreach ($request->items as $item) {
                    $qty = $item['quantity'] ?? 1;
                    $unit = $item['unit_price'] ?? 0;
                    $net = $qty * $unit;

                    $quotation->items()->create([
                        'product_id' => $item['product_id'] ?? null,
                        'description' => $item['description'] ?? '',
                        'quantity' => $qty,
                        'unit_price' => $unit,
                        'net_price' => $net
                    ]);

                    $sum += $net;
                }

                // Update totals - Fixed the syntax error here
                $quotation->update([
                    'total_amount' => $sum,
                    'amount_in_words' => $this->numToWords($sum)  // Removed the extra dot
                ]);
            }

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating quotation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update quotation: ' . $e->getMessage()]);
        }
    }

    /**
     * ลบใบเสนอราคา
     */
    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted');
    }

    /**
     * ฟังก์ชันแปลงตัวเลขเป็นคำ (ภาษาอังกฤษ)
     */
    private function numToWords($num)
    {
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        return strtoupper($formatter->format($num)) . ' ONLY';
    }

    /**
     * ส่งออกใบเสนอราคาเป็น PDF
     */
    public function export($id)
    {
        $quotation = Quotation::with(['items.product', 'customer'])->findOrFail($id);
        $seller = Company::where('company_name', $quotation->seller_company)->first(); // Assuming seller_company is the company name
        $customer = Customer::find($quotation->customer_id);

        $pdf = PDF::loadView('quotations.pdf', compact('quotation', 'seller', 'customer'));
        return $pdf->stream("quotation-{$quotation->quotation_number}.pdf");
    }

    public function generatePdf(Quotation $quotation)
    {
        $seller = Company::first();
        $customer = Customer::where('companyname', $quotation->customer_company)->first();

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'THSarabunNew',
            'default_font_size' => 13,
            'tempDir' => storage_path('temp'),
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts')
            ]),
            'fontdata' => [
                'THSarabunNew' => [
                    'R' => 'THSarabunNew.ttf',
                    'B' => 'THSarabunNew-Bold.ttf',
                    'useOTL' => 0x00
                ]
            ],
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetDisplayMode('fullpage');

        $view = view('quotations.pdf', compact('quotation', 'seller', 'customer'))->render();
        $mpdf->WriteHTML($view);

        return $mpdf->Output('Quotation-' . $quotation->quotation_number . '.pdf', 'I');
    }

    /**
     * Update quotation status and create invoice if approved
     */
    public function updateStatus(Request $request, Quotation $quotation)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'status_id' => 'required|exists:job_statuses,id'
            ]);

            // Log before update
            \Log::info('Updating quotation status:', [
                'quotation_id' => $quotation->id,
                'old_status' => $quotation->status_id,
                'new_status' => $request->status_id
            ]);

            $quotation->update($validated);

            // Check if status is "ลูกค้าอนุมัติใบเสนอราคา"
            $approvedStatus = JobStatus::where('name', 'ลูกค้าอนุมัติใบเสนอราคา')->first();
            if ($approvedStatus && $request->status_id == $approvedStatus->id) {
                // Create invoice number
                $lastInvoice = Invoice::orderBy('id', 'desc')->first();
                $nextNum = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 2)) + 1 : 1;
                $invoiceNumber = 'IV' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);

                // Create invoice
                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'quotation_id' => $quotation->id,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(30), // Default 30 days
                    'total_amount' => $quotation->total_amount,
                    'payment_amount' => 0, // Will be set when creating invoice
                    'remaining_balance' => $quotation->total_amount,
                    'payment_percentage' => 0, // Will be set when creating invoice
                    'amount_in_words' => $quotation->amount_in_words,
                    'your_ref' => $quotation->your_ref,
                    'our_ref' => $quotation->our_ref,
                    'payment_terms' => $quotation->payment,
                    'created_by' => auth()->id()
                ]);

                \Log::info('Created invoice from approved quotation:', [
                    'quotation_id' => $quotation->id,
                    'invoice_id' => $invoice->id
                ]);
            }

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation status updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating quotation status: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }
}
