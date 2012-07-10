<?php
/**
 * @category 	FormElement
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

class CFormElement
{
	public $label = '';
	public $html = '';
	public $position = '';


	/**
	 * Renders the provided elements into their respective HTML formats.
	 * 	 
	 * @param	Array	formElements	An array of CFormElement objects.
	 * @param	string	position		The position of the field 'before' will be loaded before the rest of the form and 'after' will be loaded after the rest of the form loaded.
	 * 
	 * returns	string	html			Contents of generated CFormElements.
	 **/	
	static public function renderElements( $formElements , $position )
	{
		$tmpl	= new CTemplate();
		$tmpl->set( 'formElements'	, $formElements );
		$tmpl->set( 'position'		, $position );
		$html	= $tmpl->fetch( 'form.elements' );
		return trim($html);
	}
}
