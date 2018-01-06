<?php
require_once('models/model.php');
require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Survey_Type extends Model{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING);
	protected $name;
}

class Survey_Type_Enum extends Enum{
	const POST_EXERCISES = 1;
	const PRE_PROJECT =    2;
	const POST_PROJECT =   3;
}
?>
