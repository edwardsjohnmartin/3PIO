<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Type extends Enum
{
    const INTEGER =		1;
	const DOUBLE = 		2;
    const BOOLEAN =		3;
    const DATETIME =	4;
    const STRING =		5;
	const CODE =		6;
	const EMAIL =		7;
	const PASSWORD =	8;
	const FILE =	9;

	const LANGUAGE =	10;
	const PROBLEM =		11;
	const USER =		12;
	const LESSON =		13;
	const PROJECT =		14;
	const SECTION =		15;
	const CONCEPT =		16;
	const FUNCTION =	17;
	const COURSE =		18;
	const PARTICIPATION_TYPE =	19;
	const ROLE = 20;
	const TAG = 21;
	const PERMISSION = 22;

	const LIST_TAG = 23;
	const LIST_EXERCISE = 24;
	const LIST_LANGUAGE = 25;
	const LIST_PROBLEM = 26;
	const LIST_USER = 27;
	const LIST_LESSON = 28;
	const LIST_PROJECT = 29;
	const LIST_SECTION = 30;
	const LIST_CONCEPT = 31;
	const LIST_FUNCTION = 32;
	const LIST_COURSE =	33;
	const LIST_PARTICIPATION_TYPE =	34;
	const LIST_ROLE = 35;
	const LIST_PERMISSION = 36;
	
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
