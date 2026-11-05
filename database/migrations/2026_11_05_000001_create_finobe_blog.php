<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        $schema = Schema::connection('finobe');
        $schema->create('blog_authors', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100);
            $t->string('slug', 130)->unique();
            $t->string('avatar_url', 500)->nullable();
            $t->text('bio')->nullable();
            $t->timestamps();
        });
        $schema->create('blog_tags', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100);
            $t->string('slug', 130)->unique();
            $t->text('description')->nullable();
            $t->timestamps();
        });
        $schema->create('blog_posts', function (Blueprint $t) {
            $t->id();
            $t->string('title', 255);
            $t->string('slug', 255)->unique();
            $t->text('excerpt')->nullable();
            $t->longText('body');
            $t->string('cover_url', 500)->nullable();
            $t->unsignedBigInteger('author_id')->nullable()->index();
            $t->string('status', 16)->default('draft')->index();
            $t->timestamp('published_at')->nullable()->index();
            $t->timestamps();
        });
        $schema->create('blog_post_tag', function (Blueprint $t) {
            $t->unsignedBigInteger('post_id');
            $t->unsignedBigInteger('tag_id');
            $t->primary(['post_id','tag_id']);
            $t->index('tag_id');
        });
        $schema->create('blog_settings', function (Blueprint $t) {
            $t->string('key', 80)->primary();
            $t->longText('value')->nullable();
        });
    }
    public function down(): void {
        $schema = Schema::connection('finobe');
        foreach (['blog_settings','blog_post_tag','blog_posts','blog_tags','blog_authors'] as $table) {
            $schema->dropIfExists($table);
        }
    }
};
