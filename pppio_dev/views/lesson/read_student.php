<?php

	require_once('views/shared/html_helper.php');
	$lesson_props = $lesson->get_properties();
	echo '<h1>' . htmlspecialchars($lesson_props['name']) . '</h1>';
	echo '<p>' . htmlspecialchars($lesson_props['description']) . '</p>';

	echo '<div class="progress">
		  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span class="sr-only">0% Complete (success)</span>
		  </div>
		</div>';
	echo '<p><a href="#" class="btn btn-default btn-lg">Continue</a></p>';
	echo '<div class="row">'; //abusing row...
	$exercises = $lesson_props['exercises'];
	$i = 1;
	foreach($exercises as $exercise_id => $exercise_name)
	{
		echo '<div class="col-md-2 col-xs-4 text-center"><a href="/?controller=exercise&action=try_it&id=' . $exercise_id . '&lesson_id=' . $lesson->get_id() . '" class="tile btn btn-default"><span>' . $i . '</span></a></div>';
		$i++;
	}
	echo '</div>';

?>
