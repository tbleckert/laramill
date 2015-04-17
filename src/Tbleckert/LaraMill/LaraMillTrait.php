<?php namespace Tbleckert\LaraMill;

trait LaraMillTrait {
	
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
			throw new LaraMillException('The user is has to be connected to a Paymill client to make a payment.', 401);
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
	
	public function subscription($plan = false, $payment_interval = false, $payment = false)
	{
		if (!$this->client_id) {
			throw new LaraMillException('The user is has to be connected to a Paymill client to make a payment.', 401);
		}
		
		$subscription = new \Paymill\Models\Request\Subscription();
		$subscription->setClient($this->client_id);
		
		if ($plan AND $payment_interval) {
			// Get offer id
			$offers = \Config::get('billing::offers');
			
			if (!$offers OR (!isset($offers[$plan]) OR !isset($offers[$plan][$payment_interval]))) {
				throw new LaraMillException('No offers found.', 412);
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

	public function transaction($payment = false, $id = false, $amount = false, $currency = 'GBP')
	{
		if (!$this->client_id) {
			throw new LaraMillException('The user has to be connected to a Paymill client to make a payment.', 401);
		}

		$transaction = new \Paymill\Models\Request\Transaction();
		$transaction->setClient($this->client_id);

		if ($id) {
			$transaction->setId($id);
		} else {
			// Get payment
			if ($payment) {
				$payment = $this->payment(false, $payment)->details();
			} else {    
				$payments = $this->payment()->all();

				if (empty($payments)) {
					throw new LaraMillException('The user has to have a payment to create a transaction.', 401);
				}

				$payment  = $payments[count($payments) - 1];
			} 

			$transaction->setPayment($payment->getId());

			$transaction->setAmount($amount);
			$transaction->setCurrency($currency);
		}

		$this->currentAction = 'transaction';

		return new PaymillGateway($this, $transaction);
	}

}
