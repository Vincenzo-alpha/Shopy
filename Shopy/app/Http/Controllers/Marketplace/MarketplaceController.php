<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\ProductService;
use App\Models\SellerCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceController
{
    public function index(Request $request)
    {
        $query = ProductService::with(['seller.cities'])
            ->where('availability_status', 'available')
            ->whereHas('seller', function ($q) {
                $q->where('account_status', 'active');
            });

        // Keyword Search
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('prod_service_name', 'ilike', "%{$keyword}%")
                  ->orWhere('description', 'ilike', "%{$keyword}%")
                  ->orWhere('category', 'ilike', "%{$keyword}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Item Type filter (product / service)
        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('listed_price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('listed_price', '<=', (float) $request->max_price);
        }

        // City filter
        if ($request->filled('city')) {
            $city = trim($request->city);
            $query->where(function ($q) use ($city) {
                // For products: seller base city
                $q->where(function ($sub) use ($city) {
                    $sub->where('item_type', 'product')
                        ->whereHas('seller', function ($sq) use ($city) {
                            $sq->where('city', 'ilike', "%{$city}%");
                        });
                })
                // For services: service coverage cities or seller base city
                ->orWhere(function ($sub) use ($city) {
                    $sub->where('item_type', 'service')
                        ->whereHas('seller', function ($sq) use ($city) {
                            $sq->where('city', 'ilike', "%{$city}%")
                               ->orWhereHas('cities', function ($cq) use ($city) {
                                   $cq->where('city_name', 'ilike', "%{$city}%");
                               });
                        });
                });
            });
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Distinct categories and cities for filters
        $categories = ProductService::where('availability_status', 'available')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $cities = SellerCity::distinct()
            ->pluck('city_name')
            ->sort()
            ->values();

        if ($request->ajax()) {
            return view('marketplace.partials.listing-grid', compact('listings'))->render();
        }

        return view('marketplace.index', compact('listings', 'categories', 'cities'));
    }

    public function show($id)
    {
        $item = ProductService::with(['seller.cities'])
            ->where('prod_service_id_pk', $id)
            ->firstOrFail();

        $existingInterest = null;
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $existingInterest = Interest::where('customer_id_fk', $customer->customer_id_pk)
                ->where('prod_service_id_fk', $item->prod_service_id_pk)
                ->where('interest_status', 'Active')
                ->first();
        }

        return view('marketplace.show', compact('item', 'existingInterest'));
    }
}
