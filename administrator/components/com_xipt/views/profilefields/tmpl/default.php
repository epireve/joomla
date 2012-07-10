<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');
?>

<form action="<?php echo JURI::base();?>index.php?option=com_xipt" method="post" name="adminForm" id="adminForm">
<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="1%"><?php echo XiptText::_( '#' ); ?></th>
			<th width="29%" class="title"><?php echo XiptText::_( 'FIELD_NAME' ); ?></th>
			<?php foreach($this->categories as $catIndex => $catInfo) : ?>
				<?php $catName = $catInfo['name'];?>	
				<th width="15%" class="title">
				<?php echo XiptText::_($catName) ; ?>
				</th>
			<?php endforeach;?>
		</tr>
	</thead>
	
	<?php 
		$count = 0; 
		$i  = 0; 
		
		if(!empty($this->fields)) :
		foreach($this->fields as $field) :
			?><tr class="row<?php echo $i%2;?>" id="rowid<?php echo $field->id;?>"><?php 
			if($field->type != "group") :
				++$i;	?>				
				<td><?php echo $i;?></td>
				<td>
					<span class="editlinktip" title="<?php echo $field->name; ?>" id="name<?php echo $field->id;?>">
					<?php $link = XiptRoute::_('index.php?option=com_xipt&view=profilefields&task=edit&id='.$field->id, false); ?>
						&nbsp;&nbsp;|_ <A HREF="<?php echo $link; ?>"><?php echo $field->name; ?></A>
					</span>
				</td>
				
				<?php else :?>				
				<td><?php echo ""; ?></td>
				<td>
					<span class="editlinktip" title="<?php echo $field->name; ?>" id="name<?php echo $field->id;?>">
					<?php $link = XiptRoute::_('index.php?option=com_xipt&view=profilefields&task=edit&id='.$field->id, false); ?>				
						<?php echo XiptText::_('GROUP');?> :- <A HREF="<?php echo $link; ?>"><?php echo $field->name; ?></A>
					</span>
				</td>
			<?php endif;?>
				
			<?php foreach($this->categories as $catIndex => $catInfo) : ?>
				<?php $controlName = $catInfo['controlName'];	?>
				<td align="center">
					<span id="<?php echo "$controlName"."$field->id";?>" onclick="$('typeOption').style.display = 'block';$(this).style.display = 'none';">
					<?php echo XiptHelperProfilefields::getProfileTypeNames( $field->id,$catIndex);	?>
					</span>
				 </td>
				<?php endforeach; ?>				
			</tr>
		<?php endforeach;?>
		<?php endif;?>		
</table>

<div class="clr"></div>

	<input type="hidden" name="option" value="com_xipt" />
	<input type="hidden" name="view" value="profilefields" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php 
