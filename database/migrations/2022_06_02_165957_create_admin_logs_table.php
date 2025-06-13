<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(0)->comment('类型：0-正常访问/1-非法访问');
            $table->unsignedBigInteger('admin_id')->default(0)->comment('管理员ID');
            $table->string('username', 30)->default('')->comment('管理员名字');
            $table->string('url', 1500)->default('')->comment('操作页面');
            $table->string('title', 100)->default('')->comment('日志标题');
            $table->string('method', 10)->comment('请求方式');
            $table->jsonb('content')->comment('内容');
            $table->string('ip', 50)->default('')->comment('IP');
            $table->text('useragent')->comment('User-Agent');
            $table->softDeletes()->index('idx_deleted_at');
            $table->timestamps();

            $table->index(['admin_id'], 'idx_a');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_logs');
    }
}
