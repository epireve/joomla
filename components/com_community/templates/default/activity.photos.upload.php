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
<ul class ="cDetailList clrfix">
	
	<?php
	for($i = 0; ($i < CPhotos::ACTIVITY_SUMMARY_ITEM_COUNT && $i < count($acts)); $i++)
	{
		$aact 		= $acts[$i];
		$aparam		= new CParameter( $aact->params );
			
		$aphotoid  	= $aparam->get('photoid'); 
		$photo		= JTable::getInstance( 'Photo' , 'CTable' );
		$photo->load( $aphotoid );
		
	?>
	<li class="avatarWrap">
		<a href="<?php echo $photo->getPhotoLink();?>"><img alt="<?php echo $this->escape($photo->caption);?>" src="<?php echo $photo->getThumbURI();?>" class="cAvatar cAvatar-Large <?php if($stream){echo " onstream"; }?>"/></a>
	</li>
	<?php
	}
	?>
</ul>
<!-- details -->
<div class="detailWrap alpha small"><?php echo JString::substr($album->description, 0, $config->getInt('streamcontentlength'));?></div>
<!-- details -->