<?php namespace Tbleckert\LaraMill;

class PaymillGateway {
	
	protected $billing;
	protected $paymillObject;
	
	public function __construct(LaraMillInterface $billing, $paymillObject)
	{
		$this->billing = $billing;
		$this->paymillObject = $paymillObject;
		$this->request = new \Paymill\Request(\Config::get('laramill::private'));
	}
	
	public function create($token = null)
	{
		$action     = $this->billing->currentAction;
		
		if ($action == 'client' AND $this->billing->client_id) {
			throw new LaraMillException('The user is already connected to a Paymill client.', 403);
		}
		
		$response   = $this->request->create($this->paymillObject);
		$responseId = $response->getId();
		
		$this->billing->saveId($responseId);
		
		return $this;
	}
	
	public function details()
	{
		$action = $this->billing->currentAction;
		
		if ($action == 'subscription' AND $this->billing->subscription_id) {
			$this->paymillObject->setId($this->billing->subscription_id);
		} elseif ($action == 'subscription' AND !$this->billing->subscription_id) {
			throw new LaraMillException('The user is not subscribed to a subscription.', 403);
		}
		
		if (!$this->paymillObject->getId()) {
			return false;
		}
		
		$response = $this->request->getOne($this->paymillObject);
		
		return $response;
	}
	
	public function swap()
	{
		$action = $this->billing->currentAction;
		
		if ($action != 'subscription') {
			throw new LaraMillException('This method is not allowed for the provided action.', 405);
		}
		
		if (!$this->billing->subscription_id) {
			throw new LaraMillException('The user is not subscribed to a subscription.', 403);
		}
		
		if (!$this->paymillObject->getOffer()) {
			throw new LaraMillException('No offer set to swap to.', 428);
		}
		
		$this->paymillObject->setId($this->billing->subscription_id);
		$response = $this->request->update($this->paymillObject);
		
		return $this;
	}
	
	public function pause()
	{
		$action = $this->billing->currentAction;
		
		if ($action != 'subscription') {
			throw new LaraMillException('This method is not allowed for the provided action.', 405);
		}
		
		if (!$this->billing->subscription_id) {
			throw new LaraMillException('The user is not subscribed to a subscription.', 403);
		}
		
		$this->paymillObject->setId($this->billing->subscription_id)->setPause(true);
		
		$response = $this->request->update($this->paymillObject);
		return $this;
	}
	
	public function resume()
	{
		$action = $this->billing->currentAction;
		
		if ($action != 'subscription') {
			throw new LaraMillException('This method is not allowed for the provided action.', 405);
		}
		
		if (!$this->billing->subscription_id) {
			throw new LaraMillException('The user is not subscribed to a subscription.', 403);
		}
		
		$this->paymillObject->setId($this->billing->subscription_id)->setPause(false);
		
		$response = $this->request->update($this->paymillObject);
		return $this;
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
	
	public function cancel()
	{
		$action = $this->billing->currentAction;
		
		if ($action != 'subscription') {
			throw new LaraMillException('This method is not allowed for the provided action.', 405);
		}
		
		if (!$this->billing->subscription_id) {
			throw new LaraMillException('The user is not subscribed to a subscription.', 403);
		}
		
		$this->paymillObject->setId($this->billing->subscription_id)->setRemove(false);
	
		$response = $this->request->delete($this->paymillObject);
		return $this;
	}
	
	public function remove()
	{
		$action = $this->billing->currentAction;
		
		if ($action == 'subscription' AND $this->billing->subscription_id) {
			$this->paymillObject->setId($this->billing->subscription_id)->setRemove(true);
		} elseif ($action == 'subscription' AND !$this->billing->subscription_id) {
			throw new LaraMillException('The user is not subscribed to a subscription.', 403);
		}
	
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