<?php
$project_props = $project->get_properties();

echo '<link rel="stylesheet" href="css/editor.css">';
require_once('views/shared/CodeMirror.php');
require_once('views/shared/Skulpt.php');

echo '<div class="col-xs-12 height-100 flex-columns">';

echo '<div class="row no-shrink">
		<div class="col-xs-12">
			<h3>' . $project_props['name']  . '</h3>'; //$exercise_props['lesson']->value); //bugs leftover from switching to only one lesson per project
			//check if empty
			//if($exercise_props['name'] !== '') echo '<h4>' . htmlspecialchars($exercise_props['name']). '</h4>';

			echo '<p id="prompt">' . $project_props['description'] . '</p>
		</div>
	</div>';

echo '<div class="row no-shrink navbar-default navbar-form navbar-left">
					<button type="button" class="btn btn-default" id="runButton"><span class="glyphicon glyphicon-play" aria-hidden="true"></span><span class="sr-only">Run</span></button>';
			//<span>Choose a test file:</span><input type="file"  class="form-control" id="fileInput">
			echo '</div>
			<div class="row no-shrink"> <!--this alert needs to be filled with the error, or the next button-->
			<div class="col-xs-12 pad-0">
				<div id="codeAlerts"></div>
			</div>
		</div>
			<div class="row overflow-hidden height-100">
				<div class="col-xs-6 height-100 overflow-hidden pad-0">
					<textarea id="code" name="code">';
					if($code != null)
					{
						echo $code;
					}
					else
					{
						echo $project_props['starter_code'];
					}
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

echo '<script>var concept_id = ' . $concept->get_id() . '; var readonly = ' . ($readonly ? 'true' : 'false') . ';</script>';
echo '<script src="js/project_editor.js"></script>';

?>
