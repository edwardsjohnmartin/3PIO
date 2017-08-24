<?php
	class Authorization_Requirements {
		// Does the user need to be logged in?
		public $login_state; //true for logged in, false for not logged in, null for no requirement
		// What permissions need to pass to do this action.
		public $permissions;

		public function __construct($login_state, $permissions)
		{
			$this->login_state = $login_state;
			$this->permissions = $permissions;
		}
	}
?>
