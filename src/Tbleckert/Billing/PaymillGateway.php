<?php namespace Tbleckert\Billing;

class PaymillGateway {
	
	protected $billing;
	protected $paymillObject;
	
	public function __construct(BillingInterface $billing, $paymillObject)
	{
		$this->billing = $billing;
		$this->paymillObject = $paymillObject;
	}
	
	public function create($token = null)
	{
		$request = new \Paymill\Request(\Config::get('billing::private'));
		
		try {
			$response   = $request->create($this->paymillObject);
			$responseId = $response->getId();
		} catch (PaymillException $e) {
			throw $e;
		}
	}
	
}