<?php
/**
 * @package	JomSocial
 * @subpackage 	Template 
 * @copyright	(C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
$count = 0;
?>

<div id="tag-container">
	<ul id="tag-list" class="cResetList clrfix small">
		<?php
		$last_key = end(array_keys($tags));
		foreach($tags as $key=>$row) {
		?>
		<li id="tag-<?php echo $row->id; ?>" class="<?php if($row->highlight) { echo "highlight ";} if($count > 8) { echo " tag-more"; } ?> ">
			<span class="tag-token">			
				<a class="tag-link" href="javascript:void(0);" onclick="joms.tag.list('<?php echo $this->escape( $row->tag); ?>');"><?php echo $this->escape($row->tag); ?></a>
				<?php if($edit) { ?> <a class="tag-delete" href="javascript:void(0);" onclick="joms.tag.remove('<?php echo $row->id; ?>');" title="<?php echo JText::_('COM_COMMUNITY_REMOVE_TAG'); ?>">x</a><?php } ?> 
			</span>
		</li>
		<?php $count++; } ?>		
	</ul>
	
	<?php if( $count > 8  || $edit ) { ?> 
	<div class="clrfix">
		<?php if( $count > 8 ) { ?>
			<span class="more-tag-show"><a href="javascript:void(0);" onclick="joms.tag.moreShow( '<?php echo $element;?>', '<?php echo $cid; ?>');" class="tag-btn more" title="Display more tags">+</a></span>
			<span class="more-tag-hide"><a href="javascript:void(0);" onclick="joms.tag.moreHide( '<?php echo $element;?>', '<?php echo $cid; ?>');" class="tag-btn less" title="Display less tags">-</a></span>
		<?php } ?>		
		<?php if( $edit ) { ?><span class="edit-tag"><a href="javascript:void(0);" onclick="joms.tag.edit( '<?php echo $element;?>', '<?php echo $cid; ?>');" class="tag-btn">Edit tags</a></span><?php } ?> 
	</div>	
	<?php } ?>
	
	
	
	<?php if($edit) { ?>
	<div id="tag-editor" class="tag-editor-<?php echo $element . '-'. $cid; ?> tag-editor-container">
		<div id="tag-form" class="clrfix">
			<input type="text" value="" id="tag-addbox" name="tag-addbox">
			<div class="clr"></div>
			<a href="javascript:void(0);" onclick="joms.tag.add( '<?php echo $element;?>', '<?php echo $cid; ?>' );"  class="tag-btn">Add</a>
			<a href="javascript:void(0);" onclick="joms.tag.done( '<?php echo $element;?>', '<?php echo $cid; ?>' );" class="tag-btn">Done</a>
		</div>
		
		<!-- Start recent tags list -->
		<?php if(!empty($recentTags)) { ?>
		<div>
		<ul id="tag-words" class="cResetList clrfix small">
			<?php foreach ($recentTags as $row){ ?>
			<li>
			<span class="tag-token">
				<span class="tag-add"></span>			
				<a class="tag-link"href="javascript:void(0);" onclick="joms.tag.pick( '<?php echo $element;?>', '<?php echo $cid; ?>', '<?php echo $this->escape($row); ?>');"><?php echo $this->escape($row); ?></a>
			</span>
			</li>
			<?php } ?>
		</ul>
		</div>
		<?php } ?>
		<!-- End recent tags list -->
	</div>
	<?php } ?>

</div>


