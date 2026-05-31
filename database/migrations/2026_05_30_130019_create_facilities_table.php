<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table): void {
            $table->id();
            $table->string('number')->unique();
            $table->string('gender');
            $table->float('latitude');
            $table->float('longitude');
            $table->string('type'); // restaurant mosque bathroom ...
        });
    }
};
