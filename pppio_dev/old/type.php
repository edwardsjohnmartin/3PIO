<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Type extends Enum //where should i include this?
{
    const INTEGER =		1;
    const BOOLEAN =		2;
    const DATETIME =	3;
    const STRING =		4; //also want more specific things like code area, text area...
	const CODE =		5;
	//these should go first
	const EMAIL =		6; //should this be a type?
	const PASSWORD =	7;

	const MODEL =		8; //can check if greater than model?
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



	//i want an "is model function"
	//right now i'm using > Type::MODEL which depends on models being last.
}
?>
