<?php
echo '<h1>Pick a Student to View Sessions For</h1>';
foreach($users as $key => $value)
{
	echo '<h3>';
	echo '<a href="?controller=session&action=read_all_for_student&user_id=' . $value->get_id() . '">' . $value->get_name() . '</a>';
	echo '</h3>';
}
?>
