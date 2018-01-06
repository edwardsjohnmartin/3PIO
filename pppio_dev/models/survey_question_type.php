<?php
require_once('models/model.php');
require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Survey_Question_Type extends Model{
	public $name;
}

class Question_Type_Enum extends Enum{
	const MULTIPLE_CHOICE = 1;
	const RANGE =           2;
	const SHORT_ANSWER =    3;
}
?>
