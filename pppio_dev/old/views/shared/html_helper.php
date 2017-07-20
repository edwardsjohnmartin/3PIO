<?php
//helper class
//who should include this? each view? i guess whichever views need it?
	class HtmlHelper //should these return strings or print? i think they should return strings.
	{
		private function __construct() {} //......... don't allow to call construct

		//types i need
		//integer - number
		//string - text
		//datetime - datetime? text? should have class for styling
		//boolean - checkbox

		//dropdown of some sort...

		//default to string if nothing else matches

		static function view($types, $properties)
		{
			//labels and values
			$view = '<div>';
			foreach($properties as $key => $value)
			{
				if(isset($types[$key])) //it had better be set! should i just use string if it's not set?
				{
					$view .= static::label($key);
					$view .= static::span($types[$key], $key, $value);
				}
			}
			$view .= '</div>';
			return $view;
		}

		//need empty form and filled form options
		//should set ids
		//dropdowns for model types... how do i know what model types are?
		static function form($types, $properties, $action = null) //should pass in... action, submit test, post/get
		{
			//where to put breaks and labels
			//where to pass in action
			if($action === null) $action = $_SERVER["REQUEST_URI"];
			$form = '<form action="' . $action . '" method="post">';
			foreach($properties as $key => $value)
			{
				if(isset($types[$key])) //it had better be set! should i just use string if it's not set?
				{
					$form .= static::label($key);
					$form .= static::input($types[$key], $key, $value);
				}
			}
			$form .= static::input_submit();
			$form .= '</form>';
			return $form;
		}

		static function label($property)
		{
			return '<label for="' . $property . '">' .  str_replace('_', ' ', ucfirst($property)) . '</label>'; //should replace "_" with " "
		}

		static function span($type, $key, $value)
		{
			if($type == Type::CODE)
			{
				$value = static::span_code($key, $value);
			}
			else if($type >= Type::MODEL) //...please be more careful than this
			{
				$value = $value->value; //seriously be more careful than that
			}
			return '<div>' . $value . '</div>';
		}

		static function span_code($property, $value = null) //these code mirror windows need to put the correct language
		{
			//it's kind of sloppy to do the include here
			include_once('views/shared/CodeMirror.php');	
			//$input = '<input type="text" id="' . $property .'" name="' . $property . '" value="' . nl2br($value) . '">';
			$input = '<textarea id="' . $property .'" name="' . $property .'">' . $value . '</textarea>';
			//put the language properly.
			//var editor' . $property . ' = 
			$js = '<script type="text/javascript">CodeMirror.fromTextArea(document.getElementById("' . $property . '"), {
						mode: {name: "python",
							   version: 3,
							   singleLineStringErrors: false},
						lineNumbers: true,
						indentUnit: 4,
						matchBrackets: true,
						readOnly: true,
						theme: "solarized dark"
    				});</script>';
		return $input . $js;
		}

		static function input($type, $property, $value = null)
		{
			//better way for types? i thought enum, but i also want models.
			//check type and call different input functions...
			if($type == Type::INTEGER) //no, i should have an enum for the types.
			{
				return static::input_integer($property, $value);
			}
			else if($type == Type::BOOLEAN)
			{
				return static::input_boolean($property, $value);
			}
			else if($type == Type::DATETIME)
			{
				return static::input_datetime($property, $value);
			}
			else if($type == Type::CODE)
			{
				return static::input_code($property, $value);
			}
			else if($type > Type::MODEL) //should put function on enum. 'is model'
			{
				//get what model it is, if any, or if it's not a proper one (better be) then show string input
				//return 'need a select for ' . (new Type($type))->getKey();
				$type = strtolower((new Type($type))->getKey());
				require_once('models/' . $type . '.php');
				return static::input_dropdown($property, $value, $type::pairs()); //haha...
			}
			else //assume string
			{
				return static::input_string($property, $value);
			}
			
		}

		static function input_integer($property, $value = null)
		{
			return '<input type="number" class="form-control" name="' . $property . '" value="' . $value . '">'; 
		}

		static function input_boolean($property, $value = null)
		{
			return '<input type="checkbox" class="form-control" name="' . $property . '" value="' . $value . '">'; //may want to pass in value.
		}

		static function input_datetime($property, $value = null)
		{
			return '<input type="datetime-local" class="form-control" name="' . $property . '" value="' . $value . '">';
		}

		static function input_string($property, $value = null)
		{
			return '<input type="text" class="form-control" name="' . $property . '" value="' . $value . '">';
		}

		static function input_code($property, $value = null)
		{
			//it's kind of sloppy to do the include here
			include_once('views/shared/CodeMirror.php');	
			//$input = '<input type="text" id="' . $property .'" name="' . $property . '" value="' . nl2br($value) . '">';
			$input = '<textarea id="' . $property .'" name="' . $property .'">' . $value . '</textarea>';
			//put the language properly.
			//var editor' . $property . ' = 
			$js = '<script type="text/javascript">CodeMirror.fromTextArea(document.getElementById("' . $property . '"), {
						mode: {name: "python",
							   version: 3,
							   singleLineStringErrors: false},
						lineNumbers: true,
						indentUnit: 4,
						matchBrackets: true,
						theme: "solarized dark"
    				});</script>';
		return $input . $js;
		}

		static function input_dropdown($property, $value = null, $options)
		{
			$select = '<select class="form-control" name="'. $property . '" >';
			foreach($options as $option)
			{
				$select .= '<option value="' . $option->key . '"';
				if($value != null && $value->key === $option->key)
				{
					$select .= 'selected';
				}
				$select .= '>' . $option->value . '</option>';
			}
			$select .= '</select>';
			return $select;
		}
		
		static function input_submit($value = 'Submit')
		{
			return '<input type="submit" class="form-control" value="' . $value . '">';
		}


	}

?>
