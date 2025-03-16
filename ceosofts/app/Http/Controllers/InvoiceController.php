<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\JobStatus;
use App\Models\PaymentStatus; // เพิ่ม PaymentStatus
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ThaiPdfService;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $invoices = Invoice::with(['quotation', 'status'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('invoice.invoice-index', compact('invoices'));
        } catch (\Exception $e) {
            \Log::error('Error loading invoices:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'ไม่สามารถโหลดรายการใบแจ้งหนี้ได้');
        }
    }

    public function show(Invoice $invoice)
    {
        return view('invoice.invoice-show', compact('invoice'));
    }

    public function createFromQuotation(Quotation $quotation)
    {
        try {
            \Log::info('Creating invoice from quotation', ['quotation_id' => $quotation->id]);

            // ตรวจสอบสถานะการอนุมัติ
            $approvedStatus = JobStatus::where('name', 'ลูกค้าอนุมัติใบเสนอราคา')->first();
            
            if (!$approvedStatus || $quotation->status_id !== $approvedStatus->id) {
                return back()->with('error', 'กรุณาอนุมัติใบเสนอราคาก่อนสร้างใบแจ้งหนี้');
            }

            // สร้างเลขที่ใบแจ้งหนี้อัตโนมัติในรูปแบบ INV + ปี 2 หลัก + running number 4 หลัก
            $year = date('y'); // Gets 2-digit year (e.g. 25 for 2025)
            $lastInvoice = Invoice::where('invoice_number', 'like', "INV{$year}%")->latest()->first();
            $sequentialNumber = $lastInvoice 
                ? intval(substr($lastInvoice->invoice_number, 5)) + 1 
                : 1;
            $invoiceNumber = "INV{$year}" . str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);

            $jobStatuses = JobStatus::where('is_active', true)->orderBy('sort_order')->get();
            $paymentStatuses = PaymentStatus::all();

            // โหลดความสัมพันธ์ที่จำเป็น
            $quotation->load(['customer', 'items.product']);

            return view('invoice.invoice-create', compact(
                'quotation',
                'invoiceNumber',
                'jobStatuses',
                'paymentStatuses'
            ));

        } catch (\Exception $e) {
            \Log::error('Error creating invoice:', ['error' => $e->getMessage()]);
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $paymentTerms = PaymentTerm::where('is_active', true)->get();
        $jobStatuses = JobStatus::where('is_active', true)->orderBy('sort_order')->get();
        
        // Generate invoice number with new format
        $year = date('y'); // จะได้ 25 สำหรับปี 2025
        $lastInvoice = Invoice::where('invoice_number', 'like', "INV{$year}%")->latest()->first();
        $sequentialNumber = $lastInvoice 
            ? intval(substr($lastInvoice->invoice_number, 5)) + 1 
            : 1;
        $invoiceNumber = "INV{$year}" . str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);
        
        return view('invoice.invoice-create', compact('paymentTerms', 'jobStatuses', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Received invoice data:', $request->all());

            $validated = $request->validate([
                'quotation_id' => 'required|exists:quotations,id',
                'invoice_date' => 'required|date',
                'payment_percentage' => 'required|numeric|min:0|max:100',
                'payment_terms' => 'required|string',
                'due_date' => 'required|date|after_or_equal:invoice_date',
                'status_id' => 'required|exists:job_statuses,id'
            ]);

            DB::beginTransaction();

            // Generate invoice number automatically
            $year = date('y'); // Gets 2-digit year (e.g. 25 for 2025)
            $lastInvoice = Invoice::where('invoice_number', 'like', "INV{$year}%")->latest()->first();
            $sequentialNumber = $lastInvoice 
                ? intval(substr($lastInvoice->invoice_number, 5)) + 1 
                : 1;
            $invoiceNumber = "INV{$year}" . str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::findOrFail($request->quotation_id);
            
            $totalAmount = $quotation->total_amount;
            $paymentPercentage = $request->payment_percentage;
            $paymentAmount = $totalAmount * ($paymentPercentage / 100);
            $remainingBalance = $totalAmount - $paymentAmount;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber, // Auto-generated invoice number
                'quotation_id' => $quotation->id,
                'invoice_date' => $request->invoice_date,
                'your_ref' => $quotation->your_ref,
                'our_ref' => $quotation->our_ref,
                'payment_percentage' => $paymentPercentage,
                'payment_terms' => $request->payment_terms,
                'due_date' => $request->due_date,
                'status_id' => $request->status_id,
                'total_amount' => $totalAmount,
                'payment_amount' => $paymentAmount,
                'remaining_balance' => $remainingBalance,
                'amount_in_words' => $this->numberToWords($paymentAmount),
                'created_by' => auth()->id()
            ]);

            \Log::info('Invoice created successfully:', ['invoice_id' => $invoice->id]);

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'ใบแจ้งหนี้ถูกสร้างเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create invoice:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'ไม่สามารถสร้างใบแจ้งหนี้ได้: ' . $e->getMessage()]);
        }
    }

    public function edit(Invoice $invoice)
    {
        $jobStatuses = JobStatus::where('is_active', true)->orderBy('sort_order')->get();
        return view('invoice.invoice-edit', compact('invoice', 'jobStatuses'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        try {
            $validated = $request->validate([
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:invoice_date',
                'payment_percentage' => 'required|numeric|min:0|max:100',
                'payment_terms' => 'required|string',
                'status_id' => 'required|exists:job_statuses,id',
                'remarks' => 'nullable|string',
                'actual_payment_amount' => 'required|numeric',
                'actual_remaining_balance' => 'required|numeric',
            ]);

            DB::beginTransaction();

            $invoice->update([
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'payment_percentage' => $request->payment_percentage,
                'payment_amount' => $request->actual_payment_amount,
                'remaining_balance' => $request->actual_remaining_balance,
                'payment_terms' => $request->payment_terms,
                'status_id' => $request->status_id,
                'remarks' => $request->remarks,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'ใบแจ้งหนี้ถูกอัปเดตเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update invoice:', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'ไม่สามารถอัปเดตใบแจ้งหนี้ได้: ' . $e->getMessage()]);
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            DB::beginTransaction();
            
            $invoice->delete();
            
            DB::commit();
            
            return redirect()->route('invoices.index')
                ->with('success', 'Invoice deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    protected function numberToWords($amount)
    {
        // Add your number to words conversion logic here
        // You might want to use a package like kwn/number-to-words
        return ""; // Placeholder
    }

    public function generatePDF(Invoice $invoice)
    {
        try {
            $invoice->load(['quotation', 'status', 'creator', 'updater']);
            
            // ถ้าใช้ ThaiPdfService
            if (class_exists('ThaiPdfService')) {
                $thaiPdf = app(ThaiPdfService::class);
                $pdf = $thaiPdf->loadView('invoice.invoice-pdf', compact('invoice'));
            } else {
                // ใช้ Barryvdh\DomPDF\Facade\Pdf
                $pdf = Pdf::loadView('invoice.invoice-pdf', compact('invoice'));
            }
            
            return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Error generating invoice PDF:', ['error' => $e->getMessage()]);
            return back()->with('error', 'ไม่สามารถสร้าง PDF ได้: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Invoice $invoice)
    {
        try {
            $paidStatus = JobStatus::where('name', 'Paid')->first();
            if (!$paidStatus) {
                throw new \Exception('Paid status not found');
            }

            $invoice->update([
                'status_id' => $paidStatus->id,
                'updated_by' => auth()->id()
            ]);

            return back()->with('success', 'Invoice marked as paid');
        } catch (\Exception $e) {
            Log::error('Mark as paid failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to mark invoice as paid');
        }
    }
}
