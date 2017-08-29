<?php
$project_props = $project->get_properties();

echo '<link rel="stylesheet" href="css/editor.css">';
require_once('views/shared/CodeMirror.php');
require_once('views/shared/Skulpt.php');

echo '<div class="col-xs-12 height-100 flex-columns">';

echo '<div class="row no-shrink">
		<div class="col-xs-12">
			<h3><a href="?controller=user&action=read&id=' . $user->get_id() . '">' . $user->get_properties()['name'] . '\'s</a> ' . $project_props['name']  . '</h3>'; //$exercise_props['lesson']->value); //bugs leftover from switching to only one lesson per project
			//check if empty
			//if($exercise_props['name'] !== '') echo '<h4>' . htmlspecialchars($exercise_props['name']). '</h4>';

			echo '</h3>
			<p id="prompt">' . $project_props['description'] . '</p>
		</div>
	</div>';

echo '<div class="row no-shrink navbar-default navbar-form navbar-left">
					<button type="button" class="btn btn-default" id="runButton"><span class="glyphicon glyphicon-play" aria-hidden="true"></span><span class="sr-only">Run</span></button>';
			echo '</div>
			<div class="row no-shrink"> <!--this alert needs to be filled with the error, or the next button-->
			<div class="col-xs-12 pad-0">
			<div id="codeAlerts"></div>
			</div>
		</div>
			<div class="row overflow-hidden height-100">
				<div class="col-xs-6 height-100 overflow-hidden pad-0">
					<textarea id="code" name="code">';
						echo $code;
					echo '</textarea>
				</div>
				<div class="col-xs-6 height-100">

					<div id="mycanvas" class="graphicalOutput"></div>
                    <div class="textOutput">
      				  <pre id="output"></pre>
                    </div>

				</div>
			</div>
			';

echo '</div></div>';

echo '<script src="js/python_ide_util.js"></script>';
echo '<script src="js/run_only.js"></script>';

?>
