<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Type extends Enum //where should i include this?
{
    const INTEGER =		1;
	const DOUBLE = 		2;
    const BOOLEAN =		3;
    const DATETIME =	4;
    const STRING =		5; //also want more specific things like code area, text area...
	const CODE =		6;
	//these should go first
	const EMAIL =		7; //should this be a type?
	const PASSWORD =	8;

	//const MODEL =		9; //can check if greater than model?
	const LANGUAGE =	9;
	const PROBLEM =		10;
	const USER =		11;
	const LESSON =		12;
	const PROJECT =		13;
	const SECTION =		14; //this is a problem
	const CONCEPT =		15;
	const FUNCTION =	16;
	const COURSE =		17;
	const PARTICIPATION_TYPE =	18;
	const ROLE = 19;
	const TAG = 20;

	//const LIST_MODEL = 22;
	const LIST_TAG = 21;
	const LIST_EXERCISE = 22;
	const LIST_LANGUAGE = 23;
	const LIST_PROBLEM = 24;
	const LIST_USER = 25;
	const LIST_LESSON = 26;
	const LIST_PROJECT = 27;
	const LIST_SECTION = 28;
	const LIST_CONCEPT = 29;
	const LIST_FUNCTION = 30;
	const LIST_COURSE =	31;
	const LIST_PARTICIPATION_TYPE =	32;
	const LIST_ROLE = 33;
	
	
	public static function is_model($type)
	{ 
		return ($type >= static::LANGUAGE && $type < static::LIST_TAG);
	}

	public static function is_list_model($type)
	{
		return ($type >= static::LIST_TAG && $type <= static::LIST_ROLE);
	}
}
?>
