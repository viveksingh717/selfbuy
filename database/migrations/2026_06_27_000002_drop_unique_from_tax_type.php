<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tax_models', function (Blueprint $table) {
            $table->dropUnique('tax_models_tax_type_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tax_models', function (Blueprint $table) {
            $table->unique('tax_type');
        });
    }
};
