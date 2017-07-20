
<?php
	require_once('views/shared/html_helper.php'); 
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo 'Create Account';
	$model_props = $model->get_properties();
	$properties = array('email' => $model_props['email'], 'name' => $model_props['name'], 'password' => '', 'confirm_password' => '');
	$types = array('email' => Type::EMAIL, 'name' => Type::STRING, 'password' => Type::PASSWORD, 'confirm_password' => Type::PASSWORD);
	echo HtmlHelper::form($types, $properties);
?>



