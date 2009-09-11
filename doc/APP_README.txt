Creating a Wax Application:
-----------------------------------------------------------

	BEFORE YOU CONTINUE:
	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	!! before reading this, you should read DCI.txt !!
	!! for a basic understanding of the paradigm    !!
	!! that lies beneath the wax framework          !!
	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

1 - Create the application directory
2 - Create the resource block
3 - Write the initialization script
4 - Loading External Blocks
5 - Adding functionality

-----------------------------------------------------------

1 - Create the application directory

To create a wax application, you need to first create a 
directory specifically for the application.  The location
of the directory is irrelevant, but it must be accessible
from the web and must have access to the wax initialization
script (wax_init.php).

Example structure:

/var
	/www
		/htdocs
			/wax
			/yourapplication
			
Where /var/www/htdocs/wax is the wax installation directory
containing the framework initialization script and 
/var/www/htdocs/yourapplication is where you're going to be
building your application

-----------------------------------------------------------
2 - Create the resource block

Your application is probably going to need to use images,
css files, scripts, etc.  All of these resources can be 
grouped together in a 'wax block'.  To create a wax block,
just create a new folder with .wax appended to the
directory name.  In this case, since we're creating the 
resource block, we create the directory:

	/var/www/htdocs/yourapplication/resources.wax
	
Within the resources block, we need to actually put in the
resources that the application will use.  Wax blocks have
a specific file structure designed for quick resolution
of paths to resources.  The directories allowed in a wax
block are as follows:

	/resources.wax
		/css		CSS Stylesheets
		/images		Images (wax prefers PNG files)
		/js			Scripts and Script Libraries
		/lib		PHP Includes (for external libraries, class declarations, etc.) -- see BLOCKS.txt
		/roles		DCI Roles -- see ROLES.txt for more information 
		/tests		Test cases -- see TESTS.txt
		/views		View files -- see VIEWS.txt
		
So go ahead and create the directories that you need-
in this case we'll keep it simple and say that you'll 
need css, js, and images.  Now our wax application looks
like this:

/var
	/www
		/html
			/yourapplication
				/resources.wax
					/images
					/css
					/js
			/wax
			
See RESOURCES.txt for information on how to access these
resources from view files and controller code.

-----------------------------------------------------------
3 - Write the initialization script

Wax application initialization scripts are extremely
simple, as most of the grunt work is offloaded into the wax
init script.  The first line of the init script should
(nearly) always be:

	require_once "../path/to/wax_init.php";
	
This will cause the wax framework to initialize before
any of your application code runs.  You need this because
the next step involves loading up external libraries.

If your application uses a database or some other data
source, you would do the initialization for that after
initializing wax.  For example, if we had a wax app 
that used a MySQL database, this would be the init file:

<?php
	require_once "../wax/wax_init.php";
	
	mysql_connect("localhost","root","password101");
	mysql_select_db("database");
	
	... (content from step 4) ...
?>

-----------------------------------------------------------
4 - Loading external blocks (libraries)

Loading a WaxBlock is a very simple process.  Loading a wax
block does several different things, such as including 
PHP library files, pre-loading paths to resources, and
defining a series of roles for objects to implement.

To load a block, simply type:

	Wax::LoadBlock("blockname");
	
Wax automatically looks through the blockpath as defined
in it's configuration and attempts to load the block with
the specified name.  Let's say we're creating an MVC based
web application that uses a database and runs on an iPhone.

The MVC block is autoloaded thanks to the $autoload array
located in /wax/core/config.php

In order to get data in and out of a database, we'll need 
to render some HTML forms, so we need to use the 'forms'
block.  To actually make the changes, we need to use
the 'database' block.

Finally, we want this to run on an iPhone, so we want 
to include wax's iPhone CSS and utility files, located
in the 'iphone' block.

So in this case, there are 3 libraries that we need to load
	- database
	- iphone
	- forms
	
so we append them to the init file, creating a final 
initialization file that looks like this:

	<?php
		require_once "../wax/wax_init.php";

		mysql_connect("localhost","root","password101");
		mysql_select_db("database");

		// load the blocks
		Wax::LoadBlock("iphone");
		Wax::LoadBlock("database");
		Wax::LoadBlock("forms");

		// load the local resource block
		Wax::LoadBlock("resources");
	?>
	
Now that we have the initialization script, we can
start creating the application.

-----------------------------------------------------------
5 - Adding functionality to your application

The standard way of adding some sort of functionality to 
a wax application is to contain the functionality within
a block and then direct the HTTP request into it.  This
process is what we'll cover in this section.


