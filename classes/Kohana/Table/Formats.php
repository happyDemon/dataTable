<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Javascript defined functions for rendering dataTable columns.
 * 
 * @author 		happydemon
 * @package 	happyDemon/notePad
 * @category	grds
 */
class Kohana_Table_Formats {
	
	/**
	 * parses column value into an image.
	 * 
	 * You can optionally assign width and height as param array(w,h)
	 * or even add a class (w,h,c) || (false,false,c)
	 * 
	 * @param array $param
	 * @return string
	 */
	public static function image($param=null) {
		if(is_array($param))
		{
			 return self::_view('image', $param);
		}
		
		return 'return: \'<img src="\'+data+\'" />\';';
	}
	
	/**
	 * parses a boolean column value into the supplied icons
	 * @param mixed $param
	 * @return string
	 */
	public static function icon($param=null) {
		return self::_view('icon', $param);
	}
	
	/**
	 * Parses a column value into a checkbox
	 * 
	 * Param is used to name the checkbox element and is gets the class record-{param}
	 * 
	 * @param mixed $param input name and class-suffix
	 * @return string
	 */
	public static function checkbox($param=null) {
		if($param == null)
		{
			return "return '<div class=\"text-center\"><input type=\"checkbox\" data-id=\"'+data+'\" name=\"record_id['+data+']\" class=\"record-select\" /></div>';";
		}
		
		return self::_view('checkbox', $param);
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
			
			$return .= ' <button data-id="\'+data+\'" class="btn'.$class.' btn-xs btn-action-'.$id.'"><i class="icon-'.$def['icon'].'"></i></button>';
		}
		
		return $return."';\n";
	}
	
	/**
	 * Render the format's v
	 * 
	 * @param string $file
	 * @param array $param
	 */
	protected static function _view($file, $param) {
		return View::factory('notePad/formats/'.$file, array('param' => $param))->render();
	}
}