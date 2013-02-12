dataTable
=========

Kohana 3.3 wrapper for generating standardised jQuery DataTables (http://datatables.net/)

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
		"composer/installers": "*",
		"happydemon/datatable": "*"
	}
}
```

Example
=========
A controller was added to show how you could implement this module's functionality, all that's left to do is setup the table's columns.

```php
protected function _setup_table($table) {
    $table->add_column('username', array('head' => 'Username'));
    $table->add_column('email', array('head' => 'E-mail'));
    $table->add_column('logins', array('head' => '# logins', 'class' => 'span1'));
    return $table;
}
```
