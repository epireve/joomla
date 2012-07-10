<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	groups		Array	Array of groups object
 * @param	total		integer total number of groups
 * @param	user		CFactory User object 
 */
defined('_JEXEC') or die();
?>
<div class="cModule">
	<h3><span><?php echo JText::_('COM_COMMUNITY_GROUPS'); ?></span></h3>
	<ul class="cResetList cThumbList clrfix">
	<?php
	if (count($groups) > 0)
	{
		for($i = 0; ($i < 12) && ($i < count($groups)); $i++)
		{	
			$row	= $groups[$i];
	?>
	<li>
		<a href="<?php echo $row->getLink( true );?>">
			<img title="<?php echo $this->escape($row->name);?>" alt="<?php echo $this->escape($row->name);?>" src="<?php echo $row->getThumbAvatar(); ?>" class="cAvatar cAvatar-sidebar jomNameTips"/>
		</a>
	</li>
	<?php
		}
	}
	else
	{?>
		<li><?php echo JText::_('COM_COMMUNITY_GROUPS_NO_JOINED_YET');?></li>
	<?php
	}
	?>
</ul>
	<div style="clear: both;"></div>
	<div class="app-box-footer">
		<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=mygroups&userid=' . $user->id ); ?>">
			<span><?php echo JText::_('COM_COMMUNITY_GROUPS_VIEW_ALL'); ?></span>
			<span>(<?php echo $total;?>)</span>
		</a>
	</div>
	
</div>
	
