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
        $query = Product::with('unit'); // โหลดข้อมูล unit ด้วย

        // ✅ ค้นหาจาก Code, Name, SKU, และสถานะ
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%')
                    ->orWhere('is_active', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(10); // ใช้ pagination

        return view('products.index', compact('products'));
    }

    /**
     * แสดงฟอร์มสร้างสินค้าใหม่ + สร้างรหัสอัตโนมัติ
     */
    public function create()
    {
        $units = Unit::all(); // ดึงข้อมูลหน่วยสินค้า
        $latestProduct = Product::where('code', 'like', 'P%')->orderBy('id', 'desc')->first();
        $newCodeNumber = $latestProduct ? intval(substr($latestProduct->code, 1)) + 1 : 1;
        $generatedCode = 'P' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);

        return view('products.create', compact('units', 'generatedCode'));
    }

    /**
     * บันทึกสินค้าใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit_id' => 'required|exists:units,id',
            'sku' => 'required|string|max:20|unique:products,sku',
        ]);

        // ✅ สร้างรหัสสินค้าใหม่
        $latestProduct = Product::where('code', 'like', 'P%')->orderBy('id', 'desc')->first();
        $newCodeNumber = $latestProduct ? intval(substr($latestProduct->code, 1)) + 1 : 1;
        $generatedCode = 'P' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);

        // ✅ บันทึกสินค้า
        Product::create(array_merge($request->all(), ['code' => $generatedCode]));

        return redirect()->route('products.index')->with('success', 'Product added successfully with code: ' . $generatedCode);
    }

    /**
     * แสดงฟอร์มแก้ไขสินค้า
     */
    public function edit(Product $product)
    {
        $units = Unit::all(); // ดึงข้อมูลหน่วยสินค้า
        return view('products.edit', compact('product', 'units'));
    }

    /**
     * อัปเดตข้อมูลสินค้า
     */

    // public function update(Request $request, Product $product)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'price' => 'required|numeric|min:0',
    //         'stock_quantity' => 'required|integer|min:0',
    //         'unit_id' => 'required|exists:units,id',
    //         'sku' => 'required|string|max:20|unique:products,sku,' . $product->id,
    //     ]);

    //     // ✅ Debug ข้อมูลก่อนอัปเดต
    //     Log::info('Before Update:', $product->toArray());
    //     Log::info('New Data:', $request->all());

    //     $product->fill($request->all());

    //     if ($product->isDirty()) { // ถ้าข้อมูลมีการเปลี่ยนแปลง
    //         $product->save();
    //         Log::info('Product updated successfully:', $product->toArray());
    //     } else {
    //         Log::warning('No changes detected for product:', $product->toArray());
    //     }

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    // }

    //     public function update(Request $request, Product $product)
    // {
    //     dd($request->all()); // ✅ เช็คค่าที่ส่งมาจากฟอร์ม
    // }


        public function update(Request $request, Product $product)
    {
        $product->update([
            'unit_id' => $request->unit_id,
        ]);

        // ✅ บังคับให้โหลดค่าล่าสุดจากฐานข้อมูล
        $product->refresh();

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }


    /**
     * ลบสินค้า
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
