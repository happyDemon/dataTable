<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Javascript defined functions for rendering dataTable columns.
 * 
 * @author happydemon
 * @package happyDemon/notePad
 */
class NotePad_Table_Formats {
	
	/**
	 * parses column value into an image.
	 * 
	 * You can optionally assign width and height as param array(w,h).
	 * 
	 * @param mixed $param
	 * @return string
	 */
	public static function image($param=null) {
		if(is_array($param))
		{
			 return 'return: \'<img src="\'+data+\'" width="'.$param[0].'" heigth="'.$param[1].'" />\';';
		}
		
		return 'return: \'<img src="\'+data+\'" />\';';
	}
	
	/**
	 * parses a boolean column value into the supplied icons
	 * @param mixed $param
	 * @return string
	 */
	public static function icon($param=null) {
		return "
			if(data == 1)
				return '<i class=\"icon-{$param[0]}\"></i>';
			else
				return '<i class=\"icon-{$param[1]}\"></i>';";
	}
	
	/**
	 * Parse the option buttons for at the end of the dataTable
	 * 
	 * @param mixed $param
	 * @return string
	 */
	public static function options($param=null) {
		$return = "return '";

		foreach($param as $id => $def) {
			$class = (isset($def['class'])) ? ' btn-'.$def['class'] : '';
			
			$return .= '<button data-id="\'+data+\'" class="btn'.$class.' btn-action-'.$id.'"><i class="icon-'.$def['icon'].'"></i></button> ';
		}
		
		return $return."';";
	}
}