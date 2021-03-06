<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 用户表(管理员)
 * Class CreateUsersTable
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->char('user_name', 10)->unique()->comment('用户名');
            $table->string('name', 20)->comment('姓名');
            $table->string('password');
            $table->char('wx_openid', 28)->unique()->nullable()->comment('微信openid');
            // $table->boolean('gender')->comment('性别 false-男 true-女');
            $table->unsignedInteger('department_id')->index()->comment('学院id');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
