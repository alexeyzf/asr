<?php

require_once ('Zend/Filter.php');

class UnixTimeStampFilter extends Zend_Filter 
{
	public function filter($value)
	{
		return strtotime($value);
	} 
}