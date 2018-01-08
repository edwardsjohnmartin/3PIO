<?php
//title
//progress bar
//panels div
//each panel
//exams

foreach($concepts as $concept){
	$concept_props = $concept->get_properties();
	$concept_completed = true;

	foreach($concept_props['lessons'] as $lesson_id => $lesson){
		$has_ex = false;

		if($lesson_has_exercises[$lesson_id]){
			$has_ex = true;
		}

		if($lesson->status == 3){
			$concept_completed = false;
		}
	}

	if($has_ex){
		$total_concepts_with_exercises++;
	}

	if($concept_completed){
		$total_concepts_completed++;
	}
}

echo '<h1>Section Name</h1>';

//progress bar
echo '<div class="progress">';
echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:80%">';
echo '<span class="sr-only">80% Complete (success)</span>';
echo '</div>';
echo '</div>';
//close progress bar

//all panels
echo '<div class="panel-group" id="accordion">';

foreach($concepts as $concept){
	$concept_props = $concept->get_properties();

	//single panel
	echo '<div class="panel panel-success">';

	//panel heading
	echo '<div class="panel-heading">';
	echo '<h4 class="panel-title">';
	echo '<a data-toggle="collapse" data-parent="#accordion" href="#collapseConcept1">Test Concept</a>';
	echo '</h4>';
	echo '</div>';
	//close panel heading

	//panel body
	echo '<div id="collapseConcept1" class="panel-collapse collapse">';
	echo '<div class="panel-body">';
	echo '<ul class="list-group concept-list-group">';

	echo '<a class="list-group-item list-group-item-success" href="">';
	echo 'Exercises';
	echo '<span class="pull-right">Opens at time</span>';
	echo '</a>';

	echo '<a class="list-group-item list-group-item-success" href="">';
	echo 'Project';
	echo '<span class="pull-right">Opens from time to time</span>';
	echo '</a>';

	echo '</ul>';
	echo '</div>';
	echo '</div>';
	//close panel body

	echo '</div>';
	//close single panel
}

echo '</div>';
//close all panels

require_once('views/exam/exam_table.php');
?>
