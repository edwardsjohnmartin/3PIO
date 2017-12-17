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
	const SECTION =		            15;
	const CONCEPT =		            16;
	const EXAM =                    17;
	const QUESTION =                18;
	const SESSION =                 19;
	const SURVEY =                  20;
	const SURVEY_QUESTION =         21;
	const SURVEY_CHOICE =           22;
	const SURVEY_TYPE =             23;
	const STUDENTFUNCTION =	        24;
	const COURSE =		            25;
	const PARTICIPATION_TYPE =	    26;
	const ROLE =                    27;
	const TAG =                     28;
	const PERMISSION =              29;

	const LIST_TAG =                30;
	const LIST_EXERCISE =           31;
	const LIST_LANGUAGE =           32;
	const LIST_PROBLEM =            33;
	const LIST_USER =               34;
	const LIST_LESSON =             35;
	const LIST_PROJECT =            36;
	const LIST_SECTION =            37;
	const LIST_CONCEPT =            38;
	const LIST_EXAM =               39;
	const LIST_QUESTION =           40;
	const LIST_SESSION =            41;
	const LIST_SURVEY =             42;
	const LIST_SURVEY_QUESTION =    43;
	const LIST_SURVEY_CHOICE =      44;
	const LIST_STUDENTFUNCTION =    45;
	const LIST_COURSE =	            46;
	const LIST_PARTICIPATION_TYPE =	47;
	const LIST_ROLE =               48;
	const LIST_PERMISSION =         49;

	public static function is_model($type)
	{
		return ($type >= static::LANGUAGE && $type <= static::PERMISSION);
	}

	public static function is_list_model($type)
	{
		return ($type >= static::LIST_TAG && $type <= static::LIST_PERMISSION);
	}
}
?>
