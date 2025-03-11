<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Update records with NULL values to avoid constraint violations
        DB::table('accounts')->whereNull('name')->update(['name' => '']);
        DB::table('accounts')->whereNull('password')->update(['password' => '']);

        // Change the table structure
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
