<?php namespace Tbleckert\Billing;

trait BillingTrait {
	
	public $currentAction = null;
	
	public function saveId($id)
	{
		switch ($this->currentAction) {
			case 'client':
				$this->update(array('client_id' => $id));
				break;
		}
	}
	
	public function nullId()
	{
		switch ($this->currentAction) {
			case 'client':
				$this->update(array('client_id' => null));
				break;
		}
	}
	
	public function client($description = null)
	{
		$this->currentAction = 'client';
		
		$client = new \Paymill\Models\Request\Client();
		$client
			->setEmail($this->email)
			->setDescription($description);
			
		if ($this->client_id) {
			$client->setId($this->client_id);
		}
			
		return new PaymillGateway($this, $client);
	}
	
	public function payment($token = false, $id = false)
	{
		
		if (!$this->client_id) {
			return \App::abort(500, 'No client is connected to this account');
		}
		
		$this->currentAction = 'payment';
		
		$payment = new \Paymill\Models\Request\Payment();
		$payment->setClient($this->client_id);
			
		if ($id) {
			$payment->setId($id);
		}
		
		if ($token) {
			$payment->setToken($token);
		}
			
		return new PaymillGateway($this, $payment);
	}

}
