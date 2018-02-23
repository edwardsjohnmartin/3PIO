<?php
//TODO: This is just a mess. 
$finished_arr = $_SESSION['arr'];
unset($_SESSION['arr']);
if(empty($finished_arr)){
	add_alert("There are no sections to show.", Alert_Type::DANGER);
	return call('pages', 'error');
}
else
{
	$wrote_section_name = false;
	$wrote_exam_name = false;
	foreach($finished_arr as $s_key => $s_value){
		if(!empty($s_value['exams'])){
			foreach($s_value['exams'] as $e_key => $e_value){
				if(!empty($s_value['students'])){
					foreach($s_value['students'] as $st_key => $st_value){
						$occurrences = Question::read_occurrences($st_key, $e_key);
						if(!empty($occurrences)){
							echo '<div>';
							if(!$wrote_section_name){
								echo'<h1>' . $s_key . '</h1>';
								$wrote_section_name = true;
							}
							echo '<div>';
							if(!$wrote_exam_name){
								echo '<h4>' . $e_value . '</h4>';
								$wrote_exam_name = true;
							}
							echo '<table class="table table-striped table-bordered">';
							echo '<thead>';
							echo '<tr>';
							echo '<th>Student</th>';
							echo '<th>Question</th>';
							echo '<th>Date Of Occurrence</th>';
							echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							foreach($occurrences as $o_value){
								echo '<tr>';
								echo '<td>' . $st_value . '</td>';
								echo '<td>' . $o_value['question_id'] . '</td>';
								echo '<td>' . $o_value['date_of_occurrence'] . '</td>';
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
							echo '</div>';
							echo '</div>';
						}
					}
				}
				$wrote_exam_name = false;
			}
		}
		$wrote_section_name = false;
	}
}
?>
