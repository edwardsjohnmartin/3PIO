<?php
	require_once('views/shared/html_helper.php');
	require_once('enums/completion_status.php');
	require_once('models/question.php');

	$exam_props = $exam->get_properties();

	echo '<h1>' . htmlspecialchars($exam_props['name']) . '</h1>';

	$exam_id = $exam->get_id();
	$questions = $exam_props['questions'];
	$total_question_count = count($questions);

	$i = 1;
	foreach($questions as $question_id => $question_obj)
	{
		echo '<div class="col-md-2 col-xs-4 text-center">';

		if($question_obj->status == Completion_Status::COMPLETED)
		{
			echo '<a href="?controller=question&action=read_for_student&id=' . $question_id . '&exam_id=' . $exam_id . '
			" class="tile btn btn-success"><span class="tile-number">' . $i . '</span><span class="tile-label">'
			. htmlspecialchars($question_obj->value) . '</span></a>';
		}
		else//if ($question_obj->status == Completion_Status::NOT_STARTED)
		{
			echo '<a href="?controller=question&action=read_for_student&id=' . $question_id . '&exam_id=' . $exam_id . '
			" class="tile btn btn-default"><span class="tile-number">' . $i . '</span><span class="tile-label">'
			. htmlspecialchars($question_obj->value) . '</span></a>';
		}

		echo '</div>';
		$i++;
	}
?>