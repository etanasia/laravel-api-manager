<?php

/**
 * @Author: ahmadnorin
 * @Date:   2017-11-28 00:54:01
 * @Last Modified by:   ahmadnorin
 * @Last Modified time: 2017-11-28 00:54:10
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiManagerTable extends Migration {

	public function up()
	{
		Schema::create('api_manager', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('user_id', 100);
			$table->string('client', 225);
			$table->string('api_keys', 255);
			$table->string('api_token', 255);
			$table->text('description');
			$table->integer('is_published')->default('1');
		});
	}

	public function down()
	{
		Schema::drop('api_manager');
	}
}
