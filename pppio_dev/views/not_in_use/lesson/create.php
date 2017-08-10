<!-- i need to know the name of the model... there isn't one created. how should i pass it? an empty one? for now i just put $model_name-->

<?php
	require_once('views/shared/html_helper.php');
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo $this->model_name;
	$properties = $this->model_name::get_available_properties();
	$types = $this->model_name::get_types();
	unset($properties['exercises']);
	unset($types['exercises']);
	unset($properties['owner']);
	unset($types['owner']);
	echo HtmlHelper::form($types, $properties);
?>



