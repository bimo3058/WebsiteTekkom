<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('eo_praktikum',fn(Blueprint $t)=>$t->json('final_components')->nullable());
        Schema::create('eo_prak_extra_scores',function(Blueprint $t){
            $t->id();$t->uuid('praktikum_id')->index();$t->uuid('praktikan_id');
            $t->json('scores');$t->unsignedInteger('version')->default(0);$t->timestamps();
            $t->unique(['praktikum_id','praktikan_id']);
        });
        Schema::create('eo_prak_score_histories',function(Blueprint $t){
            $t->id();$t->uuid('praktikum_id')->index();$t->string('scope');$t->string('module_id')->nullable();
            $t->uuid('praktikan_id');$t->string('score_key');$t->unsignedBigInteger('editor_id')->nullable();
            $t->string('editor_name');$t->decimal('before_value',8,4)->nullable();$t->decimal('after_value',8,4)->nullable();$t->timestamp('created_at');
            $t->index(['scope','module_id','praktikan_id'],'eo_score_history_lookup');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('eo_prak_score_histories');Schema::dropIfExists('eo_prak_extra_scores');
        Schema::table('eo_praktikum',fn(Blueprint $t)=>$t->dropColumn('final_components'));
    }
};
