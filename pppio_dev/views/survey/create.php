<h2>Create Survey</h2>

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="token" value="<?php echo getToken();?>" />

    <label for="name">Name</label>
	<input type="text" class="form-control" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" placeholder="Enter survey name" />

    <label for="survey_questions">Survey Questions</label>
    <select class="form-control" name="survey_questions[]" id="survey_questions" multiple style="position: absolute; left: -9999px;">
		<?php foreach($survey_questions as $key => $value){
				  echo '<option value=' . $key . '>' . $value . '</option>';
			  }?>
	</select><?php include_once('views/shared/MultiSelect.php');?>

    <script type="text/javascript">
        $("#survey_questions").multiSelect({
            keepOrder: true,
            afterSelect: function (value) {
                $('#survey_questions option[value="' + value + '"]').remove();
                $("#survey_questions").append($("<option></option>").attr("value", value).attr('selected', 'selected'));
            }
        });
    </script>

    <input type="submit" class="form-control" value="Create">
</form>
