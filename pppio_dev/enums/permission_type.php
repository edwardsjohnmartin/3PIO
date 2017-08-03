<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Permission_Type extends Enum
{
    const READ =		1;
	const CREATE = 		2;
    const EDIT =		3;
    const DELETE =		4;
}
?>
