<?php 
defined('SYSPATH') or die('No direct script access.');
/**
 * notePad form base
 * 
 * easily create a form manually or based on a model.
 * 
 * @author happydemon
 * @package notePad
 */
class Note {
	
	/**
	 * The name of the form
	 * @var string
	 */
	protected $_form_name = '';
	
	/**
	 * Where to place our buttons (top|bottom|both)
	 * @var string
	 */
	protected $_button_placement = 'both';
	
	/**
	 * Name of the model class we're expecting
	 * @var string 
	 */
	protected $_fixed_model = null;
	
	/**
	 * To activate provide a name for a field you'd want to put a token in.
	 * @var mixed
	 */
	protected $_CSRF = false;
	
	/**
	 * Activate honeypot protection
	 * @var boolean
	 */
	protected $_honeypot = false;
	
	/**
	 * Form attributes
	 * @var array
	 */
	protected $_attributes = array(
		'method' => 'POST',
		'action' => '',
		'id' => '',
		'class' => 'form-horizontal',
	);
	
	/**
	 * Provide the field names that you want to display in the form
	 * @var array
	 */
	protected $_include_fields = array();
	
	/**
	 * Provide the field names that you don't want to display in the form
	 * @var array
	 */
	protected $_exclude_fields = array();
	
	/**
	 * Wheter or not to load relations from the data driver.
	 * @var bool
	 */
	protected $_load_relations = true;
	
	/**
	 * An index containing relational field names
	 * @var array
	 */
	protected $_relation_index = array();
	
	/**
	 * Which relation aliasses you want to display in the form
	 * @var array
	 */
	protected $_include_relations = array();
	
	/**
	 * Which relation aliasses you don't want to display in the form
	 * @var array
	 */
	protected $_exclude_relations = array();
	
	/**
	 * Contains the data driver object
	 * @var object
	 */
	protected $_data = null;
	
	/**
	 * Contains a list of validation rules
	 * @var array
	 */
	protected $_rules = array();
	
	/**
	 * A list of all the fields and their definition
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * A list with fieldsets 
	 * @var array
	 */
	protected $_field_sets = array();
	
	/**
	 * A list of form buttons
	 * @var array
	 */
	protected $_buttons = array(
		'save' => array(
			'value' => 'save',
			'attr' => array(
				'type' => 'submit',
			 	'class' => 'btn-primary'
			 )
		),
	);
	
	/**
	 * Contains the notePad configuration
	 * @var Config
	 */
	protected $_cfg = 'default';
	
	/**
	 * Relation map, specify which columns we need to load from
	 * a relation when parsing the widget.
	 *
	 * 'alias' => array(where,columns,template, limit);
	 * @var array
	 */
	protected $_relations = array();
	
	/**
	 * Contains data the form should have submitted
	 * @var array
	 */
	protected $_input = null;
	
	/**
	 * A list of submitted file data
	 * @var array
	 */
	protected $_files = false;
	
	/**
	 * Can contain a list of errors to show on top of the form
	 * @var array
	 */
	protected $_errors = false;
	
	/**
	 * Contains a list of callbacks 
	 * 
	 * Option: gets a datadriver instance as parameter
	 * File: gets a datadriver instance and the corresponding $_FILES property
	 * Save: gets a datadriver instance
	 * 
	 * @var array
	 */
	protected $_callbacks = array('option' => array(), 'file' => array(), 'save' => array());
	
	/**
	 * Initiate a new note
	 * 
	 * @param string $note The name of the note
	 * @param string $cfg Name of the config set this note will use
	 * @return Note
	 */
	public static function factory($note, $cfg=null) {
		$class = 'Note_' . ucfirst($note);
		
		return new $class($cfg);
	}
	
	/**
	 * This function is called right after the config is loaded
	 * 
	 * Here you can set up your form fields, fieldsets and validation rules before the data driver is called,
	 * field definitions won't be overwritten by the data driver
	 * and validation rules will be merged.
	 * 
	 * @return NULL
	 */
	protected function _setup() {
		return null;
	}
	
	/**
	 * Loads the required notePad config file and sets up the form.
	 * @param string $config
	 * @throws NotePad_Exception
	 */
	public function __construct($config=null) {	
		$cfg = Kohana::$config->load('notePad');
		
		$config = ($config == null) ? $this->_cfg : $config;
		
		if($config != 'engine' && array_key_exists($config, $cfg)) 
			$this->_cfg = $cfg->get($config);
		else
			throw new NotePad_Exception('Couldn\'t load the config file ":file" properly, non-existing name supplied', array(':file' => $config));
	
		if($this->_form_name == '')
			$this->_form_name = str_replace('Note_', '', get_class($this));
		
		//bring the relation map up to code
		if(count($this->_relations) > 0) {
			foreach($this->_relations as $alias => $options) {
				if(!array_key_exists('where', $options))
					$this->_relations[$alias]['where'] = null;
				
				if(!array_key_exists('columns', $options))
					$this->_relations[$alias]['columns'] = null;
			}
		}
		
		//run the setup function
		$this->_setup();
		
		//update the form's fields
		if(count($this->_fields) > 0) {
			$fields = $this->_index_fields(array_keys($this->_fields), array($this->_include_fields, $this->_exclude_fields));
			
			if(count($fields) > 0) {
				foreach($fields as $name)
					unset($this->_fields[$name]);
			}
		}
	}
	
	/**
	 * Add a form button
	 * @param string $name Name of the button (unique)
	 * @param string $value Value of the button (text displayed)
	 * @param string $type Type of button
	 * @param array $attributes List of html attributes
	 */
	public function add_button($name, $value, $type='submit', $attributes=array()) {
		if(!array_key_exists($name, $this->_buttons))
			$this->_buttons[$name] = array('type' => $type, 'value' => $value, 'attr' => $attributes);
	}
	
	/**
	 * Defines how options will be loaded for this relation
	 * 
	 * @param string $name
	 * @param array $fields
	 * @param string $tpl
	 * @param array $where_clauses
	 */
	public function define_relation($name, $fields, $tpl=null, $where_clauses=null, $limit=null) {
		if(is_string($fields))
			$fields = array($fields);
		
		if(!array_key_exists($name, $this->_relations)) {			
			$this->_relations[$name] = array(
				'fields' => $fields,
				'template' => $tpl,
				'where' => $where_clauses,
				'limit' => $limit	
			);
		}
	}
	
	/**
	 * Add a validation rule to the set.
	 * 
	 * @param string $field Name of the field this rule applies to
	 * @param mixed $rule Validation function
	 * @param array $param Validation parameters the function will use
	 */
	public function add_rule($field, $rule, $param=null) {
		$spec = array();
		
		$spec[0] = $rule;
		
		if(is_array($param))
			$spec[1] = $param;
		
		$this->_rules[$field][] = $spec;
	}
	
	/**
	 * Create a new field set.
	 * 
	 * You're able to possition it anywhere in your field set list;
	 * at the start or the end of the fieldset list,
	 * even before or after and existing fieldset.
	 * 
	 * @param string $name Name of the fieldset
	 * @param array $fields A list containing field names
	 * @param string $legend Legend title optional
	 * @param string $possition (end|start|before|after)
	 * @param string $relative Possition subject fieldset name (in before|after)
	 * @throws NotePad_Exception
	 */
	public function add_field_set($name, array $param, $possition='end', $relative=null) {
		if(array_key_exists($name, $this->_field_sets))
			Throw new NotePad_Exception('A fieldset called ":name" cannot be redeclared.', array(':name' => $name));
		
		$fields = $param['fields'];
		$legend = (isset($param['legend'])) ? $param['legend'] : '';
		
		$spec = array('fields' => $fields, 'legend' => ($legend == 'null') ? 'field_collection' : $legend);
		
		$spec['description'] = (isset($param['description'])) ? $param['description'] : false;
		$spec['attributes'] = (isset($param['attr'])) ? $param['attr'] : array();
		
		switch($possition) {
			default:
			case'end': 
				$this->_field_sets[$name] = $spec;
			break;
			case 'start':
				$this->_field_sets = array_merge(array($name => $spec), $this->_field_sets);
			break;
			case 'before':
			case 'after':
				$insertion = array_search($relative, array_keys($this->_field_sets));
				
				if($possition == 'after')
					$insertion++;
				
				$before = array_slice($this->_field_sets, 0, $insertion, true);
				$after  = array_slice($this->_field_sets, $insertion, null, true);
				
				$this->_field_sets = array_merge($before, array($name => $spec), $after);
			break;
		}
		
	}
	
	/**
	 * Remove a field set from the list
	 * 
	 * @param string $name name of the fieldset you want to delete
	 */
	public function remove_field_set($name) {
		if(array_key_exists($name, $this->_field_sets))
			unset($this->_field_sets[$name]);
	}
	
	/**
	 * You're able to possition the field set anywhere in your field set list;
	 * at the start or the end of the field set list,
	 * even before or after and existing field set.
	 * 
	 * @param string $name name of the fieldset you want to move
	 * @param string $possition (end|start|before|after)
	 * @param string $relative Possition subject fieldset name (in before|after)
	 */
	public function move_field_set($name, $possition='end', $relative=null) {
		if(array_key_exists($name, $this->_field_sets)) {
			$set = $this->_field_sets[$name];
			unset($this->_field_sets[$name]);
			$this->add_field_set($name, $set['fields'], $set['legend'], $possition, $relative);
		}
	}
	
	/**
	 * Retrieve a field to set properties
	 * 
	 * @param string $name Name of the field
	 * @return NotePad_Field|NULL returns the corresponding field
	 */
	public function field($name) {
		if(array_key_exists($name, $this->_fields))
			return $this->_field_sets[$name];
		
		return null;
	}
	
	/**
	 * Add a field to the form.
	 * 
	 * Field spec options:
	 *> 		array(
	 *> 			'widget' => "name of the widget to apply to this field",
	 *> 			'label' => "if undefined the field's name will be used",
	 *> 			'name' => "name of the form field",
	 *> 			'value' => "optional",
	 *> 			'info' => "Information about this form optional",
	 *> 			'options' => "offering limited choice tot he user optional",
	 *> 			'editable' => "if the field is editable by the user, default true",
	 *> 			'render' => "if the field should be rendered default true",
	 *>				'reindex' => "if the options can be reindexed, see config",
	 *> 		);
	 * 
	 * Anything but these predefined keys will be treated as attributes.
	 * 
	 * You're able to possition the field anywhere in your field list;
	 * at the start or the end of the field list,
	 * even before or after and existing field.
	 * 
	 * @param string $name Name of the field
	 * @param array $spec The field specification
	 * @param string $possition (end|start|before|after)
	 * @param string $relative Possition subject fieldset name (in before|after)
	 * @throws NotePad_Exception
	 */
	public function add_field($name, $spec, $possition='end', $relative=null) {
		if(array_key_exists($name, $this->_fields))
			Throw new NotePad_Exception('A field called ":name" cannot be redeclared.', array(':name' => $name));
		
		$field = new NotePad_Field($name, $spec, $this->_cfg, $this->_form_name);
		
		//if it's a file, register it
		if($field->is_file()) {
			$this->_files[] = $name;
		}
		
		switch($possition) {
			default:
			case'end':
				$this->_fields[$name] = $field;
				break;
			case 'start':
				$this->_fields = array_merge(array($name => $field), $this->_fields);
				break;
			case 'before':
			case 'after':
				$insertion = array_search($relative, array_keys($this->_fields));
		
				if($possition == 'after')
					$insertion++;
		
				$before = array_slice($this->_fields, 0, $insertion, true);
				$after  = array_slice($this->_fields, $insertion, null, true);
		
				$this->_fields = array_merge($before, array($name => $field), $after);
				break;
		}
	}
	
	/**
	 * Remove a field from the field list
	 * @param string $name
	 */
	public function remove_field($name) {
		if(array_key_exists($name, $this->_fields))
			unset($this->_fields['name']);
	}
	
	/**
	 * Move a field in your field list
	 * 
	 * You're able to possition the field anywhere in your field list;
	 * at the start or the end of the field list,
	 * even before or after and existing field.
	 * 
	 * @param string $name Name of the field
	 * @param string $possition (end|start|before|after)
	 * @param string $relative Possition subject fieldset name (in before|after)
	 */
	public function move_field($name, $possition='end', $relative=null) {
		if(array_key_exists($name, $this->_fields)) {
			$field = $this->_fields[$name];
			unset($this->_fields[$name]);
			$this->add_field($name, $field, $possition, $relative);
		}
	}
	
	/**
	 * Disable a form field
	 * @param string $name
	 */
	public function disable($name) {
		$this->_fields[$name]->editable = false;
	}
	
	/**
	 * Load a model we can use for overloading columns & validation rules.
	 * 
	 * @param object $model a model object will be loaded into the data driver
	 * @param boolean $load_relations wheter to load the model's relations
	 * @throws NotePad_Data_Exception
	 */
	public function model($model=null, $load_relations=true) {	
		//load the data driver if it's the first time
		if($this->_data == null) {
			$driver_name = 'NotePad_Data_' . $this->_cfg['data']['driver'];
			$this->_data = new $driver_name();
		}
		
		if($this->_fixed_model != null && $model == null)
			$model = $this->_data->initiate($this->_fixed_model);
		else if(is_string($model))
			$model = $this->_data->initiate($model);
		else if($this->_fixed_model != null && !is_a($model, $this->_fixed_model))
			Throw new NotePad_Data_Exception('The model you\'re trying to load isn\'t a ":model_name" model', array(':model_name' => $this->_fixed_model));
		
		//load the model
		$this->_data->load($model, $load_relations);		
		
		//Remove data field definitions if already defined
		$fields = array(
			'data' => array_diff($this->_data->field_names(), array_keys($this->_fields)),
			'delete' => array_keys($this->_fields)
		);
		
		//limit relational fields if needed
		if($load_relations == true && $this->_load_relations == true)
		{
			$this->_relation_index = $this->_data->relation_names();
			$fields = $this->_index_fields($fields['delete'], array($this->_include_relations, $this->_exclude_relations), $fields['data']);
		}
		
		//limit the fields		
		$fields = $this->_index_fields($fields['delete'], array($this->_include_fields, $this->_exclude_fields), $fields['data']);
			
		//Parse the field definitions into fields
		if(count($fields['data']) > 0) {
			foreach($fields['data'] as $field) {
				$this->add_field($field, $this->_data->field($field, true));
			}
		}
		
		//update the form's fields		
		if(count($fields['delete']) > 0) {
			foreach($fields['delete'] as $name)
				unset($this->_fields[$name]);
		}
	}
	
	/**
	 * Index fields
	 * 
	 * return which fields to delete
	 * if data fields are supplied it also returns which data fields to parse
	 * 
	 * @param array $fields List containing field names to remove
	 * @param array $change List of fields to keep (or remove from data fields) (include|exclude)
	 * @param array $data List of data fields to keep
	 * @return array
	 */
	protected function _index_fields($fields, array $change, $data=null) {
		if(count($change[0]) > 0)
			return $this->_limit_fields('include', $fields, $change[0], $data);
		else if(count($change[1]) > 0)
			return $this->_limit_fields('exclude', $fields, $change[1], $data);
		else if($data == null)
			return array();
		else {
			return array(
				'data' => $data,
				'delete' => array(),		
			);
		}
	}
	
	/**
	 * Limit fields
	 * 
	 * @param string $action Name of action to perform (include|exclude)
	 * @param array $fields Field names to manipûlate
	 * @param array $change Field names
	 * @param array $data_fields Data field names to manipulate
	 * @return array
	 */
	protected function _limit_fields($action, $fields, $change, $data_fields=null) {
		switch($action) {
			case 'include':
				if($data_fields != null)
					$data_fields = array_intersect($data_fields, $change);
					
				$fields = array_diff($fields, $change);
			break;
			case 'exclude':
				if($data_fields != null)
					$data_fields = array_diff($data_fields, $change);
					
				$fields = array_intersect($fields, $change);
			break;
			default:
				return null;
		}
		
		if($data_fields == null)
			return $fields;
		else {
			return array(
				'data' => $data_fields,
				'delete' => $fields
			);
		}
	}
	
	/**
	 * Parse & render field widgets.
	 * 
	 * Loads options with a call back is specified 
	 * (create an in-class method called _{$fieldname}_options)
	 * 
	 * if it's a relational field and no callback is specified,
	 * it will load the options from the data driver.
	 * 
	 * returns the generated html.
	 * 
	 * @param array $fields a list of field names
	 * @return string HTML output
	 */
	protected function _parse_fields($fields) {
		$content = '';
		
		if(count($fields) > 0) {
			foreach($fields as $name) {		
				$option_tpl = null;
				
				//Check callbacks for loading options for specified fields
				if($this->_fields[$name]->options == true) 
					$option_tpl = $this->_load_options($name);
				
				$input = ($this->_input) != null && isset($this->_input[$name]) ? $this->_input[$name] : '';
				$content .= $this->_fields[$name]->render($input, $option_tpl);
			}
		}
		else 
			$content = '';
		
		return $content;
	}
	
	/**
	 * Load the options of a form element if a callback is available
	 * 
	 * @param string $name Field name
	 * @return NULL|string if a template was specified for the options return it
	 */
	protected function _load_options($name) {
		$options = array();
		
		if($this->_fields[$name]->reindex == false && $this->_fields[$name]->indexed != null)
			NotePad::instance()->localiseable($name);
		else if($this->_fields[$name]->reindex != false || $this->_fields[$name]->indexed == null) {
			//load options through an optionals handler
			$handler = $this->_callback_exists('option', $name);
			$handler_name = '_options_'.$name;
		
			//Check for the default option handler defined in this note
			if(method_exists($this, $handler_name)) {
				$options = call_user_func(array($this, $handler_name));
			}
			else if($handler == true) { //otherwise perform a registered callback
				$options = $this->_callback('option', $name, array($this->_data));
			}
			else if(in_array($name, $this->_relation_index)) { //if it's a relation get the options from the datadriver
				if(array_key_exists($name, $this->_relations)) {
					$options = $this->_data->load_options($name, $this->_relations[$name]['where'], $this->_relations[$name]['columns'], $this->_relations[$name]['limit']);
					
					return $this->_relations[$name]['template'];
				}
				else 
					$options = $this->_data->load_options($name, null, null, $this->_cfg['data']['relation_cap']);
			} 
			else if($this->_fields[$name]->indexed != null) 
				NotePad::instance()->localiseable($name);
			
			if(count($options) > 0) {
				$this->_fields[$name]->options = $options;
				$this->_fields[$name]->indexed = true;
			}
		}
		
		return null;
	}
	
	/**
	 * Register data as input,
	 * this will mark the form as submitted.
	 * 
	 * @param array $data
	 * @throws NotePad_Exception
	 */
	public function input($data) {
		if(!array($data))
			Throw new NotePad_Exception('Input data should be in array format');
		
		if($this->_cfg['widget']['alias'] == true && array_key_exists(strtolower($this->_form_name), $data))
			$this->_input = $data[strtolower($this->_form_name)];
		else
			$this->_input = $data;
	}	
	
	/**
	 * Render the entire form.
	 * 
	 * @param string $url location for sending the form data
	 * @return string Form's HTML
	 */
	public function render($url=false) {
		$content = '';
		$buttons = $this->_buttons;
		
		if($this->_cfg['widget']['translate']['buttons'] == true) {
			foreach($buttons as $btn_name => $btn_value)
				$buttons[$btn_name]['value'] = Notepad::instance()->localise('button', $btn_name, $btn_value['value'], $this->_form_name);
		}
		
		if($this->_errors != false && $this->_cfg['widget']['error']['display'] == 'global')
			$content .= View::factory('notePad/form/'.$this->_cfg['view']['form_errors'], array('errors' => $this->_errors));
		
		if($this->_data != null)
			$this->_buttons['save']['value'] = ($this->_data->model_loaded()) ? 'Update' : 'Create';
		
		$this->_attributes['id'] = 'form-'.strtolower($this->_form_name);
		$this->_attributes['action'] = (!$url) ? Request::$initial->url() : URL::site($url);
		
		if($this->_files != false) 
			$this->_attributes['enctype'] = 'multipart/form-data';
		
		$content .= View::factory('notePad/form/'.$this->_cfg['view']['form_open'], array('action' => $this->_attributes['action'], 'attr' => $this->_attributes));
		
		if($this->_button_placement != 'bottom')
			$content .= View::factory('notePad/form/buttons/'.$this->_cfg['view']['buttons']['top'], array('buttons' => $buttons));
		
		if($this->_CSRF != false) {
			$this->add_field($this->_CSRF, array('widget' => 'text', 'name' => $this->_CSRF, 'value' => Security::token()));
			$this->_fields[$this->_CSRF]->hide();
		}
		
		if($this->_honeypot == true) {
			$honey = ($this->_cfg['widget']['honeypot'] == null) ? strtolower($this->_form_name).'_email_check' : $this->_cfg['widgets']['honeypot'];
			$this->add_field($honey, array('widget' => 'text', 'name' => $honey, 'value' => ''));
			$this->_fields[$honey]->hide();
		}
		
		$field_names = array_keys($this->_fields);
		
		//First parse the fieldsets if they're defined
		if(count($this->_field_sets) > 0) {	
			foreach($this->_field_sets as $fs_name => $sf_options) {
				//remove field names used in this fieldset
				$fs_fields = array_intersect($sf_options['fields'], $field_names);
				$field_names = array_diff($field_names, $fs_fields);
				
				$data = array(
					'legend' => (!isset($sf_options['legend'])) ? ucfirst(Inflector::humanize($fs_name)) : $sf_options['legend'],
					'fields' => $this->_parse_fields($fs_fields),
					'description' => (isset($sf_options['description'])) ? $sf_options['description'] : false,
					'attributes' => (isset($sf_options['attr'])) ? $sf_options['attr'] : array(),
				);
				
				//translate the fieldset legend & description if needed
				if($this->_cfg['widget']['translate']['fieldsets'] == true) {
					$data['legend'] = NotePad::instance()->localise('legend', $fs_name, $data['legend'], $this->_form_name);
					if($data['description'] != false)
						$data['description'] = NotePad::instance()->localise('description', $fs_name, $data['description'], $this->_form_name);
				}
				
				$content .= View::factory('notePad/form/fieldset', $data);	
			}
			
			if(count($field_names) > 0) 
				$content .= View::factory('notePad/form/fieldset', array('legend' => 'Misc', 'fields' => $this->_parse_fields($field_names), 'attributes' => array(), 'description' => false));
			
		} 
		else {
			//process all the fields located in $field_names
			$content .= $this->_parse_fields($field_names);
		}
		
		if($this->_button_placement != 'top')
			$content .= View::factory('notePad/form/buttons/'.$this->_cfg['view']['buttons']['bottom'], array('buttons' => $buttons));
		
		$content .= View::factory('notePad/form/'.$this->_cfg['view']['form_close']);
		
		return $content;
	}
	
	/**
	 * Check if the form has been submitted
	 * @return boolean
	 */
	public function is_submitted() {
		return ($this->_input != null);
	}
	
	/**
	 * Save the data the form has
	 * 
	 * @throws NotePad_Exception
	 * @return boolean
	 */
	public function save() {
		if(!$this->is_submitted())
			throw new NotePad_Exception('You can\'t save a form that hasn\'t been submitted.');
		
		$files_errors = array();
		
		if($_FILES) {
			//validate files
			$files = NotePad_Validation::factory($_FILES[strtolower($this->_form_name)]);
			
			foreach($this->_files as $file)
				$files->rule($file, array('Upload', 'valid'));
			
			//perform file save actions
			if(!$files->check()) {
				$files_errors = Arr::merge($files_errors, $files->errors('notePad/'.strtolower($this->_form_name)));
			}
		}		
		
		//load validation rules
		$rules = $this->_data->load_validation(array_keys($this->_fields));
		$this->_rules = array_merge($rules, $this->_rules);
		
		$validation = NotePad_Validation::factory($this->_input);
		
		foreach($this->_rules as $name => $callbacks)
			$validation->rules($name, $callbacks);
		
		if($this->_CSRF != false)
			$validation->rule($this->_CSRF, array('Security', 'check'));
		if($this->_honeypot == true) {
			$honeypot = ($this->_cfg['widget']['honeypot'] == null) ? strtolower($this->_form_name).'_email_check' : $this->_cfg['widgets']['honeypot'];
			$validation->rule($honeypot, 'NotePad::valid_honeypot');
		}
		
		if($validation->check()) {
			$values = $validation->data();
			
			//save the needed fields
			$file_error = false;
			$files_parsed = array();
			
			if(count($files_errors) > 0) {
				//run registered callbacks
				foreach ($files as $name => $options) {
					if($this->_callback_exists('file', $name)) //check for a field specific file handler
						$callback = $this->_callback('file', $name, array($name, $this->_data, $options, 'save'));
					else //fallback to a general file handler
						$callback = call_user_func_array(array($this, 'save_file'), array($name, $this->_data, $options));
					
					if(is_string($callback)) {
						$file_error = $callback;
						break;
					}
					else $files_parsed[] = $name;
				}
			}
			
			//try to save the form
			if($file_error == false) {
				$this->_data->save($values);
			
				$callbacks = $this->_callback_exists('save');
				if($callbacks == true)
					return $this->_callback('save', array($this->_data, $this->_input));
				
				return true;
			}
			else {
				//delete previously saved files, if any
				if(count($files_parsed) > 0) {
					foreach($files_parsed as $file) {
						if($this->_callback_exists('file', $file)) //check for a field specific file handler
							$this->_callback('file', $file, array($file, null, $files[$file], 'delete'));
						else //fallback to a general file handler
							call_user_func_array(array($this, 'handle_file'), array($file, null, $files[$file], 'delete'));
					}
				}
				
				//set errors
				$this->_errors = $file_error;
				return false;
			}
			
		}
		else {			
			$error_file = 'notePad/'.strtolower($this->_form_name);
			$errors = Arr::merge($files_errors, $validation->errors($error_file));
			
			if($this->_cfg['widget']['error']['display'] == 'inline') {
				//set the errors to the fields
				foreach($errors as $field => $value) {
					$this->_fields[$field]->error = $value;
				}
			}

			$this->_errors = $errors;
			
			return false;
		}
	}	
	
	/**
	 * Retrieve information on the file from the specified field.
	 * 
	 * @param string $field_name File field name
	 * @return array
	 */
	protected function _retrieve_file($field_name) {
		$form_name = strtolower($this->_form_name);
		
		return array(
				'name'     => $_FILES[$form_name]['name'][$field_name],
				'type'     => $_FILES[$form_name]['type'][$field_name],
				'tmp_name' => $_FILES[$form_name]['tmp_name'][$field_name],
				'error'    => $_FILES[$form_name]['error'][$field_name],
				'size'     => $_FILES[$form_name]['size'][$field_name],
		);
	}
	
	/**
	 * Register callbacks
	 * @param string $type The type of callback we're registering it to
	 * @param string $name The name of the callback
	 * @param mixed $callback Handled the same way Validation does its rules
	 * @return NULL|boolean
	 */
	public function register($type, $name, $callback=null) {
		if(in_array($type, array('options', 'file'))) {
			if($callback == null)
				return null;
				
			$this->_callbacks[$type][$name] = $callback;
		}
		else if($type == 'save')
			$this->_callbacks['save'][0] = $name;
		else
			return false;
	
		return true;
	}
	
	/**
	 * Check if a callback exists
	 *
	 * @param string $type What kind of callback we're checking
	 * @param string $name The name of the callback
	 * @return boolean
	 */
	protected function _callback_exists($type, $name=null) {
		if(!in_array($type, array('options', 'file')) || !array_key_exists($name, $this->_callbacks[$type]))
			return false;
		else if ($type == 'save')
			return (count($this->_callbacks['save']) > 0);
	
		return true;
	}
	
	/**
	 * Perform a callback
	 *
	 * @param string $type The type of callback
	 * @param string $name The name of the callback
	 * @param array $param An array with parameters for the callback
	 * @return NULL|mixed
	 */
	protected function _callback($type, $name=null, $param=null) {
		if(!in_array($type, array('options', 'file', 'save')))
			return null;
		if($type != 'save' && array_key_exists($name, $this->_callbacks[$name]))
			return null;
	
		if($type == 'save') {
			$callback = $this->_callbacks['save'][0];
			$param = $name;
		}
		else
			$callback = $this->_callbacks[$type][$name];
	
		if(is_array($callback) || ! is_string($callback)) {
			//lambda or array callback
			return call_user_func_array($callback, $param);
		}
		elseif (strpos($callback, '::') === FALSE)
		{
			// Use a function call
			$function = new ReflectionFunction($callback);
	
			// Call $function($param, ...) with Reflection
			return $function->invokeArgs($param);
		}
		else
		{
			// Split the class and method of the rule
			list($class, $method) = explode('::', $callback, 2);
	
			// Use a static method call
			$method = new ReflectionMethod($class, $method);
	
			// Call $Class::$method($param, ...) with Reflection
			return $method->invokeArgs($param);
		}
	}
	
	public function error() {
		return $this->_errors;
	}
}