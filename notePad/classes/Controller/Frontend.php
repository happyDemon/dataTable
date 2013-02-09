<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Abstract base controller for frontend controllers.
 * 
 * @package    Modular Gaming
 * @category   Controller
 * @author     Modular Gaming Team
 * @copyright  (c) 2012-2013 Modular Gaming Team
 * @license    BSD http://modulargaming.com/license
 */
class Controller_Frontend extends Controller {

	protected $auth; // Auth instance.
	protected $user; // Current logged in user.

	protected $protected = FALSE; // Require user to be logged in.
	protected $view = null; // View to render.

	protected $layout = 'layout';

	public function before()
	{		
		$assets = Kohana::$config->load('assets.notePad');
		$this->_register_assets($assets);
		$this->view = View::factory('notePad/main');
	}
	
	protected function _register_assets($config)
	{
		foreach ($config as $type => $files)
		{
			if (count($files) > 0)
			{
				foreach($files as $desc)
				{
					$assets = NotePad_Assets::factory('notePad');
					$position = (isset($desc['location'])) ? $desc['location'] : 'end';
					$relative = (isset($desc['location'])) ? $desc['relative'] : null;
					$options = (isset($desc['options'])) ? $desc['options'] : array();
					
					call_user_func(array($assets, $type), $desc['name'], $desc['file'], $options, $position, $relative);
				}
			}
		}
	}

	public function after()
	{
		if ($this->view != null)
		{
			$this->view->assets = NotePad_Assets::factory('notePad')->render();
			$this->response->body($this->view);
		}
	}
	
	public function action_index() {
		$table = new Table();
		$table->add_column('username', array('head' => 'Username', 'class' => 'span2'));
		$table->add_column('email', array('head' => 'E-mail', 'class' => 'span3'));
		$table->model(ORM::factory('User'), 'id');
		
		$this->view->content = $table->request()->render();
	}

} // End Frontend
