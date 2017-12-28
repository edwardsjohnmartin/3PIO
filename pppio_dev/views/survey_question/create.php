<?php
require_once('views/shared/html_helper.php');

echo '<h2>Survey Question</h2>';
if(!isset($options)){
	$options = null;}
echo HtmlHelper::form($types, $properties, null, $options);
?>
<div>
	<textarea id="txt_survey_choice">Empty</textarea>
	<button id="btn_create_survey_choice">Create Survey Choice</button>
</div>

<script src="js/survey_question_create.js"></script>
