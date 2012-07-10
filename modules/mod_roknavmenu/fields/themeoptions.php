<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

/**
 * Supports an HTML select list of folder
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldThemeOptions extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'ThemeOptions';

    public function __construct($form = null){
        parent::__construct($form);
    }

    /**
     * Method to get the field input markup.
     *
     * @return    string    The field input markup.
     * @since    1.6
     */
    protected function getInput()
    {
        $buffer ='';
        $form = RokSubfieldForm::getInstance($this->form);

        JForm::addFieldPath(dirname(__FILE__) . '/fields');
		
		$this->load_js_switcher();
		$themesets = $form->getSubFieldsets('roknavmenu-themes');
		
        foreach($themesets as $themeset => $themeset_val)
        {
            $themeset_fields = $form->getSubFieldset('roknavmenu-themes', $themeset, 'params');
            ob_start();
            ?>
            <div class="themeset" id="themeset-<?php echo $themeset;?>">
                <ul class="themeset">
                <?php foreach ($themeset_fields as $themeset_field): ?>
                    <li>
                        <?php echo $themeset_field->getLabel(); ?>
                        <?php echo $themeset_field->getInput(); ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php
            $buffer .= ob_get_clean();
        }

        return $buffer;
    }

	private function load_js_switcher(){
		$doc =& JFactory::getDocument();
		$module_js_path = JURI::root(true).'/modules/mod_roknavmenu/lib/js';
		
		$doc->addScript($module_js_path."/switcher.js");
	}
}
