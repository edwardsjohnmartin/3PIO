<?php

	require_once('completion_status.php');
//	this expects either a problem or a project.
//id, name, description, starter code
$exercise_props = $exercise->get_properties();
$lesson_props = $lesson->get_properties();
$concept_id = intval($_GET['concept_id']);

echo '<link rel="stylesheet" href="css/site.css">
<link rel="stylesheet" href="css/editor.css">';
include_once('views/shared/CodeMirror.php');
echo '<script src="js/skulpt/skulpt.min.js"></script>
<script src="js/skulpt/skulpt-stdlib.js"></script>';


echo '<div class="row height-100 overflow-hidden">
<div class="col-xs-3 height-100 overflow-auto right-pad-0">
<div class="container-fluid">';

echo '<h2>' . $lesson_props['name'] . '</h2>';

$exercises = $lesson_props['exercises'];
echo '<div class="row">'; //abusing row...

/*
if doing latest, open the next
otherwise don't bother
either way, show the link for the next exercise, or the section if this one is the last
*/
	$trying_latest = false;
	$trying_last = (end($exercises)->key == $exercise->get_id());
	$i = 1;
	$found_latest = false;
	$found_current = false;
	$next_exercise_id = null;
	$next_index = null;

	foreach($exercises as $exercise_id => $exercise_obj)
	{
		echo '<div class="col-xs-4 text-center">';
		if(!$trying_last)
		{
			if($found_current && !isset($next_exercise_id))
			{
				$next_exercise_id = $exercise_id;
				$next_index = $i;
			}
			if($exercise_id == $exercise->get_id())
			{
				$found_current = true;
			}
		}

		if($exercise_obj->status == Completion_Status::COMPLETED)
		{
			echo '<a href="/?controller=exercise&action=try_it&id=' . $exercise_id . '&lesson_id=' . $lesson->get_id() . '&concept_id=' . $concept_id . '" class="tile btn btn-success" id="exercise-' . $exercise_id . '"><span>' . $i . '</span></a>';
		}
		elseif (!$found_latest)
		{
			echo '<a href="/?controller=exercise&action=try_it&id=' . $exercise_id . '&lesson_id=' . $lesson->get_id() . '&concept_id=' . $concept_id . '" class="tile btn btn-default" id="exercise-' . $exercise_id . '"><span>' . $i . '</span></a>';
			$found_latest = true;
			if($exercise_id == $exercise->get_id())
			{
				$trying_latest = true;
			}
		}
		else
		{
			echo '<a class="tile btn btn-default disabled" id="exercise-' . $exercise_id . '"><span>' . '<span class="glyphicon glyphicon-lock" aria-hidden="true"></span><span class="sr-only">Locked</span>' . '</span></a>';
		}

		echo '</div>';
		$i++;
	}
	echo '</div>';

echo '</div></div>';
echo '<div class="col-xs-9 height-100 flex-columns">';

echo '<div class="row no-shrink">
		<div class="col-xs-12">
			<h3>' . htmlspecialchars($exercise_props['name']) . '</h3>
			<p id="prompt">' . htmlspecialchars($exercise_props['description']) . '</p>
		</div>
	</div>';

echo '<div class="row no-shrink navbar-default navbar-form navbar-left">
					<button type="button" class="btn btn-default" id="runButton"><span class="glyphicon glyphicon-play" aria-hidden="true"></span><span class="sr-only">Run</span></button>';
			//<span>Choose a test file:</span><input type="file"  class="form-control" id="fileInput">
			echo '</div>
			<div class="row overflow-hidden height-100">
				<div class="col-xs-6 height-100 overflow-hidden pad-0">
					<textarea id="code" name="code">' . $exercise_props['starter_code'] . '</textarea>
				</div>
				<div class="col-xs-6 height-100">

					<div id="mycanvas" class="graphicalOutput"></div>
					<pre id="output" ></pre>

				</div>
			</div>
			<div class="row no-shrink"> <!--this alert needs to be filled with the error, or the next button-->
				<div class="col-xs-12 pad-0">
					<div class="alert alert-default mar-0" role="alert" id="infoAlert">
					</div>
				</div>
			</div>';

echo '</div></div>';

//if the next exercise id isn't set, it should go back to... somewhere... the section page? the next lesson? let's stick with the section page for now.

//trying latest -> color tiles
//trying last -> link is next exercise
//link
echo	'<script>';
echo	'var exercise_id = ' . $exercise->get_id() . ';'; //use to get id of tile to color
echo	'var lesson_id = ' . $lesson->get_id() . ';'; //use to get id of tile to color
echo	'var concept_id = ' . $concept->get_id() . ';'; //use to get id of tile to color

echo	'var trying_latest = ' . ($trying_latest ? 'true' : 'false') . ';';
echo	'var trying_last = ' . ($trying_last ? 'true' : 'false') . ';';
	if($trying_last)
	{
echo	'var link = "' . '/?controller=section&action=read_student&id=' . $concept->get_properties()['section']->key . '";';
	}
	else
	{
echo	'var next_exercise_id = ' . $next_exercise_id . ';';
echo	'var next_index = ' . $next_index . ';';
echo	'var link = "' . '/?controller=exercise&action=try_it&id=' . $next_exercise_id . '&lesson_id=' . $lesson->get_id() . '&concept_id=' . $concept_id . '";';
	}
//echo	'var completion_link = "' . '/?controller=exercise&action=mark_as_completed&id=' . $exercise->get_id() . '&lesson_id=' . $lesson->get_id() . '&concept_id=' . $concept_id . '";';
echo	'</script>';
echo '<script src="js/editor.js"></script>';


