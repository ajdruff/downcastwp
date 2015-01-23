<?php


/*
 * Declare the form object with our Form's ID
 */

$form = new DowncastForm(
        "find_host", // form id , arbitrary string that is unique for the form
        true, //$ajax whether we want the form to use ajax. 
        'find_host'
        
);

if ( isset($_POST['form'])) {
    $FindMyHostPlugin=$form->downcast()->getPlugin( 'FindMyHost') ;
       $FindMyHostPlugin->formActionFindHost();
}
?>

<?php
    

$options = array(
   // 'response_target' => 'form_response', //hides the form on success
           'ajax_loader_html'=> '<img src="'.$this->getPlugin('Forms')->getRootUrl().'/content/img/ajax-loader.png" class="ajax-loader">',
            'response_target_attributes' => 'class="response_target"', //any attributes to be added to the downcast_response_target element. e.g: class="response_target"     
    'hide_on_success' => false, //hides the form on success
    'collapse_on_hide' => true, //completely removes all form html from page when form is hidden);
    'reset_on_success' => false,
    "view" => new View_Inline,
    "labelToPlaceholder" => 0
);
$form->setAjaxOptions( $options );


/*
 * Add Form Elements
 */
//$form->addElement( new Element_HTML( '<legend>Login</legend>' ) );


$form->addElement(new Element_Textbox("Website:", "website", array(
    "class"=>"input-xxlarge",
     "style"=>"height: 40px;font-size: 36px;",
    "placeholder" => "http://problogger.com", 
   // "append" => '<button class="btn-large btn-primary">Get That Host</button>'
)));
$form->addElement( new Element_HTML( '<button class="btn-large btn-primary">Get That Host</button>' ) );
$form->render();

?>

<div class="response_target" id="downcast_response_target"></div>