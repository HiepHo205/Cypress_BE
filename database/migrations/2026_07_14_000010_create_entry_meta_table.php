<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('entry_meta')) {
            return;
        }

        Schema::create('entry_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('entries')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('collection_fields')->cascadeOnDelete();
            $table->text('meta_value')->nullable();
            $table->timestamps();
            $table->index('entry_id');
            $table->index('field_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_meta');
    }
};
