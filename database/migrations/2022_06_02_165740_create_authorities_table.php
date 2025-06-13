<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthoritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('权限名称');
            $table->string('alias')->comment('权限别名');
            $table->unsignedTinyInteger('type')->comment('类型：0-菜单/1-导航/2-按钮');
            $table->unsignedBigInteger('pid')->default(0)->comment('父级ID');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->softDeletes()->index('idx_deleted_at');
            $table->timestamps();

            $table->unique(['name', 'pid'], 'unq_n_p');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authorities');
    }
}
