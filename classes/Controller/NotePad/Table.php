<?php defined('SYSPATH') or die('No direct script access.');

/**
 * An example on how you could implement this module
 * 
 * @author happydemon
 * @package happyDemon/notePad
 */
abstract class Controller_NotePad_Table extends Controller {	
	/**
	 * A Table instance
	 * @var Table
	 */
	public $table = null;
	
	/**
	 * A model instance to retrieve column row values from
	 * @var ORM
	 */
	protected $_model = null;
	
	
	public function before() {
		parent::before();
		
		$this->table = $this->_setup_table(new Table);
	}
	
	/**
	 * Set up the dataTable definition for this controller.
	 * 
	 * @see Table
	 * @param Table $table
	 * @return Table A fully configured dataTable definition
	 */
	abstract protected function _setup_table($table);
	
	/**
	 * Render the provided table
	 */
	public function action_table() {
		//asset file that still needs to be included would be {base_url}/grid/initTable.js
		$this->response->body($this->table->template());
	}
	/**
	 * Create a javascript that stores the dataTable setup json
	 */
	public function action_js(){		
		$this->response->headers('Content-Type','application/x-javascript');
		$this->response->body($this->table->js(Route::url('notePad.grds.fill')));
	}
	
	/**
	 * Handle a request sent by the dataTable plugin
	 * @throws HTTP_Exception_500
	 */
	public function action_fill_table() {
		if (DataTables::is_request())
		{
			$data = $this->table->request();
			//set a model and render
			$data->model($this->_model)->render($this->response);
		}
		else
			throw new HTTP_Exception_500();
	}

} // End notePad Table
