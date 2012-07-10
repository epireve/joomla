<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.parameter');
class CParameter extends JParameter {	
	/**
	* @param string name
	* @param string group [currently not used, being put there to imitate JParameter render()]
	* @return string html
	*/
	public function render($name = 'params', $group = '_default'){
		if (!isset($this->_xml[$group])) {
			return false;
		}

		$params = $this->getParams($name, $group);
		$html = array ();
		$html[] = '<table width="100%" class="paramlist admintable" cellspacing="1">';

		if ($description = $this->_xml[$group]->attributes('description')) {
			// add the params description to the display
			$desc	= JText::_($description);
			$html[]	= '<tr><td class="paramlist_description" colspan="2">'.$desc.'</td></tr>';
		}

		foreach ($params as $param)
		{
			$html[] = '<tr>';

			if ($param[0]) {
				$html[] = '<td width="40%" class="paramlist_key"><span class="editlinktip">'.$param[0].'</span></td>';
				$html[] = '<td class="paramlist_value">'.$param[1].'</td>';
			} else {
				$html[] = '<td class="paramlist_value" colspan="2">'.$param[1].'</td>';
			}

			$html[] = '</tr>';
		}

		if (count($params) < 1) {
			$html[] = "<tr><td colspan=\"2\"><i>".JText::_('There are no Parameters for this item')."</i></td></tr>";
		}

		$html[] = '</table>';

		return implode("\n", $html);
	}
	public function bind($data, $group = '_default')
	{
		if (is_array($data)) {
			return $this->loadArray($data, $group);
		} elseif (is_object($data)) {
			return $this->loadObject($data, $group);
		} else {
			if(method_exists($this,'loadJSON')){
				return $this->loadJSON($data);
			} else {
				return $this->loadINI($data);
			}
		}
	}	
}
?>