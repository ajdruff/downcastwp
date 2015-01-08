###Simpliwp-Downcast

The Simpliwp-Downcast WordPress plugin enables you to use much of the Downcast Framework from within WordPress.
This allows you to easily port over content with just a few shortcodes.


The following features are supported:

* skins
* plugins
* Markdown with the bootdown extension or other configured parser via its markdowner plugin


Most plugins are directly compatible from the original downcast code with no modificiations necessary.








##How to Add Content


##Todo/Planned

* Add a template redirect api (such as addPage) that will use template_include 
to detect markdown urls and include the proper markdown file. 
This should eliminate the need to 
use shortcodes
      add_filter( 'template_include', array($this,'include_template'), 1 );     
        function include_template( $template ) {

	if ( is_page( 'downcast' )  ) {
            $this->downcast()->debugLog( '$template = ', $template, true, true );

            $new_template = locate_template( array( 'portfolio-page-template.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}

	return $template;
}