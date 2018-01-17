window.onload = function () {
    document.getElementById("sel_concept").disabled = true;

    document.getElementById("sel_section").addEventListener("change", handleSectionDropdown);
    document.getElementById("btn_assign").addEventListener("click", assignSurvey);
};

function unassignSurvey(assigned_survey_id, row_id){
    var row = document.getElementById("row_unassign_" + row_id).parentNode.parentNode;

    clearAlerts();
    var html_class;

    //call unassign db func
    $.ajax({
        method: "POST",
        url: "?controller=survey&action=unassign_survey",
        data: { assigned_survey_id: assigned_survey_id },
        success: function (data) {
            if (data.success) {
                html_class = "success";
                deleteTableRow(row, "tbl_surveys");
                addRowToUnassignedTable(row);
            } else {
                html_class = "danger";
            }
            showAlert(data.message, html_class);
        }
    });
}

function reassignSurvey(assigned_survey_id, row_id) {
    var row = document.getElementById("row_reassign_" + row_id).parentNode.parentNode;

    clearAlerts();
    var html_class;

    //call reassign db func
    $.ajax({
        method: "POST",
        url: "?controller=survey&action=reassign_survey",
        data: { assigned_survey_id: assigned_survey_id },
        success: function (data) {
            if (data.success) {
                html_class = "success";
                deleteTableRow(row, "tbl_unassigned_surveys");
                addRowToAssignedTable(row);
            } else {
                html_class = "danger";
            }
            showAlert(data.message, html_class);
        }
    });
}

var div_alert = document.getElementById('div_alert')

function handleSectionDropdown(sel) {
    clearAlerts();
    var sel_section = document.getElementById("sel_section");
    var sel_concept = document.getElementById("sel_concept");

    sel_concept.disabled = false;

    for (var i = sel_concept.options.length - 1; i > 0; i--) {
        sel_concept.remove(i);
    }

    var index = 1;
    for (var key in concepts[sel_section[sel_section.selectedIndex].text]) {
        var option = document.createElement("option");
        option.value = key;
        option.text = concepts[sel_section[sel_section.selectedIndex].text][key];
        sel_concept.add(option, index);
        index++;
    }

    sel_concept.selectedIndex = 0;
}

function assignSurvey() {
    clearAlerts();
    var survey_id = document.getElementById("sel_survey").value;
    var concept_id = document.getElementById("sel_concept").value;
    var survey_type_id = document.getElementById("sel_survey_type").value;
    var html_class;

    $.ajax({
        method: "POST",
        url: "?controller=survey&action=assign_survey",
        data: { survey_id: survey_id, concept_id: concept_id, survey_type_id: survey_type_id },
        success: function (data) {
            if (data.success) {
                html_class = "success";
                insert_row();
            } else {
                html_class = "danger";
            }
            showAlert(data.message, html_class);
        }
    });
}

function showAlert(alertMessage, html_class) {
    div_alert.innerHTML += '<div class="alert alert-' + html_class + ' alert-dismissible mar-0" role="alert" id="infoAlert">' 
        + alertMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span ' +
        'aria-hidden="true">&times;</span></button></div>';
}

function clearAlerts() {
    div_alert.innerHTML = '';
}

function insert_row() {
    var new_row = tbl_surveys.insertRow(tbl_surveys.rows.length - 1);

    var td_section = new_row.insertCell(0);
    td_section.value = sel_section[sel_section.selectedIndex].value;
    td_section.appendChild(document.createTextNode(sel_section[sel_section.selectedIndex].text));

    var td_concept = new_row.insertCell(1);
    td_concept.value = sel_concept[sel_concept.selectedIndex].value;
    td_concept.appendChild(document.createTextNode(sel_concept[sel_concept.selectedIndex].text));

    var td_project = new_row.insertCell(2);
    td_project.appendChild(document.createTextNode("-----"));

    var td_survey = new_row.insertCell(3);
    td_survey.value = sel_survey[sel_survey.selectedIndex].value;
    td_survey.appendChild(document.createTextNode(sel_survey[sel_survey.selectedIndex].text));

    var td_survey_type = new_row.insertCell(4);
    td_survey_type.value = sel_survey_type[sel_survey_type.selectedIndex].value;
    td_survey_type.appendChild(document.createTextNode(sel_survey_type[sel_survey_type.selectedIndex].text));

    var td_date = new_row.insertCell(5);
    td_date.appendChild(document.createTextNode("-----"));

    var td_actions = new_row.insertCell(6);
    var new_btn = td_actions.appendChild(document.createElement("button"));
    new_btn.appendChild(document.createTextNode("Unassign"));

    resetSelection();
}

function deleteTableRow(r, tableName){
    var i = r.rowIndex;
    document.getElementById(tableName).deleteRow(i);
}

function addRowToUnassignedTable(r) {
    r.deleteCell(-1);
    r.insertCell(-1);
    var newRow = $(r).clone();
    $('#tbl_unassigned_surveys tbody').append(newRow);
}

function addRowToAssignedTable(r) {
    r.deleteCell(-1);
    r.insertCell(-1);
    var newRow = $(r).clone();
    $('#tbl_surveys tbody tr').eq(-1).before(newRow);
}

function resetSelection() {
    document.getElementById("sel_concept").disabled = true;

    sel_section.selectedIndex = 0;
    sel_concept.selectedIndex = 0;
    sel_survey.selectedIndex = 0;
    sel_survey_type.selectedIndex = 0;
}
