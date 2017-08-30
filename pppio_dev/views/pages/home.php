<h2>Welcome to 3PIO!</h2>

<?PHP
//start of a dashboard on the home index page
if(isset($_SESSION['user']) && $_SESSION['user'] != null) {
    if(isset($_SESSION['sections_student']) && $_SESSION['sections_student'] != null && count($_SESSION['sections_student']) >0) {
        echo '<h3><u>Directory</u></h3>';
        foreach($_SESSION['sections_student'] as $section_kvp)
        {
            echo '<h2><a href="?controller=Section&action=read_student&id=' . $section_kvp->key . '">' . htmlspecialchars($section_kvp->value) . '</a></h2>';
            require_once('models/concept.php');
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
}
?>