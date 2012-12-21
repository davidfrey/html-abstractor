<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Example</title>
	<style type="text/css">
		.data {margin:1em 0;padding:1em;background-color:#eee;border:1px solid #ddd}
		.error {border:1px solid red}
		.error .message {color:red}
	</style>
</head>
<body>
<?php

require_once( 'html5.php' );

$content = "<h2>Dynamically create an HTML5 form</h2>";

$user_data = '{
	"first_name":{
		"type":"text",
		"value":"Joe",
		"required":1
	},
	"last_name":{
		"type":"text",
		"value":"Joe",
		"required":1
	},
	"email":{
		"type":"text",
		"value":"joe@smith",
		"required":1
	},
	"username":{
		"type":"text",
		"value":"",
		"required":1
	},
	"bio":{
		"type":"textarea",
		"value":"Joe",
		"required":0
	}
}';

$joe = json_decode( $user_data, true );

// Create a new form container
$form = new Form( $_SERVER['PHP_SELF'] );

// Add fields manually using the tag method
$form->add( $form->tag( 'input', array( 'type' => 'hidden', 'name' => 'registration', 'value' => 'user' ) ) );

foreach ( $joe as $segment['name'] => $segment )
{
	$options = array();
	$validations = array();
	$errors = array();
	
	$options['show_errors'] = true;
	
	if ( $segment['required'] == 1 )
	{
		$validations[] = 'presence_of';
	}
	if ( $segment['name'] == 'email' )
	{
		$validations[] = 'email';
	}
	
	$field = new Form_field( $segment['type'], $segment['name'], $segment['value'], $options, $validations );
	$errors = $field->process_validations();

	$form->add( $field->render( $options ) );
}

// Manually add a submit button
$button = $form->tag( 'input', array( 'type' => 'submit', 'value' => 'Send' ) );

// Attach the button to the form
$form->add( $form->tag( 'fieldset', array( 'class' => 'actions' ), $button ) );

// Finally, render our completed form object and attach it to the content output
$content .= $form->render();

echo $content;

if ( $_POST )
{
	print "<div class=\"data\">";
	print "<h3>POST Data</h3>";
	print "<pre>";
	print_r( $_POST );
	print "</pre>";
	print "</div>";
}
?>
</body>
</html>