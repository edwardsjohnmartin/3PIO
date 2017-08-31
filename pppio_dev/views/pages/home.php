<h2>Welcome to 3PIO!</h2>

<!--This code controls the dashboard on the initial home screen-->
<?PHP

//require statements to access the various models to fill the links
require_once('models/user.php');
require_once('models/concept.php');
require_once('models/section.php');

//IF the user is a student in any sections
if(isset($_SESSION['user']) && $_SESSION['user'] != null) {

    //This will be the logic if the user is a student
    if(isset($_SESSION['sections_student']) && $_SESSION['sections_student'] != null && count($_SESSION['sections_student']) >0) {
        echo '<h3><u>Directory (Student)</u></h3>';
        foreach($_SESSION['sections_student'] as $section_kvp)
        {
            echo '<h2><a href="?controller=Section&action=read_student&id=' . $section_kvp->key . '">' . htmlspecialchars($section_kvp->value) . '</a></h2>';
			$concepts = concept::get_all_for_section_and_student($section_kvp->key, $_SESSION['user']->get_id());
            if(isset($concepts)){
                foreach($concepts as $concept_kvp){
                    $concept_props = $concept_kvp->get_properties();
                    echo '<h3>|   ' . $concept_props['name'] . '</h3>';
                    $lessons = $concept_props['lessons'];
                    echo '<h4><a href="?controller=Lesson&action=read_for_concept_for_student&concept_id=' . $concept_kvp->get_id() . '">| |   Exercises </a></h4>';
                    echo '<h4><a href="?controller=project&action=try_it&concept_id=' . $concept_kvp->get_id() . '">| |   Project </a></h4>';
                }
            } 
        }
    }

    //If the user is the teaching assistant of any sections
    if(isset($_SESSION['sections_ta']) && $_SESSION['sections_ta'] != null && count($_SESSION['sections_ta']) >0){
        echo '<h3><u>Directory (Teaching Assistant)</u></h3>';
        foreach($_SESSION['sections_ta'] as $section_kvp)
        {
            echo '<h2><a href="?controller=Section&action=read&id=' . $section_kvp->key . '">' . htmlspecialchars($section_kvp->value) . '</a></h2>';
			$concepts = concept::get_all_for_section_and_student($section_kvp->key, $_SESSION['user']->get_id());
            if(isset($concepts)){
                foreach($concepts as $concept_kvp){
                    $concept_props = $concept_kvp->get_properties();
                    echo '<h3><a href="?controller=concept&action=read&id=' . $concept_kvp->get_id() . '">|   ' . $concept_props['name'] . '</a></h3>';
                    $lessons = $concept_props['lessons'];
                    echo '<h4><a href="?controller=Lesson&action=read_for_concept_for_student&concept_id=' . $concept_kvp->get_id() . '">| |   Exercises </a></h4>';
                    echo '<h4><a href="?controller=project&action=try_it&concept_id=' . $concept_kvp->get_id() . '">| |   Project </a></h4>';
                }
            } 
        }
    }

    //If the user is the owner of any sections
    if(isset($_SESSION['sections_owner']) && $_SESSION['sections_owner'] != null && count($_SESSION['sections_owner']) >0){
        echo '<h3><u>Directory (Owner)</u></h3>';
        $sections = $_SESSION['sections_owner'];
        foreach($sections as $key => $value)
        {
            echo '<h2><a href="?controller=Section&action=read&id=' . $key . '">' . htmlspecialchars($value) . '</a></h2>';
            $concepts = concept::get_all_for_section($key);
            if(isset($concepts)){
                foreach($concepts as $concept_kvp){
                    $concept_props = $concept_kvp->get_properties();
                    echo '<h3><a href="?controller=concept&action=read&id=' . $concept_kvp->get_id() . '">|   ' . $concept_props['name'] . '</a></h3>';
                    $lessons = $concept_props['lessons'];
                    foreach($lessons as $key => $lesson){
                        echo '<h4><a href="?controller=lesson&action=read&id=' . $key . '">| |   ' . htmlspecialchars($lesson->value) . '</a></h4>';
                    }
                }
            } 
        }
    }
}
?>