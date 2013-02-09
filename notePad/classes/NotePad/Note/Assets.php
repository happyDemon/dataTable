<?php
class NotePad_Assets {
	protected $_js = array();
	protected $_css = array();
	
	protected $_DOM = '';
	
	public function register($type, $file) {
		if(in_array($type, array('js', 'css'))) {
			switch ($type) {
				case 'js':
					$this->_js[] = $file;
					break;
				case 'css':
					$this->_css[] = $file;
					break;
			}
		}
		else
			throw new Kohana_Exception('Unable to register the asset ":file" as a :type type.', array(':file' => $file, ':type' => $type));
	}
	
	public function append($dom) {
		$this->_DOM .= $dom;
	}
	
	public function prepend($dom) {
		$this->_DOM = $dom . $this->_DOM;
	}
	
	public function assets($type) {
		if(in_array($type, array('js', 'css'))) {
			switch ($type) {
				case 'js':
					return $this->_js;
					break;
				case 'css':
					return $this->_css;
					break;
			}
		}
		else
			throw new Kohana_Exception('No assets with a :type as type exist.', array(':type' => $type));
	}
	
	public function DOM() {
		return $this->_DOM;
	}
}