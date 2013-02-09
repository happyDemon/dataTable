<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Widget driver base
 * 
 * @author happydemon
 * @package notePad
 * @category Widget
 */
abstract class NotePad_Widget_Core {
	
	/**
	 * Load options or not
	 * @var boolean
	 */
	public $options_required = false;
	
	/**
	 * If the widget is a HTML5 widget
	 * (provide a fallback for other browsers if so)
	 * @var bool
	 */
	protected $_html5 = false;
	
	/**
	 * Declare the visibility of your form element
	 * @var boolean
	 */
	public $hidden = false;
	
	/**
	 * The name of the view file
	 * @var  string  
	 */
	protected $_tpl = '';
	
	/**
	 * @var  array  HTML attributes
	 */
	protected $_attr = array(
		'id' => array(),
		'class' => array()
	);
	
	/**
	 * Define a list of assets to load when 
	 * the widget is rendered.
	 * 
	 * @var  array  Grouped assets [css,js]
	 */
	protected $_assets = null;	
	
	/**
	 * Return a string containing code you want the widget to inject the DOM with.
	 * 
	 * @return NULL|string
	 */
	protected function _DOM_inject(){
		return null;
	}
	
	// end of adjustable functionality
	
	/**
	 * @var  array  Widget specific settings
	 */
	protected $_settings = array(
				'form_name' => false,
				'label' => '',
				'name' => '',
				'options' => array(),
				'value' => '',
			);
	
	/**
	 * Everything but settings or hidden will be stored in attributes
	 * 
	 */
	public function __set($key, $value) {
		if(array_key_exists($key, $this->_settings))
			$this->_settings[$key] = $value;
		else if($key == 'type' && $value == 'hidden'){
			$this->_attr['type'] = 'hidden';
			$this->_tpl = 'input';
		}
		else if (in_array($key, array('id', 'class')))
			$this->_attr[$key][] = $value;
		else
			$this->_attr[$key] = $value;
	}
	
	public function __get($key) {
		if(array_key_exists($key, $this->_settings))
			return $this->_settings[$key];
	}
	
	/**
	 * @var  object  View object container
	 */
	protected $_view = null;
	
	/**
	 * Set up the widget's settings and assign HTML attributes
	 * 
	 * @param string $name HTML element name
	 * @param array $definition Widget definition
	 */
	public function __construct($name, array $definition, $form_name=false) {
		//set HTML attributes if specified
		if(array_key_exists('attr', $definition)) {
			$this->_attr = array_merge($definition['attr'], $this->_attr);
			unset($definition['attr']);
		}
		
		$definition['form_name'] = $form_name;
		
		//populate settings 
		foreach($definition as $setting => $value) {
			if($setting != 'attr' && isset($this->_settings[$setting]))
				$this->_settings[$setting] = $value;
		}
		
		$this->_settings['name'] = $name;
		$this->_settings['id'] = 'input-'.strtolower($form_name).'-'.$name;
	}
	
	/**
	 * Render the widget.
	 * 
	 * If input is provided we'll overwrite the widget's value.
	 * 
	 * @param mixed $input data for the widget's value
	 * @return array [html, files, dom]
	 */
	public function output($input=null) {
		if($input != null)
			$this->_settings['value'] = $input;
		
		if($this->hidden == true)
			$this->_attr['hidden'] = true;
		else if(isset($this->_attr['hidden']))
			unset($this->_attr['hidden']);
		
		if($this->options_required && !is_array($this->_settings['value'])) {
			$this->_settings['value'] = array($this->_settings['value']);
		}
		
		//make sure we add the html attributes without overwriting
		$data = array_merge(array('attr' => $this->_attr), $this->_settings);
		
		//Set and easy to find
		$data['attr']['id'][] = $this->_settings['id'];
		
		//default error & info
		$data['error'] = false;
		$data['info'] = false;
		
		//Combine id's and classes if needed
		foreach (array('id', 'class') as $key) {
			if(count($data['attr'][$key]) > 0)
				$data['attr'][$key] = join($data['attr'][$key], ' ');
			else 
				unset($data['attr'][$key]);
		}

		$view = NotePad::instance();
		
		//Handle assets and DOM
		if(count($this->_assets['css']) > 0)
			$view->add_asset('css', $this->_settings['name'], $this->_assets['css']);
		if(count($this->_assets['css']) > 0)
			$view->add_asset('js', $this->_settings['name'], $this->_assets['js']);
		if($this->_html5 == true)
			$view->html5($this->_settings['id'], $this->_attr['type']);
		
		$dom = $this->_DOM_inject();
		
		if($dom != null)
			$view->apped_DOM($this->_DOM_inject());
		
		//output the widget info
		return array(
			'file' => 'notePad/form/widgets/'.$this->_tpl,
			'data' => $data,
		);
	}
}