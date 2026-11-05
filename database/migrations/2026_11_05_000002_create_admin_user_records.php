<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::connection('finobe')->create('admin_user_notes', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index();
            $t->unsignedBigInteger('moderator_id')->nullable();
            $t->text('body'); $t->timestamps();
        });
        Schema::connection('finobe')->create('admin_user_log', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->index();
            $t->unsignedBigInteger('moderator_id')->nullable();
            $t->string('action',100); $t->text('details')->nullable(); $t->timestamps();
        });
    }
    public function down(): void {
        Schema::connection('finobe')->dropIfExists('admin_user_log');
        Schema::connection('finobe')->dropIfExists('admin_user_notes');
    }
};
