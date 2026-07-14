<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('collection_fields')) {
            return;
        }

        Schema::create('collection_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->string('field_name');
            $table->string('field_label')->nullable();
            $table->string('field_type');
            $table->foreignId('related_collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->text('default_value')->nullable();
            $table->text('validation_rule')->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_fields');
    }
};
