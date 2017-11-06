<?php
//This is the view for when a student clicks the 'View Grade' link from the section/read_student view
//It will show the name of the exam and a table the contains what their score was on each question and what their final grade on the exam is

//Fills array $scores with objects that contain the question_id and the score multiplier(based on completion_status) for the entire exam

//TODO: To display final grade, dont add up percentage of weigths on each question, add the weights themselves and get percentage of that
foreach($exam_scores as $key => $value)
{
	if($value['student'] == $_SESSION['user']->get_id()){
		$scores = $value['scores'];
		break;
	}
}

//Fills array $questions with objects that contain the question_id, name, and weight of each question on the exam
foreach($exams as $key => $value)
{
	if($value['id'] == $exam_id)
	{
		$questions = $value['questions'];
		break;
	}
}

$q_index = 1;
$student_score = 0;
$question_headers = '';
$question_cells = '';

//Go through each question and generate an html string for the headers and cells
foreach($questions as $q_key => $q_value)
{
	//Use $q_index to name questions that don't have a name
	if($q_value->name !== '')
	{
	    $question_headers .= '<th>' . $q_value->name . ' (' . round($q_value->weight/$exam_weight*100) . ')</th>';
	}
	else
	{
	    $question_headers .= '<th>Q' . $q_index . ' (' . round($q_value->weight/$exam_weight*100) . ')</th>';
	}

	$q_index++;
	$value_found = false;
	$score_var = 0;

	foreach($scores as $s_key => $s_value)
	{
		if($s_value->question_id == $q_value->id)
		{
			//This is hardcoded to account for a small portion of students that got a .5 for a score on a question due to a bug
			//The bug is now fixed so this can be deleted before the next semester 10/23/2017
			if($s_value->score == 0.5)
			{
			    $score_var = 0;
			    break;
			}
			else
			{
			    //Show question weight as a number out of 100
				$score_var = $s_value->score * round($q_value->weight/$exam_weight*100);
				$student_score += $s_value->score * $q_value->weight;
				break;
			}
		}
	}

	$question_cells .= '<td class="warning">' . $score_var . '</td>';
}
?>

<!--HTML To Make Grades Table-->
<h1>
	<?php echo $exam_props['name'];?>
</h1>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<?php echo $question_headers;?>
			<th>Grade (%)</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?php echo $question_cells;?>
			<td class="warning"><?php echo round($student_score/$exam_weight*100);?></td>
		</tr>
	</tbody>
</table>
