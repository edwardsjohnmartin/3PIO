<?php
	class Db {

		private static $reader = NULL;
		private static $writer = NULL;

		private function __construct() {}

		private function __clone() {}

		public static function getReader()
		{
			if (!isset(self::$reader))
			{
				$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
				self::$reader = new PDO('pgsql:dbname=pppio_dev', 'pppio_dev_reader', 'tester', $pdo_options);
			}
			return self::$reader;
		}

		public static function getWriter()
		{
			if (!isset(self::$writer))
			{
				$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
				self::$writer = new PDO('pgsql:dbname=pppio_dev', 'pppio_dev_writer', 'tester', $pdo_options);
			}
			return self::$writer;
		}
	}
?>
