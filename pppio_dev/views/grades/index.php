<?php
echo '<h1>Pick a Section to View Grades For</h1>';
foreach($sections as $key => $value)
{
	echo '<h3>';
	echo '<a href="?controller=grades&action=get_section_grades&id=' . $value['id'] . '">' . $value['name'] . '</a>';
	echo '</h3>';
}
?>
