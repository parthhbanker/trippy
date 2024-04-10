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
        Schema::create('media', function (Blueprint $table) {

            $table->id();
            $table->string("url");
            $table->string("media_type");
            $table->string("name");

            //****************************

            $table->foreignId("sent_by")->constrained("users");
            $table->foreignId("group")->nullable()->constrained("groups");
            $table->foreignId("sent_to")->nullable()->constrained("users");
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
