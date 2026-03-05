<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['private', 'group']);
            $table->string('title')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->boolean('is_draft')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_muted')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
