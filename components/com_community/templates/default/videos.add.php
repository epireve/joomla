<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * 
 */
defined('_JEXEC') or die();
?>

<link rel="stylesheet" href="<?php echo JURI::root();?>components/com_community/assets/jquery-ui-tabs-1.8.14.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_community/assets/jquery-ui-tabs.min.js"></script>
<?php
if( $enableVideoUpload )
{
?>
<script>
joms.jQuery(function() {
	joms.jQuery( "#tabs" ).tabs();
});
</script>
<?php
}
?>




<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<?php
	if( $enableVideoUpload )
	{
	?>
	<ul>
		<li><a href="#tabs-1"><?php echo JText::_('COM_COMMUNITY_VIDEOS_LINK'); ?></a></li>		
		<li><a href="#tabs-2"><?php echo JText::_('COM_COMMUNITY_VIDEOS_UPLOAD'); ?></a></li>
	</ul>
	<?php
	}
	?>
	<div id="tabs-1">
		<div style="clear:both;display:block">
			<?php echo $linkUploadHtml;?>
		</div>

		<div style="diplay:block;margin:0 0 10px 20px;">
			<p class="video-addType-description"><?php echo JText::_('COM_COMMUNITY_VIDEOS_LINK_ADDTYPE_DESC'); ?></p>
			<ul class="video-providers">
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/youtube.png' ?>" title="YouTube" alt="YouTube"/></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/yahoo.png' ?>" title="Yahoo Video" alt="Yahoo Video" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/myspace.png' ?>" title="MySpace Video" alt="MySpace Video" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/flickr.png' ?>" title="Flickr" alt="Flickr" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/vimeo.png' ?>" title="Vimeo" alt="Vimeo" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/metacafe.png' ?>" title="Metacafe" alt="Metacafe" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/bliptv.png' ?>" title="Blip.tv" alt="Blip.tv" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/dailymotion.png' ?>" title="Dailymotion" alt="Dailymotion" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/break.png' ?>" title="Break" alt="Break" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/liveleak.png' ?>" title="Live Leak" alt="Live Leak" /></span>
				<span><img src="<?php echo JURI::root() . 'components/com_community/assets/videoicons/viddler.png' ?>" title="Viddler" alt="Viddler" /></span>
			</ul>
			<!--input class="video-action button" type="button" onclick="joms.videos.linkVideo('<?php echo $creatorType; ?>', '<?php echo $groupid; ?>');" value="<?php echo JText::_('COM_COMMUNITY_NEXT'); ?>"/-->
			<br/>
			<?php echo '<button class="button" onclick="joms.videos.submitLinkVideo();">' . JText::_('COM_COMMUNITY_VIDEOS_LINK') . '</button>'; ?>
		</div>
	</div>
	<?php
	if( $enableVideoUpload )
	{
	?>
	<div id="tabs-2">
	    <!--div style="diplay:block; width:350px; height:150px">
            <p class="video-addType-description"><?php echo JText::_('COM_COMMUNITY_VIDEOS_FILE_ADDTYPE_DESC'); ?></p>
            <ul class="video-uploadRules">
                <li class="video-uploadRule"><?php echo JText::sprintf('COM_COMMUNITY_VIDEOS_UPLOAD_SIZE_RULE', $uploadLimit); ?></li>
                <li class="video-uploadRule"><?php echo JText::_('COM_COMMUNITY_VIDEOS_UPLOAD_LENGTH_RULE'); ?></li>
                <li class="video-uploadRule"><?php echo JText::_('COM_COMMUNITY_VIDEOS_RULE_FORMAT'); ?></li>
            </ul>
			<input class="video-action button" type="button" onclick="joms.videos.uploadVideo('<?php echo $creatorType; ?>', '<?php echo $groupid; ?>');" value="<?php echo JText::_('COM_COMMUNITY_NEXT'); ?>"/>
	    </div-->
		<div style="clear:both;display:block">
			  <?php echo $videoUploadHtml;?>
			  <?php echo '<button class="button" onclick="joms.videos.submitUploadVideo();">' . JText::_('COM_COMMUNITY_VIDEOS_UPLOAD') . '</button>'; ?>
		  </div>
       
	</div>
	    <?php
		}
		?>
	
</div>
<script type="text/javascript">
    joms.jQuery( document ).ready( function(){
    	joms.privacy.init();
	});
</script>



