<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * แสดงรายการสินค้าทั้งหมด + ค้นหา
     */
    public function index(Request $request)
    {
        $query = Product::with('unit');

        // ค้นหาจาก code, name, sku, is_active
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('is_active', 'LIKE', "%{$searchTerm}%");
            });
        }

        // สามารถเพิ่มเงื่อนไขอื่น ๆ ได้ตามต้องการ เช่น filter ตามราคาหรือสถานะ
        // if ($request->filled('status')) { ... }

        $products = $query->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * แสดงฟอร์มสร้างสินค้าใหม่ + สร้างรหัสอัตโนมัติ
     */
    public function create()
    {
        $units = Unit::all();

        // ถ้าคุณมี method generateNewProductCode() ใน Model Product
        // เพื่อสร้าง code อัตโนมัติ ก็เรียกใช้ได้
        $generatedCode = Product::generateNewProductCode();

        return view('products.create', [
            'units'         => $units,
            'generatedCode' => $generatedCode,
        ]);
    }

    /**
     * บันทึกสินค้าใหม่
     */
    public function store(Request $request)
    {
        // Validation ตามที่ต้องการ
        $validated = $request->validate([
            'code'           => 'nullable|unique:products,code|max:50', // ถ้าต้องการ unique code
            'sku'            => 'nullable|unique:products,sku|max:50', // ถ้าต้องการ unique sku
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit_id'        => 'required|exists:units,id',
            'is_active'      => 'nullable|boolean', // ถ้าใช้ boolean (0/1)
        ]);

        try {
            // ใช้ Transaction หากต้องการความปลอดภัย (optional)
            return DB::transaction(function () use ($validated) {

                // ถ้าไม่ได้ส่ง code มา หรืออยากใช้ code auto
                if (empty($validated['code'])) {
                    $validated['code'] = Product::generateNewProductCode();
                }

                // ถ้าไม่ได้ส่ง is_active มา default = true
                if (!array_key_exists('is_active', $validated)) {
                    $validated['is_active'] = true;
                }

                $product = Product::create($validated);

                Log::info('Product created', ['product_id' => $product->id]);

                return redirect()->route('products.index')
                    ->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว: ' . $product->code);
            });
        } catch (\Exception $e) {
            // ถ้าเกิดข้อผิดพลาด ให้เขียน Log และส่ง error กลับ
            Log::error('Error storing product: ' . $e->getMessage(), $request->all());

            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกสินค้า: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขสินค้า
     */
    public function edit(Product $product)
    {
        $units = Unit::all();
        return view('products.edit', compact('product', 'units'));
    }

    /**
     * อัปเดตข้อมูลสินค้า
     */
    public function update(Request $request, Product $product)
    {
        // Validation
        $validated = $request->validate([
            'code'           => 'nullable|unique:products,code,' . $product->id . '|max:50',
            'sku'            => 'nullable|unique:products,sku,' . $product->id . '|max:50',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit_id'        => 'required|exists:units,id',
            'is_active'      => 'nullable|boolean',
        ]);

        try {
            return DB::transaction(function () use ($validated, $product, $request) {

                // ถ้าไม่ได้ส่ง is_active มา จะคงค่าของเดิมไว้
                if (!array_key_exists('is_active', $validated)) {
                    $validated['is_active'] = $product->is_active;
                }

                $product->update($validated);
                $product->refresh();

                Log::info('Product updated', ['product_id' => $product->id]);

                return redirect()->route('products.index')
                    ->with('success', 'อัปเดตสินค้าเรียบร้อยแล้ว');
            });
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage(), $request->all());

            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการอัปเดตสินค้า: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบสินค้า
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            Log::info('Product deleted', ['product_id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการลบสินค้า: ' . $e->getMessage()]);
        }
    }
}
