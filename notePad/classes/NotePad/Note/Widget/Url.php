<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 
 * @author happydemon
 * @package notePad
 * @category Widget
 */
class NotePad_Widget_Url extends NotePad_Widget_Core {
	protected $_tpl = 'input';
	protected $_html5 = true;
	
	protected $_attr = array(
		'id' => array(),
		'class' => array(),
		'type' => 'url',
	);
}