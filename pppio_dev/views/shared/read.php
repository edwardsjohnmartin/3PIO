
<?php
	require_once('views/shared/html_helper.php'); 
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo $this->model_name;
	echo HtmlHelper::view($model::get_types(), $model->get_properties());
?>

