<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductService;
use App\Models\SellerCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductServiceController
{
    public function index()
    {
        $seller = Auth::guard('seller')->user();
        $products = ProductService::where('seller_id_fk', $seller->seller_id_pk)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        return view('seller.products.create');
    }

    public function store(Request $request)
    {
        $seller = Auth::guard('seller')->user();

        $validated = $request->validate([
            'prod_service_name' => 'required|string|max:200',
            'category' => 'required|string|max:100',
            'item_type' => 'required|in:product,service',
            'listed_price' => 'required|numeric|min:1',
            'minimum_rate' => 'required|numeric|min:1|lte:maximum_rate',
            'maximum_rate' => 'required|numeric|min:1|gte:minimum_rate',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'minimum_rate.lte' => 'The minimum rate must be less than or equal to the maximum rate.',
            'maximum_rate.gte' => 'The maximum rate must be greater than or equal to the minimum rate.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('listings', 'public');
        }

        $prefix = $validated['item_type'] === 'service' ? 'SRV' : 'PRD';
        $uniqueNo = $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        ProductService::create([
            'prod_servics_unique_no' => $uniqueNo,
            'seller_id_fk' => $seller->seller_id_pk,
            'prod_service_name' => $validated['prod_service_name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'item_type' => $validated['item_type'],
            'listed_price' => $validated['listed_price'],
            'minimum_rate' => $validated['minimum_rate'],
            'maximum_rate' => $validated['maximum_rate'],
            'image_path' => $imagePath,
            'availability_status' => 'available',
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Listing created successfully.');
    }

    public function edit($id)
    {
        $seller = Auth::guard('seller')->user();
        $product = ProductService::where('prod_service_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        return view('seller.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $seller = Auth::guard('seller')->user();
        $product = ProductService::where('prod_service_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        $validated = $request->validate([
            'prod_service_name' => 'required|string|max:200',
            'category' => 'required|string|max:100',
            'item_type' => 'required|in:product,service',
            'listed_price' => 'required|numeric|min:1',
            'minimum_rate' => 'required|numeric|min:1|lte:maximum_rate',
            'maximum_rate' => 'required|numeric|min:1|gte:minimum_rate',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'availability_status' => 'required|in:available,unavailable,deactivated',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = $request->file('image')->store('listings', 'public');
        }

        $product->update([
            'prod_service_name' => $validated['prod_service_name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'item_type' => $validated['item_type'],
            'listed_price' => $validated['listed_price'],
            'minimum_rate' => $validated['minimum_rate'],
            'maximum_rate' => $validated['maximum_rate'],
            'availability_status' => $validated['availability_status'],
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Listing updated successfully.');
    }

    public function toggleAvailability($id)
    {
        $seller = Auth::guard('seller')->user();
        $product = ProductService::where('prod_service_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        $newStatus = $product->availability_status === 'available' ? 'unavailable' : 'available';
        $product->update(['availability_status' => $newStatus]);

        return back()->with('success', "Listing status updated to {$newStatus}.");
    }

    public function cities()
    {
        $seller = Auth::guard('seller')->user();
        $cities = SellerCity::where('seller_id_fk', $seller->seller_id_pk)->get();

        return view('seller.cities.index', compact('cities'));
    }

    public function addCity(Request $request)
    {
        $seller = Auth::guard('seller')->user();
        $request->validate([
            'city_name' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        SellerCity::firstOrCreate([
            'seller_id_fk' => $seller->seller_id_pk,
            'city_name' => trim($request->city_name),
        ], [
            'state' => $request->state,
        ]);

        return back()->with('success', 'Service city added.');
    }

    public function deleteCity($id)
    {
        $seller = Auth::guard('seller')->user();
        SellerCity::where('city_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->delete();

        return back()->with('success', 'Service city removed.');
    }
}
