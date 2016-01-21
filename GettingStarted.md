# Introduction #

Programming languages do "Hello World...", frameworks do "Build a Blog in 15 minutes".  Below is a walkthrough of how to use Wax and the Dynamic Data Model (DDM) to build a very basic blog (though it might take longer than 15 minutes)

If you haven't read anything about DCI, then I recommend you check out this article: http://code.google.com/p/php-coredci/wiki/UsingCoreDCI about how Wax's runtime network is structured.  You don't actually need it to make it through this tutorial, but it explains the underlying object system that Wax uses.

# What You'll Need #
  * A webserver that support url rewriting
  * PHP 5.2 (earlier may work, I don't know)
  * MySQL database (and a way to import the schema)
  * [Wax 0.11](http://waxphp.googlecode.com/files/waxphp-0.11.tar.gz)
  * [Source Code and Schema for This Tutorial](http://waxphp.googlecode.com/files/BlogTutorial-0.11.tar.gz)

# The Dynamic Data Model #

Before we get into the actual code, a little bit of explanation is in order.  To make development easier and more flexible, many Wax applications make use of the DDM instead of a regular SQL database.  The DDM is composed of a series of Wax libraries and a specially designed database schema.  The resulting system allows for easy modification of the system's data model from the browser (instead of modifying SQL tables).  More is explained in the details below.

# Setting Up Wax #

The first step to getting started with Wax is to get the source distribution.  Extract and move the folder to the location where you want to create your application.

**NOTE**: Due to the way blocks are currently implemented, the .wax extension is required at the end of all blocks (including applications).  For example, if you wanted to build a blog at http://server.com/blog, you would actually need to name it http://server.com/blog.wax

Your directory structure should now look something like this (sorry for the terrible highlighting...just ignore it):
```
`- /DOCUMENT_ROOT/blog.wax
        |- .htaccess
	`- waxphp-0.11
		|- blocks (directories vary)
		`- core
			 |- include/
			 |   |- CoreDCI/
			 |   |   |- Context.php
			 |   |   |- DCIException.php
			 |   |   `- DCIObject.php
			 |   |- config.php
			 |   |- exceptions.php
			 |   |- lib.php
			 |   `- wax.php
			 |- lib/
			 |   |- ArraySurrogate.php
			 |   |- HTTPArrays.php
			 |   |- Router.php
			 |   |- WaxBlock.php
			 |   |- WaxObject.php
			 |   `- iWaxDataSource.php
			 |- managers/
			 |   |- BlockManager.php
			 |   `- DataSourceManager.php
			 `- wax_init.php
```

Where DOCUMENT\_ROOT is the root directory of your web server, often one of:
  * /var/www
  * /var/www/htdocs
  * /home/username/public\_html

Consult your webserver's documentation to determine the correct path.

Also note that there is a .htaccess file located in the root.  This file is not actually included by default, so you'll need to create it manually.  The file contents are as follows:
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?$1 [QSA,L]
</IfModule>
```

# Ready to Get Started: index.php #

At this point, the framework is installed and the directory is ready to go.  The next step is to create the index.php file to handle requests and load additional libraries.

## Setup ##

The first parts of the index.php file are the most important parts.  First- the actual Wax initialization script must be run:

```
<?php
    require_once(dirname(__FILE__) . "/waxphp-0.11/core/wax_init.php");
?>
```

Next, the supporting libraries (blocks) need to be loaded.  In the case of this blog, we want the application, 'database' and 'ddm' blocks.  We'll need to access the application and 'ddm' blocks later, so we assign them to a variable.

```
<?php
    require_once(dirname(__FILE__) . "/waxphp-0.11/core/wax_init.php");

    Wax::LoadBlock("database");             // The WaxPDO wrapper for database functionality
    $ddmblock = Wax::GetBlock("ddm");       // The Dynamic Data Model libraries
    $block = Wax::LoadBlockAt(getcwd());    // The Application block
?>
```

Note that 3 different functions were used to load these blocks.  Each one performs slightly differently:

  * **::LoadBlock** - Loads a block into the runtime
  * **::GetBlock** - Loads a block into the runtime and returns the WaxBlock object
  * **::LoadBlockAt** - Loads a block by its directory rather than by its name.

Finally, we connect to the datasource and register it with Wax.  Since the database block and the ddm use PDO as a backend, they share constructor definitions:

```
<?php
    require_once(dirname(__FILE__) . "/waxphp-0.11/core/wax_init.php");

    Wax::LoadBlock("database");             // The WaxPDO wrapper for database functionality
    $ddmblock = Wax::GetBlock("ddm");       // The Dynamic Data Model libraries
    $block = Wax::LoadBlockAt(getcwd());    // The Application block
    
    // init and register the iwh datasource
    $dsn = "mysql:dbname=myblog;host=localhost";    // Create a PDO Connection String
    $ddm = new DDS($dsn,"username","password");     // Init the Dynamic Data Source
    DSM::Register($dsn, $ddm);                      // Register the ddm with Wax
?>
```

At last -- the index.php file is set up.  Unfortunately, it doesn't really do anything yet.

## Routing and Executing the Request ##

The DDM implements a custom router and series of execution contexts to handle the requests.  Luckily, to use this router, you only need to add 3 more lines of code, creating a final index file which looks like this:

```
<?php
    require_once(dirname(__FILE__) . "/waxphp-0.11/core/wax_init.php");

    Wax::LoadBlock("database");             // The WaxPDO wrapper for database functionality
    $ddmblock = Wax::GetBlock("ddm");       // The Dynamic Data Model libraries
    $block = Wax::LoadBlockAt(getcwd());    // The Application block
    
    // init and register the iwh datasource
    $dsn = "mysql:dbname=myblog;host=localhost";    // Create a PDO Connection String
    $ddm = new DDS($dsn,"username","password");     // Init the Dynamic Data Source
    DSM::Register($dsn, $ddm);                      // Register the ddm with Wax

    $router = new Router($_SERVER['QUERY_STRING']);
    $app = new DDMApplicationCtx();
    echo $app->Execute($router, $block, $ddmblock);
?>
```

# Creating The Data Model #

Creating the initial data model is probably the most important part of getting a WaxDDM site up and running.  This can be accomplished by navigating to `http://server.com/app.wax/Admin`

**NOTE:** By default, this page will allow anyone to edit the model!  ACL and User Permissions Coming Soon.

## An Explanation of Attribute Types ##
The Attributes library in the DDM is where Wax's flexibility really shows.  When creating a model, each attribute must have a 'type'.  This type affects how the data is inputted, stored, and displayed.  Types as of the time of this writing are:

  * **AntiSpam** - This is probably the most unique attribute.  It demonstrates an attribute that requires no actual user input, and doesn't actually store any data.  It simply performs a javascript calculation during creating/editing and saving to ensure that the computer performing the operation is not a bot.
  * **Dropdown Menu** - Displays an HTML `<select>` tag
  * **Link** - Just like a textfield, except the value is displayed as a link
  * **Password** - Displays 2 password fields and verifies that they match before saving the value
  * **Pointer** - Stores the id of another record in the DDM.  Used for building relationships and references.
  * **Textarea** - Simple `textarea` for multiline text input
  * **Textfield** - Simple input field for single-line text input
  * **Timestamp** - Saves the time (updated  when edited)

These types, organized correctly, provide the basis for the scaffolding system used by the DDM.  The DDM API also allows new attribute types to be easily added and used.

## Creating a New Data Model ##
Creating a new data model is easy-- all you need to do is figure out a name for it.  Just navigate to `Admin/index` and type in the name.  Click 'Create Model' and you'll be redirected to the attribute editor.

## Editing the Model's Attributes ##
Once a data model is registered in the DDM, it must be defined.  Models are defined by a series of Attributes, each of which can have its own different options.

Since we're creating a blog, we're going to want to have "Posts".  The attributes for this particular Posts model are:

  * **Title** : textfield
  * **Message** : textarea
  * **Date Posted** : timestamp

Of these the timestamp is the only attribute with any options.  You can select one of several preconfigured formats or type in your own format.  For simplicity, we'll just leave the default.


## Creating a New Post ##
The way routing works with Wax is by specifying a target object and a method.  To create a new post then, the address would be `blog.wax/Posts/create`.  Here the user is presented with 2 fields, one for the title and one for the message.  Go ahead and type in a title and a message to create a post.

## Enabling Custom Views ##
After creating this post, the user is redirected to a list of all the records for the Posts data model.  Obviously, a table isn't really the best display format for a blog, so we'll write a new index view for the Posts.  The first thing that must be done is creating a representation of the Model in code (it's much easier than it sounds).  Create the file blog.wax/lib/Posts.php with the contents:
```
class Posts extends DDM {}
```

And that's all.  Now Wax knows that the model exists and can look for custom views.

## Creating a Custom View ##
So now that the Posts model is ready for its new facelift, the view can be created.  The path for the custom view will be `blog.wax/views/Posts/index.view.php`.  You will need to create the `views/Posts` directory as well.

The scaffolder passes certain variables to its respective view files.  In the case of the index function, the variables are:

  * **rows**: A list of all records for this model,
  * **structure**: The attribute definitions for this model

Using these variables, a simple view could be created that made the posts look more like a blog:
```
<?php foreach ($rows as $post): ?>
<h3><?=$post['Title']?></h3>
<i><?=date($structure['Date Posted']['options']['format'], $post['Date Posted'])?></i><br />
<?=$post['Message'];?>
<hr />
<?php endforeach; ?>
```

Now this is a view that iterates through each post and prints a heading with the title, the date posted, then the post message.  As you can see, the dynamic attributes are accessible directly via `$post['Attribute Name']`, while the structure information for each one is provided in `$structure['Attribute Name']`.

### Optional but Recommended: Rendering Attributes the Right Way ###
So this view works, however, it's not really the 'right' way to do it.  The real power of the different Attribute types is their ability to customize their display, however, just echoing the attribute skips this step.  Since the Title and Message don't really matter (the display views for them just echo the value), the 'right way' will be demonstrated with the timestamp attribute.  Below is the modified view which uses the AttrRenderCtx to render the attribute:
```
<?php foreach ($rows as $post): ?>
<h3><?=$post['Title']?></h3>
<i><?php
$arctx = new AttrRenderCtx();
echo $arctx->Execute($structure['Date Posted'],"view",$post['Date Posted']);
?></i><br />
<?=$post['Message'];?>
<hr />
<?php endforeach; ?>
```

# What's a Blog Without Comments? #

The next step will be to add the ability to add comments to Posts.  So navigate back to `Admin/index` and create a Comments model.  This model will be very simple as well.  The attributes used are:

  * **Author** : textfield
  * **Comment** : textarea
  * **ForPost** : pointer
  * **AntiSpam** : antispam

As you can see, this model makes use of the 'pointer' attribute type.  This allows the DDM to 'link' models together based on their relative ids to each other.  In this case, each comment will store a Post id specifying its parent.

Note that we included an 'antispam' attribute since anyone will be able to post comments.  Using 'antispam' will help prevent bots from posting where they shouldn't. Also notice that the 'pointer' attribute has 2 options to set.  These options are the Type and the Label, explained below:

  * **Type**: The type specifies which type of record this attribute will point to.  Since these comments are for posts, this should be set to 'Posts'
  * **Label**: This specifies which attribute from the parent the pointer should display as.  For convenience, this should be set to Title, so when the pointer renders, it will be a link to the Post with the Title as the link label.

Note that you will need to click 'Save Model' every time you change a dropdown option.  Sorry, I haven't really gotten any Ajax stuff working yet.

## Adding Comments to Posts ##
Since comments are children of Posts, it makes sense that they should be linked to the parent post automatically.  The index view will be modified to include a form that allows users to add comments to posts:

```
<?php foreach ($rows as $post): ?>
<h3><?=$post['Title']?></h3>
<i><?php
$arctx = new AttrRenderCtx();
echo $arctx->Execute($structure['Date Posted'],"view",$post['Date Posted']);
?></i><br />
<?=$post['Message'];?>
<div style='padding:40px; border-left:solid 1px gray;'>
    <form method="POST" action="<?=url_to('save','Comments')?>">
        Your Name: <input type='text' name='record[Author]' /><br />
        Comment:<textarea name='record[Comment]'></textarea><br />
        <input type='hidden' name='record[ForPost]' value='<?=$post['_id']?>' />
        <input type='submit' value='Post Comment' />
    </form>
</div>
<hr />
<?php endforeach; ?>
```

Here you see the first use of the function `url_to`, which generates a url to a resource.  The arguments for the function are as follows:
```
function url_to($method, $object = NULL, $arguments = array()) {...}
```

Similarly, the `link_to` method can be used to generate HTML links to resources:
```
function link_to($innerHTML, $method, $object = NULL, $arguments = array()) {...}
```

Wax provides several 'helper functions' to help reduce the amount of code needed for common tasks.  More information on these functions is available at WaxHelperFunctions.

## Showing Comments On Posts ##
Now that comments can be added to posts, it would be nice if they would all display underneath a post.  This is finally the part where actions need to be hard coded.  In this case, we want to override the index method from the scaffolder role.  This can be done by creating a new context that wraps the call to the Posts object.  This context would be created at `blog.wax/contexts/PostsIndexCtx.php`.

**Creating the PostsIndexCtx**
```
<?php
    class PostsIndexCtx extends Context {
        function Execute() {
            $ddm = DSM::Get();
                        
            // This is usually what happens automatically--
            // By wrapping this role method call in a context, the view arguments can be 
            // further processed- in this case, the Comments can be included.
            $postsobj = new Posts();
            $view = $postsobj->index();
            
            // add each Post's comments to the returned view args
            foreach ($view['rows'] as $index => $row) {
                $view['rows'][$index]['Comments'] = $ddm->Find("Comments",array("ForPost" => $row['_id']));
            }
            
            // and return the args
            return $view;
        }
    }
?>
```

There are a few things that should be explained about this Context:

  * The Posts Object
  * DSM::Get() and $ddm->Find(...)
  * $view args

### The Posts Object ###
In Wax, the router works by going directly to the target object and calling the method.  However, Wax is based off of DCI, so don't go looking for for any of these target methods in Posts, DDM, or any other object.  The target methods that Wax calls are called 'role' methods and they are 'injected' into these objects at runtime.  In this case the index method that we want to wrap is located in the rScaffolder role (located in /waxphp/blocks/ddm.wax/roles).

### DSM::Get() and $ddm->Find(...) ###
These functions are responsible for interacting with the dynamic data model.  The DSM is Wax's DataSourceManager.  When new DataSources are created, they are Registered in the DSM so they are easily available across the application.  The DSM::Get function returns a DataSource with a specified name.  If no name is specified, then the last registered data source is returned.

The $ddm->Find method is implemented in the actual Dynamic Data Source object.  The Find method takes an array of conditions and builds a special SQL query that can search through the records.  Passing `array("ForPost" => $row['_id'])` as the second argument tells the Find method to look for records whose attribute 'ForPost' is equal to the parent record's id.  Relationships between DDM models are created using the pointer attribute types and the Find method.

### $view args ###
This context (along with rScaffolder::index) returns an array that will be passed into the view as variables.  In this case, the context modifies the $rows variable to contain child Comments.

## The Modified View ##
This modified view file takes the information from the PostsIndexCtx and renders the comments beneath each post:

```
<?php foreach ($rows as $post): ?>
<h3><?=$post['Title']?></h3>

<i>
<?php
    $arctx = new AttrRenderCtx();
    echo $arctx->Execute($structure['Date Posted'],"view",$post['Date Posted']);
?>
</i><br />

<?=$post['Message'];?>

<div style='padding:40px; border-left:solid 1px gray;'>
    <?php foreach ($post['Comments'] as $comment): ?>
        <b>By:</b> <?=$comment['Author']?><br />
        <?=$comment['Comment']?><br /><br />
    <?php endforeach; ?>

    <form method="POST" action="<?=url_to('save','Comments')?>">
        Your Name: <input type='text' name='record[Author]' /><br />
        Comment:<textarea name='record[Comment]'></textarea><br />
        <?php
            // The antispam attribute must be rendered using the
            // AttrRenderCtx since it would be useless or tedious to
            // implement it otherwise.
            $arctx = new AttrRenderCtx();
            echo $arctx->Execute($comment_structure['AntiSpam'],"edit");
        ?>
        <input type='hidden' name='record[ForPost]' value='<?=$post['_id']?>' />

        <input type='submit' value='Post Comment' />
    </form>

</div>

<hr />

<?php endforeach; ?>
```