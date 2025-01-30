<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with search functionality.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Universal Search (Code, Name, SKU, Status)
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%')
                    ->orWhere('is_active', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    // public function create()
    // {
    //     $categories = ['Products', 'Parts', 'Material']; // SKU Categories
    //     return view('products.create', compact('categories'));
    // }

    public function create()
{
    $latestProduct = Product::where('code', 'like', 'P%')->orderBy('id', 'desc')->first();
    $newCodeNumber = $latestProduct ? intval(substr($latestProduct->code, 1)) + 1 : 1;
    $generatedCode = 'P' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);

    return view('products.create', compact('generatedCode'));
}


    /**
     * Store a newly created product in storage.
     */

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'nullable',
    //         'price' => 'required|numeric',
    //         'stock_quantity' => 'required|integer',
    //         'sku' => 'required|in:Products,Parts,Material',
    //     ]);

    //     // Generate a new code with P000X format if not set
    //     $latestProduct = Product::where('code', 'like', 'P%')->orderBy('id', 'desc')->first();
    //     $newCodeNumber = $latestProduct ? intval(substr($latestProduct->code, 1)) + 1 : 1;
    //     $generatedCode = 'P' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);

    //     Product::create(array_merge($request->all(), ['code' => $generatedCode]));

    //     return redirect()->route('products.index')->with('success', 'Product created successfully with code: ' . $generatedCode);
    // }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
        'sku' => 'required|string|max:20|unique:products,sku',
    ]);

    // ✅ เช็คว่ามีรหัสสินค้าเก่าสุดในฐานข้อมูลหรือไม่
    $latestProduct = Product::where('code', 'like', 'P%')->orderBy('id', 'desc')->first();
    $newCodeNumber = $latestProduct ? intval(substr($latestProduct->code, 1)) + 1 : 1;
    $generatedCode = 'P' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);

    // ✅ บันทึกข้อมูลสินค้า
    Product::create(array_merge($request->all(), ['code' => $generatedCode]));

    return redirect()->route('products.index')->with('success', 'Product added successfully with code: ' . $generatedCode);
}


    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = ['Products', 'Parts', 'Material']; // SKU Categories
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'sku' => 'required|in:Products,Parts,Material',
        ]);

        $product->update($request->except('code'));

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
