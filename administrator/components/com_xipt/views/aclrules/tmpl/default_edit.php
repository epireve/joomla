<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');
?>

<script language="javascript" type="text/javascript">

<?php 
		If(!XIPT_JOOMLA_15)
		{
			?>
		/** FOR JOOMLA1.6++ **/
		Joomla.submitbutton=function(action) {
			submitbutton(action);
		}
  <?php }?>
		
	function submitbutton(pressbutton) {
		if (pressbutton == "cancel") {
			submitform(pressbutton);
			return;
		}
		// validation
		var form = document.adminForm;
		if (form.rulename.value == "") {
			alert( "<?php echo XiptText::_( 'RULE_MUST_HAVE_A_NAME', true ); ?>" );
		} else {
			submitform(pressbutton);
		}
	}
</script>

<div id="JSPT">
<div class="xippElements">
	<form action="index.php" method="post" name="adminForm">
		<div class="elementColumn">
			<fieldset class="adminform">
				<legend><?php echo XiptText::_( 'DETAILS' ); ?></legend>
			
				<div class="elementParams">
					<div class="paramTitle">
						<label for="name" class="hasTip" title="<?php echo XiptText::_('NAME'); ?>::<?php echo XiptText::_('ACL_NAME_DESC'); ?>">
							<?php echo XiptText::_( 'NAME' ); ?>
						</label>
					</div>
					<div class="paramValue"><label><?php echo XiptText::_($this->aclruleInfo['aclname']); ?></label></div>
				</div>
				
				<div class="elementParams">
					<div class="paramTitle">
						<label for="featurename" class="hasTip" title="<?php echo XiptText::_('RULE_NAME'); ?>::<?php echo XiptText::_('RULE_NAME_DESC'); ?>">
							<?php echo XiptText::_( 'RULE_NAME' ); ?>
						</label>
					</div>
					<div class="paramValue"><input class="text_area" type="text" name="rulename" id="rulename" size="35" value="<?php echo $this->aclruleInfo['rulename']; ?>" /></div>
				</div>
				
				<div class="elementParams">
					<div class="paramTitle">
						<label class="hasTip" title="<?php echo XiptText::_('PUBLISHED'); ?>::<?php echo XiptText::_('ACL_PUBLISHED_DESC'); ?>">
							<?php echo XiptText::_( 'PUBLISHED' ); ?>
						</label>
					</div>
					<div class="paramValue">
						<?php echo JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $this->aclruleInfo['published'] ); ?>
					</div>
				</div>
				

				
			</fieldset>
			
			<fieldset class="adminform">
				<legend><?php echo XiptText::_( 'RULE_PARAMETER' ); ?></legend>
				<?php
					if($this->aclParamsHtml)
						echo $this->aclParamsHtml;
					else
						echo "<div style=\"text-align: center; padding: 5px; \">".XiptText::_('THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM')."</div>" ;
				?>
			</fieldset>
		</div>
		
		<div class="elementColumn">
			<fieldset class="adminform">
				<legend><?php echo XiptText::_( 'GENERAL_PARAMETERS' ); ?></legend>
				<?php
					if ($this->coreParamsHtml)
						echo $this->coreParamsHtml;
					else
						echo "<div style=\"text-align: center; padding: 5px; \">".XiptText::_('THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM')."</div>"
					?>
				</fieldset>
				<br />
				<?php if($this->helpMsg) :?>	
					<fieldset class="adminform">
						<legend><?php echo XiptText::_('EXAMPLE'); ?></legend>
						<div style=" background-color:#F9F9F9; padding:5px;">			
							<?php echo $this->helpMsg; ?>
						</div>						
					</fieldset>	
				<?php endif;?>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="option" value="com_xipt" />
		<input type="hidden" name="id" value="<?php echo $this->aclruleInfo['id'];?>" />
		<input type="hidden" name="aclname" value="<?php echo $this->aclruleInfo['aclname'];?>" />
		<input type="hidden" name="cid[]" value="" />
		<input type="hidden" name="view" value="<?php echo JRequest::getCmd( 'view' , 'aclrules' );?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		
	</form>
</div>
</div>
<?php
