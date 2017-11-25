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
	static function form($types, $properties, $action = null, $custom_multiselect_options = null) //should pass in... action, submit test, post/get
	{
		if($custom_multiselect_options === null) $custom_multiselect_options = array();
		//where to put breaks and labels
		//where to pass in action
		if(in_array(Type::FILE, $types))
		{
			$enctype = 'multipart/form-data';
		}
		else
		{
			$enctype = 'application/x-www-form-urlencoded';
		}

		if($action === null) $action = $_SERVER["REQUEST_URI"];
		$form = '<form action="' . $action . '" method="post" enctype="' . $enctype . '">';
		$form .= static::input_token();
		foreach($properties as $key => $value)
		{
			if(isset($types[$key])) //it had better be set! should i just use string if it's not set?
			{
				$form .= static::label($key);
				if(array_key_exists($key, $custom_multiselect_options))
				{
					$form .= static::input($types[$key], $key, $value, $custom_multiselect_options[$key]);
				}
				else
				{
					$form .= static::input($types[$key], $key, $value);
				}
			}
		}
		$form .= static::input_submit();
		$form .= '</form>';
		return $form;
	}

	static function label($property)
	{
		return '<label for="' . $property . '">' .  str_replace('_', ' ', ucfirst($property)) . '</label>';
	}

	static function span($type, $key, $value) //todo: would be nice to have different displays. like labels for tags.
	{
		if($type == Type::CODE)
		{
			$value = static::span_code($key, $value);
		}
		else if(Type::is_model($type))
		{
			$typestr = strtolower((new Type($type))->getKey());
			$value ='<a href="?controller=' . $typestr . '&action=read&id=' . $value->key . '">' . htmlspecialchars($value->value) . '</a>'; //seriously be more careful than that
		}
		else if(Type::is_list_model($type)) //really
		{
			$typestr = substr(strtolower((new Type($type))->getKey()), 5);
			$str = '';
			reset($value);
			if(key($value) != null) // ... is this necessary?
			{

				if($type == Type::LIST_TAG)
				{
					$str .= '<div class="list-group">';
					foreach($value as $key => $val)
					{
						$str .= '<a href="?controller=' . $typestr . '&action=read&id=' . $key . '" class="label label-primary">' . htmlspecialchars($val->value) . '</a> ';
					}
					$str .= '</div>';
				}
				else if($type == Type::LIST_EXAM)
				{
					$str .= '<div class="list-group">';
					foreach($value as $key => $val)
					{
						$str .= '<a href="?controller=' . $typestr . '&action=update_times&id=' . $key . '" class="list-group-item">' . htmlspecialchars($val->value) . '</a>';
					}
					$str .= '</div>';
				}
				else
				{
					$str .= '<div class="list-group">';
					foreach($value as $key => $val)
					{
						$str .= '<a href="?controller=' . $typestr . '&action=read&id=' . $key . '" class="list-group-item">' . htmlspecialchars($val->value) . '</a>';
					}
					$str .= '</div>';
				}

			}
			$value = $str;
		}
		else if (substr($value, 0, 5 ) === "<div>")
		{
		}
		else
		{
			$value = htmlspecialchars($value);
		}
		return '<div>' . $value . '</div>';
	}

	static function span_code($property, $value = null) //these code mirror windows need to put the correct language
	{
		//it's kind of sloppy to do the include here
		include_once('views/shared/CodeMirror.php');
		//$input = '<input type="text" id="' . $property .'" name="' . $property . '" value="' . nl2br($value) . '">';
		$input = '<textarea id="' . $property .'" name="' . $property .'">' . htmlspecialchars($value) . '</textarea>';
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
						theme: "default"
    				});</script>';
		return $input . $js;
	}

	static function input($type, $property, $value = null, $options = null)
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
		else if($type == Type::PASSWORD)
		{
			return static::input_password($property, $value);
		}
		else if($type == Type::CODE)
		{
			return static::input_code($property, $value);
		}
		else if($type == Type::FILE)
		{
			return static::input_file($property);
		}
		else if(Type::is_model($type)) //should put function on enum. 'is model'
		{
			//get what model it is, if any, or if it's not a proper one (better be) then show string input
			//return 'need a select for ' . (new Type($type))->getKey();
			if($options === null)
			{
				$type = strtolower((new Type($type))->getKey());
				require_once('models/' . $type . '.php');
				return static::input_select($property, $value, $type::get_pairs()); //haha...
			}
			else
			{
				return static::input_select($property, $value, $options);
			}
		}
		else if(Type::is_list_model($type)) // todo: do a safer check
		{
			if($options === null)
			{
				$type = substr(strtolower((new Type($type))->getKey()), 5); //it will be called "LIST_something"
				require_once('models/' . $type . '.php');
				return static::input_select_multiple($property, $value, $type::get_pairs()); //maybe pairs should just be arrays... another step though
			}
			else
			{
				return static::input_select_multiple($property, $value, $options);
			}
		}
		else //assume string
		{
			return static::input_string($property, $value);
		}

	}

	static function input_integer($property, $value = null)
	{
		return '<input type="number" class="form-control" name="' . $property . '" value="' . htmlspecialchars($value) . '">';
	}

	static function input_boolean($property, $value = null)
	{
		return '<input type="checkbox" class="form-control" name="' . $property . '" value="' . htmlspecialchars($value) . '">'; //may want to pass in value.
	}

	static function input_datetime($property, $value = null)
	{
		//return '<input type="datetime-local" class="form-control" name="' . $property . '" id="' . $property . '" value="' . htmlspecialchars($value) . '">'
		return ' <div class="input-group date" id="' . $property . '">
                    <input type="text" class="form-control" name="' . $property . '"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>'
	.  '<script type="text/javascript">$(function () {$(\'#' . $property . '\').datetimepicker({defaultDate: "' . htmlspecialchars($value) . '"});});</script>';
	}

	static function input_string($property, $value = null)
	{
		return '<input type="text" class="form-control" name="' . $property . '" value="' . htmlspecialchars($value) . '">';
		//return '<textarea class="form-control" name="' . $property . '" value="' . htmlspecialchars($value) . '"></textarea>';
	}

	static function input_password($property, $value = null)
	{
		return '<input type="password" class="form-control" name="' . $property . '" value="' . htmlspecialchars($value) . '">';
	}

	static function input_code($property, $value = null)
	{
		//it's kind of sloppy to do the include here
		include_once('views/shared/CodeMirror.php');
		//$input = '<input type="text" id="' . $property .'" name="' . $property . '" value="' . nl2br($value) . '">';
		$input = '<textarea id="' . $property .'" name="' . $property .'">' . htmlspecialchars($value) . '</textarea>';
		//put the language properly.
		//var editor' . $property . ' =
		$js = '<script type="text/javascript">CodeMirror.fromTextArea(document.getElementById("' . $property . '"), {
						mode: {name: "python",
							   version: 3,
							   singleLineStringErrors: false},
						lineNumbers: true,
						indentUnit: 4,
						matchBrackets: true,
						theme: "default"
    				});</script>';
		return $input . $js;
	}

	static function input_file($property)
	{
		return '<input type="file" name="' . $property . '">';
	}

	static function input_select($property, $value = null, $options)
	{
		$select = '<select class="form-control" name="'. $property . '" >';
		foreach($options as $k => $v)
		{
			$select .= '<option value="' . $k . '"';
			if($value != null && ((is_int($value) && $value == $k)  || (is_object($value) && $value->key === $k)))
			{
				$select .= 'selected';
			}
			$select .= '>' . htmlspecialchars($v) . '</option>';
		}
		$select .= '</select>';
		return $select;
	}

	static function input_select_multiple($property, $value = null, $options) //these are in the wrong order on update for lesson's exercises
	{
		include_once('views/shared/MultiSelect.php');
		$select = '<select class="form-control" name="'. $property . '[]" id="' . $property . '" multiple>';
		if($value != null)
		{
			reset($value);
			if(key($value) != null) {
				foreach($value as $key => $val) // todo... this can either be a key value array or an int array (if sent back).
				{
					$select .= '<option value="' . $key. '" selected>' . htmlspecialchars($val->value) . '</option>';
				}
			}
		}
		foreach($options as $k => $v)
		{
			if($value == null || $value != null && !array_key_exists($k, $value))
			{
				$select .= '<option value="' . $k . '">' . htmlspecialchars($v) . '</option>';
			}
		}

		$select .= '</select>';
		//https://stackoverflow.com/questions/13243417/jquery-multiselect-selected-data-order
		$js = '<script type="text/javascript">$("#' . $property . '").multiSelect({ keepOrder: true,
				afterSelect: function(value){
        $(\'#' . $property . ' option[value="\'+value+\'"]\').remove();
        $("#' . $property . '").append($("<option></option>").attr("value",value).attr(\'selected\', \'selected\'));
      	}
			});
			</script>';
		return $select . $js;
	}

	/*

	afterSelect: function(value, text){
	var get_val = $("#' . $property . '").val();
	var hidden_val = (get_val != "") ? get_val+"," : get_val;
	$("#' . $property . '").val(hidden_val+""+value);
	},
	afterDeselect: function(value, text){
	var get_val = $("#' . $property . '").val();
	var new_val = get_val.replace(value, "");
	$("#' . $property . '").val(new_val);
	}
	 */

	static function input_submit($value = 'Submit')
	{
		return '<input type="submit" class="form-control" value="' . $value . '">';
	}

	static function input_token()
	{
		return '<input type="hidden" name="token" value="' . getToken() . '"/>';
	}
}
?>
