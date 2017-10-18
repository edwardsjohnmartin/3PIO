<?php
	require_once('models/model.php');
	require_once('models/lesson.php');
	require_once('models/exercise.php');
	require_once('models/exam.php');
	require_once('models/question.php');
	require_once('enums/type.php');

	class Importer
	{
		public static function get_lessons($file_string)
		{
          //print_r($file_string);

            //$content_regex = '((?:.|\n)*)';
            $content_regex = '(.*?)';
            $exercise_regex = 'Ex:\s*<d>\s*'.$content_regex.'<\/d>\s*<s>\s*'.$content_regex.'<\/s>\s*<t>\s*'.$content_regex.'<\/t>';
            //$exercise_regex = 'Ex:\s*<desc>\s*'.$content_regex.'<\/desc>\s*<starter>\s*'.$content_regex.'<\/starter>\s*<test>\s*'.$content_regex.'<\/test>';
            $regex_string = '/Lesson: ([^\n]+)(\s*' . $exercise_regex . ')+/s';

			$lessons = [];
            //pcre.jit = false;
            //ini_set("pcre.jit", 0);
            //ini_set('pcre.jit', false);

            //print_r($exercise_regex);
			if (preg_match_all($regex_string, $file_string, $matches, PREG_OFFSET_CAPTURE))
			//if (preg_match_all(static::$regex_string, $file_string, $matches, PREG_OFFSET_CAPTURE))
			{

              //print_r(count($matches[0]));
				for ($i=0; $i < count($matches[0]); $i++)
				{
					$lesson_name = $matches[1][$i][0];	//name of the current lesson
					$lesson = new Lesson(); 	//current lesson

					preg_match_all('/('.$exercise_regex.')+/s', $matches[0][$i][0], $exercise_matches, PREG_OFFSET_CAPTURE);
					//preg_match_all(static::$exercise_regex, $matches[0][$i][0], $exercise_matches, PREG_OFFSET_CAPTURE);

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
            else
            {
              print_r($file_string);
              $err = preg_last_error();
              if ($err == PREG_BACKTRACK_LIMIT_ERROR) {
                print_r('Backtrack limit was exhausted!');
              } else if ($err == PREG_NO_ERROR) {
                print_r('PREG_NO_ERROR');
              } else if ($err == PREG_INTERNAL_ERROR) {
                print_r('PREG_INTERNAL_ERROR');
              } else if ($err == PREG_BACKTRACK_LIMIT_ERROR) {
                print_r('PREG_BACKTRACK_LIMIT_ERROR');
              } else if ($err == PREG_RECURSION_LIMIT_ERROR) {
                print_r('PREG_RECURSION_LIMIT_ERROR');
              } else if ($err == PREG_BAD_UTF8_ERROR) {
                print_r('PREG_BAD_UTF8_ERROR');
              } else if ($err == PREG_BAD_UTF8_OFFSET_ERROR) {
                print_r('PREG_BAD_UTF8_OFFSET_ERROR');
              } else if ($err == PREG_JIT_STACKLIMIT_ERROR) {
                print_r('PREG_JIT_STACKLIMIT_ERROR');
              }
              print_r('Failed to parse');
            }
			return $lessons;
		}

		public static function get_exams($file_string){
			$content_regex = '(.*?)';
			$question_regex = 'Q:\s*<w>\s*'.$content_regex.'<\/w>\s*<d>\s*'.$content_regex.'<\/d>\s*<s>\s*'.$content_regex.'<\/s>\s*<t>\s*'.$content_regex.'<\/t>';
			$regex_string = '/Exam: ([^\n]+)(\s*' . $question_regex . ')+/s';

			$exams = [];
			if (preg_match_all($regex_string, $file_string, $matches, PREG_OFFSET_CAPTURE))
			{
				for ($i=0; $i < count($matches[0]); $i++)
				{
					$exam_name = $matches[1][$i][0];	//name of the current exam
					$exam = new Exam(); 	//current exam

					preg_match_all('/('.$question_regex.')+/s', $matches[0][$i][0], $question_matches, PREG_OFFSET_CAPTURE);

					$questions = [];	//Holds the questions for the current exam

					for ($j=0; $j < count($question_matches[0]); $j++)
					{
						$question = new Question();

						$weight = $question_matches[2][$j][0];
						$instructions = $question_matches[3][$j][0];
						$start_code = $question_matches[4][$j][0];
						$test_code = $question_matches[5][$j][0];

						//For now, I'm just assuming that the language is Python, which is why 'language' is always 1.
						$q_attributes = array('weight' => $weight, 'instructions' => $instructions, 'start_code' => $start_code, 'test_code' => $test_code);
						$question->set_properties($q_attributes);

						$questions[] = $question;
					}

					$x_attributes = array('name' => $exam_name, 'questions' => $questions, 'instructions' => 'Default instructions');
					$exam->set_properties($x_attributes);

					$exams[] = $exam;
				}
			}
			else
			{
				print_r($file_string);
				$err = preg_last_error();
				if ($err == PREG_BACKTRACK_LIMIT_ERROR) {
					print_r('Backtrack limit was exhausted!');
				} else if ($err == PREG_NO_ERROR) {
					print_r('PREG_NO_ERROR');
				} else if ($err == PREG_INTERNAL_ERROR) {
					print_r('PREG_INTERNAL_ERROR');
				} else if ($err == PREG_BACKTRACK_LIMIT_ERROR) {
					print_r('PREG_BACKTRACK_LIMIT_ERROR');
				} else if ($err == PREG_RECURSION_LIMIT_ERROR) {
					print_r('PREG_RECURSION_LIMIT_ERROR');
				} else if ($err == PREG_BAD_UTF8_ERROR) {
					print_r('PREG_BAD_UTF8_ERROR');
				} else if ($err == PREG_BAD_UTF8_OFFSET_ERROR) {
					print_r('PREG_BAD_UTF8_OFFSET_ERROR');
				} else if ($err == PREG_JIT_STACKLIMIT_ERROR) {
					print_r('PREG_JIT_STACKLIMIT_ERROR');
				}
				print_r('Failed to parse');
			}
			return $exams;
		}
	}

?>
