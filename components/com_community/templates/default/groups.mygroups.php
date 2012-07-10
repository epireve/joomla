<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	sortings	string	HTML code for the sorting
 * @params	groupsHTML	string HTML code for the group listings
 * @params	pagination	JPagination JPagination object 
 */
defined('_JEXEC') or die();
?>

<?php echo $sortings; ?>

<div class="cLayout"> 
    	<?php if ( !empty($discussionsHTML)) { ?>
        <div class="cSidebar clrfix">
            
            <div class="clrfix"><?php echo $pendingListHTML;?></div>
	    
	    <?php echo $discussionsHTML; ?>
    
        </div>
        <?php } ?>

	<!-- ALL MY GROUP LIST -->
	<div class="<?php if ( !empty($discussionsHTML)) { ?>cMain<?php } ?> clrfix">
		<?php echo $groupsHTML; ?>
	</div>
	<!-- ALL MY GROUP LIST -->
	<div class="clr"></div>
        
</div>