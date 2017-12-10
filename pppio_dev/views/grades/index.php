<?php
echo '<h1>Pick a Section to View Grades For</h1>';
if(!$is_ta){
	foreach($sections as $key => $value){
		echo '<h3>';
		echo '<a href="?controller=grades&action=get_section_grades&id=' . $value['id'] . '">' . $value['name'] . '</a>';
		echo '</h3>';
	}
}
else{
	foreach($sections as $key => $value){
		echo '<h3>';
		echo '<a href="?controller=grades&action=get_section_grades&id=' . $value->key . '">' . $value->value . '</a>';
		echo '</h3>';
	}
}
?>
