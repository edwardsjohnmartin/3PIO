<?php
	class SandboxController
	{
		public function index()
		{
			include('views/shared/site_functions.php');
			include('models/html_objects/button.php');
			include('models/html_objects/dropdown_item.php');

			$title = 'Sandbox Mode';
			$default_code = 'for i in range(50):\n    print(\"Sandbox Mode \" + str(i))';
			$dropdown_items = array(
				new dropdown_item('drp_instructions', 'Instructions', 'Write code and test it out here. Any code entered here will not be saved. Play around and try new things.'),
				new dropdown_item('drp_default', 'Default Code', str_replace('"', "&quot", $default_code)),
				new dropdown_item('drp_get_sum', 'Random Showcase', 'import random\nimport turtle\n\n#Returns the sum of the numbers in the range from a lower limit to an upper limit\ndef getSum(lowerLimit, upperLimit):\n    sum = 0\n\n    for num in range(lowerLimit, upperLimit):\n        sum += num\n    return sum\n\n#randrange acts like the range used in for loops and its endValue is exclusive\n#range(startValue, endValue, stepAmount)\nlowerLimit = random.randrange(0, 60, 3)\n\n#generate an upperLimit to pass into getSum\nupperLimit = random.randint(5, 5000)\n\ntheSum = getSum(lowerLimit, upperLimit)\nprint(&quotThe sum of all numbers up to &quot + str(upperLimit) + &quot is &quot + str(theSum))\n\n#random.choice will pick a random item in the list passed into it\nlstColors = [&quotred&quot, &quotgreen&quot, &quotyellow&quot, &quotblue&quot]\nbgColor = random.choice(lstColors)\n\n#lists have an index function that will return the first position an object appears\nindexOfbgColor = lstColors.index(bgColor)\n\n#del deletes the element at the passed in index\n#we do this to guarantee the fontcolor wont be the same color as the background\ndel lstColors[indexOfbgColor]\n\nfontColor = random.choice(lstColors)\n\n#A screen object has a bgcolor function to change the background color to parameter passed in\nscreenObj = turtle.Screen()\nscreenObj.bgcolor(bgColor)\n\nturtleObj = turtle.Turtle()\nturtleObj.color(fontColor)\nturtleObj.ht()\nturtleObj.write(fontColor, align=&quotcenter&quot, font=(&quotArial&quot, 30,  &quotbold&quot))\n'),
				new dropdown_item('drp_sample_turtle', 'Turtle Code', 'import turtle\n\nt = turtle.Turtle()\ns = turtle.Screen()\n\n#t.tracer(0)\n\nh = s.window_height()\nw = s.window_width()\n\nprint(&quotThe current height of the drawable area is &quot + str(h))\nprint(&quotThe current width of the drawable area is &quot + str(w))\n\nt.forward(w/2 - 10)\nt.right(90)\nt.forward(h/2 - 10)\nt.right(90)\nt.forward(w - 20)\nt.right(90)\nt.forward(h - 20)\nt.right(90)\nt.forward(w - 20)\nt.right(90)\nt.forward(h/2 - 10)\n\n#t.update()')
			);
			$params = array(
				'title' => $title,
				'dropdown_items' => $dropdown_items,
				'default_code' => $default_code
			);
			$view_to_show = "";
			require_once('views/shared/layout.php');
			create_code_editor_view($params);
		}
	}
?>
