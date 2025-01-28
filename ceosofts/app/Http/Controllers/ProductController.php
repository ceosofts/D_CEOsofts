<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with search, filter, and sorting functionality.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by price range
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        // Filter by status (is_active)
        if ($request->has('status')) {
            $query->where('is_active', $request->status);
        }

        // Sorting
        if ($request->has('sort_by')) {
            $sortColumn = $request->sort_by;
            $sortDirection = $request->has('sort_order') && $request->sort_order === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sortColumn, $sortDirection);
        }

        $products = $query->paginate(10); // Paginate the results

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'sku' => 'required|unique:products',
        ]);

        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Other CRUD methods (show, edit, update, destroy) remain unchanged.
     */
}
