<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('collections')) {
            return;
        }

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('collection_name')->unique();
            $table->string('display_name')->nullable();
            $table->string('api_endpoint')->unique()->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
