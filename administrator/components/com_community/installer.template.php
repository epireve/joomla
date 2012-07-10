<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class communityInstallerTemplate
{
	var $title;
	
	function __construct()
	{
		$this->title	= '';
	}
	
	function getHTML($page='', $params='')
	{
		$page	= '_'.$page;
		return $this->$page($params);
	}
	
	function _welcome($params)
	{
		$this->pageTitle 	= JText::_('COM_COMMUNITY_INSTALLATION_JOMSOCIAL');
		
		ob_start();
		?>
		<style type="text/css">
			.button1-left {
				background: transparent url(<?php echo JURI::root(); ?>administrator/templates/<?php echo DEFAULT_TEMPLATE_ADMIN;?>/images/j_button1_left.png) no-repeat scroll 0 0;
				float: left;
				margin-left: 5px;
				margin-top: -5px;
				cursor: pointer;
			}
			.button1-left .next {
				background: transparent url(<?php echo JURI::root(); ?>administrator/templates/<?php echo DEFAULT_TEMPLATE_ADMIN;?>/images/j_button1_next.png) no-repeat scroll 100% 0;
				float: left;
				cursor: pointer;
			}
			.button-next {
				border: 0;
				background: none;
				font-size: 11px;
				height: 26px;
				line-height: 24px;
				padding-right: 30px;
				cursor: pointer;
			}
			#toolbar-box,
			#submenu-box,
			#header-box {
			#	display: none;
			#}
			</style>		
	
			<script type="text/javascript">
			var dom = document.getElementById('stepLast');
			dom.removeAttribute('class');
			dom.setAttribute('class', 'steps');	
			dom = document.getElementById('stepFirst');
			dom.setAttribute('class', 'steps on');		
			</script>
                        
			 <table width="100%" border="0">
				<tr>
					<td>				
						<div style="font-weight:700;">
							<h2>Another great component brought to you by JomSocial Team</h2>
						</div>
						<p>
							If you require professional support just head on to the forums at 
							<a href="http://www.jomsocial.com/forum/" target="_blank">
							http://www.jomsocial.com/forum
							</a>
							For developers, you can browse through the documentations at 
							<a href="http://www.jomsocial.com/support/docs.html" target="_blank">http://www.jomsocial.com/support/docs.html</a>
						</p>
						<p>
							If you found any bugs, just drop us an email at support@jomsocial.com
						</p>					
					</td>
				</tr>
				<tr>
					<td>				
						<div style="font-weight:700; margin-bottom:2px;"></div>
						<div>
							<?php echo $params; ?>
						</div>					
					</td>
				</tr>
			</table> 
                    
			<style type="text/css">
			#timer {
				display: none;
			}
			</style>
		<?php
		$html = ob_get_contents();
		@ob_end_clean();
		
		return $html;
	}
	
	function _checkingRequirement($params)
	{
		$this->pageTitle 	= JText::_('COM_COMMUNITY_INSTALLATION_CHECKING_REQUIREMENT');
		
		ob_start();
		?>
		<table width="100%" border="0">
		</table>
		<?php
		$html = ob_get_contents();
		@ob_end_clean();
		
		return $html;
	}
	
	function cInstallDraw($output, $step, $title, $status, $install= 1, $substep=0)
	{
		$html 		= '';
		$version	= communityInstallerHelper::getVersion();
		
		$html .= '
	<script type="text/javascript">
	/* jQuery("span.version").html("Version ' . $version . '"); */
	var DOM = document.getElementById("element-box");
	DOM.setAttribute("id","element-box1");
	</script>
	
	<style type="text/css">
	/**
	 * Reset Joomla! styles
	 */
	div.t, div.b {
		height: 0;
		margin: 0;
		background: none;
	}
	
	body #content-box div.padding {
		padding: 0;
	}
	
	body div.m {
		padding: 0;
		border: 0;
	}
	
	.button1-left {
		background: transparent url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_button1_left.png) no-repeat scroll 0 0;
		float: left;
		margin-left: 5px;
		cursor: pointer;
	}
	
	.button1-left .next {
		background: transparent url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_button1_next.png) no-repeat scroll 100% 0;
		float: left;
		cursor: pointer;
	}
	
	.button-next,
	.button-next:focus {
		border: 0;
		background: none;
		font-size: 11px;
		height: 26px;
		line-height: 24px;
		cursor: pointer;
		font-weight: 700;
	}
	
	h1.steps{
		color:#0B55C4;
		font-size:20px;
		font-weight:bold;
		margin:0;
		padding-bottom:8px;
	}
	
	div.steps {
		font-size: 12px;
		font-weight: bold;
		padding-bottom: 12px;
		padding-top: 10px;
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_divider.png) 0 100% repeat-x;
	}
	
	div.on {
		color:#0B55C4;
	}
	
	#toolbar-box,
	#submenu-box,
	#header-box {
		display: none;
	}
	
	div#cElement-box div.m {
		padding: 5px 10px;
	}
	
	div#cElement-box div.t, div#cElement-box div.b {
		height: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	
	div#cElement-box div.m {
		padding: 0 8px;
		border:1px solid #ccc;
	}
	
	div#cElement-box div.t {
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_border.png) 0 0 repeat-x;
	}
	
	div#cElement-box div.t div.t {
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_tr_light.png) 100% 0 no-repeat;
	}
	
	div#cElement-box div.t div.t div.t {
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_tl_light.png) 0 0 no-repeat;
	}
	
	div#cElement-box div.b {
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_border.png) 0 100% repeat-x;
	}
	
	div#cElement-box div.b div.b {
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_br_light.png) 100% 0 no-repeat;
	}
	
	div#cElement-box div.b div.b div.b {
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_bl_light.png) 0 0 no-repeat;
	}
	#stepbar {
		float: left;
		width: 170px;
	}
	
	#stepbar div.box {
		background: url('.JURI::root().'administrator/components/com_community/box.jpg) 0 0 no-repeat;
		height: 140px;
	}
	
	#stepbar h1 {
		margin: 0;
		margin-top:5px;
		padding-bottom: 8px;
		font-size: 20px;
		color: #0B55C4;
		font-weight: bold;
		background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_divider.png) 0 100% repeat-x;
	}
	
	div#stepbar {
	  background: #f7f7f7;
	}
	
	div#stepbar div.t {
	  background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_border.png) 0 0 repeat-x;
	}
	
	div#stepbar div.t div.t {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_tr_dark.png) 100% 0 no-repeat;
	}
	
	div#stepbar div.t div.t div.t {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_tl_dark.png) 0 0 no-repeat;
	}
	
	div#stepbar div.b {
	  background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_border.png) 0 100% repeat-x;
	}
	
	div#stepbar div.b div.b {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_br_dark.png) 100% 0 no-repeat;
	}
	
	div#stepbar div.b div.b div.b {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_bl_dark.png) 0 0 no-repeat;
	}
	
	div#stepbar div.t, div#stepbar div.b {
		margin: 0;
		overflow: hidden;
		padding: 0;
	}
	
	div#stepbar div.m,
	div#cToolbar-box div.m {
		padding: 0 8px;
		border: 1px solid #ccc;
	}
	
	div#cToolbar-box {
		background: #f7f7f7;
		position: relative;
	}
	
	div#cToolbar-box div.m {
		padding: 0;
		height: 30px;
	}
	
	div#cToolbar-box {
		background: #f4f4f4;
	}
	
	div#cToolbar-box div.t,
	div#cToolbar-box div.b {
		height: 0;
	}
	
	div#cToolbar-box span.title {
		color: #0B55C4;
		font-size: 20px;
		font-weight: bold;
		line-height: 30px;
		padding-left: 10px;
	}
	
	div#cToolbar-box div.t {
	  background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_border.png) 0 0 repeat-x;
	}
	
	div#cToolbar-box div.t div.t {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_tr_med.png) 100% 0 no-repeat;
	}
	
	div#cToolbar-box div.t div.t div.t {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_tl_med.png) 0 0 no-repeat;
	}
	
	div#cToolbar-box div.b {
	  background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_border.png) 0 100% repeat-x;
	}
	
	div#cToolbar-box div.b div.b {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_br_med.png) 100% 0 no-repeat;
	}
	
	div#cToolbar-box div.b div.b div.b {
	   background: url('.JURI::root().'administrator/templates/'.DEFAULT_TEMPLATE_ADMIN.'/images/j_crn_bl_med.png) 0 0 no-repeat;
	}
	
	.cInstaller-border {
		border: 1px solid #ccc;
		padding: 6px;
		-moz-border-radius: 10px;
		webkit-border-radius: 10px;
		border-radius: 10px;
	}
	</style>
	
	
	<table cellpadding="6" width="100%">
		<tr>
			<td rowspan="2" valign="top" width="10%">' . $this->cInstallDrawSidebar($step) . '</td>
			<td valign="top" height="30">' . $this->cInstallDrawTitle($title, $step, $status, $install, $substep) . '</td>
		</tr>
		<tr>
			<td valign="top">
				<div id="cElement-box" class="cInstaller-border">
					<div style="height: 529px; padding: 0 10px;">
					'. $output . '
					</div>
				</div>
			</td>
		</tr>
	</table>';
	   		
		echo $html;
	}
	
	function cInstallDrawSidebar($activeSteps)
	{
		ob_start();
		?>
		
		<div id="stepbar" class="cInstaller-border">
				<h1 class="steps">Steps</h1>
				<div id="stepFirst" class="steps<?php if($activeSteps == 1) echo " on"; ?>">1 : Welcome</div>
				<div class="steps<?php if($activeSteps == 2) echo " on"; ?>">2 : Checking Requirement</div>
				<div class="steps<?php if($activeSteps == 3) echo " on"; ?>">3 : Installing Backend</div>
				<div class="steps<?php if($activeSteps == 4) echo " on"; ?>">4 : Installing Ajax</div>
				<div class="steps<?php if($activeSteps == 5) echo " on"; ?>">5 : Installing Frontend</div>
				<div class="steps<?php if($activeSteps == 6) echo " on"; ?>">6 : Installing Templates</div>
				<div class="steps<?php if($activeSteps == 7) echo " on"; ?>">7 : Preparing Database</div>
				<div class="steps<?php if($activeSteps == 8) echo " on"; ?>">8 : Updating Database</div>
				<div class="steps<?php if($activeSteps == 9) echo " on"; ?>">9 : Installing Plugins</div>
				<div class="steps<?php if($activeSteps == 100) echo " on"; ?>">10 : Installing Zend Framework</div>
				<div id="stepLast" class="steps<?php if($activeSteps == 0) echo " on"; ?>">11 : Done!</div>	
				<div class="box"></div>
	  	</div>
	
		<?php
		 $html = ob_get_contents();
		 ob_end_clean();
		 return $html;
	}
	
	function cInstallDrawTitle($title, $step, $status, $install = 1, $substep = 0) 
	{
		ob_start();
		?>
			<div id="cToolbar-box" class="cInstaller-border">
					<span class="title">
						<?php echo $title; ?>
					</span>

					<div style="position: absolute; top: 8px; right: 10px;">
						<div id="communityContainer">
							<?php
							if($status)
							{
							?>
							<form action="?option=com_community" method="POST" name="installform" id="installform">
								<input type="hidden" name="install" value="<?php echo $install; ?>"/>
								<input type="hidden" name="step" value="<?php echo $step; ?>"/>
								<input type="hidden" name="substep" value="<?php echo $substep; ?>"/>
								<div class="button1-left">
									<div id="div-button-next" class="next" onclick="document.installform.submit();">
										<input type="submit" id="input-button-next" class="button-next" onclick="" value="Next"/> <span style="margin-right: 30px;" id="timer"></span>
									</div>
								</div>
							</form>
							<?php
							}
							?>
						</div>
					</div>
	  	</div>	
	
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}