<?php
	class alert {
		public $message;
		public $type;

		public function __construct($message, $type)
		{
			$this->message = $message;
			$this->type = $type;
		}
	}
?>
