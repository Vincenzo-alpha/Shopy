<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DealArchive;
use App\Models\DealMaster;
use App\Models\Payment;
use App\Models\PlatformSetting;
use App\Models\ProductService;
use App\Models\Seller;
use Illuminate\Http\Request;

class AdminDashboardController
{
    public function dashboard()
    {
        $totalSellers = Seller::count();
        $totalCustomers = Customer::count();
        $totalListings = ProductService::count();
        $activeDealsCount = DealArchive::count();
        $completedDealsCount = DealMaster::count();

        $totalRevenue = DealMaster::sum('platform_fee') ?? 0;
        $totalVolume = DealMaster::sum('agreed_amount') ?? 0;
        $activePlatformFee = PlatformSetting::getVal('platform_fee_percent', '5.00');

        $recentArchiveDeals = DealArchive::with(['seller', 'customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentCompletedDeals = DealMaster::with(['seller', 'customer', 'product'])
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSellers',
            'totalCustomers',
            'totalListings',
            'activeDealsCount',
            'completedDealsCount',
            'totalRevenue',
            'totalVolume',
            'activePlatformFee',
            'recentArchiveDeals',
            'recentCompletedDeals'
        ));
    }

    public function sellers()
    {
        $sellers = Seller::withCount(['products', 'activeDeals', 'completedDeals'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.sellers.index', compact('sellers'));
    }

    public function updateSellerStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,deactivated',
        ]);

        $seller = Seller::findOrFail($id);
        $seller->update(['account_status' => $request->status]);

        return back()->with('success', "Seller {$seller->seller_name} status updated to {$request->status}.");
    }

    public function customers()
    {
        $customers = Customer::withCount(['activeDeals', 'completedDeals'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function updateCustomerStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,deactivated',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update(['account_status' => $request->status]);

        return back()->with('success', "Customer {$customer->customer_name} status updated to {$request->status}.");
    }

    public function listings()
    {
        $listings = ProductService::with('seller')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.listings.index', compact('listings'));
    }

    public function deals()
    {
        $archiveDeals = DealArchive::with(['seller', 'customer', 'product', 'negotiation'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'archive_page');

        $completedDeals = DealMaster::with(['seller', 'customer', 'product'])
            ->orderBy('completed_at', 'desc')
            ->paginate(15, ['*'], 'master_page');

        return view('admin.deals.index', compact('archiveDeals', 'completedDeals'));
    }

    public function settings()
    {
        $feePercent = PlatformSetting::getVal('platform_fee_percent', '5.00');
        $currencySymbol = PlatformSetting::getVal('currency_symbol', '₹');
        $contactEmail = PlatformSetting::getVal('contact_email', 'support@shopy.local');

        return view('admin.settings.index', compact('feePercent', 'currencySymbol', 'contactEmail'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'platform_fee_percent' => 'required|numeric|min:0|max:50',
            'currency_symbol' => 'required|string|max:5',
            'contact_email' => 'required|email|max:150',
        ]);

        PlatformSetting::setVal('platform_fee_percent', (string) $request->platform_fee_percent, 'Platform fee percentage charged on completed deals');
        PlatformSetting::setVal('currency_symbol', $request->currency_symbol, 'Currency symbol');
        PlatformSetting::setVal('contact_email', $request->contact_email, 'Support contact email');

        return back()->with('success', 'Platform settings updated successfully. New deals will use the updated fee.');
    }
}
