dataTable
=========

Kohana 3.3 wrapper for generating standardised jQuery [DataTables](http://datatables.net/) that's prepared to be integrated with twitter bootstrap layouts.

This module generates:
 - your table's HTML
 - a javascript file that initialises your table
 - handles the dataTable request to load/filter/sort your table.

The table's HTML and javascript get cached after being generated (its lifetime can be set in the config file).

Install
=========

### Git
```bash
git clone git://github.com/morgan/kohana-paginate.git modules/paginate
git clone git://github.com/morgan/kohana-datatables.git modules/datatables
git clone git://github.com/happyDemon/dataTable.git modules/dataTable
```

### Composer
Add paginate and datatables to your composer.json repositories
```javascript
"repositories": [
        {
            "type": "package",
            "package": {
                "name": "morgan/kohana-paginate",
                "version": "0.3.0",
                "type" : "kohana-module",
                "source": {
                    "url": "https://github.com/morgan/kohana-paginate",
                    "type": "git"
                }
            }
        },
        {
            "type": "package",
            "package": {
                "name": "morgan/kohana-datatables",
                "version": "0.2.0",
                "type" : "kohana-module",
                "source": {
                    "url": "https://github.com/morgan/kohana-datatables",
                    "type": "git"
                }
            }
        }
    ]
```

Add "happydemon/datatable" to your dependencies
```javascript
{
  "require": {
  		"php":">=5.4",
		"composer/installers": "*",
		"happydemon/datatable": "0.3"
	}
}
```

Example
=========
A controller was added to show how you could implement this module's functionality, all that's left to do is setup the table's columns.

```php
protected function _setup_table($table) {
	$this->_model = ORM::factory('User');
    
	$table->name('users');
    $table->add_column('username', array('head' => 'Username'));
    $table->add_column('email', array('head' => 'E-mail'));
    $table->add_column('logins', array('head' => '# logins', 'class' => 'span1'));
    
    return $table;
}
```

[![Gittip Badge](http://img.shields.io/gittip/happyDemon.svg)](https://www.gittip.com/happyDemon/ "Gittip donations")


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/happyDemon/datatable/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

