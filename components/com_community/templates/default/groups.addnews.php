<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	isMine		boolean is this group belong to me
 * @params	members		An array of member objects 
 */
defined('_JEXEC') or die();
?>
<form name="addnews" method="post" action="<?php echo CRoute::getURI(); ?>">
<table class="formtable">
		
	<?php if ( $config->get( 'htmleditor' ) == 'jce' ) : ?>
	
	<tr>
		<td class="key" colspan="2">
			<label for="title" class="label" style="text-align: left;">*<?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_TITLE'); ?></label>
		</td>
	</tr>
	<tr>
		<td class="value" colspan="2">
			<input type="text" name="title" id="title" size="40" class="inputbox" style="width: 90%" />
		</td>
	</tr>	
	<tr>
		<td class="key" colspan="2">
			<label for="message" class="label" style="text-align: left;">*<?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_DESC'); ?></label>
		</td>
	</tr>
	<tr>
		<td class="value" colspan="2">
			<?php
			if( $config->getBool('allowhtml') )
			{
			?>
				<div class="clr"></div>
   				<div class="htmlTag"><?php echo JText::_('COM_COMMUNITY_HTML_TAGS_ALLOWED');?></div>
			<?php
			}?>
			<?php if( $config->get( 'htmleditor' ) ) : ?>
				<?php echo $editor->displayEditor( 'message',  $message , '95%', '450', '10', '20' , false ); ?>
			<?php else : ?>
				<textarea rows="3" cols="40" name="message" id="message" class="inputbox" style="width: 90%"></textarea>
			<?php endif; ?>
		</td>
	</tr>
		
	<?php else : ?>
	
	<tr>
		<td class="key">
			<label for="title" class="label">*<?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_TITLE'); ?></label>
		</td>
		<td class="value">
			<input type="text" name="title" id="title" size="40" class="inputbox" style="width: 90%" />
		</td>
	</tr>	
	<tr>
		<td class="key">
			<label for="message" class="label">*<?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_DESC'); ?></label>
		</td>
		<td class="value">
        <script type="text/javascript">
        function saveContent()
        {
			<?php echo $editor->saveText( 'message' ); ?>
			return true;
		}
        </script>
		<?php if( $config->get( 'htmleditor' ) == 'none' && $config->getBool('allowhtml')) { ?>
			<div class="htmlTag"><?php echo JText::_('COM_COMMUNITY_HTML_TAGS_ALLOWED'); ?></div>
		<?php } ?>
			<?php if( $config->get( 'htmleditor' ) ) : ?>
				<?php echo $editor->displayEditor( 'message',  $message , '95%', '450', '10', '20' , false ); ?>
			<?php else : ?>
				<textarea rows="3" cols="40" name="message" id="message" class="inputbox" style="width: 90%"></textarea>
			<?php endif; ?>

		</td>
	</tr>
	<?php endif; ?>
	
	<tr>
		<td class="key"></td>
		<td class="value">
			<span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span>
		</td>
	</tr>
	
	<tr>
		<td class="key"></td>
		<td class="value">
			<input type="submit" name="submit" value="<?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_ADD'); ?>" class="button" onclick="saveContent();" />
			<input type="button" name="cancel" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON'); ?>" onclick="javascript:history.go(-1);return false;" class="button" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</td>
	</tr>
</table>
</form>
