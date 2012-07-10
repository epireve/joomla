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

<ul class="newsflash-horiz">
	<?php for ($i = 0, $n = count($list); $i < $n; $i ++) :
		$item = $list[$i]; ?>
	<li>
			<?php require JModuleHelper::getLayoutPath('mod_articles_news', '_item');
		
			if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>
		
			<span class="article-separator">&#160;</span>
		
			<?php endif; ?>
	</li>
	<?php endfor; ?>
</ul>