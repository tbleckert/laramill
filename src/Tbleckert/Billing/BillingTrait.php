<?php namespace Tbleckert\Billing;

trait BillingTrait {
	
	public function client($description = null)
	{
		$client = new \Paymill\Models\Request\Client();
		$client
			->setEmail($this->email)
			->setDescription($description);
			
		return new PaymillGateway($this, $client);
	}

}
