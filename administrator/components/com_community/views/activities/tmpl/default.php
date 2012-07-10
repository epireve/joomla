<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_community" method="post" name="adminForm">
<script type="text/javascript" language="javascript">
Joomla.submitbutton = function(action){
	submitbutton( action );
}
  
function submitbutton(action)
{
	if( action == 'purge' )
	{
		if(confirm('<?php echo JText::_('COM_COMMUNITY_ACTIVITIES_PURGE_ACTIVITIES');?>'))
		{
			submitform( action );
		}
	}
	else
	{
		submitform( action );
	}
}
</script>
<table class="adminform" cellpadding="5">
	<tr>
		<td width="95%">
			<?php echo JText::_('COM_COMMUNITY_ACTIVITIES_FILTER_BY_ACTOR'); ?><input type="text" name="actor" onchange="submitform();" /><button onclick="submitform();"><?php echo JText::_('COM_COMMUNITY_GO_BUTTON');?></button>
		</td>
		<td nowrap="nowrap" align="right">
			<select name="app" onchange="submitform();">
				<option value="none"<?php echo ( $this->currentApp == 'none' ) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_ACTIVITIES_SELECT_APPLICATION');?></option>
				<?php
				for( $i = 0; $i < count( $this->filterApps ); $i++ )
				{
				?>
					<option value="<?php echo $this->filterApps[ $i ]->app;?>"<?php echo ( $this->currentApp === $this->filterApps[ $i ]->app ) ? ' selected="selected"' : '';?>><?php echo $this->filterApps[ $i ]->app; ?></option>
				<?php
				}
				?>
			</select>

			<select name="archived" onchange="submitform();">
				<option value="0"<?php echo ( $this->currentArchive == 0 ) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_ACTIVITIES_SELECT_STATE');?></option>
				<option value="1"<?php echo ($this->currentArchive == 1 ) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_ACTIVITIES_ACTIVE');?></option>
				<option value="2"<?php echo ($this->currentArchive == 2 ) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_ACTIVITIES_ARCHIVED');?></option>
			</select>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%"><?php echo JText::_('COM_COMMUNITY_NUMBER'); ?></th>
			<th width="1%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->activities ); ?>);" />
			</th>
			<th style="text-align: left;">
				<?php echo JText::_('COM_COMMUNITY_TITLE'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_COMMUNITY_CREATED');?>
			</th>
		</tr>
	</thead>
<?php
	if( $this->activities )
	{
		$i	= 0;
		foreach($this->activities as $row )
		{
			
			$row->title	= CString::str_ireplace('{target}', $this->_getUserLink( $row->target ) , $row->title);
			$row->title	= preg_replace('/\{multiple\}(.*)\{\/multiple\}/i', '', $row->title);
			$search		= array('{single}','{/single}');
			$row->title	= CString::str_ireplace($search, '', $row->title);
			$row->title	= CString::str_ireplace('{actor}', $this->_getUserLink( $row->actor ) , $row->title);
			$row->title	= CString::str_ireplace('{app}', $row->app, $row->title); 
			
			//strip out _QQQ_
			$row->title	= CString::str_ireplace('_QQQ_','', $row->title);
			preg_match_all("/{(.*?)}/", $row->title, $matches, PREG_SET_ORDER);
			if(!empty( $matches )) 
			{
				$params = new CParameter( $row->params );
				foreach ($matches as $val) 
				{	
					
					$replaceWith = $params->get($val[1], null);
					//if the replacement start with 'index.php', we can CRoute it
					if( strpos($replaceWith, 'index.php') === 0){
						$replaceWith = JURI::root().$replaceWith;
					}
					
					if( !is_null( $replaceWith ) ) 
					{
						$row->title	= CString::str_ireplace($val[0], $replaceWith, $row->title);
					}
				}
			}			
?>
	<tr>
		<td align="center"><?php echo ( $i + 1 ); ?></td>
		<td><?php echo JHTML::_('grid.id', $i++, $row->id); ?></td>
		<td><?php echo $row->title;?></td>
		<td align="center"><?php echo $row->created;?></td>
	</tr>
<?php
		}
?>
	<tfoot>
	<tr>
		<td colspan="5">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
<?php
	}
	else
	{
?>
	<tr>
		<td colspan="5" align="center"><?php echo JText::_('COM_COMMUNITY_ACTIVITIES_NO_ACTIVITIES_YET');?></td>
	</tr>
<?php
	}
?>
</table>
<input type="hidden" name="view" value="activities" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="activities" />
<input type="hidden" name="boxchecked" value="0" />
</form>