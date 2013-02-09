<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class
 * 
 * @package notePad
 * @author happydemon
 */
class NotePad {	
	/**
	 * Contains a list of asset files for javascript and css.
	 * 
	 * @var array
	 */
	protected $_assets = array('js' => array(), 'css' => array());
	
	/**
	 * Contains a string you can inject in your DOM
	 * @var string
	 */
	protected $_DOM = '';
	
	/**
	 * Contains a map of field => HTML5 input element
	 * so you could provide a fallback
	 * @var array
	 */
	protected $_html5 = array();
	
	/**
	 * Contains field names of elements that have options we can translate
	 * @var array
	 */
	protected $_translateable_options = array();
	
	/**
	 * Get the instance of this class
	 * @return NotePad
	 */
	static public function instance() {
		static $inst = null;
		
		if($inst == null)
			$inst = new NotePad();
		
		return $inst;
	}
	
	/**
	 * Set a list of options to localisable.
	 * 
	 * @param string $option Name of the form field that contains these options
	 */
	public function localiseable($option) {
		if(!in_array($option, $this->_translateable_options))
			$this->_translateable_options[] = $option;
	}
	
	/**
	 * retrieve a list of localisable options.
	 *
	 * @return array
	 */
	public function localiseables() {
		return $this->_translateable_options;
	}
	
	/**
	 * Register a form field as HTML5 to offer fallback support
	 * 
	 * @param string $id Form field name
	 * @param strin $type HTML5 input element type
	 */
	public function html5($id, $type) {
		//register field to offer a fallback if needed
		$this->_html5[$id] = $type;
	}
	
	/**
	 * Register an asset
	 * 
	 * @param string $type
	 * @param string $name
	 * @param string $files
	 */
	public function add_asset($type, $name, $files) {
		if(is_string($files))
			$files = array($files);
		
		if(in_array($type, array('js', 'css'))) {
			if(isset($this->_assets[$type][$name]))
				$this->_assets[$type][$name] = array_merge($this->_assets[$type][$name], $files);
			else
				$this->_assets[$type][$name] = $files;
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Retrieve assets by type
	 * 
	 * @param string $type Type of asset (js|css)
	 * @return multitype:
	 */
	public function assets($type) {
		if(!in_array($type, array('js', 'css')))
			return null;
		
		$files = array();
		
		if(count($this->_assets[$type]) > 0) {
			foreach($this->_assets[$type] as $assets)
				$files = array_merge($files, $assets);
		}
		
		return $files;
	}
	
	/**
	 * Append code you want to include in your DOM
	 * 
	 * @param string $dom
	 */
	public function append_DOM($dom) {
		if(!is_string($dom))
			return null;
		
		$this->_DOM .= $dom;
		return true;
	}
	
	/**
	 * Retrieve the doe you want to include in your DOM
	 * @return string
	 */
	public function DOM() {
		return $this->_DOM;
	}
	
	/**
	 * Translates info, labels, options, legends and descriptions if possible, otherwise return the original value.
	 * Takes the same approach as Validation::errors();
	 *
	 * Keys are mapped to a message file called notePad/{form name} for the default translation
	 *
	 * @param string $type The type of element we're translating
	 * @param string $name	The name of the element
	 * @param string $value The default value if there's no translation available
	 * @param string $form_name The name of the form this element's being translated for
	 * @return string|NULL Translated string
	 */
	public function localise($type, $name, $value, $form_name) {
		$file = 'notePad/'.strtolower($form_name);
	
		switch($type) {
			case 'label':
				$path = 'fields.'.$name.'.label';
				$msg = Kohana::message($file, $path, $value);
				
				return __($msg);
			break;
			case 'info':
				$path = 'fields.'.$name.'.info';
				$msg = Kohana::message($file, $path, $value);
			
				return __($msg);
				break;
			case 'options':
				if(!in_array($name, $this->_translateable_options))
					return $value;
				if(count($value) > 0) {
					$options = array();
						
					foreach($value as $key => $txt) {
						$path = 'fields.'.$name.'.options.'.$key;
						$msg = Kohana::message($file, $path, $txt);
						$options[$key] = __($msg);
					}
						
					return $options;
				}
				
				return array();
			break;
			case 'legend':
				$path = 'fieldsets.'.$name.'.legend';
				$msg = Kohana::message($file, $path, $value);
					
				return __($msg);
			break;
			case 'description':
				$path = 'fieldsets.'.$name.'.description';
				$msg = Kohana::message($file, $path, $value);
				
				return __($msg);
			break;
			case 'button':
				$path = 'buttons.'.$name;
				$msg = Kohana::message($file, $path, $value);
				
				return __($msg);
				break;
			default:
				return null;
		}
				
	}
	
	/**
	 * Validates a honeypot field
	 * 
	 * @param string $value Expecting an empty string
	 */
	public static function valid_honeypot($value) {
		return (empty($value));
	}
}