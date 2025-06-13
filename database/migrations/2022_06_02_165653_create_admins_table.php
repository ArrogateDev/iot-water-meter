<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('姓名');
			$table->string('avatar')->nullable()->comment('头像');
			$table->string('account')->nullable()->comment('账号');
			$table->string('chinese_name')->nullable()->comment('中文名');
			$table->string('english_name')->nullable()->comment('英文名');
			$table->string('simple_name')->nullable()->comment('简称');
			$table->string('gender')->nullable()->comment('性别');
			$table->string('job')->nullable()->comment('职务');
			$table->string('role_name')->nullable()->comment('角色');
			$table->string('department')->nullable()->comment('部门');
			$table->string('parent_department')->nullable()->comment('上级部门');
			$table->string('password')->comment('密码');
			$table->tinyInteger('status')->default(0)->comment('状态:0-正常/1-禁用');
            $table->softDeletes()->index('idx_deleted_at');
            $table->timestamps();

            $table->unique(['account'], 'unique_a');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
