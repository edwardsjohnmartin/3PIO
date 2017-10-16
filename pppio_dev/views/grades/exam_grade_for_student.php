<?php
require_once('models/exam.php');

$user_id = $_SESSION['user']->get_id();
$exam_id = $_GET['exam_id'];
$exam = exam::get_for_student($exam_id);
$exams = Exam::get_all_for_section($_GET['section_id']);
$exam_props = $exam->get_properties();
$exam_scores = Grades::get_exam_scores($exam_id);

foreach($exam_scores as $key => $value)
{
	if($value['student'] == $user_id)
	{
		$scores = $value['scores'];
	}
}

foreach($exams as $ekey => $evalue)
{
	if($evalue['id'] == $exam_id)
	{
		$questions = $evalue['questions'];
	}
}

echo '<h1>' . $exam_props['name'] . '</h1>';

echo '<table class="table table-striped table-bordered">';
echo '<thead>';
echo '<tr>';
$question_array = array();
$weights_array = array();
foreach($questions as $question_key => $question_value)
{
	array_push($question_array, $question_value->id);
	array_push($weights_array, $question_value->weight);
	echo '<th>' . $question_value->name . ' (' . $question_value->weight . ')</th>';
}
echo '<th>Grade (' . array_sum($weights_array) . ')</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
echo '<tr>';
$score_array = array();
foreach($question_array as $q_key => $q_value)
{
	$value_found = false;
	foreach($scores as $skey => $svalue)
	{
		if($svalue->question_id == $q_value)
		{
			$score_var = $svalue->score * $weights_array[$q_key];
			$value_found = true;
		}
	}

	if(!$value_found)
	{
		$score_var = 0;
	}
	array_push($score_array, $score_var);
	echo '<td class="warning">' . $score_var . '</td>';
}
echo '<td class="warning">' . array_sum($score_array) . ' - ' . array_sum($score_array)/array_sum($weights_array)*100 . '%</td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';
?>
