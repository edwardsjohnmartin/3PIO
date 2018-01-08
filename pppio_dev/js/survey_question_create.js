window.onload = function () {
    document.getElementById("btn_create_survey_choice").addEventListener("click", addChoice);
};

function addChoice() {
    var choice = document.getElementById("txt_survey_choice").value;

    if (choice !== "") {
        $.ajax({
            method: "POST",
            url: "?controller=survey_choice&action=ajax_create",
            data: { choice: choice },
            success: function () {
                window.location.reload();
            }
        });
    }
} 
