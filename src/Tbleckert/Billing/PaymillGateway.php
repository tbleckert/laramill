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
		try {
			$response   = $this->request->create($this->paymillObject);
			$responseId = $response->getId();
			
			$this->billing->saveId($responseId);
			
			return $this;
		} catch (PaymillException $e) {
			throw $e;
		}
	}
	
	public function details()
	{
		try {
			$response = $this->request->getOne($this->paymillObject);
			
			return $response;
		} catch (PaymillException $e) {
			throw $e;
		}
	}
	
	public function update($email = null, $description = null)
	{
		if (!$email) {
			$email = $this->billing->email;
		}
		
		try {
			$this->paymillObject->setEmail($email);
			
			if ($description) {
				$this->paymillObject->setDescription($description);
			}
			
			$response = $this->request->update($this->paymillObject);
			
			return $this;
		} catch (PaymillException $e) {
			throw $e;
		}
	}
	
}