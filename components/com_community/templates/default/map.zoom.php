<?php
/**
 * @package	JomSocial
 * @subpackage 	Template 
 * @copyright	(C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>
<div class="cMap" style="display: block;">
	<div class="cMapFade" style="width:<?php echo $width;?>px;height:<?php echo $height;?>px">
		<div class="cMapHeatzone" style="top: <?php echo (($height - 40)/2)-20 ?>px; left: <?php echo (($width - 30)/2) ?>px;">&nbsp;</div>
		<div class="cMapFiller"></div>
		<img src="http://maps.google.com/maps/api/staticmap?center=<?php echo urlencode($address); ?>&amp;zoom=14&amp;size=<?php echo $width; ?>x<?php echo $height; ?>&amp;sensor=false&amp;markers=color:red|<?php echo urlencode($address); ?>">
		<img src="http://maps.google.com/maps/api/staticmap?center=<?php echo urlencode($address); ?>&amp;zoom=5&amp;size=<?php echo $width; ?>x<?php echo $height; ?>&amp;sensor=false&amp;markers=color:red|<?php echo urlencode($address); ?>" style="display: block;">
		<img src="http://maps.google.com/maps/api/staticmap?center=<?php echo urlencode($address); ?>&amp;zoom=2&amp;size=<?php echo $width; ?>x<?php echo $height; ?>&amp;sensor=false&amp;markers=color:red|<?php echo urlencode($address); ?>" style="display: block;">
	</div>
	<small class="cMapLoc"><span><?php echo $address; ?></span></small>
	<small class="cMapBigger"><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $address; ?>" target="_blank"><?php echo JText::_('COM_COMMUNITY_VIEW_LARGER_MAP');?></a></small>
	<div class="clear"></div>
</div>