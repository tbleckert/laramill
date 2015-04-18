<?php namespace Tbleckert\LaraMill;

class LaraMill {
	
	public static function getClients(array $filter = [])
	{
		$request = new \Paymill\Request(\Config::get('laramill::private'));
		$client  = new \Paymill\Models\Request\Client();
		
		$client->setFilter($filter);
		
		$response = $request->getAll($client);
		
		return $response;
	}
	
	public static function offer($id = null)
	{
		return new LaraMillOffers($id);
	}
	
}