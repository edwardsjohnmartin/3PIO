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
	
	
	//const MODEL =		9;
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
	
	public static function is_model($type) { 
		if ($type >= static::LANGUAGE) {
			return true;
		}
		else {
			return false;
		}
	}

	//i want an "is model function"
	//right now i'm using > Type::MODEL which depends on models being last.
}
?>
