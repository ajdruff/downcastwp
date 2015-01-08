###DowncastWP

The DowncastWP WordPress plugin enables you to use much of the Downcast Framework from within WordPress.
This allows you to easily use your Downcast content within WordPress without modification.



The following features are supported:

* skins
* plugins
* Markdown


Most plugins are directly compatible with WordPress without having to modify their code.


##How to Add Content

##Method 1##
Use this method if you have no PHP within your Markdown file.

1. Create a new WordPress page or post.
2. Paste in any Downcast content or create new content by writing some Markdown.



##Method 2

Use this method if you have PHP within your content, are using Downcast Forms, or simply wish to keep your files intact without having to add them to the WordPress database.

1. Create a new WordPress page or post.
2. Add the following shortcode

        [downcast_content path="/path/to/content/file.md"]
 
3. Edit the /path/to/content/file to be the path to your file. The file can be any extension. If its php, it will be parsed as php. The path can be an absolute path for the file system, or can be relative to the root of your webserver.       
    
