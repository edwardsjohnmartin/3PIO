<div id="test-form">
	<?php
	//This is the view that is used when a student does a question on an exam
	//TODO: Figure out how to css style better to handle scaling objects to screen resolution better and resizing with zoom.

	echo '<link rel="stylesheet" href="css/editor.css">';
	require_once('views/shared/CodeMirror.php');
	require_once('views/shared/Skulpt.php');
	require_once('enums/completion_status.php');

	//HACK: Manually adding the students answer and their completion status for the current question to the props array. Needs to be added to model in a way that won't break it everywhere else.
	$question_props = $question->get_full_properties();
	$question_props['contents'] = $question->contents;
	$question_props['completion_status'] = $question->completion_status;
	$current_question_id = ($question_props['id']);
	$exam_props = $exam->get_properties();
	$trying_last = intval($current_question_id === end($exam_props['questions'])->key);
	$total_weight = $exam->get_total_weight1();
	$question_counter = 1;

	//If the student doesn't have code saved for this question, use the original start code.
	if($question_props['contents'] ===  null){
		$start_area_code = $question_props['start_code'];}
	else{
		$start_area_code = $question_props['contents'];
	}
    ?>

	<!--Window Content-->
	<div class="row height-100 overflow-hidden">

		<!--Left Navbar-->
		<div class="col-xs-2 height-100 right-pad-0 test-sidebar">

			<!--Exam Name-->
			<div class="row text-center">
				<h4><?php echo $exam_props['name'];?></h4>
			</div>

			<!--Scrollable Area-->
			<div class="scrollable-div container-fluid">

				<!--Question Tiles-->
				<div class="row"><?php
					//Draw each question tile on the left navbar
					foreach($exam_props['questions'] as $question_id => $question_obj){
						echo '<div class="col-xs-4 text-center">';

						//Set the id of the question after the one the user is currently on. Used for the button link on the alert bar
						if(isset($set_next_question_id) and $set_next_question_id === true){
							$next_question_id = $question_id;
							$set_next_question_id = false;
						}

						if($question_id == $current_question_id){
							$set_next_question_id = true;
							$q_pos = $question_counter;
							$question_status = "primary";
						}
						else if($question_obj->status == Completion_Status::COMPLETED){
							$question_status = "success";
						}
						else if($question_obj->status == Completion_Status::STARTED){
							$question_status = "started";
						}
						else{
							$question_status = "default";
						}

						echo '<a href="?controller=question&action=read_for_student&id=' . $question_id . '&exam_id=' . $exam_props['id'] . '"';
						echo 'class="tile btn btn-' . $question_status . '" id="question-' . $question_id . '-exam-' . $exam_props['id'] . '">';
						echo '<span class="tile-number">' . $question_counter . '</span>';
						$q_tile_point_val = round($question_obj->value/$total_weight*100);
						if($q_tile_point_val < 1){
							$q_tile_point_val = 1;
						}
						echo '<div><span class="tile-number">' . $q_tile_point_val . 'pts</span></div>';
						echo '</a></div>';

						$question_counter++;
					}
?></div>
			</div>

			<!--Tile Colors Legend-->
			<div class="row text-center">
				<h4>Legend</h4>
				<h5>Blue = Current Question</h5>
				<h5>White = Not Started</h5>
				<h5>Yellow = Started</h5>
				<h5>Green = Completed</h5>
			</div>
		</div>

		<!--Right Column-->
		<div class="col-xs-9 height-100 flex-columns">

			<!--Title Row-->
			<div class="row no-shrink">

				<!--Question Properties-->
				<div class="col-xs-12">

					<!--Question Index and Point Value-->
					<h2>Q<?php 
						 $q_point_val = round($question_props['weight']/$total_weight*100);
						 if($q_point_val < 1){
							 $q_point_val = 1;
						 }
						 echo $q_pos . ' - ' . $q_point_val;
						 ?>pts</h2>

					<!--Collapsible Instructions Area-->
					<div>
						<h4 class="panel-title collapse-link">
							<a data-toggle="collapse" data-target="#instructions" href="#prompt">Instructions</a>
						</h4>

						<div id="instructions" class="collapse in">
							<p id="prompt"><?php echo htmlspecialchars($question_props['instructions']);?></p>
						</div>
					</div>

					<!--Collapsible Start Code Area-->
					<div>
						<h4 class="panel-title collapse-link">
							<a data-toggle="collapse" data-target="#start_code" href="#prompt1">Start Code</a>
						</h4>
						<div id="start_code" class="collapse">
							<p id=prompt1>
								<pre><?php echo $question_props['start_code'];?></pre>
							</p>
						</div>
					</div>
				</div>
			</div>

			<!--Run Button Row-->
			<div class="row no-shrink navbar-default navbar-form navbar-left">
				<button type="button" class="btn btn-default" id="runButton">
					<span class="glyphicon glyphicon-play" aria-hidden="true"></span>
					<span class="sr-only">Run</span>
				</button>
			</div>

			<!--Alert Row-->
			<div class="row no-shrink">
				<!--this alert needs to be filled with the error, or the next button-->
				<div class="col-xs-12 pad-0">
					<div id="codeAlerts"></div>
				</div>
			</div>

			<!--Code Input and Output Row-->
			<div class="row overflow-hidden height-100">

				<!--Code Input Area-->
				<div class="col-xs-6 height-100 overflow-hidden pad-0">
					<textarea id="code" name="code"><?php echo $start_area_code;?></textarea>
				</div>

				<!--Code and Graphics Output Area-->
				<div class="col-xs-6 height-100 overflow-auto">

					<!--Turtle Canvas-->
					<div id="mycanvas" class="graphicalOutput"></div>

					<!--Text Output-->
					<div class="textOutput">
						<pre id="output"></pre>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	//Set the link for the button on the alert bar that comes up when a question is answered correctly
	if($trying_last){
		$link = '"?controller=section&action=read_student&id=' . $exam_props['section']->key . '"';
	}
	else{
		$link = '"?controller=question&action=read_for_student&id=' . $next_question_id . '&exam_id=' . $exam_props['id'] . '"';
	}

	//Set test code for the question
	echo '<script type="text/x-python" id="test_code_to_run">';
	require('py_test/METHODS.py');
	echo $question_props['test_code'] . '</script>';
    ?>
	<script>
		var current_tile_id = "question-<?php echo $current_question_id . '-exam-' . $exam_props['id'];?>";
		document.getElementById(current_tile_id).scrollIntoView();
		var user_id = <?php echo $_SESSION['user']->get_id();?>;
		var exam_id = <?php echo $exam_props['id'];?>;
		var current_question_id = <?php echo $current_question_id;?>;
		var trying_last = <?php echo $trying_last;?>;
		var link = <?php echo $link;?>;
		var test_code = <?php echo json_encode($question_props['test_code']); ?>;
	</script>
	<script src="js/question_editor.js"></script>
	<?php
	if(array_key_exists($exam_props['section']->key, $_SESSION['sections_is_study_participant'])){
		echo '<script src="js/sessions_handler.js"></script>';
	}
    ?>
</div>
