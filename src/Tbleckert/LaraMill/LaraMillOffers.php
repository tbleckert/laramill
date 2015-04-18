<?php namespace Tbleckert\LaraMill;

class LaraMillOffers {
	
	public function __construct($id = null)
	{
		$this->request = new \Paymill\Request(\Config::get('laramill::private'));
		$this->id = (string) $id;
	}
	
	public function all(array $filter = [])
	{
		$offer = new \Paymill\Models\Request\Offer();
		$offer->setFilter($filter);
		
		return $this->request->getAll($offer);
	}
	
	public function details()
	{
		if (!$this->id) {
			throw new LaraMillException('Offer ID is needed to fetch details.', 403);
		}
		
		$offer = new \Paymill\Models\Request\Offer();
		$offer->setId($this->id);
		
		return $this->request->getOne($offer);
	}
	
	public function create($amount, $currency, $interval, $name)
	{
		if (!$amount OR !$currency OR !$interval OR !$name) {
			throw new LaraMillException('All attributes has to be set.', 403);
		}
		
		$offer = new \Paymill\Models\Request\Offer();
		$offer->setAmount((int) $amount)
			->setCurrency((string) $currency)
			->setInterval((string) $interval)
			->setName((string) $name);
		
		return $this->request->create($offer);
	}
	
	public function update(array $update = [], $updateSubscriptions = false)
	{
		if (!$this->id) {
			throw new LaraMillException('Offer ID is needed to update offer.', 403);
		}
		
		$offer = new \Paymill\Models\Request\Offer();
		$offer->setId($this->id)
			->setUpdateSubscriptions($updateSubscriptions);
		
		if (isset($update['amount'])) {
			$offer->setAmount($update['amount']);
		}
		
		if (isset($update['currency'])) {
			$offer->setCurrency($update['currency']);
		}
		
		if (isset($update['interval'])) {
			$offer->setInterval($update['interval']);
		}
		
		if (isset($update['name'])) {
			$offer->setName($update['name']);
		}
		
		return $this->request->update($offer);
	}
	
	public function remove($removeWithSubscriptions = false)
	{
		if (!$this->id) {
			throw new LaraMillException('Offer ID is needed to remove offer.', 403);
		}
		
		$offer = new \Paymill\Models\Request\Offer();
		$offer->setId($this->id)
			->setRemoveWithSubscriptions($removeWithSubscriptions);
		
		return $this->request->delete($offer);
	}
	
}