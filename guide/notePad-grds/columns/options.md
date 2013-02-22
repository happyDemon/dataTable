#Column options

When you define a column you're able to pass along specific *options* to customise them further.

option key		| description
----------------|-------------
head			| Value at the top of the table presenting this column
visible			| Is the html shown on-screen?
class			| Column's head class (can be used for spacing the columns), defaults to span2
default			| Default content if the column has no value
width			| Exact width the column should be (20px, 12em, ..)
sort			| How to start sorting the column
format			| Specify how the column's values should be parsed by mRender by callback
param			| Parameters for formatting the column's value
retrieve		| Specify how to retrieve the model's value for this column by callback

##Format callbacks
These are callbacks that are used by dataTable's mRender, in other words these are javascript callbacks. They render the value of a column for every row (by defining it in javascript we save a little bandwith when sending requests).

grids ships with a few default format callbacks which are defined in *NotePad_Table_Formats*:

###Image
This parses provided URLs into images:

	$options = array(
		'format' => 'image',
	);
	$dataTable->add_column('image', $options);

optionally you can also define the images' dimensions :

	$options = array(
		'format' => 'image',
		'param' => array(50, 50)
	);
	$dataTable->add_column('available', $options);

###Icons
These are useful for boolean fields, instead of just showing 1 or 0 you can define in the option's param which icons represent which.

	$options = array(
		'format' => 'icon',
		'param' => array('eye-open', 'lock')
	);
	$dataTable->add_column('available', $options);

if the available column's value is 1 we'll show the icon called 'icon-eye-open', otherwise we'll show 'icon-lock'.

###Checkbox

	$options = array(
		'format' => 'checkbox',
		'param' => 'element-name'
	);
	$dataTable->add_column('selectable', $options);

This column's value will be rendered as a checkbox, the checkbox's name will be 'element-name' and the value will be the same as the column's value.

###Providing your own mRender callbacks
You'll probably want to be able to provide your own callbacks, that's easy to do, although it's a little bit like 'Inception': you have to provide a PHP callback that describes your mRender callback.

This callback always requires 1 parameter to be defined (it's passed the option's param key) and needs to return the javascript that [mRender](http://datatables.net/usage/columns#mRender) will use for formatting your column cell. 

	$options = array(
		'format' => function ($param=null) {
			return "return '<input type=\"checkbox\" name=\"someField[]\" value=\"'+data+'\" />';";
		}
	);
	$dataTable->add_column('someField', $options);

The format function is called by [call_user_func](http://php.net/manual/en/function.call-user-func.php) 
and get's wrapped in mRender's standard javascript:

	function(data, type, full) { '.$format.' }


##Retrieve callbacks
By default we'll just try to get a column's value by calling the ORM's get method, but if you want to specify your way of getting a column's value you can do so by specifying a callback:

	$options = array(
		'retrieve' => function($record) { return $record->id; }
	);
	$dataTable->add_column('id', $options);

If you'd rather get the property of a relation of the loaded record you could do so like this:

	$options = array(
		'retrieve' => 'avatar.url()'
	);
	$dataTable->add_column('avatar', $options);

Every time a request is made to fill the dataTable we'll add a column named avatar that will be filled by calling the URL method of an avatar relation.
In short, it will be mapped like this: 

	$record->avatar->url();

This does not work for a has_many or has_many through relation, than you're better of providing a custom callback.
