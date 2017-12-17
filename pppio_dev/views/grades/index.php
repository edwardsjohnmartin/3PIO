<?php
//This is the index page for navigating to exam grades for a section
//There has to be sections to show to get past the check in the index action on the grades controller

echo '<h1>Pick a Section to View Grades For</h1>';

//Show sections the user has access to
foreach($all_exams as $section_id => $exam_list){
	echo '<h3><a href="?controller=grades&action=get_section_grades&id=' . $section_id . '">' . $sections[$section_id] . '</a></h3>';
	echo '<ul>';

	//Create a list item for each exam
	foreach($all_exams[$section_id] as $exam_id => $exam_name){
		echo '<li><a href="?controller=grades&action=get_exam_grades&exam_id=' . $exam_id . '">' . $exam_name . '</a></li>';
	}
	echo '</ul>';
}
?>
