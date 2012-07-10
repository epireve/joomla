<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	posted	boolean	Determines whether the current state is a posted event.
 * @param	search	string	The text that the user used to search 
 */
defined('_JEXEC') or die();
?>
<div id="community-groups-wrap">
	<!--SEARCH FORM-->
	<div class="group-search-form">
	<form name="jsform-groups-search" method="get" action="">
		<?php if(!empty($beforeFormDisplay)){ ?>
			<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
				<?php echo $beforeFormDisplay; ?>
			</table>
		<?php } ?>
		
		<input type="text" class="inputbox" name="search" value="<?php echo $this->escape($search); ?>" size="50" />
		<?php if(!empty($afterFormDisplay)){ ?>
			<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
				<?php echo $afterFormDisplay; ?>
			</table>
		<?php } ?>
		<?php
			echo JText::_('COM_COMMUNITY_SEARCH_FOR');

			foreach ($searchLinks as $key => $value) {
		?>
			<a href="<?php echo $value; ?>"><?php echo $key; ?></a>
		<?php
			}
		?>
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" value="com_community" name="option" />
		<input type="hidden" value="groups" name="view" />
		<input type="hidden" value="search" name="task" />
		<input type="hidden" value="<?php echo CRoute::getItemId();?>" name="Itemid" />
                <div class="formtable" style="padding: 5px 0;">
                    <?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY');?>
                    <select name="catid" id="catid" class="required inputbox jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_TIPS');?>">
                        <option value="0" selected></option>
                        <?php
                            foreach( $categories as $category )
                            {
                            ?>
                                    <option value="<?php echo $category->id; ?>" <?php if( $category->id == $catId ) { ?>selected<?php } ?>><?php echo JText::_( $this->escape($category->name) ); ?></option>
                            <?php
                            }
                            ?>
                    </select>
                </div>
		<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH_BUTTON');?>" class="button" />
	</form>
	</div>
	<!--SEARCH FORM-->
	<?php
	if( $posted )
	{
	?>
		<!--SEARCH DETAIL-->
		<div class="group-search-detail">
			<span class="search-detail-left">
				<?php echo JText::sprintf( 'COM_COMMUNITY_GROUPS_SEARCH_RESULT' , $search ); ?>
			</span>
			<span class="search-detail-right">
				<?php echo JText::sprintf( (CStringHelper::isPlural($groupsCount)) ? 'COM_COMMUNITY_GROUPS_SEARCH_RESULT_TOTAL_MANY' : 'COM_COMMUNITY_GROUPS_SEARCH_RESULT_TOTAL' , $groupsCount ); ?>
			</span>
			<div style="clear:both;"></div>
		</div>
		<!--SEARCH DETAIL-->
		<?php echo $groupsHTML; ?>
	<?php
	}
	?>
</div>