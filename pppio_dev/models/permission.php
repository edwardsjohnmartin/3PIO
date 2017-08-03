<?php
	class Permission {
		public $securable;
		public $permission_type;

		public function __construct($securable, $permission_type)
		{
			$this->securable = $securable;
			$this->permission_type = $permission_type;
		}
	}
?>
