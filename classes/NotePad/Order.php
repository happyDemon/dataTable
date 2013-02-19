<?php defined('SYSPATH') or die('No direct script access.');

trait NotePad_Order {
	/**
	 * Re-order an array based on values
	 *
	 * @param array $array Array source where we'll be moving the value 
	 * @param string $value The value you'd like to move
	 * @param string $possition (end|start|before|after)
	 * @param string $relative Which value to possition this value (before|after)
	 */
	protected function _place_value($array, $value, $position='end', $relative=null) {
		
		if(in_array($value, $array))
			unset($array[array_search($value,$array)]);
		
		if($position == 'start')
		{
			$insertion_point = 0;
		}
		else if($position == 'end')
		{
			$insertion_point = count($array);
		}
		else if(in_array($position, array('before', 'after'))) 
		{
			$insertion_point = array_search($relative, $array);
				
			if($possition == 'after')
				$insertion_point++;
		}
		else
			return false;

		$before = array_slice($array, 0, $insertion_point, true);
		$after  = array_slice($array, $insertion_point, null, true);
		
		$array = array_merge($before, array($value), $after);
		return $array;			
		
	}
	
	/**
	 * Re-order an array based on keys
	 *
	 * @param array $array Array source where we'll be moving the value
	 * @param string $key The key you'd like to move
	 * @param mixed $value The value of the key you're adding
	 * @param string $possition (end|start|before|after)
	 * @param string $relative Which key to possition this key (before|after)
	 */
	protected function _place_key(&$array, $key, $value=null, $position='end', $relative=null) {
	
		if(array_key_exists($key, $array))
		{
			$value = $array[$key];
			unset($array[$key]);
		}
	
		$insertion_point = count($array);
		
		if ($position == 'start') {
			$insertion_point = 0;
		} else if ($position == 'before' && $relative) {
			$insertion_point = array_search($relative, array_keys($array));
		} else if ($position == 'after' && $relative) {
			$insertion_point = array_search($relative, array_keys($array))+1;
		}

		$before = array_slice($array, 0, $insertion_point, true);
		$after  = array_slice($array, $insertion_point, null, true);

		$array = array_merge($before, array($key => $value), $after);
	
		return true;
	}
}
