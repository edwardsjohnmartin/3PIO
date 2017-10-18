<?php
	//controllers must be named
	//name of model + "Controller"
	//in order to get the model name automatically
	//otherwise, set $model_name in the constructor to use a different model name.
	//please make sure to call the parent constructor from the child classes if writing a new constructor and you want the default model name.
	abstract class BaseController
	{
		//needed... index (list), create, read, update, delete
		//will need to check permissions

		 //i need get and post.... what to do

		//"shadowing it" could be a problem. should set in constructor.
		protected $model_name;

		public function __construct()
		{
			//cut off last "Controller" - 10 chars.
			//not case-sensitive
			$this->model_name = substr(static::class, 0, -10);
		}

		public function index()
		{
			//who keeps track of the count, etc...
			//do some pagination
			//get the offset and limit from $_GET
			//if it's not set, default to offset 0
			//idk, the limit will be in a dropdown...
			//well that can be a later problem. i can have the limit be a default value

			//will want shared views... but not completely...
			//where will I put permissions?
			//i suppose those should be checked here

			//if i'm using the shared view, i won't know the model there, so i need to let it know somehow

			//who should get the models? the controller or the view?
			//the example did it in the view, but i think it should be in the model
			//the view shouldn't care... it should just show it.
			//i think that's how it should work.
			//well, i found another source that claims that the view should get data directly from the model... maybe it makes sense? look into more.
			//this is a decision that needs to be made...

			//pagination
			$models = ($this->model_name)::get_pairs(); //rename from all.. do this in the view...?
			//require_once('views/shared/index.php');
			//how will view know which properties to show? actually it looks like just about every model will have a name... maybe should put that in the base model, and use that for the link text
			//probably will want to have some kind of overall description of the model, or at least an option for it...

			$view_to_show = 'views/' . strtolower($this->model_name) . '/index.php';
			if(!file_exists($view_to_show))
			{
				$view_to_show = 'views/shared/index.php';
			}
			require_once('views/shared/layout.php');
		}

		public function create()
		{

			//get from post.
			//validate, fill.
			//$model_name = $this->model_name; //not the best way to do this.
			//if there isn't post data, or if the data is not valid, i need to show the form.
			//i should show errors somehow. how?

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken))
				{
					//probably i should do that isset stuff
					$model = new $this->model_name();
					$model->set_properties($_POST);
					if($model->is_valid())
					{
						//add alerts to session or something
						//http://getbootstrap.com/components/#alerts
						//redirect header("Location: ...");
						$model->create();
						//$_SESSION['alerts'][] = 'Successfully created!';
						add_alert('Successfully created!', Alert_Type::SUCCESS);
						//session_write_close();
						return redirect($this->model_name, 'index');
					}
					else
					{
						add_alert('Please try again.', Alert_Type::DANGER);
					}
				}
				else
				{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}
			//require_once('views/shared/create.php'); //will this be a problem? i think i will know what model by what controller is called...
			$view_to_show = 'views/' . strtolower($this->model_name) . '/create.php';
			if(!file_exists($view_to_show))
			{
				$view_to_show = 'views/shared/create.php';
			}
			$properties = $this->model_name::get_available_properties();
			$types = $this->model_name::get_types();
			require_once('views/shared/layout.php');
		}

		public function read()
		{
			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}
			else
			{
				$model = ($this->model_name)::get($_GET['id']);
				if($model == null)
				{
					add_alert('The item you are trying to access doesn\'t exist.', Alert_Type::DANGER);
					return call('pages', 'error');
				}
				else
				{
					if(strtolower($this->model_name) == "exercise")
					{
						if(!$this->model_name::is_owner($_GET['id'], $_SESSION['user']->get_id()))
						{
							add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
							return call('pages', 'error');
						}
					}
					$view_to_show = 'views/' . strtolower($this->model_name) . '/read.php';
					if(!file_exists($view_to_show))
					{
						$view_to_show = 'views/shared/read.php';
					}
					$types = $model::get_types();
					$properties = $model->get_properties();
					require_once('views/shared/layout.php');
				}
			}
		}

		//get id from get, get the rest from post
		//get needs id, post needs model...
		//where does validation happen? view or controller? i say controller
		//well maybe... maybe both.
		public function update() {
			//must set id and the rest too. id is separate.
			//for users especially, i need to be more careful.
			//this is a basic one without permissions.

			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}

			//if there is post data...
			//todo: i need to check if the model actually exists on post, too!!!!
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken))
				{
					//probably i should do that isset stuff
					$model = new $this->model_name();
					$model->set_id($_GET['id']); //i should not trust that...
					$model->set_properties($_POST);
					if($model->is_valid())
					{
						$model->update(); //do i call validate here, or in the update function?
						//layout has already been created. can't add the alerts now
						//but redirecting anyway
						//could use ajax instead
						//for now, i'll stick to a redirect
						//add alerts to session or something
						//i want to redirect, but it doesn't seem like the php way...
						//$_SESSION['alerts'][] = 'Successfully updated!';
						add_alert('Successfully updated!', Alert_Type::SUCCESS);
						//session_write_close();
						return redirect($this->model_name, 'index');

						//http://getbootstrap.com/components/#alerts
						//exit properly first!
						//redirect header("Location: ...");
					}
					else
					{
						add_alert('Please try again.', Alert_Type::DANGER);
					}
				}
				else
				{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}

			$model = ($this->model_name)::get($_GET['id']);
			if($model == null)
			{
				return call('pages', 'error');
			}
			else
			{
				//require_once('views/shared/update.php');
				$view_to_show = 'views/' . strtolower($this->model_name) . '/update.php';
				if(!file_exists($view_to_show))
				{
					$view_to_show = 'views/shared/update.php';
				}
				$types = $model::get_types();
				$properties = $model->get_properties();
				require_once('views/shared/layout.php');
			}
			//i need to be better about the order of things.

		}

		//create and update are almost the same view... can i just put them into one? even if so, i need different controllers.

		public function delete() {
			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}
			$model = ($this->model_name)::get($_GET['id']);
		}
	}
?>
