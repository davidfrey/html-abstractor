<?php

function __autoload( $class )
{
	require_once( strtolower( $class ) . ".php" );
}

/**
 * HTML5 Helper Class.
 *
 * Simplifies the construction of HTML5 markup in PHP.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 	Copyright 2011, David Frey
 * @package 	HTML5_Helper
 * @since 		v 0.1
 * @license 	http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class HTML5
{
	/**
	 * Attribute Order.
	 * 
	 * A sequential list of html tag attributes for code vanity.
	 * 
	 * @var 	array
	 * @access 	public
	 */
	public $attribute_order = array( 
		'type',
		'name',
		'href',
		'src',
		'for',
		'rel',
		'id',
		'class'
	);

	/**
	 * Create an HTML5 tag.
	 *
	 * Wraps optional content between start and end tags. If content is ommited
	 * a self-closing tag is created.
	 *
	 * @access 	public
	 * @param 	string 	$name
	 * @param 	array 	$attributes
	 * @param 	string 	$content
	 *
	 * @uses 	start_tag() 
	 * @uses 	end_tag()
	 * @return 	string 	complete tag
	 */
	public function tag( $name, $attributes = array(), $content = null, $options = array() )
	{
		$format = ( isset( $options['format'] ) ) ? $options['format'] : 'block';

		$markup = '';

		if ( 'inline' == $format ) $markup .= "\t"; //vanity hack

		$markup .= $this->auto_format( $this->start_tag( $name, $attributes ), $format );

		if ( isset( $content) )
		{
			$markup .= $this->auto_format( $content, $format );
			$markup .= $this->auto_format( $this->end_tag( $name ), $format )."\n";
		}

		return $markup;
	}

	/**
	 * Creates the opening part of a tag.
	 *
	 * @access 	private
	 * @param 	string 	$name the tag name (e.g. a, img, input, textarea, etc.).
	 * @param 	array 	$attributes (optional) names and values of tag attributes
	 * @return 	string 	tag
	 *
	 * @todo 	Make sure all attributes are unique and not repeated
	 */
	private function start_tag( $name, $attributes = array() )
	{
		return "<$name".$this->stringify_attributes( $this->attribute_sorter( $attributes ) ).">";
	}

	/**
	 * Creates the closing part of a tag.
	 *
	 * @access 	private
	 * @param 	string 	$name the tag name (e.g. a, img, input, textarea, etc.).
	 * @return 	string 	closing tag
	 */
	private function end_tag( $name )
	{
		return "</$name>";
	}

	/**
	 * Titleizes a string
	 *
	 * @param 	string 	$string
	 * @return 	string 	reformated with words uppercased
	 */
	function to_label( $string )
	{
		/** todo: add a lot more patterns */

		$string = str_replace( '-', ' ', $string );
		$string = str_replace( '_', ' ', $string );

		return ucwords( $string );
	}



	/**
	 * Form tag
	 *
	 * @param 	string 	$action
	 * @param 	array 	$fields
	 * @param 	array 	$attributes
	 * @return 	string 	valid label tag
	 */
	function form( $action, $fields = array(), $attributes = array() )
	{
		$attributes['action'] = ( isset( $action ) ) ? $action : '/';
		$attributes['id'] = ( isset( $attributes['id'] ) ) ? $attributes['id'] : "form_".time();
		$attributes['method'] = ( isset( $attributes['method'] ) ) ? $attributes['method'] : 'post';

		return $this->tag( 'form', $attributes, $fields );
	}

	/**
	 * Label tag
	 *
	 * @param 	string 	$label
	 * @param 	string 	$id
	 * @param 	array 	$attributes
	 * @return 	string 	valid label tag
	 */
	function label( $label, $id, $attributes = array() )
	{
		$attributes['for'] = $id;
		return $this->tag( 'label', $attributes, $label, array( 'format' => 'inline' ) );
	}

	/**
	 * Input tag
	 *
	 * @param 	string 	$name
	 * @param 	string 	$value
	 * @param 	string 	$type
	 * @param 	array 	$attributes
	 * @return 	string 	valid input tag
	 */
	function input( $name, $value = null, $type = 'text', $attributes = array() )
	{
		$attributes['type'] = $type;
		$attributes['name'] = $name;

		if ( isset( $value ) )
		{
			$attributes['value'] = $value;
		}

		return $this->tag( 'input', $attributes );
	}

	/**
	 * Select tag
	 *
	 * @param 	string 	$name
	 * @param 	string 	$value
	 * @param 	array 	$collection
	 * @param 	array 	$attributes
	 * @return 	string 	valid select tag with options
	 */
	function select( $name, $value = null, $collection = array(), $attributes = array() )
	{
		$attributes['name'] = $name;

		$collection = $this->normalize_collection( $collection );

		$options = array();

		foreach ( $collection as $item['value'] => $item['name'] )
		{
			$option_attributes = array();
			$option_attributes['value'] = $item['value'];

			if ( isset( $value ) && $item['value'] == $value )
			{
				$option_attributes['selected'] = 'selected';
			}

			$options[] = $this->tag( 'option', $option_attributes, $item['name'], array( 'format' => 'inline' ) );
		}

		return $this->tag( 'select', $attributes, join( "\n", $options ) );
	}

	/**
	 * Textarea tag
	 *
	 * @param 	string 	$name
	 * @param 	string 	$value
	 * @param 	array 	$attributes
	 * @return 	string 	valid textarea tag
	 */
	function textarea( $name, $value = null, $attributes = array() )
	{
		$attributes['name'] = $name;

		return $this->tag( 'textarea', $attributes, $value, array( 'format' => 'inline' ) );
	}





	/**
	 * Wrap your content with an inline or block element
	 *
	 * @param 	string 	$content
	 * @param 	string 	$attributes
	 * @param 	array 	$options
	 * @return 	string 	content wrapped with <span> or <div>
	 */
	function wrap( $content, $attributes = array(), $options = array() )
	{
		if ( ! isset( $options['format'] ) ) $options['format'] = 'block';

		switch( $options['format'] )
		{
			case "block":
				$markup = $this->tag( 'div', $attributes, $content, $options );
				break;
			case "inline":
				$markup = $this->tag( 'span', $attributes, $content, $options );
				break;
			default:
				$markup = $content;
		}

		return $markup;
	}


	/**
	 * Sort tag attributes.
	 *
	 * Allows you to sort tag attributes to produce consistently formatted HTML
	 * for vanity purposes.
	 *
	 * @access 	private
	 * @param 	array $attributes names and values of tag attributes
	 * @uses 	$attribute_order compares $attribute_order with $attributes
	 * @since 	v 0.1
	 * @return 	array resorted array
	 */
	private function attribute_sorter( $attributes )
	{
		if ( ! is_array( $attributes ) ) return false;

		$common = array();

		foreach ( $this->attribute_order as $attribute )
		{
			if ( isset( $attributes[$attribute] ) )
			{
				$common[$attribute] = $attributes[$attribute];
			}
		}

		$custom = array_diff_key( $attributes, $common );

		return array_merge( $common, $custom );
	}

	/**
	 * Converts associative array into tag attributes.
	 *
	 * Takes an associative array and produces a $key="$value" pattern to be
	 * used when generating an HTML tag.
	 *
	 * @access 	private
	 * @param 	array $attributes names and values of tag attributes
	 * @since 	v 0.1
	 * @return 	string attributes in a $key="$value" pattern
	 */
	public function stringify_attributes( $attributes = array() )
	{
		$string = '';

		if ( is_array( $attributes ) && count( $attributes ) > 0 )
		{
			foreach( $attributes as $key => $value )
			{
				$string .= ' '.$key.'="'.$value.'"';
			}
		}

		return $string;
	}

	public function auto_format( $code, $format)
	{
		switch( $format )
		{
			case "inline":
				$indent = '';
				$line_break = '';
				break;
			default:
				$indent = "\t";
				$line_break = "\n";
				break;
		}

		$markup = '';

		foreach( preg_split( "/\r?\n/", $code ) as $line )
		{	
			if ( strlen( $line ) > 0 ) $markup .= $indent . $line . $line_break;
		}

		return $markup;
	}

	/**
	 * Ensures collection is made up of key/value pairs.
	 *
	 * Converts indexed arrays into associative arrays.
	 *
	 * @access 	private
	 * @param 	array $attributes names and values of tag attributes
	 * @since 	v 0.1
	 * @return 	string attributes in a $key="$value" pattern
	 */
	private function normalize_collection( $array )
	{
		$collection = array();
		
		if ( array_merge( $array ) === $array && is_numeric( implode( array_keys( $array ) ) ) )
		{
			foreach( $array as $item )
			{
				$collection[$item] = $item;
			}
		}
		else
		{
			$collection = $array;
		}
		
		return $collection;
	}
}
?>