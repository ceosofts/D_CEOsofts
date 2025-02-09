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
        if ($request->filled('search')) {
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
        $units = Unit::all();
        $generatedCode = Product::generateNewProductCode(); // ✅ ใช้ฟังก์ชันที่สร้างใน Model
        return view('products.create', compact('units', 'generatedCode'));
    }

    /**
     * บันทึกสินค้าใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit_id'        => 'required|exists:units,id',
        ]);

        // ✅ สร้างสินค้าใหม่ (SKU & Code จะถูกสร้างอัตโนมัติจาก Model)
        $product = Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว: ' . $product->code);
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
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit_id'        => 'required|exists:units,id',
        ]);

        $product->update($request->all());
        $product->refresh(); // ✅ โหลดค่าล่าสุดจากฐานข้อมูล

        return redirect()->route('products.index')->with('success', 'อัปเดตสินค้าเรียบร้อยแล้ว');
    }

    /**
     * ลบสินค้า
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
}
