<?php
	require_once('models/model.php');
	require_once('models/lesson.php');
	require_once('models/exercise.php');
	require_once('enums/type.php');
	
	class Importer
	{
		//$regex_string is used for verifying that the file is in the correct format and retrieving the names of the lessons.
		static private $regex_string = '/Lesson: ([^\n]+)((?:\r\n|\n)*Ex:(?:\r\n|\n)(?:[^{]*)\{([^}]*)\}(?:\r\n|\n)(?:[^{]*)\{([^}]*)\}(?:\r\n|\n)(?:[^{]*)\{([^}]*)\})+/';
		
		//$exercise_regex is used for retrieving the specific attributes (prompt, starter code, test code) of each exercise.
		private static $exercise_regex = '/(Ex:(?:\r\n|\n)(?:[^{]*)\{([^}]*)\}(?:\r\n|\n)(?:[^{]*)\{([^}]*)\}(?:\r\n|\n)(?:[^{]*)\{([^}]*)\})+/';
		
		public static function get_lessons($file_string)
		{
			$lessons = [];
			if (preg_match_all(static::$regex_string, $file_string, $matches, PREG_OFFSET_CAPTURE)) 
			{
				
				for ($i=0; $i < count($matches[0]); $i++)
				{
					$lesson_name = $matches[1][$i][0];	//name of the current lesson
					$lesson = new Lesson(); 	//current lesson
				
					preg_match_all(static::$exercise_regex, $matches[0][$i][0], $exercise_matches, PREG_OFFSET_CAPTURE);
					
					$exercises = [];	//Holds the exercises for the current lesson
					
					for ($j=0; $j < count($exercise_matches[0]); $j++)
					{
						$exercise = new Exercise();	
						
						$prompt = $exercise_matches[2][$j][0];
						$starter_code = $exercise_matches[3][$j][0];
						$test_code = $exercise_matches[4][$j][0];
						
						//For now, I'm just assuming that the language is Python, which is why 'language' is always 1.
						$ex_attributes = array('description' => $prompt, 'starter_code' => $starter_code, 'test_code' => $test_code);
						$exercise->set_properties($ex_attributes);
						
						$exercises[] = $exercise;
						
						//print_r($exercise->get_properties());
						
					}
					
					$l_attributes = array('name' => $lesson_name, 'exercises' => $exercises);
					$lesson->set_properties($l_attributes);
					
					$lessons[] = $lesson;
				}
				
			}
			return $lessons;
		} 
	}
	
?>
