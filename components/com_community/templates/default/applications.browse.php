<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	applications	An array of applications object
 * @param	pagination		JPagination object 
 */
defined('_JEXEC') or die();
?>

<?php
if( $applications )
{
	foreach( $applications as $application )
	{
?>
		
<div class="app-item <?php echo $this->escape($application->name); ?>">

	<div class="app-avatar">
		<img src="<?php echo $application->appFavicon; ?>" alt="<?php echo $this->escape($application->title); ?>" />
	</div>

	<h3><?php echo $application->title; ?></h3>
	
	<div class="app-item-description">
		<?php echo $this->escape($application->description); ?>
	</div>
	
	<div class="app-item-details">
		<span style="margin-right: 10px;"><?php echo JText::_('COM_COMMUNITY_APPS_COLUMN_DATE'); ?>: <strong><?php echo $application->creationDate; ?></strong></span>
		<span style="margin-right: 10px;"><?php echo JText::_('COM_COMMUNITY_APPS_COLUMN_VERSION'); ?>: <strong><?php echo $application->version; ?></strong></span>
		<?php if($this->params->get('appsShowAuthor')) { ?>
			<span><?php echo JText::_('COM_COMMUNITY_APPS_COLUMN_AUTHOR'); ?>: <strong><?php echo $application->author; ?></strong></span>
		<?php } ?>	
	</div>
	
	<?php if( !$application->added && !$application->coreapp ) { ?>
		<a class="added-button" href="javascript:void(0);" onclick="joms.apps.add('<?php echo $this->escape($application->name); ?>')" title="<?php echo JText::_('COM_COMMUNITY_APPS_ADD_BUTTON'); ?>">
		   	<?php echo JText::_('COM_COMMUNITY_APPS_LIST_ADD'); ?>
	   	</a>	
	<?php } else { ?>
	
	<span class="added-ribbon">
	   	<?php echo JText::_('COM_COMMUNITY_APPS_LIST_ADDED'); ?>
	</span>	
	
	<?php } ?>
</div>	

<?php 
	}
}
else
{
?>
<div class="app-item">
	<div class="app-item-description"><?php echo JText::_('COM_COMMUNITY_NO_APPLICATIONS_INSTALLED');?></div>
</div>
<?php
}
?>
<div class="pagination-container">
	<?php echo $pagination->getPagesLinks(); ?>
</div>