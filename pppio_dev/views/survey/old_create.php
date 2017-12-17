<?php
//TODO: Figure out a way to just use the survey_type_enum instead of needing a model for filling $survey_types
//TODO: Once that is done, rename survey_type_enum back to just survey_type
require_once('views/shared/html_helper.php');
require_once('models/lesson.php');
require_once('models/survey_type.php');

echo '<link rel="stylesheet" href="css/survey.css">';

echo '<h2>' . $this->model_name . '</h2>';

echo '<form id="frm_create_survey" action="" method="post" enctype="application/x-www-form-urlencoded">';
echo '<input type="hidden" name="token" value="' . getToken() . '"/>';

echo HtmlHelper::label('name');
echo HtmlHelper::input(Type::STRING, 'name');

//Get all survey_types as key->value pairs and display them in a dropdown
echo HtmlHelper::label('survey_type');
$survey_types = survey_type::get_pairs();
$survey_type_select_html = '<select id="sel_survey_type" class="form-control" name="survey_type">';
foreach($survey_types as $key => $value)
{
	$survey_type_select_html .= '<option value="' .$key . '">' . $value . '</option>';
}
$survey_type_select_html .= '</select>';
echo $survey_type_select_html;

echo HtmlHelper::label('concept');
echo HtmlHelper::input(Type::CONCEPT, 'concept');

/*Get all lessons as key->value pairs and display them in a dropdown
The default value will be 'Choose option'
Iterate over all the lessons and store the html in $lesson_select_html to echo once its finished*/
echo HtmlHelper::label('lesson');
$lessons = Lesson::get_pairs();
$lesson_select_html = '<select id="sel_lesson" class="form-control" name="lesson">';
$lesson_select_html .= '<option value="0" style="display:none">Choose option</option>';
foreach($lessons as $key => $value)
{
	$lesson_select_html .= '<option value="' .$key . '">';

	//If the lesson didn't have a name, show the lesson as [No Name]
	if($value === "")
	{
		$lesson_select_html .= '[No Name]</option>';
	}
	else
	{
		$lesson_select_html .= $value . '</option>';
	}
}
$lesson_select_html .= '</select>';
echo $lesson_select_html;

echo '<div id="question_container" class="container">';
//Add Question Button
echo '<button id="btn_add_question" type="button" class="add-btn">Add Question</button>';
echo '</div>';

echo '<input id="btn_frm_submit" type="submit" class="form-control" value="Submit">';
//echo '<input id="btn_frm_submit" type="button" class="form-control" value="Submit">';
echo '</form>';

echo '<script src="js/survey_create.js"></script>';
?>
