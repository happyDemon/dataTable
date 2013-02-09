<?php
class Pad {
	
	/**
	 * Name of config set to load.
	 * Gets converted to a config object.
	 * @var string|object
	 */
	protected $_cfg = 'default';
	
	/**
	 * Name of the model class we're expecting
	 * @var string
	 */
	protected $_model = null;
	
	/**
	 * Contains a list of validation rules
	 * @var array
	 */
	protected $_rules = array();
	
	/**
	 * Contains a list of callbacks
	 *
	 * Same syntax is used as in Kohana_Validate
	 *
	 * @var array
	 */
	protected $_callbacks = array();
	
	///////default properties
	/**
	 * Contains the data driver object
	 * @var object
	 */
	protected $_data = null;
	
	/**
	 * Can contain a list of errors to show on top of the form
	 * @var array
	 */
	protected $_errors = false;
	
	/**
	 * A list of all the fields and their definition
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * An index containing relational field names
	 * @var array
	 */
	protected $_relation_index = array();
	
	/**
	 * Load status of the Pad (fresh|loaded)
	 * 
	 * @var string
	 */
	protected $_status = 'fresh';
	
	public function __construct($model=null, $load_relations=true){
		//setup config
		$cfg = Kohana::$config->load('notePad.'.$this->_cfg);
		
		if($cfg == null)
			throw new NotePad_Exception('Couldn\'t load your ":set" notePad config set.', array(':set' => $this->_cfg));
		else
			$this->_cfg = $cfg->as_array();
		
		//set up the data driver
		$driver_name = 'NotePad_Data_'.$this->_cfg['general']['data_driver'];
		
		if($model == null && $this->_model == null)
			throw new NotePad_Exception('Can\'t load the data driver, no model specified.');
		else if($model != null && !is_a($model, $this->_model))
			throw new NotePad_Exception('Incorrect model specified.');
		else if($model != null && $model instanceof $this->_model)
			$driver = new $driver_name($model);
		else 
			$driver = new $driver_name($this->_model);
		
		$this->_data = $driver;
	}
	
	/**
	 * Add a validation rule to the set.
	 *
	 * @param string $field Name of the field this rule applies to
	 * @param mixed $rule Validation function
	 * @param array $param Validation parameters the function will use
	 */
	public function add_rule($field, $rule, $param=null) {
		if(!array_key_exists($field, $this->_fields))
			throw new NotePad_Exception('Can\'t add a rule for field ":field", which doesn\'t exist', array(':field' => $field));

		$spec = array();
	
		$spec[0] = $rule;
	
		if(is_array($param))
			$spec[1] = $param;
	
		$this->_rules[$field][] = $spec;
	}
	
	///////// Set up submodules
	public function note($load_relations=true) {
		//Data driver setup for the form
		
		//load bare note
		
		//setup note
		if(!method_exists($this, '_note'))
			throw new NotePad_Exception('Can\'t load the requested ":note" note.', array(':note' => $name));
		//return note
	}
	
	protected function _note($note) {
		//set form name
		
		//set config file to load
		
		//set button placement (top|bottom|both)
		
		//set form attributes
		
		//limit fields (include|exclude)
		
		//limit relations (load|include|exclude)
		
		//set up field sets
		
		//manage buttons
		
		//enable protection
		
		//return note
		return $note;
	}
}