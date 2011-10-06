<?php
/**
 * Form.
 *
 * 
 *
 */
class Form extends HTML5
{
	/**
	 * Additional field attributes stored in key/value pairs
	 * @var 	array 	$_attributes
	 */
	protected $_action;

	/**
	 * Fields array
	 * @var 	array 	$_fields
	 */
	protected $_fields;

	/**
	 * Fields array
	 * @var 	array 	$_options
	 */
	protected $_options;

	/**
	 * Class Constructor
	 *
	 * This class constructor instantiates the creation of a new form field
	 * element contained in the HTML5 Helper.
	 * 
	 * @param 	string 	$name (required)
	 * @param 	string 	$action (required)
	 * @param 	array 	$options (optional)
	 */
	function __construct( $action, $options = array() )
	{
		$this->_action = $action;
		$this->_fields = array();
		$this->_options = $options;
	}

	/**
	 * Text Field.
	 *
	 * Creates an <input type="text"> field.
	 *
	 * @param 	string $name
	 * @param 	array $options
	 */
	function text_field( $name, $value = null, $options = array() )
	{
		$field = new Form_Field( 'text', $name, $value, $options );
		$field = $field->render();

		return $field;
	}

	/**
	 * Text Box.
	 *
	 * Creates a <textarea> field.
	 *
	 * @param 	string $name
	 * @param 	array $options
	 */
	function text_box( $name, $value = null, $options = array() )
	{
		$field = new Form_Field( 'textarea', $name, $value, $options );
		$field = $field->render();

		return $field;
	}

	/**
	 * Select.
	 *
	 * Creates a <select> field with options.
	 *
	 * @param 	string $name
	 * @param 	array $options
	 */
	function select_box( $name, $value = null, $collection = array(), $options = array() )
	{
		$options['collection'] = $collection;

		$field = new Form_Field( 'select', $name, $value, $options );
		$field = $field->render();

		return $field;
	}


	function add( $field )
	{
		$this->_fields[] = $field;
	}


	function render()
	{
		return $this->form( $this->_action, join( "", $this->_fields ), $this->_options );
	}
}
?>