<?php
class dropdown_item
{
	private $id;
	private $property_name;
	private $text;
	private $use_pre = false;

	function __construct($new_id,$new_property_name, $new_text)
	{
		$this->id = $new_id;
		$this->property_name = $new_property_name;
		$this->text = $new_text;
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_property_name()
	{
		return $this->property_name;
	}

	public function get_text()
	{
		return $this->text;
	}

	public function set_text($value)
	{
		$this->text = $value;
	}

	public function set_use_pre($value)
	{
		$this->use_pre = $value;
	}
}
?>
