<?php namespace Tbleckert\Billing;

interface BillingInterface {
	
	public function saveId($id);
	
	public function nullId();
	
	public function client();

}
