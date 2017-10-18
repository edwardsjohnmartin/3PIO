<?php
echo '<h1>Section Grades</h1>';

$exams = Exam::get_all_for_section($_GET['id']);

if(count($exams) > 0)
{
	$section_props = $section->get_properties();
	$students = $section_props['students'];

	foreach($exams as $exam_key => $exam_value)
	{
		//get an array of students and their scores
		$exam_scores = Grades::get_exam_scores($exam_value['id']);

		if(!empty($exam_scores))
		{
			foreach($exam_scores as $key => $value)
			{
				$exam_scores[$value['student']] = $value;
				unset($exam_scores[$key]);
				unset($exam_scores[$value['student']]['student']);
				$var = $value['student'];
				foreach($exam_scores[$var]['scores'] as $key1 => $value1)
				{
					$exam_scores[$var]['scores'][$value1->question_id] = $value1->score;
					unset($exam_scores[$var]['scores'][$key1]);
				}
			}
		}

		echo '<table class="table table-striped table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th>' . $exam_value['name'] . '</th>';
		$question_array = array();
		$weights_array = array();
		$q_index = 1;
		foreach($exam_value['questions'] as $question_key => $question_value)
		{
			//iterate through all the questions putting their
			//id's and weights into seperate parallel arrays
			array_push($question_array, $question_value->id);
			array_push($weights_array, $question_value->weight);

			//create a header in the table for each question
			if($question_value->name !== '')
			{
				echo '<th>' . $question_value->name . ' (' . $question_value->weight . ')</th>';
			}
			else
			{
				echo '<th>Q' . $q_index . ' (' . $question_value->weight . ')</th>';
			}
			$q_index++;
		}
		echo '<th>Total Weight (' . array_sum($weights_array) . ')</th>';
		echo '<th>Grade</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach($students as $student_key => $student_value)
		{
			$current_student = $student_value->key;
			echo '<tr>';
			echo '<td class="warning">' . $student_value->value . '</td>';

			$score_array = array();
			foreach($question_array as $q_key => $q_value)
			{
				if(array_key_exists($current_student, $exam_scores))
				{
					if(array_key_exists($q_value, $exam_scores[$current_student]['scores']))
					{
						$score_var = $exam_scores[$current_student]['scores'][$q_value] * $weights_array[$q_key];
					}
					else
					{
						$score_var = 0;
					}
				}
				else
				{
					$score_var = 0;
				}
				array_push($score_array, $score_var);
				echo '<td class="warning">' . $score_var . '</td>';
			}
			echo '<td class="warning">' . array_sum($score_array) . '</td>';
			echo '<td class="warning">' . number_format(array_sum($score_array)/array_sum($weights_array)*100, 2) . '%</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
	}
}
else
{
	echo '<h3>No Exams To Show</h3>';
}
?>
