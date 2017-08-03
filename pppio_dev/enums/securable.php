<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Securable extends Enum
{
    const CONCEPT =	1;
	const COURSE = 	2;
    const EXERCISE =3;
    const FUNCTION =4;
    const LANGUAGE =5;
	const LESSON =	6;
	const PROJECT =	7;
	const ROLE =	8;
	const SECTION =	9;
	const TAG =		10;
	const USER =	11;
}
?>
