function complete_previous_exercises(ex_id, les_id, con_id, u_id, btn_id) {
    $.ajax({
        method: "POST",
        url: "?controller=concept&action=complete_exercises_ajax",
        data: { exercise_id: ex_id, lesson_id: les_id, concept_id: con_id, user_id: u_id }
    });

    var i = 1;
    var btn_num = parseInt(btn_id.substring(9)); //length of "btn_tile_"

    for (i = 1; i <= btn_num; i++) {
        var btn_id_temp = "btn_tile_" + i.toString();
        var btn = document.getElementById("btn_tile_" + i.toString());
        btn.classList.remove("btn-default");
        btn.classList.remove("btn-success");

        btn.classList.add("btn-success");
    }
}
