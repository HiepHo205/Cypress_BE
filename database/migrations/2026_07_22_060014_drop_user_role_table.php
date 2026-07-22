<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_role');
    }

    public function down(): void
    {
        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary([
                'user_id',
                'role_id'
            ]);
        });
    }
};