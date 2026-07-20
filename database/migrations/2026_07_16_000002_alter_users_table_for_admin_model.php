<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable();
            }

            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable();
            }

            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
        });

        if (Schema::hasColumn('users', 'full_name')) {
            DB::table('users')
                ->whereNull('name')
                ->update(['name' => DB::raw('COALESCE(full_name, email)')]);
        }

        if (Schema::hasColumn('users', 'password_hash') && Schema::hasColumn('users', 'password')) {
            DB::table('users')
                ->whereNull('password')
                ->update(['password' => DB::raw('password_hash')]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('users', 'password')) {
                $table->dropColumn('password');
            }

            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }

            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
