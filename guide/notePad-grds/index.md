#notePad.grds

notePad grids a simple helper that manages the setup of your [dataTables](http://www.datatables.net).

grids will generate your table's HTML, the javascript required to initialise the table and handle the ajax request to fill, order and paginate the table.

##Getting started
An example implementation has been provided as a controller which has 3 actions;

- The table action that renders the table's HTML (the example does not directly include the JS file)
- The js action that renders the javascript needed to setup and initialise the dataTable 
- The fill_table action that handles ajax requests which fills and sorts data for the dataTable.

All that's left to do is for you to extend the *NotePad_Table* controller, set up your first table in the *_setup_table* method that's abstract, initialise and register an ORM model and change this module's init.php to handle the routing.

	protected function _setup_table($table) {
	    $this->_model = ORM::factory('User');
	
	    $table->name('users');
	    $table->add_column('username', array('head' => 'Username'));
	    $table->add_column('email', array('head' => 'E-mail'));
	    $table->add_column('logins', array('head' => '# logins', 'class' => 'span1'));
	
	    return $table;
	}

##Caching

Since grids is a generator, you'll probably want to cache its results so it doesn't run every time a request is sent.

I decided to make use of Kohana's own caching system, pluging it in is a simple as opening the grids config file, specifying a cache lifetime and which cache config group grids should use.