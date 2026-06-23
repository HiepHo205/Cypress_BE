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
        Schema::create('entry_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_entry_id')->constrained('entries')->cascadeOnDelete();
            $table->foreignId('child_entry_id')->constrained('entries')->cascadeOnDelete();
            $table->string('relation_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_relations');
    }
};
