<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Permission_Type extends Enum
{
    const STUDENT =		1;
	const TEACHING_ASSISTANT = 		2;
}
?>
