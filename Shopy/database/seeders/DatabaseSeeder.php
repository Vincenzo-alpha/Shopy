<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Seller;
use App\Models\SellerCity;
use App\Models\SellerWallet;
use App\Models\Customer;
use App\Models\ProductService;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Platform Settings
        PlatformSetting::setVal('platform_fee_percent', '5.00', 'Platform fee percentage charged to sellers upon completed deal');
        PlatformSetting::setVal('currency_symbol', '₹', 'Marketplace currency display symbol');
        PlatformSetting::setVal('contact_email', 'support@shopy.local', 'Marketplace support contact email');

        // 2. Default Admin
        Admin::updateOrCreate(
            ['email' => 'admin@shopy.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Sellers
        $seller1 = Seller::updateOrCreate(
            ['email' => 'seller1@shopy.com'],
            [
                'seller_unique_no' => 'SEL-2026-0001',
                'seller_name' => 'TechHub Electronics & Gadgets',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 98765 43210',
                'address' => 'Plot 42, Lamington Road, Grant Road East',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'account_status' => 'active',
            ]
        );

        SellerWallet::firstOrCreate(
            ['seller_id_fk' => $seller1->seller_id_pk],
            ['available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        foreach (['Mumbai', 'Pune', 'Thane', 'Navi Mumbai'] as $city) {
            SellerCity::firstOrCreate([
                'seller_id_fk' => $seller1->seller_id_pk,
                'city_name' => $city,
            ], [
                'state' => 'Maharashtra',
            ]);
        }

        $seller2 = Seller::updateOrCreate(
            ['email' => 'seller2@shopy.com'],
            [
                'seller_unique_no' => 'SEL-2026-0002',
                'seller_name' => 'Urban Spark Cleaning & Repair',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 98765 43211',
                'address' => '120, 80 Feet Road, 4th Block, Koramangala',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'account_status' => 'active',
            ]
        );

        SellerWallet::firstOrCreate(
            ['seller_id_fk' => $seller2->seller_id_pk],
            ['available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        foreach (['Bengaluru', 'Mysuru'] as $city) {
            SellerCity::firstOrCreate([
                'seller_id_fk' => $seller2->seller_id_pk,
                'city_name' => $city,
            ], [
                'state' => 'Karnataka',
            ]);
        }

        $seller3 = Seller::updateOrCreate(
            ['email' => 'seller3@shopy.com'],
            [
                'seller_unique_no' => 'SEL-2026-0003',
                'seller_name' => 'Delhi Furniture & Home Decor',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 98765 43212',
                'address' => 'Shop 7, Kirti Nagar Furniture Market',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'account_status' => 'active',
            ]
        );

        SellerWallet::firstOrCreate(
            ['seller_id_fk' => $seller3->seller_id_pk],
            ['available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        foreach (['Delhi', 'Gurugram', 'Noida', 'Faridabad'] as $city) {
            SellerCity::firstOrCreate([
                'seller_id_fk' => $seller3->seller_id_pk,
                'city_name' => $city,
            ], [
                'state' => 'Delhi',
            ]);
        }

        $seller4 = Seller::updateOrCreate(
            ['email' => 'seller4@shopy.com'],
            [
                'seller_unique_no' => 'SEL-2026-0004',
                'seller_name' => 'Chennai Fashion Studio',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 98765 43213',
                'address' => '15, Anna Salai, T. Nagar',
                'city' => 'Chennai',
                'state' => 'Tamil Nadu',
                'account_status' => 'active',
            ]
        );

        SellerWallet::firstOrCreate(
            ['seller_id_fk' => $seller4->seller_id_pk],
            ['available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        foreach (['Chennai', 'Coimbatore', 'Madurai'] as $city) {
            SellerCity::firstOrCreate([
                'seller_id_fk' => $seller4->seller_id_pk,
                'city_name' => $city,
            ], [
                'state' => 'Tamil Nadu',
            ]);
        }

        $seller5 = Seller::updateOrCreate(
            ['email' => 'seller5@shopy.com'],
            [
                'seller_unique_no' => 'SEL-2026-0005',
                'seller_name' => 'Hyderabad Biryani & Catering Co.',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 98765 43214',
                'address' => '8-2-293, Rd No. 78, Jubilee Hills',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'account_status' => 'active',
            ]
        );

        SellerWallet::firstOrCreate(
            ['seller_id_fk' => $seller5->seller_id_pk],
            ['available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        foreach (['Hyderabad', 'Secunderabad', 'Warangal'] as $city) {
            SellerCity::firstOrCreate([
                'seller_id_fk' => $seller5->seller_id_pk,
                'city_name' => $city,
            ], [
                'state' => 'Telangana',
            ]);
        }

        // 4. Customers
        Customer::updateOrCreate(
            ['email' => 'customer1@shopy.com'],
            [
                'customer_unique_no' => 'CUS-2026-0001',
                'customer_name' => 'Santanu Kumar',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 91234 56780',
                'address' => 'Flat 304, Palm View Heights, Andheri West',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'account_status' => 'active',
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'customer2@shopy.com'],
            [
                'customer_unique_no' => 'CUS-2026-0002',
                'customer_name' => 'Priya Sharma',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 91234 56781',
                'address' => 'Villa 12, Green Glen Layout, Bellandur',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'account_status' => 'active',
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'customer3@shopy.com'],
            [
                'customer_unique_no' => 'CUS-2026-0003',
                'customer_name' => 'Rahul Verma',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 91234 56782',
                'address' => 'B-47, Sector 18, Rohini',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'account_status' => 'active',
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'customer4@shopy.com'],
            [
                'customer_unique_no' => 'CUS-2026-0004',
                'customer_name' => 'Anjali Reddy',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 91234 56783',
                'address' => 'Flat 102, Aparna Sarovar Grande, Nallagandla',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'account_status' => 'active',
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'customer5@shopy.com'],
            [
                'customer_unique_no' => 'CUS-2026-0005',
                'customer_name' => 'Karthik Subramaniam',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 91234 56784',
                'address' => '23, Besant Nagar, 5th Avenue',
                'city' => 'Chennai',
                'state' => 'Tamil Nadu',
                'account_status' => 'active',
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'customer6@shopy.com'],
            [
                'customer_unique_no' => 'CUS-2026-0006',
                'customer_name' => 'Neha Joshi',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 91234 56785',
                'address' => 'Row House 9, Baner Road, Balewadi',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'account_status' => 'active',
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'customer7@shopy.com'],
            [
                'customer_unique_no' => 'CUS-2026-0007',
                'customer_name' => 'Amitesh Patel',
                'password' => Hash::make('password123'),
                'contact_no' => '+91 91234 56786',
                'address' => 'C/103, Shivranjani Society, Satellite',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'account_status' => 'active',
            ]
        );

        // 5. Products & Services
        ProductService::updateOrCreate(
            ['prod_servics_unique_no' => 'PRD-2026-0001'],
            [
                'seller_id_fk' => $seller1->seller_id_pk,
                'prod_service_name' => 'Sony WH-1000XM5 Wireless Noise Canceling Headphones',
                'description' => 'Flagship noise cancelling headphones with 30-hour battery life, 8 microphones, and ultra-comfortable lightweight design. Brand new in box with warranty.',
                'category' => 'Electronics',
                'item_type' => 'product',
                'listed_price' => 26999.00,
                'minimum_rate' => 23000.00,
                'maximum_rate' => 28000.00,
                'image_path' => null,
                'availability_status' => 'available',
            ]
        );

        ProductService::updateOrCreate(
            ['prod_servics_unique_no' => 'PRD-2026-0002'],
            [
                'seller_id_fk' => $seller1->seller_id_pk,
                'prod_service_name' => 'Apple MacBook Air M2 (16GB RAM, 512GB SSD) - Space Gray',
                'description' => 'Stunning 13.6-inch Liquid Retina display, MagSafe charging, 18-hour battery life. Mint condition, open box testing unit with original invoice.',
                'category' => 'Computers',
                'item_type' => 'product',
                'listed_price' => 94999.00,
                'minimum_rate' => 88000.00,
                'maximum_rate' => 96000.00,
                'image_path' => null,
                'availability_status' => 'available',
            ]
        );

        ProductService::updateOrCreate(
            ['prod_servics_unique_no' => 'SRV-2026-0001'],
            [
                'seller_id_fk' => $seller2->seller_id_pk,
                'prod_service_name' => 'Complete Villa & Apartment Deep Cleaning Service',
                'description' => 'Professional 4-member crew deep cleaning including steam sanitation for bathrooms, kitchen degreasing, sofa vacuuming, floor scrubbing, and balcony jet wash.',
                'category' => 'Cleaning',
                'item_type' => 'service',
                'listed_price' => 3500.00,
                'minimum_rate' => 2800.00,
                'maximum_rate' => 4000.00,
                'image_path' => null,
                'availability_status' => 'available',
            ]
        );

        ProductService::updateOrCreate(
            ['prod_servics_unique_no' => 'SRV-2026-0002'],
            [
                'seller_id_fk' => $seller2->seller_id_pk,
                'prod_service_name' => 'Split AC Deep Cleaning & Gas Pressure Checkup',
                'description' => 'Foam wash of indoor unit coils, high pressure blower cleaning, filter disinfection, outdoor condenser jet spray, and refrigerant pressure audit.',
                'category' => 'Appliance Repair',
                'item_type' => 'service',
                'listed_price' => 1499.00,
                'minimum_rate' => 1100.00,
                'maximum_rate' => 1600.00,
                'image_path' => null,
                'availability_status' => 'available',
            ]
        );
    }
}
