<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('business_sector')->index();
            $table->json('social_links');
            $table->string('phone');
            $table->integer('year_founded');
            $table->text('description');
            $table->float('headquarters_lat')->nullable();
            $table->float('headquarters_lng')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
