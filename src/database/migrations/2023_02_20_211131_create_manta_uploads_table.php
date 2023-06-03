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
        Schema::create('manta_uploads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('sort')->default(0);
            $table->tinyInteger('main')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->string('host')->nullable();
            $table->string('locale')->nullable();
            $table->string('title')->nullable();
            $table->string('seo_title')->nullable();
            $table->boolean('private')->default(0);
            $table->string('disk')->nullable();
            $table->text('url')->nullable();
            $table->string('location')->nullable();
            $table->string('filename')->nullable();
            $table->string('extension')->nullable();
            $table->string('mime')->nullable();
            $table->integer('size')->nullable();
            $table->string('model')->nullable();
            $table->string('pid')->nullable();
            $table->string('identifier')->nullable();
            $table->string('originalName')->nullable();
            $table->boolean('pdfLock')->default(0);
            $table->text('comments')->nullable();
            $table->text('error')->nullable();
            $table->integer('pages')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manta_uploads');
    }
};
