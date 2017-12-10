<?php
require_once('models/model.php');
class Survey_Type extends Model
{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING);
	protected $name;

	public function get_name()
	{
		return $this->name;
	}
}
?>
