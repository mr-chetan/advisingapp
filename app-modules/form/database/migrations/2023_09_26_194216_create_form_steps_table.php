<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('form_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('label');
            $table->foreignUuid('form_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};