<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   1.3 October 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

// no direct access
defined('_JEXEC') or die;
?>
<ul class="latestnews">
<?php foreach ($list as $item) :  ?>
	<li class="latestnews">
		<a href="<?php echo $item->link; ?>" class="latestnews"><?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ul>