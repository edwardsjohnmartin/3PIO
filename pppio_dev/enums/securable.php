<?php

require_once('php-enum/Enum.php');
use MyCLabs\Enum\Enum;

class Securable extends Enum
{
    const CONCEPT =	         1;
	const COURSE = 	         2;
    const EXERCISE =         3;
    const STUDENTFUNCTION =  4;
    const LANGUAGE =         5;
	const LESSON =	         6;
	const PROJECT =	         7;
	const ROLE =	         8;
	const SECTION =	         9;
	const TAG =		        10;
	const USER =	        11;
	const EXAM =	        12;
	const QUESTION =        13;
	const SESSION =         14;
	const SURVEY =          15;

	public static function get_id_from_string($activity)
	{
		$activity = strtolower($activity);

		switch ($activity)
		{
			case 'exercise':
				return Securable::EXERCISE;
			case 'project':
				return Securable::PROJECT;
			case 'question':
				return Securable::QUESTION;
			default:
				return false;
		}
	}

	public static function get_string_from_id($securable_id)
	{
		switch ($securable_id)
		{
			case Securable::EXERCISE:
				return 'Exercise';
			case Securable::PROJECT:
				return 'Project';
			case Securable::QUESTION:
				return 'Question';
			default:
				return 'none';
		}
	}
}
?>
