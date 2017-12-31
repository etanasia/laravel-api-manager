<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHostKeysTable extends Migration {

	public function up()
	{
		Schema::create('host_keys', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('hostname', 255)->unique();
			$table->string('keys', 255)->unique();
			$table->string('state', 255);
			$table->string('transition', 255);
			$table->integer('user_id')->unsigned()->index();
		});
	}

	public function down()
	{
		Schema::drop('host_keys');
	}
}
