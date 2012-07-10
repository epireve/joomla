<?php
/**
 * @category 	Template
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

$script = '
<script type="text/javascript">
joms.jQuery(document).ready( function() {
	joms.jQuery("#community-wrap ul.submenu li a:last").css("border-right", "0");
});
</script>';

$mainframe =& JFactory::getApplication();
$document =& JFactory::getDocument();
if($document->getType() == 'html'){
	$document->addCustomTag($script);
}
?>

<div id="cSubmenu">
	<ul class="submenu-items cResetList clrfix">
	<?php
	foreach($submenu as $menu)
	{
		$menuClass='';
		if( isset($menu->action) && ($menu->action) )
		{
			$menuClass .= 'action ';
		}
		if( isset($menu->childItem) && $menu->childItem )
		{
			$menuClass .= 'hasChildItem ';
		}

		$link=''; $linkClass=''; $onclick='';
		if( isset($menu->onclick) && !empty($menu->onclick) )
		{
			$link    = 'javascript: void(0);';
			$onclick =  $menu->onclick;
		} else {
			$link    = CRoute::_($menu->link);

			if( JString::strtolower( $menu->view ) == JString::strtolower($view) &&
			    JString::strtolower( $menu->task ) == JString::strtolower($task) )
			{
				$linkClass .= ' active';
			}
		}
	?>
		<li class="submenu-item <?php echo $menuClass ?>">
			<a href="<?php echo $link ?>"
			   class="<?php echo $linkClass ?>"
			   onclick="<?php echo $onclick ?>"><?php echo $menu->title ?></a>
			<?php echo $menu->childItem ?>
		</li>		
	<?php
	}
	?>
	</ul>
</div>