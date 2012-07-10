<?php
/**
 * @package	JomSocial
 * @subpackage 	Template 
 * @copyright	(C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>

<div id="tag-list-container">
<?php	
foreach($items as $row) { ?>
	<div id="tag-item-<?php echo $row->id; ?>">
		<span><?php echo $row->tagGetTitle(); ?></span>
	</div>
<?php } ?>
</div>
