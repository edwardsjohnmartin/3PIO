<?php
//Class to hold the information used to create the tiles on the left navbar of the dynamic view
class button
{
	private $id;
	private $text;
	private $link;
	private $color;

	function __construct($new_id, $new_text, $new_link, $new_color)
	{
		$this->id = $new_id;
		$this->text = $new_text;
		$this->link = $new_link;
		$this->color = $new_color;
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_text()
	{
		return $this->text;
	}

	public function get_link()
	{
		return $this->link;
	}

	public function get_color()
	{
		return $this->color;
	}
}
