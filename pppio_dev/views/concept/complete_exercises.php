<?php
require_once('enums/completion_status.php');

?><h1><?php echo $concept_props['name'] . ' for ' . $student_props['name'];?></h1>
<div class="row"><?php
	$i = 1;
	foreach($lessons as $lesson){
		$les_id = $lesson->get_id();
		$lesson_props = $lesson->get_properties();
		$exercises = $lesson_props['exercises'];

		foreach($exercises as $exercise_id => $exercise_obj){
			echo '<div class="col-md-2 col-xs-4 text-center">';

			if($exercise_obj->status == Completion_Status::COMPLETED){
				$btn_class = 'btn-success';
			}else{
				$btn_class = 'btn-default';
			}

			echo '<button id="btn_tile_' . $i . '" class="tile btn ' . $btn_class . '" onclick="complete_previous_exercises(' . $exercise_id . ',' . $les_id . ',' . $_GET['concept_id'] . ',' . $_GET['user_id'] . ', this.id)">' .
			'<span class="tile-number">' . $i . '</span>' .
			'<span class="tile-label">' . htmlspecialchars($lesson_props['name']) . '</span>' .
			'</button></div>';

			$i++;
		}
	}
?></div><?php
	echo '<script src="js/admin_functions.js"></script>';
?>
