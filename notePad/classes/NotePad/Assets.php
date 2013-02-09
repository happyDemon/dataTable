<?php defined('SYSPATH') OR die('No direct script access.');

class NotePad_Assets {
	use NotePad_Order;
	
	protected $_name ='';
	
	protected $_assets = array('js' => array(), 'css' => array());
	
	public function __construct($name) {
		$this->_name = $name;
	}
	
	/**
	 * Return a new Assets object
	 *
	 * @param   $name   string
	 * @return  Assets
	 */
	static public function factory($name)
	{
		static $instances = array();
		
		if(!isset($instances[$name]))
		{
			$instances[$name] = new NotePad_Assets($name);
		}
		
		return $instances[$name];
	}
	
	/**
	 * Adds assets to the appropriate type
	 *
	 * @param   string  $class
	 * @param   string  $type
	 * @param   string  $file
	 * @param   array   $options
	 * @return  Assets
	 */
	protected function _add($class, $type, $name, $file, array $options = array(), $position='end', $relative=null)
	{
		$this->_place_key($this->_assets[$type], $name, array('file' => $file, 'options' => $options), $position, $relative);
	
		return $this;
	}
	
	/**
	 * Add a js file
	 *
	 * @param   string  $file
	 * @param   array   $options
	 * @return  Assets
	 */
	public function js($name, $file, array $options = array(), $position='end', $relative=null)
	{
		return $this->_add('Asset', Assets::JAVASCRIPT, $name, $file, $options, $position, $relative);
	}
	
	/**
	 * Add a css file
	 *
	 * @param   string  $file
	 * @param   array   $options
	 * @return  Assets
	 */
	public function css($name, $file, array $options = array(), $position='end', $relative=null)
	{
		return $this->_add('Asset', Assets::STYLESHEET, $name, $file, $options, $position, $relative);
	}
	
	public function render() {
		$assets = Assets::factory($this->_name);
		
		foreach($this->_assets['css'] as $css) {
			$assets->css($css['file'], $css['options']);
		}
		
		foreach($this->_assets['js'] as $js) {
			$assets->js($js['file'], $js['options']);
		}
		
		return $assets->render();
	}
}