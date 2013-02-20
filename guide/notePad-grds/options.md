#Table options

dataTable itself offers numerous options for you to specify your table and make it your own.
grids does not support all of them, it does support quite a few though:

config key		| description
----------------|-------------
display_length	| How many records to display (defaults to 10)
display_start	| Which page to start display from (starts at 1)
state_save		| Save the state of the table in a cookie (false by default)
state_duration	| How long the state will be saved in seconds (2 hours default)
pagination		| Pagination plugin to use (bootstrap|fullnumbers|twobutton)
class_render	| Set the table's container dimensions (defaults to offset3 span6)
class_table		| Table specific classes (defaults to 'table table-hover table-striped')
sDom			| How the dataTable will be rendered (see [dataTables](http://datatables.net/usage/options#sDom))

All these settings can be modified with the cfg method that the table class offers, it's a simple setter:

	$dataTable->cfg('display_length', 25);


##Defining a table's name
By default every table get *notePad-dataTable* as its name. Make sure you change this every time you create a new table.

The name is used as an identifier for:
- caching 
- the HTML table (uses Kohana's Inflector's underscore, this would mean the id of the table would become notePad_dataTable)

You can easily change it by calling

	$dataTable->name('some-new-table-name');


##Row selection

If you want to support row selection you could turn on the ' *checkbox* ' config option, this will make the first column of every row have a checkbox, making it easy for you to handle multi-row selections.

	$dataTable->cfg('checkbox', true);

These checkbox elements get an array as name: record_id[ *id* ] if you'd rather submit them as a form, they also get a data-id property that's easier to read out in javascript.

	var selected_rows = $('table#notePad-dataTable tbody').find('input.record-select:checked');
	
	//get the specific id's
	$.each(selected_rows, function(){
		var row_id = $(this).data('id');
	});
