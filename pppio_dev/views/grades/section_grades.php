<?php
$exams = Exam::get_all_for_section($section_id);

$html_string = '<h1>' . $section_props['name'] . 'Grades</h1>';

$section_props = $section->get_properties();
$students = $section_props['students'];

if(count($exams) > 0)
{
	foreach($exams as $exam_key => $exam_value)
	{
		$total_weight = 0;

		$questions = $exam_value['questions'];
		$exam_scores = Grades::get_exam_grades($exam_value['id']);
		$q_index = 1;
		$header_filled = false;

		$html_string .= '<table class="table table-striped table-bordered">';
		$head_string = '<thead><tr><th>' . $exam_value['name'] . '</th>';
		$body_string = '<tbody>';

		foreach($students as $s_key => $s_value)
		{
			$total_score = 0;

			$body_string .= '<tr><td><a href="?controller=exam&action=review_exam&stud_id=' . $s_key . '&exam_id=' . $exam_value['id'] . '">' . $s_value->value . '</a></td>';

			foreach($questions as $q_key => $q_value)
			{
				if(!$header_filled)
				{
					$total_weight += $q_value->weight;

					if($q_value->name !== '')
					{
						$head_string .= '<th>' . $q_value->name . ' (' . $q_value->weight . ')</th>';
					}
					else
					{
						$head_string .= '<th>Q' . $q_index . ' (' . $q_value->weight . ')</th>';
					}
				}

				if(array_key_exists($s_key, $exam_scores))
				{
					if(array_key_exists($q_value->id, $exam_scores[$s_key]))
					{
						$cell_val = intval($exam_scores[$s_key][$q_value->id]);
					}
					else
					{
						$cell_val = 0;
					}
				}
				else
				{
					$cell_val = 0;
				}

				$total_score += $cell_val * $q_value->weight;
				$body_string .= '<td>' . $cell_val * $q_value->weight . '</td>';
				$q_index++;

			}
			if(!$header_filled)
			{
				$head_string .= '<th>Total Weight (' . $total_weight . ')</th>';
				$head_string .= '<th>Grade (%)</th></tr></thead>';
				$html_string .= $head_string;
			}
			$header_filled = true;

			$body_string .= '<td>' . $total_score . '</td>';
			$body_string .= '<td>' . round($total_score / $total_weight * 100) . '</td></tr>';
		}
		$body_string .= '</tbody></table>';
		$html_string .= $body_string;
	}
	echo $html_string;
}
else
{
	$html_string .= '<h3>No Exams To Show</h3>';
	echo $html_string;
}
?>
