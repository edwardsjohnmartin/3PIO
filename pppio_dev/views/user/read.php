
<?php
	require_once('views/shared/html_helper.php'); 
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo '<h2>' . $this->model_name . '</h2>';
	unset($properties['password']);
	unset($types['password']);
	echo HtmlHelper::view($types, $properties);

if(has_permission(new Permission(constant('Securable::' . strtoupper($this->model_name)), Permission_Type::EDIT)))
{
	echo '<a href="/?controller=' . $this->model_name . '&action=update&id=' . $model->get_id() . '" class="btn btn-primary">Update</a><br>';
}
?>

