<?php
$project_props = $project->get_properties();

echo '<link rel="stylesheet" href="css/editor.css">';
require_once('views/shared/CodeMirror.php');
require_once('views/shared/Skulpt.php');

echo '<div class="col-xs-12 height-100 flex-columns">
	<div class="row no-shrink">
		<div class="col-xs-12">
			<h3>' . $project_props['name']  . '</h3>
			<h4 class="panel-title"><a data-toggle="collapse" data-target="#description" href="#prompt">Description</a></h4><div id="description" class="collapse in"><p id="prompt">' . $project_props['description'] . '</p></div>
		</div>
	</div>
	<div class="row no-shrink navbar-default navbar-form navbar-left">
		<button type="button" class="btn btn-default" id="runButton"><span class="glyphicon glyphicon-play" aria-hidden="true"></span><span class="sr-only">Run</span></button>
	</div>
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
		</div>;
	</div>
</div>';

echo '<script>var concept_id = ' . $concept->get_id() . '; var readonly = ' . ($readonly ? 'true' : 'false') . ';</script>';
echo '<script src="js/project_editor.js"></script>';
?>
