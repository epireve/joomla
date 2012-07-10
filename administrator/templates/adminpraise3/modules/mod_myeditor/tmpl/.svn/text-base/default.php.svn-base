<?php defined('_JEXEC') or die('Restricted access');
$myEditor = JFactory::getEditor();
$user = JFactory::getUser();
?>

<script type="text/javascript">
	function updateEditor(editor)
	{
		var myXHR=new XHR({method:'post', onSuccess:showUpdateSuccess}).send('index.php', 'option=com_users&task=save&id=<?php echo $user->get('id'); ?>&sendEmail=0&<?php echo JUtility::getToken(); ?>=1&username=<?php echo $user->get('username');?>&params[editor]='+editor+'&tmpl=COMPONENT');
		colorSelectBox(true);
	}

	function showUpdateSuccess(req)
	{
		setTimeout('colorSelectBox(false)', 1000);
	}

	function colorSelectBox(set)
	{
		var editor=document.getElementById('myeditor_selection');
		if (set)
			editor.setAttribute("style", "border-color: #3AC521");
		else
			editor.removeAttribute("style");
	}
</script>

<ul id="ap-myeditor">
	<li class="parent" id="myeditor_selection">
		<a><span class="myeditor-icon"><span class="myeditor-title"><?php echo JText::_( 'EDITOR' ) . " " . $myEditor->_name;?></span></a>
		<div class="submenu">
		<ul>
		<?php foreach ($editors as $editor)
				echo '<li><a href="#" onClick="javascript:updateEditor(\''.$editor->element.'\')" rel="'.$editor->element.'">'.$editor->text.'</a></li>';
		?>
		</ul>
		</div>
	</li>
</ul>

