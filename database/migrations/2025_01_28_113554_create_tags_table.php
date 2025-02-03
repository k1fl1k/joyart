<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->ulid('id')->primary(); // ULID як первинний ключ
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->json('aliases')->nullable();
            $table->string('icon')->nullable(); // Маленька картинка
            $table->string('image', 128)->nullable(); // Генерована картинка
            $table->string('image_alt', 256)->nullable();
            $table->string('slug', 71)->unique(); // Унікальний ідентифікатор
            $table->string('meta_title', 128)->unique();
            $table->string('meta_description', 376)->nullable();
            $table->timestamps();
        });

        Schema::Table('tags', function($table) {
            $table->foreignUlid('parent_id')->nullable()->constrained('tags')->onDelete('cascade'); // Використання `foreignUlid` для посилання на ULID
        });
    }

    public function down()
    {
        Schema::dropIfExists('tags');
    }
};
