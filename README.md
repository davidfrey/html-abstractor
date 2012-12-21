HTML Abstractor
===============
A simple PHP library to generate HTML5 markup using an object abstraction layer.

Usage
-----

```php
// Require the HTML5 Library
require_once( 'html5.php' );

// Create a new Form
$form = new Form( $_SERVER['PHP_SELF'] );

// Add form fields
$form->add( $form->tag( 'input', array( 'type' => 'text', 'name' => 'username' ) ) );

// Attach a Submit Button
$button = $form->tag( 'input', array( 'type' => 'submit', 'value' => 'Submit' ) );
$form->add( $form->tag( 'fieldset', array( 'class' => 'actions' ), $button ) );

// Render the form as HTML
echo $form->render();
```

For more detailed usage see example.php