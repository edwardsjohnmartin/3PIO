function complete_previous_exercises(ex_id, les_id, con_id, u_id) {
    console.log('ex_id: ' + ex_id);
    console.log('les_id: ' + les_id);
    console.log('con_id: ' + con_id);
    console.log('u_id: ' + u_id);

    $.ajax({
        method: "POST",
        url: "?controller=concept&action=complete_exercises_ajax",
        data: { exercise_id: ex_id, lesson_id: les_id, concept_id: con_id, user_id: u_id }
    });
}
