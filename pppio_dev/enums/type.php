<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Type extends Enum{
    const INTEGER =		             1;
	const DOUBLE = 		             2;
    const BOOLEAN =		             3;
    const DATETIME =	             4;
    const STRING =	                 5;
	const CODE =	                 6;
	const EMAIL =		             7;
	const PASSWORD =	             8;
	const FILE =	                 9;

	const LANGUAGE =	            10;
	const PROBLEM =		            11;
	const USER =		            12;
	const LESSON =		            13;
	const PROJECT =		            14;
	const SECTION =		            15;
	const CONCEPT =		            16;
	const EXAM =                    17;
	const QUESTION =                18;
	const SESSION =                 19;
	const SURVEY =                  20;
	const SURVEY_QUESTION =         21;
	const SURVEY_QUESTION_TYPE =    22;
	const SURVEY_CHOICE =           23;
	const SURVEY_TYPE =             24;
	const STUDENTFUNCTION =	        25;
	const COURSE =		            26;
	const PARTICIPATION_TYPE =	    27;
	const ROLE =                    28;
	const TAG =                     29;
	const PERMISSION =              30;

	const LIST_TAG =                31;
	const LIST_EXERCISE =           32;
	const LIST_LANGUAGE =           33;
	const LIST_PROBLEM =            34;
	const LIST_USER =               35;
	const LIST_LESSON =             36;
	const LIST_PROJECT =            37;
	const LIST_SECTION =            38;
	const LIST_CONCEPT =            39;
	const LIST_EXAM =               40;
	const LIST_QUESTION =           41;
	const LIST_SESSION =            42;
	const LIST_SURVEY =             43;
	const LIST_SURVEY_QUESTION =    44;
	const LIST_SURVEY_CHOICE =      45;
	const LIST_STUDENTFUNCTION =    46;
	const LIST_COURSE =	            47;
	const LIST_PARTICIPATION_TYPE =	48;
	const LIST_ROLE =               49;
	const LIST_PERMISSION =         50;

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
