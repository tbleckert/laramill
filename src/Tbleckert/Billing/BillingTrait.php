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

}
