<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halls', function (Blueprint $table): void {
            $table->id();
            $table->string('number')->unique();
            $table->float('area');
            $table->string('type')->default('exhibition');
            $table->string('svg_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
