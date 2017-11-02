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
	const EXAM =        17;
	const QUESTION =     18;
	const SESSION =      19;
	const STUDENTFUNCTION =	20;
	const COURSE =		21;
	const PARTICIPATION_TYPE =	22;
	const ROLE = 23;
	const TAG = 24;
	const PERMISSION = 25;

	const LIST_TAG = 26;
	const LIST_EXERCISE = 27;
	const LIST_LANGUAGE = 28;
	const LIST_PROBLEM = 29;
	const LIST_USER = 30;
	const LIST_LESSON = 31;
	const LIST_PROJECT = 32;
	const LIST_SECTION = 33;
	const LIST_CONCEPT = 34;
	const LIST_EXAM = 35;
	const LIST_QUESTION = 36;
	const LIST_SESSION = 37;
	const LIST_STUDENTFUNCTION = 38;
	const LIST_COURSE =	39;
	const LIST_PARTICIPATION_TYPE =	40;
	const LIST_ROLE = 41;
	const LIST_PERMISSION = 42;


	public static function is_model($type)
	{
		return ($type >= static::LANGUAGE && $type <= static::LIST_TAG);
	}

	public static function is_list_model($type)
	{
		return ($type >= static::LIST_TAG && $type <= static::LIST_ROLE);
	}
}
?>
