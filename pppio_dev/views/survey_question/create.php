
<h2>Create Survey Question</h2>

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="token" value="<?php echo getToken();?>" />

	<label for="prompt">Prompt</label>
	<input type="text" class="form-control" name="prompt" value="<?php if(isset($_POST['prompt'])) echo $_POST['prompt'];?>" placeholder="Enter question prompt" />

	<label for="survey_question_type">Survey Question Type</label>
    <select class="form-control" name="survey_question_type" id="survey_question_type">
        <option value="" disabled selected>Select a question type</option>    
		<?php 
		foreach($survey_question_types as $key => $value){
			if(isset($_POST['survey_question_type']) and $_POST['survey_question_type'] == $key){
				echo '<option value=' . $key . ' selected>' . $value . '</option>';
			} else {
				  echo '<option value=' . $key . '>' . $value . '</option>';
			}
		}
		?>
    </select>

	<div id="div_choices">
		<label for="survey_choices">Survey Choices</label>
		<select class="form-control" name="survey_choices[]" id="survey_choices" multiple style="position: absolute; left: -9999px;">
			<?php foreach($survey_choices as $key => $value){
			echo '<option value=' . $key . '>' . $value . '</option>';}
			?>
		</select>
		<?php include_once('views/shared/MultiSelect.php');?>

		<label>Create New Choice</label>
		<input type="text" class="form-control" id="txt_survey_choice" placeholder="Enter new choice here"/>
		<button class="btn btn-default" id="btn_create_survey_choice">Create Survey Choice</button>
	</div>

	<div class="panel" id="div_ranges">
		<label for="min">Min</label>
		<input class="form-control" name="min" type="number" <?php if(isset($_POST['min'])) echo 'value="' . $_POST['min'] . '"';?>/>

		<label for="max">Max</label>
		<input class="form-control" name="max" type="number" <?php if(isset($_POST['max'])) echo 'value="' . $_POST['max'] . '"';?>/>
	</div>

	<script type="text/javascript">
		$("#survey_choices").multiSelect({
			keepOrder: true,
			afterSelect: function (value) {
				$('#survey_choices option[value="' + value + '"]').remove();
				$("#survey_choices").append($("<option></option>").attr("value", value).attr('selected', 'selected'));
			}
		});
	</script>

	<script src="js/survey_question_create.js"></script>

	<input type="submit" class="form-control" value="Create" />
</form>