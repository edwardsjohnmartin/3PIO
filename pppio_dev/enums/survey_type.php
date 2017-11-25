<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Survey_Type extends Enum
{
    const PRE_EXERCISES =		1;
	const POST_EXERCISES = 		2;
    const PRE_PROJECT =		    3;
    const POST_PROJECT =		4;
	const PRE_LESSON =          5;
	const POST_LESSON =         6;
}
?>
