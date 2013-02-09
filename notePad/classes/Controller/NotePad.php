<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @author happydemon
 * @package happyDemon/notePad
 */
abstract class Controller_NotePad extends Controller {

	abstract protected $_model;
	protected $_tpl = null;
	public $register = null;
	
	public function before() {
		parent::before();
		
		if(!$this->request->is_ajax())
		{
			$this->_tpl = View::factory('notePad/index');
		}
		
		$this->register = NotePad_Register;
	}
	
	public function after() {
		parent::after();
		
		if($this->_tpl != null)
			$this->response->body($this->_tpl->render());
	}
	
	//show a page with the dataTable
	abstract public function action_index();
	
	//Retrieve data from a record to fill a form
	abstract public function action_read();
	
	//Save data sent from a form (create|update)
	abstract public function action_save();
	
	/**
	 * Handles a dataTable load request
	 * @throws HTTP_Exception_500
	 */
	public function action_records() {
		if (DataTables::is_request())
		{
			$orm = ORM::factory($this->_model);
	
			$paginate = Paginate::factory($orm);
	
			$datatables = DataTables::factory($paginate)->execute();
	
			foreach ($datatables->result() as $record)
			{
				$datatables->add_row($this->register->output($record));
			}
	
			$datatables->render($this->response);
		}
		else
			throw new HTTP_Exception_500();
	}
	
	/**
	 * Delete a record
	 */
	public function action_delete(){
		$values = $this->request->post();
	
		$orm = ORM::factory($this->_model);
		
		$pk = $orm->primary_key();
		$record = $orm->where($pk, '=', $values[$pk])
					  ->find();
		
		$item->delete();
	
		$this->response->headers('Content-Type','application/json');
		$this->response->body(json_encode(array('status' => 'deleted')));
	}

} // End notePad
