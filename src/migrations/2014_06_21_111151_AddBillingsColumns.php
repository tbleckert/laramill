<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillingsColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->boolean('active')->default(1);
			$table->string('client_id')->nullable();
			$table->string('subscription_id')->nullable();
			$table->string('offer_id')->nullable();
			$table->string('card_type')->nullable();
			$table->string('card_holder')->nullable();
			$table->string('last_4', 4)->nullable();
			$table->string('expire_month', 2)->nullable();
			$table->string('expire_year', 4)->nullable();
			$table->timestamp('next_capture_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn(
				'active',
				'client_id',
				'subscription_id',
				'offer_id',
				'card_type',
				'card_holder',
				'last_4',
				'expire_month',
				'expire_year',
				'next_capture_at'
			);
		});
	}

}
