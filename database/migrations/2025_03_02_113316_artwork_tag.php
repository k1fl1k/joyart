<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('artwork_tag', function (Blueprint $table) {
            $table->foreignUlid('artwork_id')->constrained('artworks')->onDelete('cascade');
            $table->foreignUlid('tag_id')->constrained('tags')->onDelete('cascade');
            $table->primary(['artwork_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('artwork_tag');
    }
};
