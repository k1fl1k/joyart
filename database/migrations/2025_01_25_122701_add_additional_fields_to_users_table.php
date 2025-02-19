<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use k1fl1k\joyart\Enums\Gender;
use k1fl1k\joyart\Enums\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Update the users table
        Schema::table('users', function (Blueprint $table) {
            // Drop and recreate the ID with ULID
            $table->dropColumn('id');
            $table->ulid('id')->primary(); // Add ULID as new ID

            // Rename and modify username
            $table->renameColumn('name', 'username');
            $table->string('username')->unique()->change();

            // Add additional fields
            $table->date('birthday')->nullable();
            $table->enum('gender', array_column(Gender::cases(), 'value'))->nullable();  // Correctly adding the gender column with the ENUM type
            $table->enum('role', array_column(Role::cases(), 'value'))->default(Role::USER->value);  // Default role
            $table->string('avatar', 2048)->nullable();
            $table->string('backdrop', 2048)->nullable();
            $table->text('description', 2048)->nullable();
            $table->boolean('allow_adult')->default(false);
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignUlid('user_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        // Drop columns and revert to previous state
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->id();

            $table->dropColumn('role');
            $table->dropColumn('avatar');
            $table->dropColumn('backdrop');
            $table->dropColumn('gender');
            $table->dropColumn('description');
            $table->dropColumn('birthday');
            $table->dropColumn('allow_adult');
        });

        // Drop the ENUM types
        DB::unprepared('DROP TYPE role');
        DB::unprepared('DROP TYPE gender');

        // Modify the sessions table
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('user_id')->nullable()->index();
        });
    }
};
