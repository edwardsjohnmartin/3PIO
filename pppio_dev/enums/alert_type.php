<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Alert_Type extends Enum //where should i include this?
{
    const SUCCESS =		1;
	const INFO = 		2;
    const WARNING =		3;
    const DANGER =		4;
}
?>
