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
        Schema::create('course_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who suggested
            $table->string('code');
            $table->string('title');
            $table->decimal('credits', 4, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();

            $table->unique(['university_id', 'department_id', 'code']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_suggestions');
    }
};
