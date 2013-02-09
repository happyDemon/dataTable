<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A field definition
 * 
 * @author happydemon
 * @package notePad
 * @category Form
 */
class NotePad_Field {
	/**
	 * Field-specific settings
	 * @var array
	 */
	protected $_settings = array(
				'render' => true,
				'editable' => true,
				'hidden' => false,
				'options' => false,
				'error' => false,
				'info' => false,
				'indexed' => null,
				'reindex' => null,
			);
	
	/**
	 * A widget instance
	 * @var NotePad_Widget_Core
	 */
	protected $_widget = null;
	
	/**
	 * Will contain form-specific config
	 * @var array
	 */
	protected $_cfg = null;
	
	/**
	 * Set up the field and widget
	 * 
	 * @param string $name
	 * @param array $definition
	 */
	public function __construct($name, $definition, $config, $form_name=false) {
		$widget = 'NotePad_Widget_'.ucfirst($definition['widget']);
		
		if(array_key_exists('render', $definition))
			$this->_settings['render'] = $definition['render'];
		
		if(array_key_exists('editable', $definition))
			$this->_setting['editable'] = $definition['editable'];
		
		if(array_key_exists('info', $definition))
			$this->_settings['info'] = $definition['info'];
		
		//set reindexing either by definition or by config
		if(array_key_exists('reindex', $definition))
			$this->_settings['reindex'] = $definition['reindex'];
		else
			$this->_settings['reindex'] = $config['data']['reindex'];
		
		if(array_key_exists('options', $definition) && count($definition['options']) > 0){
			$this->_settings['indexed'] = true;
			$this->_settings['options'] = $definition['options'];
		}
		
		if(!isset($definition['label']))
			$definition['label'] = ucfirst(Inflector::humanize($name));
		
		$this->_widget = new $widget($name, $definition, $form_name);
		$this->_cfg = $config;
		
		if($form_name != false)
			$this->_cfg['form_name'] = $form_name;
		
		if($this->_widget->options_required && $this->_settings['indexed'] != true)
			$this->_settings['options'] = true;
	}
	
	/**
	 * Specifically set options, info and error
	 * 
	 * @param string $key
	 * @param mixed $val
	 */
	public function __set($key, $val) {		
		if($key == 'options') {
			if(($this->_settings['indexed'] == null && $this->_settings['reindex'] == false) || $this->_settings['reindex'] == true) {
				$this->_widget->{$key} = $val;
				$this->_settings['indexed'] = true;
			}
		}
		else if(in_array($key, array('info', 'error')))
			$this->_settings[$key] = $val;
	}
	
	/**
	 * Retrieve certain properties
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key) {
		if(array_key_exists($key, $this->_settings))
			return $this->_settings[$key];
		else if($key == "widget")
			return str_replace('NotePad_Widget_', '',get_class($this->_widget));
	}
	
	/**
	 * Check if the field needs file functionality
	 * @return boolean
	 */
	public function is_file() {
		return (is_a($this->_widget, 'NotePad_Form_Widget_File'));
	}
	
	/**
	 * Tells the field's widget to hide on the form
	 */
	public function hide() {
		$this->_widget->type = 'hidden';
		$this->_widget->label = '';
		$this->_settings['hidden'] = true;
	}
	
	/**
	 * Tell the field's widget to show itself on the form
	 */
	public function show() {
		$this->_widget->hidden = false;
		$this->_settings['hidden'] = false;
	}
	
	/**
	 * Render the widget
	 * 
	 * @param mixed $input overwrite the widget's value
	 * @param string $option_tpl Provide a template to parse the options provided.
	 * @return array
	 */
	public function render($input=null, $option_tpl=null) {
		if($this->_settings['render'] == false)
			return '';
		
		if($this->_settings['editable'] == false)
			$this->_widget->disabled = true;
		
		$tpl = array('before' => '', 'after' => '');
		$dir = 'notePad/form/field/';
		
		$tpl['before'] = $dir . $this->_cfg['view']['field']['open'];
		
		$tpl['after'] = ($this->_cfg['view']['field']['close'] != false) ? $dir . $this->_cfg['view']['field']['close'] : '';
		
		//get widget info
		$widget = $this->_widget->output($input);
		
		//if twitter bootstrap is used
		if($this->_cfg['widget']['twitter_bootstrap'] == true) {
			
			$widget['data']['bootstrap_error'] = ($this->_settings['error'] != false);
			
			if($this->_cfg['view']['field']['open'] == 'open')
				$tpl['before'] = $dir . 'bootstrap_open';
			
			if($this->_cfg['view']['field']['close'] == 'close')
				$tpl['after'] = $dir . 'bootstrap_close';
		}

		//if there's an error
		if($this->_settings['error'] != false) {
			$error = View::factory('notePad/form/error/'.$this->_cfg['view']['error']['inline'], array('content' => $this->_settings['error']));
			$widget['data']['error'] = $error;
		}
		
		//translate if needed
		if($this->_cfg['widget']['translate']['labels'] == true)
			$widget['data']['label'] = NotePad::instance()->localise('label', $widget['data']['name'], $widget['data']['label'], $this->_cfg['form_name']);
		if($this->_cfg['widget']['translate']['options'] == true)
			$widget['data']['options'] = notePad::instance()->localise('options', $widget['data']['name'], $widget['data']['options'], $this->_cfg['form_name']);
		
		//field aliassing
		if($this->_cfg['widget']['alias'] == true || $this->is_file())
			$widget['data']['name'] = strtolower($this->_cfg['form_name']) . '[' . $widget['data']['name'] . ']';
		
		//if there's a help msg
		if($this->_settings['info'] != false) {
			$info_msg = NotePad::instance()->localise('info', $widget['data']['name'], $this->_settings['info'], $this->_cfg['form_name']);
			$info = View::factory('notePad/form/'.$this->_cfg['view']['info'], array('content' => $info_msg));
			$widget['data']['info'] = $info;
		}
		
		//parse the widget
		$field = View::factory(($option_tpl == null) ? $widget['file'] : $option_tpl, $widget['data']);
		
		if($this->hidden != true) {
			//open the widget row
			$prepend = View::factory($tpl['before'], $widget['data'])->render();
			
			//Close the widget row
			$append = (!empty($tpl['after'])) ? View::factory($tpl['after'], $widget['data'])->render() : '';
			
			return $prepend . $field . $append;
		}
			return $field;
	}
}