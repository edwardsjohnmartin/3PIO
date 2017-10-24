<?php
class button
{
	private $id;
	private $text;
	private $link;

	function __construct($new_id, $new_text, $new_link)
	{
		$this->id = $new_id;
		$this->text = $new_text;
		$this->link = $new_link;
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
}