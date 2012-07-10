<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @param	$bulletins	An array of discussions object
 * @param	$groupId		The group id
 */
defined('_JEXEC') or die();

if( $bulletins )
{
	for($i = 0; $i < count( $bulletins ); $i++ )
	{
		$row	=& $bulletins[$i];
?>
	<div id="bulletin_<?php echo $row->id; ?>" class="groups-news-row">
		<div class="groups-news-title">
			<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=viewbulletin&groupid=' . $groupId . '&bulletinid=' . $row->id);?>">
				<?php echo $row->title; ?>
			</a>
		</div>
		<div class="groups-news-meta small">
			<span class="group-news-date">
				<?php echo JHTML::_('date' , $row->date, JText::_('DATE_FORMAT_LC')); ?>
			</span>
			<span class="group-news-author">
				<?php echo JText::sprintf( 'COM_COMMUNITY_BULLETIN_CREATED_BY' , $row->creator->getDisplayName() , CRoute::_('index.php?option=com_community&view=profile&userid=' . $row->creator->id ) ); ?>
			</span>
		</div>
		<?php
            // Only display news item for first item
            if( $i == 0 )
            {
        ?>
        <div class="groups-news-text">
            <?php echo $row->message;?>
        </div>
        <?php
            }
        ?>
	</div>


<?php
	} //end for
} // end if
else
{
?>
	<div class="cNotice cNotice-App"><?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_NOITEM'); ?></div>
<?php
}	
?>