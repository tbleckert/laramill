<?php namespace Tbleckert\LaraMill;

interface LaraMillInterface {
	
	public function saveId($id);
	
	public function nullId();
	
	public function client();
	
	public function payment($token, $id);
	
	public function subscription($plan, $payment_interval);

    public function transaction($payment, $id, $amount);

}
