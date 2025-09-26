<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('patron_udfs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('PatronUdfID')->unique();
            $table->string('Label');
            $table->boolean('Display')->nullable();
            $table->text('Values')->nullable();
            $table->boolean('Required')->nullable();
            $table->string('DefaultValue')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patron_udfs');
    }
};
