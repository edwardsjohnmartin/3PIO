
<?php
	require_once('views/shared/html_helper.php'); 
	require_once('views/shared/CodeMirror.php');
	echo '<script src="js/skulpt/skulpt.min.js"></script>
	<script src="js/skulpt/skulpt-stdlib.js"></script>';

	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo '<h2>' . $this->model_name . '</h2>';
	$props = $model->get_properties();
	echo HtmlHelper::view($model::get_types(), $props);
	echo 'Test this exercise';

echo '<div class="row no-shrink navbar-default navbar-form navbar-left">
					<button type="button" class="btn btn-default" id="runButton"><span class="glyphicon glyphicon-play" aria-hidden="true"></span><span class="sr-only">Run</span></button>';
			//<span>Choose a test file:</span><input type="file"  class="form-control" id="fileInput">
			echo '</div>
			<div class="row overflow-hidden height-100">
				<div class="col-xs-6 height-100 overflow-hidden pad-0">
					<textarea id="code" name="code">' . $props['starter_code'] . '</textarea>
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

echo '<script type="text/x-python" id="test_code_to_run">';
require('py_test/METHODS.py');
echo $props['test_code'];
echo '</script>';

echo '<script src="js/editor_test.js"></script>';

if(has_permission(new Permission(Securable::EXERCISE, Permission_Type::EDIT)))
{
	echo '<a href="/?controller=' . $this->model_name . '&action=update&id=' . $model->get_id() . '" class="btn btn-primary">Update</a><br>';
}
?>

