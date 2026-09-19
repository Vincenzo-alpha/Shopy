<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Admin Master
        Schema::create('sk_admin_master', function (Blueprint $table) {
            $table->id('admin_id_pk');
            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Seller Master
        Schema::create('sk_seller_master', function (Blueprint $table) {
            $table->id('seller_id_pk');
            $table->string('seller_unique_no', 30)->unique();
            $table->string('seller_name', 150);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('contact_no', 20);
            $table->text('address');
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('account_status', 20)->default('active'); // active, suspended, deactivated
            $table->rememberToken();
            $table->timestamps();

            $table->index('city');
            $table->index('account_status');
        });

        // 3. Seller Cities Coverage (Normalized)
        Schema::create('sk_seller_cities', function (Blueprint $table) {
            $table->id('city_id_pk');
            $table->foreignId('seller_id_fk')->constrained('sk_seller_master', 'seller_id_pk')->cascadeOnDelete();
            $table->string('city_name', 100);
            $table->string('state', 100)->nullable();
            $table->timestamps();

            $table->index('city_name');
            $table->unique(['seller_id_fk', 'city_name']);
        });

        // 4. Customer Master
        Schema::create('sk_customer_master', function (Blueprint $table) {
            $table->id('customer_id_pk');
            $table->string('customer_unique_no', 30)->unique();
            $table->string('customer_name', 150);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('contact_no', 20);
            $table->text('address');
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('account_status', 20)->default('active'); // active, suspended, deactivated
            $table->rememberToken();
            $table->timestamps();

            $table->index('city');
            $table->index('account_status');
        });

        // 5. Product & Service Master (Exact preserved table name)
        Schema::create('sk_product_sevice_master', function (Blueprint $table) {
            $table->id('prod_service_id_pk');
            $table->string('prod_servics_unique_no', 30)->unique();
            $table->foreignId('seller_id_fk')->constrained('sk_seller_master', 'seller_id_pk')->cascadeOnDelete();
            $table->string('prod_service_name', 200);
            $table->text('description')->nullable();
            $table->string('category', 100);
            $table->string('item_type', 20); // 'product' or 'service'
            $table->decimal('listed_price', 12, 2);
            $table->decimal('minimum_rate', 12, 2);
            $table->decimal('maximum_rate', 12, 2);
            $table->string('image_path', 255)->nullable();
            $table->string('availability_status', 20)->default('available'); // available, unavailable, deactivated
            $table->timestamps();

            $table->index('category');
            $table->index('item_type');
            $table->index('availability_status');
            $table->index(['minimum_rate', 'maximum_rate']);
        });

        // 6. Interest Master
        Schema::create('sk_interest_master', function (Blueprint $table) {
            $table->id('interest_id_pk');
            $table->string('interest_unique_no', 30)->unique();
            $table->foreignId('seller_id_fk')->constrained('sk_seller_master', 'seller_id_pk')->cascadeOnDelete();
            $table->foreignId('customer_id_fk')->constrained('sk_customer_master', 'customer_id_pk')->cascadeOnDelete();
            $table->foreignId('prod_service_id_fk')->constrained('sk_product_sevice_master', 'prod_service_id_pk')->cascadeOnDelete();
            $table->string('interest_status', 30)->default('Active'); // Active, Withdrawn, Converted to deal
            $table->timestamps();

            $table->index('interest_status');
        });

        // 7. Deal Archive (Pending and In-Progress Deals)
        Schema::create('sk_deal_archive', function (Blueprint $table) {
            $table->id('deal_id_pk');
            $table->string('deal_unique_no', 30)->unique();
            $table->unsignedBigInteger('interest_id_fk')->nullable();
            $table->unsignedBigInteger('seller_id_fk');
            $table->unsignedBigInteger('customer_id_fk');
            $table->unsignedBigInteger('prod_service_id_fk');
            $table->string('active_status', 20)->default('active'); // active, cancelled, disputed
            $table->unsignedBigInteger('neg_id_fk')->nullable();
            $table->decimal('agreed_amount', 12, 2)->nullable();
            $table->decimal('platform_fee_percent', 5, 2)->nullable();
            $table->decimal('platform_fee', 12, 2)->nullable();
            $table->decimal('seller_net_amount', 12, 2)->nullable();
            $table->string('fulfillment_method', 50)->default('customer_collection'); // customer_collection, service_receipt
            $table->string('payment_status', 20)->default('unpaid'); // unpaid, pending, paid, failed, refunded
            $table->string('deal_status', 30)->default('negotiating'); // negotiating, offer_accepted, payment_pending, paid, in_fulfillment, cancelled, disputed
            $table->timestamps();

            $table->index('deal_status');
            $table->index('payment_status');
            $table->index('seller_id_fk');
            $table->index('customer_id_fk');
        });

        // 8. Deal Master (Completed Deals)
        Schema::create('sk_deal_master', function (Blueprint $table) {
            $table->id('deal_id_pk');
            $table->string('deal_unique_no', 30)->unique();
            $table->unsignedBigInteger('interest_id_fk')->nullable();
            $table->unsignedBigInteger('seller_id_fk');
            $table->unsignedBigInteger('customer_id_fk');
            $table->unsignedBigInteger('prod_service_id_fk');
            $table->string('active_status', 20)->default('active');
            $table->unsignedBigInteger('neg_id_fk')->nullable();
            $table->decimal('agreed_amount', 12, 2)->nullable();
            $table->decimal('platform_fee_percent', 5, 2)->nullable();
            $table->decimal('platform_fee', 12, 2)->nullable();
            $table->decimal('seller_net_amount', 12, 2)->nullable();
            $table->string('fulfillment_method', 50)->default('customer_collection');
            $table->string('payment_status', 20)->default('paid');
            $table->string('deal_status', 30)->default('completed');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('seller_id_fk');
            $table->index('customer_id_fk');
            $table->index('completed_at');
        });

        // 9. Negotiation Table
        Schema::create('sk_negotiation', function (Blueprint $table) {
            $table->id('neg_id_pk');
            $table->unsignedBigInteger('deal_id_fk');
            $table->decimal('seller_negotiation_amt', 12, 2);
            $table->decimal('customer_negotiation_amt', 12, 2);
            $table->string('current_offer_by', 20); // 'seller' or 'customer'
            $table->string('negotiation_status', 20)->default('in_progress'); // in_progress, accepted, rejected, expired
            $table->timestamps();

            $table->index('deal_id_fk');
            $table->index('negotiation_status');
        });

        // 10. Negotiation History (Offer Log)
        Schema::create('sk_negotiation_history', function (Blueprint $table) {
            $table->id('history_id_pk');
            $table->foreignId('neg_id_fk')->constrained('sk_negotiation', 'neg_id_pk')->cascadeOnDelete();
            $table->string('offered_by', 20); // 'seller' or 'customer'
            $table->decimal('amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('neg_id_fk');
        });

        // 11. Seller Wallet
        Schema::create('sk_seller_wallet', function (Blueprint $table) {
            $table->id('wallet_id_pk');
            $table->foreignId('seller_id_fk')->unique()->constrained('sk_seller_master', 'seller_id_pk')->cascadeOnDelete();
            $table->decimal('available_balance', 12, 2)->default(0.00);
            $table->decimal('pending_balance', 12, 2)->default(0.00);
            $table->timestamps();
        });

        // 12. Wallet Transactions (Ledger)
        Schema::create('sk_wallet_transactions', function (Blueprint $table) {
            $table->id('transaction_id_pk');
            $table->foreignId('seller_id_fk')->constrained('sk_seller_master', 'seller_id_pk')->cascadeOnDelete();
            $table->unsignedBigInteger('deal_id_fk')->nullable();
            $table->string('transaction_type', 30); // 'credit_earnings', 'fee_deduction', 'refund', 'simulated_credit'
            $table->decimal('amount', 12, 2);
            $table->string('transaction_status', 20)->default('completed'); // completed, pending, cancelled
            $table->string('reference_no', 100);
            $table->timestamps();

            $table->index(['seller_id_fk', 'transaction_type']);
        });

        // 13. Payments (Sandbox)
        Schema::create('sk_payments', function (Blueprint $table) {
            $table->id('payment_id_pk');
            $table->unsignedBigInteger('deal_id_fk');
            $table->foreignId('customer_id_fk')->constrained('sk_customer_master', 'customer_id_pk')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50); // 'sandbox_card', 'sandbox_upi', 'sandbox_netbanking'
            $table->string('payment_status', 20); // 'pending', 'success', 'failed', 'refunded'
            $table->string('payment_reference', 100)->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('deal_id_fk');
            $table->index('payment_status');
        });

        // 14. Deal Completion Key (Hashed)
        Schema::create('sk_deal_completion', function (Blueprint $table) {
            $table->id('completion_id_pk');
            $table->unsignedBigInteger('deal_id_fk')->unique();
            $table->string('completion_key_hash', 100);
            $table->string('completion_status', 20)->default('pending'); // pending, completed, expired
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('deal_id_fk');
            $table->index('completion_status');
        });

        // 15. Platform Settings
        Schema::create('sk_platform_settings', function (Blueprint $table) {
            $table->id('setting_id_pk');
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_platform_settings');
        Schema::dropIfExists('sk_deal_completion');
        Schema::dropIfExists('sk_payments');
        Schema::dropIfExists('sk_wallet_transactions');
        Schema::dropIfExists('sk_seller_wallet');
        Schema::dropIfExists('sk_negotiation_history');
        Schema::dropIfExists('sk_negotiation');
        Schema::dropIfExists('sk_deal_master');
        Schema::dropIfExists('sk_deal_archive');
        Schema::dropIfExists('sk_interest_master');
        Schema::dropIfExists('sk_product_sevice_master');
        Schema::dropIfExists('sk_customer_master');
        Schema::dropIfExists('sk_seller_cities');
        Schema::dropIfExists('sk_seller_master');
        Schema::dropIfExists('sk_admin_master');
    }
};
