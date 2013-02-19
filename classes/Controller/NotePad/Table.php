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
		//asset file would be {base_url}/NotePad/Table/js
		$this->response->body($this->table->template());
	}
	/**
	 * Create a javascript that stores the dataTable setup json
	 */
	public function action_js(){		
		$this->response->headers('Content-Type','application/x-javascript');
		$this->response->body($this->table->js());
	}
	
	/**
	 * Handle a request sent by the dataTable plugin
	 * @throws HTTP_Exception_500
	 */
	public function fill_table() {
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
