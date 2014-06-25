<?php namespace Tbleckert\Billing;

class PaymillGateway {
	
	protected $billing;
	protected $paymillObject;
	
	public function __construct(BillingInterface $billing, $paymillObject)
	{
		$this->billing = $billing;
		$this->paymillObject = $paymillObject;
		$this->request = new \Paymill\Request(\Config::get('billing::private'));
	}
	
	public function create($token = null)
	{
		$response   = $this->request->create($this->paymillObject);
		$responseId = $response->getId();
		
		$this->billing->saveId($responseId);
		
		return $this;
	}
	
	public function details()
	{
		$response = $this->request->getOne($this->paymillObject);
		
		return $response;
	}
	
	public function update($email = null, $description = null)
	{
		if (!$this->paymillObject->getId()) {
			return false;
		}
		
		if (!$email) {
			$email = $this->billing->email;
		}
		
		$this->paymillObject->setEmail($email);
		
		if ($description) {
			$this->paymillObject->setDescription($description);
		}
		
		$response = $this->request->update($this->paymillObject);
		
		return $this;
	}
	
	public function remove()
	{
		$response = $this->request->delete($this->paymillObject);
		$this->billing->nullId();
		
		return $response;
	}
	
	public function all()
	{
		$action = $this->billing->currentAction;
		$userDetails = $this->billing->client()->details();
		
		switch ($action) {
			case 'payment':
				$response = $userDetails->getPayment();
				break;
			case 'subscription':
				$response = $userDetails->getSubscription();
				break;
		}
		
		$this->billing->currentAction = $action;
		
		return $response;
	}
	
}