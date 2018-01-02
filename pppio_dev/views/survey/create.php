<h2>Create Survey</h2>

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="token" value="<?php echo getToken();?>" />
    <label for="name">Name</label>
    <input type="text" class="form-control" name="name" value="" placeholder="Enter survey name" />

    <label for="survey_type">Survey Type</label>
    <select class="form-control" name="survey_type" id="sel_survey_type">
        <option value="" disabled selected>Select your option</option>
        <?php foreach($survey_types as $key => $value){?>
        <option value="<?php echo $key;?>" ><?php echo $value;?></option>
		<?php }?>
    </select>

    <label for="section">Section</label>
    <select class="form-control" name="section" id="sel_section">
        <option value="" disabled selected>Select your option</option>
		<?php foreach($concepts as $key => $value){
        echo '<option>' . $key . '</option>';
        }?>
    </select>

    <label for="concept">Concept</label>
    <select class="form-control" name="concept" id="sel_concept">
        <option value="" disabled selected>Select your option</option>
    </select>

    <label for="lesson">Lesson</label>
    <select class="form-control" name="lesson" id="sel_lesson">
        <option value="" disabled selected>Select your option</option>
    </select>

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

    <input type="submit" class="form-control" value="Submit">
</form>

<script type="text/javascript" language="javascript">
    var concepts = new Array();
    <?php foreach($concepts as $s_id => $s_val){ ?>
	    var arr = new Array();
	    <?php 
		foreach($s_val['concepts'] as $key => $val){ ?>
		    arr[<?php echo $val->key;?>] = '<?php echo $val->value; ?>';
		<?php } ?>
        concepts.push(arr);
    <?php } ?>
</script>

<script type="text/javascript" language="javascript">
    var lessons = new Array();
    <?php foreach($lessons as $c_id => $c_val){ ?>
	    var arr = new Array();
	    <?php 
		foreach($c_val['lessons'] as $key => $val){ ?>
		arr[<?php echo $val->key;?>] = "<?php echo str_replace("\r", "", $val->value); ?>";
		<?php } ?>
        lessons.push(arr);
    <?php } ?>
</script>

<script src="js/survey_create.js"></script>
