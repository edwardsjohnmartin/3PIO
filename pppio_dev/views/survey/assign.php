<h2>Assign Surveys</h2>

<div>
    <table id="tbl_surveys">
        <thead>
            <tr>
                <th>Section</th>
                <th>Concept</th>
                <th>Project</th>
                <th>Survey</th>
                <th>Survey Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>            
			<?php
			foreach($assigned_surveys as $key => $row){
				echo '<tr id="row_' . $key . '">';
				echo '<td>' . $row['section']->value . '</td>';
				echo '<td>' . $row['concept']->value . '</td>';
				echo '<td>' . $row['project']->value . '</td>';
				echo '<td>' . $row['survey']->value . '</td>';
				echo '<td>' . $row['survey_type']->value . '</td>';
				echo '<td><button id="row_unassign_' . $key . '">Unassign</button></td>';
				echo '</tr>';
			}
			?>
            <tr>
                <td>
                    <select class="form-control" name="section" id="sel_section">
                        <option value="" disabled selected>Select a section</option>                        
						<?php 
						foreach($_SESSION['sections_owner'] as $key => $value){
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
                <td><button id="btn_assign">Assign</button></td>
            </tr>
        </tbody>
    </table>
	<div id="div_alert"></div>
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
