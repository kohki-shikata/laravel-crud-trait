<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();

            // リレーションをforeignで定義し、onDeleteは設定しない
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');

            // 時刻カラムを個別に設定し、精度を指定
            $table->datetime('published_at')->nullable();
            $table->datetime('deleted_at')->nullable(); // Soft delete対応
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
