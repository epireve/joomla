<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modMyEditorHelper
{
	function getEditors()
	{
		$dbo = JFactory::getDBO();
		$query = 'SELECT element, name text '.
			'FROM #__plugins '.
			'WHERE folder = "editors" '.
			'AND published = 1 '.
			'ORDER BY ordering, name';
		$dbo->setQuery($query);
		$editors = $dbo->loadObjectList();

		return $editors;
	}

}

