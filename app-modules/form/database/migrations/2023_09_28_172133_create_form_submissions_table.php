<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->json('content');

            $table->foreignUuid('form_id')->constrained('forms');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
