<?php
/**
* @version		$Id:$
* @package		AdminPraise.Framework
* @subpackage	Parameter
* @author		Stian Didriksen
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Renders a theme preview element
 *
 * Please note this element is only meant to be used within templates as it expect to be inside the default params group.
 * If you're using this elsewhere and have another value than "_default" in your group="" attribute in the parent <params>
 * element, this particular script wont work.
 *
 * @package 	AdminPraise.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementThemes extends JElement
{
	/**
	* Themes
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Themes';

	function fetchElement($name, $value, &$node, $control_name, $i=0, $element=null, $elements=array())
	{
		if ($value) {
			foreach($this->_parent->_xml['_default']->children() as $key => $param)
			{
				if($param->attributes('name')==$value)
				{
					$i = $key; 
					$element = $param;
				}
			}
			
			$uri = JFactory::getURI();
			foreach($element->children() as $child)
			{
				$uri->setVar('templateTheme', $child->attributes('value'));
				$style = explode('/', $child->data());
				/*Experimental code below, uncomment to apply styling on each link resembling its theme*/
//				$style[0] = $style[0] ? $style[0] : 'transparent';
//				$style[1] = $style[1] ? $style[1] : 'inherit';
//				$return[] = '<a href="'.$uri->toString().'" style="bordercolor:'.($child->attributes('color')?$child->attributes('color'):$style[1]).';background-color:'.($child->attributes('bgcolor')?$child->attributes('bgcolor'):$style[0]).';">'.$child->data().'</a>';

				$return[] = '<a href="'.$uri->toString().'"'.(JRequest::getCmd('templateTheme')===$child->attributes('value')?' style="font-weight:bold;"' : null).'>'.JText::_($child->data()).'</a>';
				$uri->delVar('templateTheme');
			}
			if($node->attributes('resetlink')) 
			{
				$uri->setVar('templateTheme', null);
				$return[] = '<a href="'.$uri->toString().'">'.JText::_($node->attributes('resettext')?$node->attributes('resettext'):'Reset').'</a>';
				$uri->delVar('templateTheme');
			}
			
			return implode(' - ', $return);
		} else {
			return '<h4>Notice: no value defined.</h4></ br><h5>You need to enter the name of the parameter containing the list of available themes in the default="" attribute.'."\n".'Example:</h5><pre>&lt;param name="@themes" type="themes" default="templateTheme" /&gt;</pre></ br><h5>Example of a parameter listing themes:</h5><pre>&lt;param name="templateTheme" type="list" default="1"&gt;
	&lt;option value="theme1"&gt;Theme 1 - Beige&lt;/option&gt;
	&lt;option value="theme2"&gt;Theme 2 - Dark&lt;/option&gt;
	&lt;option value="theme3"&gt;Theme 3 - Gray&lt;/option&gt;
	&lt;option value="theme4"&gt;Theme 4 - White&lt;/option&gt;
&lt;/param&gt;</pre>';
		}
	}
}
