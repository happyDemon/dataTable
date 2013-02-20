#Managing your columns

##Adding a column
The simplest way to add a column is by calling the add_column method and passing its name as the first argument:
 
	$dataTable->add_column('username');
 
If nothing else is defined all the column's row will be filled with the username property from the provided model, in the table's head 'Username' will be displayed.

There are a lot of [options](options) you can configure by passing them as an array as the second parameter:
 
	$dataTable->add_column('email', array('head' => 'E-mail', class="span2")); 

The third and fourth parameters define if the column is sortable or searchable, these are booleans:
 
	//this would make this column sortable, but not searchable
	$dataTable->add_column('logins', array(), true, false);
	 

##Ordering columns

There are 2 ways you can order your columns:

You can do this when you first add your column, the last 2 parameters let you define where you want this column to go, the last parameter would contain the name of the column you'd want this one to be positioned **before** or **after**:
 
	$dataTable->add_column('last_login', array('head' => 'Last login'), true, false, 'after', 'email');

You can position your columns at the **end**(default) or **start** of the column list, or position them **before** or **after** an existing column.

If you'd like to move your column at a later point you can call *move_column*
 
	$dataTable->move_column('username', 'after', 'email');
 