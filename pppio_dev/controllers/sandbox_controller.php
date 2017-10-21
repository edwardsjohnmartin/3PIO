<?php
	class SandboxController
	{
		public function index()
		{
			include('views/shared/site_functions.php');
			include('models/html_objects/button.php');
			include('models/html_objects/dropdown_item.php');

			$title = 'Sandbox Mode';
			$dropdown_items = array(
				new dropdown_item('drp_instructions', 'Instructions', 'Write code and test it out here. Any code entered here will not be saved. Play around and try new things.'),
				new dropdown_item('drp_get_sum', 'getSum', '#returns the sum all the numbers up to number passed in\nimport random\ndef getSum(num):\n\tsum = 0\n\tfor n in range(0, num):\n\t\tsum += n\n\treturn sum\n\nrndNum = random.randint(5, 50)\ntheSum = getSum(rndNum)\nprint(&quotThe sum of all numbers up to &quot + str(rndNum) + &quot is &quot + str(theSum))'),
				new dropdown_item('drp_print_list', 'printList', 'Write code and test it out here.'),
				new dropdown_item('drp_reverse_string', 'reverseString', 'Write code and test it out here.'),
				new dropdown_item('drp_get_item_from_list', 'getItemFromList', 'Write code and test it out here.')
			);
			$default_code = 'for i in range(15):\n    print(\'Sandbox Mode\')';
			$params = array(
				'title' => $title,
				'left_title' => $left_title,
				'dropdown_items' => $dropdown_items,
				'default_code' => $default_code
			);
			$view_to_show = "";
			require_once('views/shared/layout.php');
			create_code_editor_view($params);
		}
	}
?>
