<?php
	require_once('enums/completion_status.php');
	$question_props = $question->get_properties();
	$exam_props = $exam->get_properties();
	$exam_id = $exam->get_id();
	$current_question_id = $_GET['id'];
	$last_question_id = end($exam_props['questions'])->key;
	$trying_last = (intval($current_question_id) === $last_question_id);
	$mark_next = false;

	echo '<link rel="stylesheet" href="css/editor.css">';
	require_once('views/shared/CodeMirror.php');
	require_once('views/shared/Skulpt.php');

	echo '
	<div class="row height-100 overflow-hidden">
		<div class="col-xs-3 height-100 overflow-auto right-pad-0">
			<div class="container-fluid">
				<h2>' . $exam_props['name'] . '</h2>
				<div class="row">';

				$i = 1;
				foreach($exam_props['questions'] as $question_id => $question_obj)
				{
					if($mark_next)
					{
						$next_question_id = $question_id;
						$mark_next = false;
					}
					if(!$trying_last)
					{
						if($question_id == $current_question_id)
						{
							$mark_next = true;
						}
					}
					echo '<div class="col-xs-4 text-center">';
					if($question_obj->status == Completion_Status::COMPLETED)
					{
						echo '<a href="?controller=question&action=read_for_student&id=' . $question_id . '&exam_id=' . $exam_id . '" class="tile btn btn-success" id="question-' . $question_id . '-exam-' . $exam->get_id() . '"><span class="tile-number">' . $i . '</span></a>';
					}
					else
					{
						echo '<a href="?controller=question&action=read_for_student&id=' . $question_id . '&exam_id=' . $exam_id . '" class="tile btn btn-default" id="question-' . $question_id . '-exam-' . $exam->get_id() . '"><span class="tile-number">' . $i . '</span></a>';

					}
					echo '</div>';
					$i++;
				}
				echo '
				</div>
			</div>
		</div>
		<div class="col-xs-9 height-100 flex-columns">
			<div class="row no-shrink">
				<div class="col-xs-12">';
				if($question_props['name'] !== '') echo '<h2>' . htmlspecialchars($question_props['name']). '</h2>';
					echo '<p id="prompt">' . htmlspecialchars($question_props['instructions']) . '</p>
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
					<textarea id="code" name="code">' . $question_props['start_code'] . '</textarea>
				</div>
				<div class="col-xs-6 height-100">
					<div id="mycanvas" class="graphicalOutput"></div>
					<div class="textOutput">
      					<pre id="output"></pre>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/x-python" id="test_code_to_run">';

	if($trying_last)
	{
		$link = '"?controller=exam&action=read_for_student&id=' . $exam_id . '"';
	}
	else
	{
		$link = '"?controller=question&action=read_for_student&id=' . $next_question_id . '&exam_id=' . $exam_id . '"';
	}
	require('py_test/METHODS.py');
	echo $question_props['test_code'];
    echo '</script>';
	echo '<script>var current_tile_id = "question-' . $question_id . '-exam-' . $exam_id . '";</script>'; //use to color tile
	echo '<script>var readonly = ' . ($readonly ? 'true' : 'false') . ';</script>';
	echo '<script>var exam_id = ' . $exam_id . ';</script>';
	echo '<script>var current_question_id = ' . $_GET['id'] . ';</script>';
	echo '<script>var trying_last = "' . $trying_last . '";</script>';
	echo '<script>var link = ' . $link . ';</script>';
	echo '<script src="js/question_editor.js"></script>';
?>