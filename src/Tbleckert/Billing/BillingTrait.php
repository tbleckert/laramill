<?php namespace Tbleckert\Billing;

trait BillingTrait {
	
	public $currentAction = null;
	
	public function saveId($id)
	{
		switch ($this->currentAction) {
			case 'client':
				$this->update(array('client_id' => $id));
				break;
			case 'subscription':
				$this->update(array('subscription_id' => $id));
				break;
		}
	}
	
	public function nullId()
	{
		switch ($this->currentAction) {
			case 'client':
				$this->update(array('client_id' => null));
				break;
			case 'subscription':
				$this->update(array('subscription_id' => null));
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
	
	public function subscription($id = false, $plan = false, $payment_interval = false, $payment = false)
	{
		if (!$this->client_id) {
			return \App::abort(500, 'No client is connected to this account');
		}
		
		$subscription = new \Paymill\Models\Request\Subscription();
		$subscription->setClient($this->client_id);
		
		if ($id) {
			$subscription->setId($id);
		}
		
		if ($plan AND $payment_interval) {
			// Get offer id
			$offers = \Config::get('billing::offers');
			
			if (!$offers) {
				return \App::abort(500, 'No offers were found');
			}
			
			if (!isset($offers[$plan]) OR !isset($offers[$plan][$payment_interval])) {
				return \App::abort(500, 'No offer were found');
			}
			
			$offer = $offers[$plan][$payment_interval];
			
			$subscription->setOffer($offer);
		}
		
		// Get payment
		if ($payment) {
			$payment = $this->payment($payment)->details();
		} else {		
			$payments = $this->payment()->all();
			$payment  = $payments[count($payments) - 1];
		}
			
		$subscription->setPayment($payment->getId());
		
		$this->currentAction = 'subscription';
		
		return new PaymillGateway($this, $subscription);
		
	}

}
