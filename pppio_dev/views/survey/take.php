<?php
$questions = $survey_questions;
?>

<h1>Take Survey</h1>
<form action="/?controller=survey&action=do_survey&survey_id=<?php echo $_GET['survey_id'];?>" method="post" enctype="application/x-www-form-urlencoded">
	<?php
	$question_index = 1;
	foreach($questions as $key => $question){
		$q_id = $question->get_id();
		$question_props = $question->get_properties();
		echo '<div class="form-group">';
		echo '<label class="input-md">' . $question_index . '. ' . $question_props['prompt'] . '</label>';

		if($question_props['survey_question_type'] == Question_Type_Enum::MULTIPLE_CHOICE){
			$ele_name = 'm_' . $q_id;

			echo '<div class="radio">';
			foreach($question_props['survey_choices'] as $c_id => $c_value){
				echo '<label class="control-label radio-inline input-md">';

				if(array_key_exists($ele_name, $_POST) and $_POST[$ele_name] == $c_key){
					$end = ' checked="checked" required>';
				} else {
					$end = ' required>';
				}

				echo '<input class="radio" value="' . $c_id . '" type="radio" name="' . $ele_name . '"' . $end;
				echo $c_value['choice'] . '</label>';
			}
			echo '</div>';
		} else if($question_props['survey_question_type'] == Question_Type_Enum::RANGE){
			$ele_name = 'r_' . $q_id;

			echo '<div>';
			echo '<input type="number" placeholder="#" name="' . $ele_name . '" min="'.$question_props['min'].'" max="'.$question_props['max'].'"';
			if(array_key_exists($ele_name, $_POST)){
				echo ' value="' . intval($_POST[$ele_name]) . '"';
			}
			echo ' required/>';
			echo '</div>';
		} else if($question_props['survey_question_type'] == Question_Type_Enum::SHORT_ANSWER){
			$ele_name = 's_' . $q_id;

			echo '<div>';
			echo '<input type="text" placeholder="Enter your answer here" name="' . $ele_name . '"';
			if(array_key_exists($ele_name, $_POST)){
				echo ' value="' . $_POST[$ele_name] . '"';
			}
			echo ' required/>';
			echo '</div>';
		}
		echo '</div>';
		$question_index++;
	}
	if($can_save){
		echo '<input type="submit" class="form-control" value="Submit"/>';
	} else {
		echo '<label class="input-md alert-danger">You cannot save the answers to the survey.</label>';
	}
	?>
</form>
