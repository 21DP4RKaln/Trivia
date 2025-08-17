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
        Schema::table('terms_of_service', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('updated_by');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('contact_address')->nullable()->after('contact_phone');
            $table->string('company_name')->nullable()->after('contact_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_of_service', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'contact_phone', 'contact_address', 'company_name']);
        });
    }
};
