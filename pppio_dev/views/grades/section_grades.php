<?php
//View that makes a table for each exam that will list every student in the specified section and the grade they got per question.

$exams = Exam::get_all_for_section($section_id);

$section_props = $section->get_properties();
$students = $section_props['students'];

$html_string = '<h1>' . $section_props['name'] . ' Grades</h1>';

if(count($exams) > 0){
	foreach($exams as $exam_key => $exam_value){
		$total_weight = 0;

		$questions = $exam_value['questions'];
		$exam_scores = Grades::get_exam_grades($exam_value['id']);
		$q_index = 1;
		$header_filled = false;

		$html_string .= '<table class="table table-striped table-bordered">';
		$head_string = '<thead><tr><th><a title="Update Exam Times" href="?controller=exam&action=update_times&id=' . $exam_value['id'] . '">' . $exam_value['name'] . '</a></th>';
		$body_string = '<tbody>';

		foreach($students as $s_key => $s_value){
			$total_score = 0;

			//$body_string .= '<tr><td><a title="Review Exam" href="?controller=exam&action=review_exam&stud_id=' . $s_key . '&exam_id=' . $exam_value['id'] . '&question_id=' . $exam_value['questions'][0]->id . '">' . $s_value->value . '</a></td>';
			$body_string .= '<tr><td>' . $s_value->value . '</td>';

			foreach($questions as $q_key => $q_value){
				$cell_class_string = "";

				//Create the table header if it hasn't already been done.
				if(!$header_filled){
					$total_weight += $q_value->weight;

					$head_string .= '<th><a title="View Question Details" href="?controller=question&action=read&id=' . $q_value->id . '">';

					//Use the question name if it has one, otherwise use 'Q [question_index]'
					if($q_value->name !== ''){
						$head_string .= $q_value->name . ' (' . $q_value->weight . ')</a></th>';}
					else{
						$head_string .= 'Q' . $q_index . ' (' . $q_value->weight . ')</th>';}
				}

				//Check if the student has an answer for a question. Get their score if they do or default the score to 0
				if(array_key_exists($s_key, $exam_scores) and array_key_exists($q_value->id, $exam_scores[$s_key])){
					$cell_score = floatval($exam_scores[$s_key][$q_value->id]);

					//Color the cell if the student had an answer. Green for correct answer, red for incorrect answer
					if($cell_score == 1){
						$cell_class_string = "class=success";
					}
					else if($cell_score == 0){
						$cell_class_string = "class=danger";
					}
					else {
						$cell_class_string = "class=warning";
					}
				}
				else{
					$cell_score = 0;
				}

				$total_score += $cell_score * $q_value->weight;
				$body_string .= '<td ' . $cell_class_string . '><a title="Review Question" href="?controller=exam&action=review_exam&stud_id=' . $s_key . '&exam_id=' . $exam_value['id'] . '&question_id=' . $exam_value['questions'][$q_key]->id . '">' . round($cell_score * $q_value->weight, 2) . '</a></td>';
				$q_index++;

			}
			if(!$header_filled){
				$head_string .= '<th>Total Weight (' . $total_weight . ')</th>';
				$head_string .= '<th>Grade (%)</th></tr></thead>';
				$html_string .= $head_string;
			}
			$header_filled = true;

			$body_string .= '<td>' . round($total_score, 2) . '</td>';
			$body_string .= '<td>' . round($total_score / $total_weight * 100, 2) . '</td></tr>';
		}
		$body_string .= '</tbody></table>';
		$html_string .= $body_string;
	}
	echo $html_string;
	echo '<script src="js/drag_drop_table.js"></script>';
}
else
{
	$html_string .= '<h3>No Exams To Show</h3>';
	echo $html_string;
}
?>
