<?php

namespace App\Http\Controllers;

use App\Models\ProductFeature;
use App\Http\Requests\StoreProductFeatureRequest;
use App\Http\Requests\UpdateProductFeatureRequest;
use Illuminate\Http\Request;

class ProductFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductFeature::query()->latest();
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
        }
        $productFeatures = $query->paginate(10)->withQueryString();
        return view('product_features.index', compact('productFeatures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product_features.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductFeatureRequest $request)
    {
        ProductFeature::create($request->validated());
        return redirect()->route('product-features.index')
                         ->with('success', 'Product feature created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductFeature $productFeature)
    {
        // $productFeature->load('products'); // If you want to show products associated with this feature
        return view('product_features.show', compact('productFeature'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductFeature $productFeature)
    {
        return view('product_features.edit', compact('productFeature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductFeatureRequest $request, ProductFeature $productFeature)
    {
        $productFeature->update($request->validated());
        return redirect()->route('product-features.index')
                         ->with('success', 'Product feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductFeature $productFeature)
    {
        if ($productFeature->products()->exists()) {
            return redirect()->route('product-features.index')
                             ->with('error', 'Cannot delete feature. It is currently assigned to one or more products.');
        }
        $productFeature->delete();
        return redirect()->route('product-features.index')
                         ->with('success', 'Product feature deleted successfully.');
    }
}