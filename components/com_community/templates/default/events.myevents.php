<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die();
?>

<div class="cLayout clrfix">

     <?php echo $sortings; ?>
    
     <div>
     
        <div class="cSidebar clrfix">
	    <?php echo $this->view('events')->modEventPendingList(); ?>
	</div>

         <div class="cMain clrfix">
            <div id="community-events-wrap">
                    <?php echo $eventsHTML; ?>
            </div>
        </div>
        
    </div>
     
</div>