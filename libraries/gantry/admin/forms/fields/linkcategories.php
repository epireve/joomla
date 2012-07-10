<?php
/**
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformfield');
gantry_import('core.config.gantryhtmlselect');
require_once('list.php');




class GantryFormFieldLinkCategories extends GantryFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'linkcategories';
    protected $basetype = 'select';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
    protected function getOptions() {
        global $gantry;
        $options = array();
        $options = parent::getOptions();

		$link_cats = get_terms('link_category');
        $options[] = GantryHtmlSelect::option('', _g('All Links'), 'value', 'text', false);
        
        foreach ( $link_cats as $link_cat ) {
			// Create a new option object based on the <option /> element.
			$tmp = GantryHtmlSelect::option($link_cat->slug, $link_cat->name, 'value', 'text', false);
			$options[] = $tmp;
		}
        return $options;
    }
}