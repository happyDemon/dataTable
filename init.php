<?php defined('SYSPATH') OR die('No direct script access.');

Route::set('notePad.grds.js', 'grid/initTable.js')
	->defaults(array(
		'directory'  => 'NotePad',
		'controller' => 'Table',
		'action'     => 'js',
));

Route::set('notePad.grds.fill', 'grid/fill')
	->defaults(array(
		'directory'  => 'NotePad',
		'controller' => 'Table',
		'action'     => 'fill_table',
));

Route::set('notePad.grds.index', 'grid')
	->defaults(array(
		'directory'  => 'NotePad',
		'controller' => 'Table',
		'action'     => 'table',
));