<?php
require_once('models/model.php');
class Survey_Choice extends Model
{
	protected static $types = array(
		'id' => Type::INTEGER, 
		'choice' => Type::STRING);
	protected $choice;
}