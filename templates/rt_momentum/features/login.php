<?php
/**
 * @package   Momentum Template - RocketTheme
 * @version   1.3 October 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureLogin extends GantryFeature {
    var $_feature_name = 'login';

	function render($position="") {
	    ob_start();
	    $user =& JFactory::getUser();
	    ?>
	    <div class="rt-block loginbutton-block">
			<div id="rt-login-button">
			<?php if ($user->guest) : ?>
				<a href="#" class="readon buttontext" rel="rokbox[385 160][module=rt-popuplogin]">
					<span class="desc"><?php echo $this->get('text'); ?></span>
				</a>
			<?php else : ?>
				<a href="#" class="readon buttontext" rel="rokbox[385 160][module=rt-popuplogin]">
					<span class="desc"><?php echo $this->get('logouttext'); ?> <?php echo JText::sprintf($user->get('username')); ?></span>
				</a>
			<?php endif; ?>
			</div>
		</div>
		<?php
	    return ob_get_clean();
	}
}