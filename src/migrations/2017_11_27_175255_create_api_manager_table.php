<?php

/**
 * @Author: bantenprov
 * @Date:   2017-11-28 00:54:01
 * @Last Modified by:   bantenprov
 * @Last Modified time: 2017-11-28 00:54:10
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiManagerTable extends Migration {

	public function up()
	{
		Schema::create('api_keys', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('user_id');
			$table->string('client', 225);
			$table->string('api_key', 255);
			$table->text('description');
			$table->integer('is_published')->default('1');
		});
	}

	public function down()
	{
		Schema::drop('api_keys');
	}
}
