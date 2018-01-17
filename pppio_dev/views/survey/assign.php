<div id="div_alert"></div>

<div>
    <h2>Assigned Surveys</h2>
    <table id="tbl_surveys" class="table table-striped">
        <thead>
            <tr>
                <th>Section</th>
                <th>Concept</th>
                <th>Project</th>
                <th>Survey</th>
                <th>Survey Type</th>
                <th>Date Assigned</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>            
			<?php
			//This is where the content of each row of the assigned surveys table is set
            foreach($assigned_surveys as $key => $row){
            echo '<tr id="row_' . $key . '">';
            echo '<td>' . $row['section']->value . '</td>';
            echo '<td>' . $row['concept']->value . '</td>';
            echo '<td>' . $row['project']->value . '</td>';
            echo '<td>' . $row['survey']->value . '</td>';
            echo '<td>' . $row['survey_type']->value . '</td>';
            echo '<td>' . $row['date_assigned'] . '</td>';
            echo '<td><button id="row_unassign_' . $key . '" onclick="unassignSurvey(' . $row['assigned_survey_id'] . ', ' . $key . ')">Unassign</button></td>';
            echo '</tr>';
            }
            ?>
            <tr>
                <td>
                    <select class="form-control" name="section" id="sel_section">
                        <option value="" disabled selected>Select a section</option>                        
						<?php
                        foreach($sections as $key => $value){
                        echo '<option value="' . $key . '">' . $value . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="concept" id="sel_concept">
                        <option value="" disabled selected>Select a concept</option>
                    </select>
                </td>
                <td>-----</td>
                <td>
                    <select class="form-control" name="survey" id="sel_survey">
                        <option value="" disabled selected>Select a survey</option>                        
						<?php
                        foreach($surveys as $key => $value){
                        echo '<option value="' . $key . '">' . $value . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="survey_type" id="sel_survey_type">
                        <option value="" disabled selected>Select a survey type</option>                        
						<?php
                        foreach($survey_types as $key => $value){
                        echo '<option value="' . $key . '">' . $value . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>-----</td>
                <td><button id="btn_assign">Assign</button></td>
            </tr>
        </tbody>
    </table>
</div>

<div>
	<h2>Unassigned Surveys</h2>
    <table id="tbl_unassigned_surveys" class="table table-striped">
        <thead>
            <tr>
                <th>Section</th>
                <th>Concept</th>
                <th>Project</th>
                <th>Survey</th>
                <th>Survey Type</th>
				<th>Date Unassigned</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>            
			<?php
			//This is where the content of each row of the unassigned surveys table is set
			foreach($unassigned_surveys as $key => $row){
				echo '<tr id="row_' . $key . '">';
				echo '<td>' . $row['section']->value . '</td>';
				echo '<td>' . $row['concept']->value . '</td>';
				echo '<td>' . $row['project']->value . '</td>';
				echo '<td>' . $row['survey']->value . '</td>';
				echo '<td>' . $row['survey_type']->value . '</td>';
				echo '<td>' . $row['date_unassigned'] . '</td>';
				echo '<td><button id="row_reassign_' . $key . '" onclick="reassignSurvey(' . $row['assigned_survey_id'] . ', ' . $key . ')">Reassign</button></td>';
				echo '</tr>';
			}
			?>
        </tbody>
    </table>
</div>

<script type="text/javascript" language="javascript">
    <?php
	echo 'var concepts = [];';
    foreach($concepts as $s_id => $s_val){ 
	    echo 'var arr = [];';
	    foreach($s_val['concepts'] as $key => $val){ 
		    echo 'arr["' . $val->key . '"] = "' . $val->value . '";';
		}
        echo 'concepts["' . $s_id . '"] = arr;';
    }
	?>
</script>

<script src="js/survey_create.js"></script>
