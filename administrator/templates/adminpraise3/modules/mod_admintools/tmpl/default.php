<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<h3><?php echo JText::_( 'ADMIN TOOLS' );?></h3>
<ul>
	<li><a href="index.php?option=com_config"><?php echo JText::_( 'GLOBAL CONFIGURATION' );?></a></li>
	<li><a href="index.php?option=com_admin&task=sysinfo"><?php echo JText::_( 'SYSTEM INFORMATION' );?></a></li>
	<li><a href="index.php?option=com_checkin"><?php echo JText::_( 'GLOBAL CHECKIN' );?></a></li>
	<li><a href="index.php?option=com_cache"><?php echo JText::_( 'CACHE MANAGER' );?></a></li>
</ul>