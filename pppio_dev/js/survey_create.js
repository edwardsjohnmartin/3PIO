window.onload = function () {
    document.getElementById("sel_concept").disabled = true;
    document.getElementById("sel_lesson").disabled = true;

    document.getElementById("sel_section").addEventListener("change", handleSectionDropdown);
    document.getElementById("sel_concept").addEventListener("change", handleConceptDropdown);
    document.getElementById("sel_survey_type").addEventListener("change", handleSurveyTypeDropdown);
};

function handleSectionDropdown(sel) {
    var sel_section = document.getElementById("sel_section");
    var sel_concept = document.getElementById("sel_concept");
    var sel_lesson = document.getElementById("sel_lesson");

    sel_concept.disabled = false;
    
    for(var i = sel_concept.options.length - 1; i > 0; i--) {
        sel_concept.remove(i);
    }

    var index = 1;
    for(var key in concepts[sel_section.selectedIndex - 1]) {
        var option = document.createElement("option");
        option.value = key;
        option.text = concepts[sel_section.selectedIndex - 1][key];
        sel_concept.add(option, index);
        index++;
    }

    sel_concept.selectedIndex = 0;
    sel_lesson.selectedIndex = 0;
}

function handleConceptDropdown(sel) {
    var sel_survey_type = document.getElementById("sel_survey_type");
    var sel_lesson = document.getElementById("sel_lesson");
    var index = sel_survey_type.selectedIndex;

    for (var i = sel_lesson.options.length - 1; i > 0; i--) {
        sel_lesson.remove(i);
    }

    var ndx = 1;
    for (var key in lessons[sel_concept.selectedIndex - 1]) {
        var option = document.createElement("option");
        option.value = key;
        option.text = lessons[sel_concept.selectedIndex - 1][key];
        sel_lesson.add(option, ndx);
        ndx++;
    }

    sel_lesson.selectedIndex = 0;

    if (sel_survey_type.options[index].text === "Pre-Lesson" || sel_survey_type.options[index].text === "Post-Lesson") {
        sel_lesson.disabled = false;
    } else {
        sel_lesson.disabled = true;
    }
}

function handleSurveyTypeDropdown(sel) {
    var sel_survey_type = document.getElementById("sel_survey_type");
    var sel_concept = document.getElementById("sel_concept");

    var index = sel_survey_type.selectedIndex;

    if (sel_survey_type.options[index].text === "Pre-Lesson" || sel_survey_type.options[index].text === "Post-Lesson") {
        if (sel_concept.selectedIndex !== 0) {
            document.getElementById("sel_lesson").disabled = false;
        }
    } else {
        document.getElementById("sel_lesson").disabled = true;
        document.getElementById("sel_lesson").selectedIndex = 0;
    }
}
