<?php 
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>

<!-- begin: .app-box -->
<div id="<?php echo 'jsapp-' . $app->id; ?>" class="app-box<?php if($app->core) echo " app-core";  ?>">
	<!-- begin: .app-box-header -->
	<div class="app-box-header">
		<div class="app-box-header">
			<h2 class="app-box-title"><?php echo $app->title; ?></h2>
			<div class="app-box-menus">
				<div class="app-box-menu toggle">
					<a class="app-box-menu-icon" href="javascript: void(0)" onclick="joms.apps.toggle('#<?php echo 'jsapp-' . $app->id; ?>');"><span class="app-box-menu-title"><?php echo JText::_('COM_COMMUNITY_VIDEOS_EXPAND');?></span></a>
				</div>
			
				<?php if( $isOwner && $app->hasConfig ){ ?>
	            <div class="app-box-menu options">
	            	<a class="app-box-menu-icon" href="javascript: void(0)" onclick="joms.apps.showSettingsWindow('<?php echo $app->id;?>','<?php echo $app->name;?>');">
	                	<span class="app-box-menu-title"><?php echo JText::_('COM_COMMUNITY_VIDEOS_OPTIONS');?></span>
	                </a>
	            </div>
	            <?php } ?>
	            
			</div>
		</div>
	</div>
	<!-- end: .app-box-header -->


	<!-- begin: .app-box-content -->
	<div class="app-box-content">
		
		<?php echo $app->data; ?>
		
	</div>
	<!-- end: .app-box-content -->
	
</div>
<!-- end: .app-box -->