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
        Schema::create('artworks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUlid('tag_id')->nullable()->constrained('tags')->onDelete('cascade');
            $table->enum('type', ['image', 'animation', 'video']);
            $table->string('md5')->unique();
            $table->enum('rating', ['general', 'sensitive', 'questionable']);
            $table->integer('width');
            $table->integer('height');
            $table->string('file_ext');
            $table->unsignedBigInteger('file_size');
            $table->string('thumbnail', 512); // Шлях до превью
            $table->string('original', 512); // Шлях до оригіналу
            $table->boolean('is_vip');
            $table->json('colors'); // Масив із 4 кольорів
            $table->string('source', 512)->nullable(); // URL оригінального джерела
            $table->boolean('is_published')->default(false);
            $table->string('slug', 71)->unique();
            $table->string('meta_title', 128)->unique();
            $table->string('meta_description', 278);
            $table->string('image', 512)->nullable();
            $table->string('image_alt', 256)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
