<?<?php
  require_once('controllers/base_controller.php');
  class Survey_ChoiceController extends BaseController
  {
	  public function ajax_create(){
		  $survey_choice = new Survey_Choice();
		  $survey_choice->set_properties($_POST);
		  if($survey_choice->is_valid()){
			  $survey_choice->create();
			  add_alert('Survey Choice created!', Alert_Type::SUCCESS);
		  }
	  }
  }
