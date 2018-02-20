<?php
//TODO: Get the question instructions in the multi-select area instead of the names

require_once('views/shared/html_helper.php');

echo '<h2>' . $this->model_name . '</h2>';

$action = $_SERVER["REQUEST_URI"];
$enctype = 'application/x-www-form-urlencoded';
$form = '<form action="' . $action . '" method="post" enctype="' . $enctype . '">';
$form .= HtmlHelper::input_token();

foreach($properties as $key => $value){
	if(isset($types[$key])){
		if($key == 'questions'){
			$form .= HtmlHelper::input_select_multiple($key, $value, Question::get_pairs());
		}else{
			$form .= HtmlHelper::label($key);
			$form .= HtmlHelper::input($types[$key], $key, $value);
		}
	}
}

$form .= HtmlHelper::input_submit();
$form .= '</form>';

echo $form;
?>
