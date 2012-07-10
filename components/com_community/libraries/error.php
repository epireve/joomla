<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');


class CError {

	public function assert( $var1, $var2, $cond , $file = __FILE__ , $line = __LINE__) {
		switch( $cond ) {
			case 'eq':	// both var must be equal
				if( $var1 != $var2 ) JError::raiseError( 500, $file . ':' . $line);
				break;
				
			case 'lt':	// var1 must be less than var2
				if( $var1 >= $var2 ) JError::raiseError( 500, $file . ':' . $line);
				break;
				
			case 'gt':	// var1 must be greater than var2
				if( $var1 <= $var2 ) JError::raiseError( 500, $file . ':' . $line);
				break;
				
			case 'contains':	// var1 must be in var2 array
				break;
				
			case '!contains':	// var1 must be in var2 array
				break;
			
			case 'empty':	// both var must be equal
				if( !empty($var1) ) JError::raiseError( 500, $file . ':' . $line);
				break;
				
			case '!empty':	// both var must be equal
				if( empty($var1) ) JError::raiseError( 500, $file . ':' . $line);
				break;
				
			case 'istype':	// $var1 must be of type $var2
				$func = 'is_'.$var2;
				if( !$func($var1) ) JError::raiseError( 500, $file . ':' . $line);
				break;
		}
	}
}