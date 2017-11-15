<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Role extends Enum
{
    const ADMIN = 1;
	const TEACHER = 2;
	const STUDENT = 3;
}
?>