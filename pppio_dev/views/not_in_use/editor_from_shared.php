<?php 
//	this expects either a problem or a project.
//id, name, description, starter code
$exercise_props = $exercise->get_properties();
$lesson_props = $lesson->get_properties();

echo '<link rel="stylesheet" href="css/site.css">
<link rel="stylesheet" href="css/editor.css">';
include_once('views/shared/CodeMirror.php');
echo '<script src="js/skulpt/skulpt.min.js"></script>
<script src="js/skulpt/skulpt-stdlib.js"></script>';


echo '<div class="row height-100 overflow-hidden">
<div class="col-xs-3 height-100 overflow-auto right-pad-0">
<div class="container-fluid">';

echo '<h2>' . $lesson_props['name'] . '</h2>';

echo '<div class="row">';//more row abuse
	$exercises = $lesson_props['exercises'];
	$i = 1;
	foreach($exercises as $exercise_id => $exercise_name)
	{
		echo '<div class="col-xs-4 text-center"><a href="/?controller=exercise&action=try_it&id=' . $exercise_id . '&lesson_id=' . $lesson->get_id() . '" class="tile btn btn-default"><span>' . $i . '</span></a></div>';
		$i++;
	}
echo '</div></div></div>';
echo '<div class="col-xs-9 height-100 flex-columns">';

echo '<div class="row no-shrink">
		<div class="col-xs-12">
			<h3>' . $exercise_props['name'] . '</h3>
			<p id="prompt">' . $exercise_props['description'] . '</p>
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

					<!--Good job! <button type="button" class="btn btn-success btn-sm"><span class="">Next exercise</span></button>-->
					</div>
				</div>
			</div>';

echo '</div></div>';


echo '<script src="js/editor.js"></script>';


