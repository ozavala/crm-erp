<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductCategory::with('parentCategory')->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
        }
        $productCategories = $query->paginate(10)->withQueryString();
        return view('product_categories.index', compact('productCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('product_categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request)
    {
        ProductCategory::create($request->validated());
        return redirect()->route('product-categories.index')
                         ->with('success', 'Product category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load(['parentCategory', 'childCategories', 'products']);
        return view('product_categories.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        $categories = ProductCategory::where('category_id', '!=', $productCategory->category_id) // Exclude self
                                     ->orderBy('name')->get();
        return view('product_categories.edit', compact('productCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $productCategory->update($request->validated());
        return redirect()->route('product-categories.index')
                         ->with('success', 'Product category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        if ($productCategory->products()->exists()) {
            return redirect()->route('product-categories.index')
                             ->with('error', 'Cannot delete category. It is currently assigned to one or more products.');
        }
        if ($productCategory->childCategories()->exists()) {
            // Optionally, decide how to handle children: reassign or prevent deletion.
            // For now, preventing deletion if it has sub-categories.
            return redirect()->route('product-categories.index')
                             ->with('error', 'Cannot delete category. It has sub-categories. Please delete or reassign them first.');
        }
        $productCategory->delete();
        return redirect()->route('product-categories.index')
                         ->with('success', 'Product category deleted successfully.');
    }
}