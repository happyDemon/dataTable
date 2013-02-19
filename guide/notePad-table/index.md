#notePad.grds

notePad grids a simple helper that manages the setup of your [dataTables](http://www.datatables.net).

grids will generate your table's HTML, the javascript required to initialise the table and handle the ajax request to fill, order and paginate the table.

##Caching

Since grids is a generator, you'll probably want to cache its results so it doesn't run every time a request is sent.

I decided to make use of Kohana's own caching system, pluging it in is a simple as opening the grids config file, specifying a cache lifetime and which cache config group grids should use.