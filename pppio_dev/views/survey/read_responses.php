<h2>Survey Responses</h2>

<div>
	<select class="form-control" name="survey" id="sel_rsp_survey">
        <option value="" disabled selected>Select a survey</option>        
		<?php
        foreach($surveys as $key => $value){
			echo '<option value="' . $key . '">' . $value . '</option>';
		}
        ?>
    </select>

	<table class="table" id="tbl_surveys">
		<thead>
            <tr>
                <th>Section</th>
                <th>Concept</th>
                <th>Lesson</th>
                <th>Survey Type</th>
				<th>Action</th>
            </tr>
		</thead>
		<tbody id="tbl_surveys_body"></tbody>
	</table>
</div>

<script>
	window.onload = function () {
		document.getElementById("sel_rsp_survey").addEventListener("change", get_assigned_surveys);
	};

	function get_assigned_surveys() {
		var survey_id = document.getElementById('sel_rsp_survey').selectedIndex;

		if (survey_id !== null && survey_id !== 0) {
			$.ajax({
				method: "POST",
				url: "?controller=survey&action=get_assigned_surveys",
				data: { survey_id: survey_id },
				success: function (data) {
					removeBodyRows();
					createTable(data);
				}
			});
		}
	};

	function removeBodyRows() {
		var body = document.getElementById('tbl_surveys_body')
		if (body) body.parentNode.removeChild(body);
	}

	function createTable(assigned_surveys) {
		var table = document.getElementById('tbl_surveys');

		var tableBody = document.createElement('tbody');
		tableBody.id = 'tbl_surveys_body';

		for (var i in assigned_surveys) {
			var row = document.createElement('tr');

			for (var j in assigned_surveys[i]) {
				var cell = document.createElement('td');
				if (assigned_surveys[i][j] == null) {
					cell.appendChild(document.createTextNode('None'));
				} else {
					cell.appendChild(document.createTextNode(assigned_surveys[i][j]));
				}
				row.appendChild(cell);
			}

			var cell = document.createElement('td');
			var btn = document.createElement('button');
			btn.id = 'btn_' + i;
			btn.appendChild(document.createTextNode('View Results'));
			btn.addEventListener('click', get_results);
			cell.appendChild(btn);
			row.appendChild(cell);

			tableBody.appendChild(row);
		}

		table.appendChild(tableBody);
	};

	function get_results(data) {
		console.log(data.toElement.id);
	};
</script>
