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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('xp')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->unsignedBigInteger('class_id')->nullable();
            $table->foreign('class_id')->references('id')->on('classes'); // Adicionando a chave estrangeira
            $table->unsignedBigInteger('guild_id')->nullable();
            $table->foreign('guild_id')->references('id')->on('guilds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['guild_id']);
            $table->dropColumn('class_id');
            $table->dropColumn('guild_id');
        });
    }
};
