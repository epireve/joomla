<?php
/**
* @copyright	Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Frontend
* @contact 		shyam@readybytes.in
*/
if(defined('_JEXEC')===false) die();

/*
 * We have extended JDocument class so that we can control what to do
 * on particular times
 */
jimport('joomla.html.parameter');
class XiptParameter extends JParameter
{
	public function __construct($data = '', $path = '')
	{
		parent::__construct($data, $path);
	}
		
	function render($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group])) {
			return false;
		}
		//XITODO : render form name as div id
		$params = $this->getParams($name, $group);
		ob_start();?>
		<?php JHTML::_('behavior.tooltip'); ?>
		<div class="xiptParameter">

		<?php if ($description = $this->_xml[$group]->attributes('description')) : ?>
			<div class="xiptParameter xiRow" >
				<div class="xiptParameter xiCol">
				<?php echo JText::_($description) ; ?>
				</div>
			</div>
		<?php endif;?>

		<?php foreach ($params as $param) : ?>
			<div class="xiptParameter xiRow" >
				<?php if ($param[0] && $param[0] != '&nbsp;'): ?>
					<div class="xiptParameter xiCol xiColKey">
						<?php echo $param[0]; ?>
					</div>
					<div class="xiptParameter xiCol xiColValue">
						<?php echo $param[1]; ?>
					</div>
				<?php else: ?>
					<div class="xiptParameter xiCol xiColDescription">
						<?php echo $param[1]; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<?php if(count($params) < 1) : ?>
			<div class="xiptParameter xiRow">
				<div class="xiptParameter xiCol"><i>
				<?php JText::_('COM_PAYPLANS_THERE_ARE_NO_PARAMETER_FOR_THIS_ITEM'); ?>
				</i></div>
			</div>
		<?php endif; ?>

		</div>

		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function bind($data, $group = '_default')
	{
		if (is_array($data)) {
			return $this->loadArray($data, $group);
		} elseif (is_object($data)) {
			return $this->loadObject($data, $group);
		} else {
			return $this->loadINI($data, $group);
		}
	}
	
	public function loadINI($data, $namespace = null, $options = array())
	{
		//for 1.5 no change in behavior
		if(XIPT_JOOMLA_15){
			return parent::loadINI($data, $namespace, $options);
		}
		
		//for 1.6+ we will use our own writer
		return $this->loadString($data, 'XiptINI', $options);
	}
	
//	function &loadElement( $type, $new = false )
//	{
//		$false = false;
//		$signature = md5( $type  );
//
//		if( (isset( $this->_elements[$signature] ) && !is_a($this->_elements[$signature], '__PHP_Incomplete_Class'))  && $new === false ) {
//			return	$this->_elements[$signature];
//		}
//
//		// Try to load XI element
//		$elementClass	=	'XiElement'.$type;
//		if( !class_exists( $elementClass,true))
//		{
//			if( isset( $this->_elementPath ) ) {
//				$dirs = $this->_elementPath;
//			} else {
//				$dirs = array();
//			}
//
//			$file = JFilterInput::clean(str_replace('_', DS, $type).'.php', 'path');
//
//			if ($elementFile = JPath::find($dirs, $file)) {
//				include_once $elementFile;
//			}
//
//			// if class does not exist then let the parent load the element
//			if( !class_exists( $elementClass,true ) ) {
//				return parent::loadElement($type, $new = false );
//			}
//		}
//
//		$this->_elements[$signature] = new $elementClass($this);
//
//		return $this->_elements[$signature];
//	}
}