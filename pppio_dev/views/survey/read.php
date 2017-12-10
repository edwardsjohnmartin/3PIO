<?php
//TODO: Make survey type, concept, and lesson appear as the the name of the item instead of their id

echo '<link rel="stylesheet" href="css/survey.css">';
?>

<?php
$question_count = 1; //will be used for putting the question numbers in
$survey_props = $model->get_properties();

echo '<div class="properties-container">';
echo '<h1>Survey Preview</h1>';

//TODO: This can probably be contained in a for loop
echo '<label>Name</label>';
echo '<div>' . $survey_props['name'] . '</div>';
echo '<label>Instructions</label>';
echo '<div>' . $survey_props['instructions'] . '</div>';

echo '<label>Survey Type</label>';
echo '<div>' . Survey_Type::get($survey_props['survey_type'])->get_properties()['name'] . '</div>';
echo '<label>Concept</label>';
echo '<div>' . Concept::get($survey_props['concept'])->get_properties()['name'] . '</div>';

//a survey may not have a lesson depending on the survey type
if($survey_props['lesson'] !== null)
{
	echo '<label>Lesson</label>';
	$lesson_name = Lesson::get($survey_props['lesson'])->get_properties()['name'];
	if($lesson_name === "")
	{
		$lesson_name = "[Unnamed Lesson]";
	}
	echo '<div>' . $lesson_name . '</div>';
	echo '</div>';
}

//Start of the form
echo '<form>';
echo '<div class="question-container">';
foreach($questions as $q_key => $q_value){
	echo '<div id="div_' . $q_key . '" class="container">';
	echo '<label>' . $question_count . '. ' . $q_value . '</label>';
	foreach($choices[$q_key] as $c_key => $c_value){
		echo '<div class="rb_div">';
		echo '<input type="radio" name="q_' . $q_key . '" value="' . $c_key . '">';
		echo '<label>' . $c_value . '</label>';
		echo '</div>';
	}
	echo '</div>';
	$question_count++;
}
echo '</div>';
echo '</form>';
?>
