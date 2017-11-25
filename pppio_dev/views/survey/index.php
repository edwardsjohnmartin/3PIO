<?php
if(!isset($surveys) and count($surveys) == 0)
{
	echo '<h1>No Surveys exist</h1>';
}
else
{
	echo '<h1>Pick a Survey to View Responses For</h1>';
	foreach($surveys as $key => $value)
	{
		echo '<h3>';
		echo '<a href="?controller=survey&action=read_responses&id=' . $key . '">' . $value . '</a>';
		echo '</h3>';
	}
}
?>
