<?php
/**
 * Form Field.
 *
 * 
 *
 */
class Form_Field extends Form
{
	/**
	 * Type of field (input, textarea, select, etc)
	 * @var 	string 	$_field
	 */
	protected $_field;

	/**
	 * Type of input (text, email, select, etc)
	 * @var 	string 	$_type
	 */
	protected $_type;

	/**
	 * Unique field name
	 * @var 	string 	$_name
	 */
	protected $_name;

	/**
	 * Unique field ID
	 * @var 	string 	$_id
	 */
	protected $_id;

	/**
	 * Additional field attributes stored in key/value pairs
	 * @var 	array 	$_attributes
	 */
	protected $_attributes;

	/**
	 * Optional label text for field
	 * @var 	string 	$_label
	 */
	protected $_label;

	/**
	 * Code formatting option (inline, block, raw, etc.)
	 * @var 	array 	$_format
	 */
	protected $_format;

	/**
	 * Field validation requirements
	 * @var 	array 	$_validation
	 */
	protected $_validations;

	/**
	 * Collection to iterate
	 * @var 	array 	$_collection
	 */
	protected $_collection;

	/**
	 * Field validation errors
	 * @var 	array 	$_errors
	 */
	protected $_errors;

	/**
	 * Valid HTML5 input types
	 * @var 	array 	$input_types
	 */
	public $input_types = array( 'button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time', 'url', 'week' );

	/**
	 * Valid HTML5 fields with options
	 * @var 	array 	$input_types
	 */
	public $field_types = array( 'input', 'textarea', 'button', 'select' );

	/**
	 * Class Constructor
	 *
	 * This class constructor instantiates the creation of a new form field
	 * element contained in the HTML5 Helper.
	 * 
	 * @param 	string 	$field (required)
	 * @param 	string 	$name (required)
	 * @param 	string 	$value (optional)
	 * @param 	array 	$options (optional)
	 * @param 	array 	$validations (optional)
	 */
	function __construct( $field_type, $name, $value = null, $options = array(), $validations = array() )
	{
		$options = ( isset( $options ) ) ? $options : array();
		$validations = ( isset( $validations ) ) ? $validations : array();

		// Decode $field_type alias
		if ( in_array( $field_type, $this->input_types ) )
		{
			$this->_field = 'input';
			$this->_type = $field_type;
		}
		else
		{
			$this->_field = $field_type;
		}

		$this->_name = $name;
		$this->_value = $value;
		$this->_validations = ( isset( $validations ) ) ? $validations : array();

		$this->process_options( $options );
	}


	/**
	 * Process Options
	 *
	 * Evaluate the options array and handle custom processing necessary to 
	 * alter the field code at render.
	 * 
	 * @param 	array 	$options (required)
	 */
	private function process_options( $options )
	{
		extract( $options );

		$this->_attributes = ( isset( $attributes ) ) ? $attributes : array();
		$this->_collection = ( isset( $collection ) ) ? $collection : null;
		$this->_format = ( isset( $format ) ) ? $format : 'block';
		$this->_label = ( isset( $label ) ) ? $label : $this->to_label( $this->_name );
		$this->_id = ( isset( $id ) ) ? $id : $this->_name;
	}

	/**
	 * Process Validations
	 *
	 * Evaluate custom validation callbacks passed in as an array. Valid options
	 * are:
	 *
	 * * presence_of
	 * * email
	 * @return 	array collection of errors
	 */
	public function process_validations()
	{
		if ( 0 == count( $this->_validations ) ) return array();

		$errors = array();

		foreach ( $this->_validations as $validation )
		{
			$method = 'validate_'.$validation;

			// Ignore any method that doesn't exist
			if ( true == method_exists( $this, $method ) )
			{
				$response = call_user_func( array( $this, $method ), $this->_value );
				
				if ( "valid" != $response )
				{
					$errors[$validation] = $response;
				}
			}
			else
			{
				error_log( 'Invalid Method Call: ' . $method . '("'.$this->_value.'")' );
			}
		}
		
		$this->_errors = ( count( $errors ) > 0 ) ? $errors : null;

		return $this->_errors;
	}

	/**
	 * Validates presence of $value
	 * 
	 * @param 	mixed 	$value
	 */
	private function validate_presence_of( $value = null )
	{
		return ( ! empty( $value ) ) ? "valid" : "cannot be left blank";
	}

	/**
	 * Alias for validate_presence_of
	 * 
	 * @param 	mixed 	$value
	 */
	private function validate_required( $value = null )
	{
		return $this->validate_presence_of( $value );
	}

	/**
	 * Validates format of $value is a valid email
	 * 
	 * @param 	string 	$value
	 */
	private function validate_email( $value )
	{
		return ( preg_match( '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', trim( $value ) ) ) ? "valid" : "not a valid email address";
	}



	private function build_field()
	{
		$this->_attributes['id'] = $this->_id;

		if ( true == method_exists( $this, $this->_field ) )
		{
			/** todo: this is a hack and and pretends to know too much about another class - needs to be refactored */
			// Method call will require a fourth argument
			if ( isset( $this->_collection ) || isset( $this->_type ) )
			{
				// Collection superseds Type
				$data = ( isset( $this->_collection ) ) ? $this->_collection : $this->_type;

				return call_user_func( array( $this, $this->_field ), $this->_name, $this->_value, $data, $this->_attributes );
			}

			return call_user_func( array( $this, $this->_field ), $this->_name, $this->_value, $this->_attributes );
		}
		else
		{
			// Backup, may produce uexpected results
			if ( isset( $this->_value ) && "input" == $this->_field )
			{
				$this->_attributes['value'] = $this->_value;
			}

			if ( isset( $this->_value ) && "input" != $this->_field )
			{
				$content = $this->_value;
			}

			$this->_attributes['name'] = $this->_name;

			return $this->tag( $this->_field, $this->_attributes, $content );
		}
	}

	function render( $options = array() )
	{
		$field = $this->build_field();
		$label = $this->label( $this->_label, $this->_id );

		$attributes = array();
		$attributes['class'] = 'field';

		if ( is_array( $options ) )
		{
			if ( isset( $options['show_errors'] ) && is_array( $this->_errors ) && count( $this->_errors ) > 0 )
			{
				$errors = $this->tag( 'span', array( 'class' => 'message' ), join( ', ', $this->_errors ) );
				$attributes['class'] = $attributes['class'] . ' error';
			}
			else
			{
				$errors = '';
			}
		}

		/** todo: allow option to reorder tags */
		$content = "$label $field $errors";

		return $this->wrap( $content, $attributes );
	}
}

$options = array(
	"attributes" => array( "id" => "my-unique-id", "class" => "required", "size" => 33 ),
	"format" => 'inline',
	"label" => 'My Custom Label'
);
?>