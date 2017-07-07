<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Completion_Status extends Enum //where should i include this?
{
    const COMPLETED =		1;
	const STARTED = 		2;
    const NOT_STARTED =		3;
}
?>
