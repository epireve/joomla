<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<h3><?php echo JText::_( 'EXTEND' );?></h3>
<ul>
	<li><a href="index.php?option=com_installer"><?php echo JText::_( 'INSTALL/UNINSTALL' );?></a></li>
	<li><a href="index.php?option=com_installer&task=manage&type=components"><?php echo JText::_( 'MANAGE COMPONENTS' );?></a></li>
	<li><a href="index.php?option=com_installer&task=manage&type=modules"><?php echo JText::_( 'MANAGE MODULES' );?></a></li>
	<li><a href="index.php?option=com_installer&task=manage&type=plugins"><?php echo JText::_( 'MANAGE PLUGINS' );?></a></li>
</ul>