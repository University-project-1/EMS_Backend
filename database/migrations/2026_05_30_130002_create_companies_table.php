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
            $table->string('name');
            $table->string('business_sector');
            $table->json('social_links');
            $table->string('phone')->unique();
            $table->integer('year_founded');
            $table->text('description');
            $table->float('headquarters_lat');
            $table->float('headquarters_lng');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
