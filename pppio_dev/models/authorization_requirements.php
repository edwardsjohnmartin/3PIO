<?php
	class Authorization_Requirements {
		public $login_state; //true for logged in, false for not logged in, null for no requirement
		public $permissions;

		public function __construct($login_state, $permissions)
		{
			$this->login_state = $login_state;
			$this->permissions = $permissions;
		}
	}
?>
