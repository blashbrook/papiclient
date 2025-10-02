<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patron_stat_class_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('StatisticalClassID');
            $table->integer('OrganizationID');
            $table->string('Description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patron_stat_class_codes');
    }
};
