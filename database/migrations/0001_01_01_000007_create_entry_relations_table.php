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
            $table->string('relation_type')->index();
            $table->timestamps();

            $table->index(['parent_entry_id', 'relation_type']);
            $table->index(['child_entry_id', 'relation_type']);
            $table->unique(['parent_entry_id', 'child_entry_id', 'relation_type']);
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
