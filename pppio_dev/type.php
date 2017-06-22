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

	const MODEL =		9; //can check if greater than model?
	const LANGUAGE =	10;
	const PROBLEM =		11;
	const USER =		12;
	const LESSON =		13;
	const PROJECT =		14;
	const SECTION =		15; //this is a problem
	const CONCEPT =		16;
	const FUNCTION =	17;
	const COURSE =		18;
	const PARTICIPATION_TYPE =	19;
	const ROLE = 20;
	const TAG = 21;

	const LIST_MODEL = 22;
	const LIST_TAG = 23;
	const LIST_EXERCISE = 24;

	//i want an "is model function"
	//right now i'm using > Type::MODEL which depends on models being last.
}
?>
