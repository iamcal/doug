doug - a simple bug tracker
===========================

Doug is a PHP & MySQL bug tracker for small teams. This first release is just a straight copy of our internal repo, with 
passwords removed - it needs a little work to get it to run standalone.

It uses our own flavor of <a href="https://github.com/exflickr/GodAuth">God Auth</a>, but replacing that with your own auth 
layer is pretty simple.

Installation instructions will follow shortly...


## Installation

1. Check out the code from Git, somewhere into your webroot.
2. Copy `include/config.php.example` to `include/config.php` and modify the setting inside.
3. Create a folder called `templates_c` and make sure the web server can write to it.
4. Something about the database...
