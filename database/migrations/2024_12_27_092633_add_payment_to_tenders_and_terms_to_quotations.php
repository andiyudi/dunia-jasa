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
        Schema::table('tenders', function (Blueprint $table) {
            $table->string('payment')->nullable()->after('estimation'); // Ganti 'column_name' dengan kolom sebelum payment
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->text('terms_price')->nullable()->after('delivery_time'); // Ganti 'column_name' dengan kolom sebelum terms
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn('payment');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('terms_price');
        });
    }
};
