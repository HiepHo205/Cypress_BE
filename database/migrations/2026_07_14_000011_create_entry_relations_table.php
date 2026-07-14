<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('entry_relations')) {
            return;
        }

        Schema::create('entry_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_entry_id')->constrained('entries')->cascadeOnDelete();
            $table->foreignId('child_entry_id')->constrained('entries')->cascadeOnDelete();
            $table->string('relation_name')->nullable();
            $table->string('relation_type')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_relations');
    }
};
