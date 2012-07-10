<?php
/**
 * Document Description
 *
 * Document Long Description
 *
 * PHP4/5
 *
 * Created on Jul 21, 2008
 *
 * @package package_name
 * @author Your Name <author@toowoombarc.qld.gov.au>
 * @author Toowoomba Regional Council Information Management Branch
 * @license GNU/GPL http://www.gnu.org/licenses/gpl.html
 * @copyright 2008 Toowoomba Regional Council/Developer Name
 * @version SVN: $Id: alpha.scriptfile.php 16235 2010-04-20 04:13:25Z pasamio $
 * @see http://joomlacode.org/gf/project/    JoomlaCode Project:
 */

class Mod_RokNavMenuInstallerScript
{

    public function install($parent)
    {
        if ($this->checkForExtender())
            $this->registerExtenderPlugin($parent);
        return true;
    }

    public function update($parent)
    {
        if ($this->checkForExtender())
            $this->registerExtenderPlugin($parent);
        return true;
    }

    public function uninstall($parent)
    {
        if ($this->checkForExtender())
            $this->unregisterExtenderPlugin($parent);
    }

    public function preflight($type, $parent)
    {

    }

    /**
     * @return bool
     */
    protected function checkForExtender()
    {
        // if the class exists and is loaded just return
        if (class_exists('plgSystemRokExtender')) return true;
        $plugin_path = JPATH_ROOT . '/plugins/system/rokextender/rokextender.php';

        // if the plugin isnt installed
        if (!file_exists($plugin_path) || !is_file($plugin_path)) return false;

        require_once($plugin_path);

        if (!class_exists('plgSystemRokExtender'))
        {
            //TODO: add error message output
            return false;
        }
        return true;
    }

    protected function registerExtenderPlugin($parent)
    {
        $manifest = $parent->getParent()->getManifest();
        $basepath = str_replace(JPATH_ROOT, '', $parent->getParent()->getPath('extension_root'));

        foreach ($manifest->plugins->plugin as $plugin)
        {
            plgSystemRokExtender::registerExtenderPlugin($basepath . (string)$plugin);
        }
    }

    protected function unregisterExtenderPlugin($parent)
    {
        $manifest = $parent->getParent()->getManifest();
        $basepath = str_replace(JPATH_ROOT, '', $parent->getParent()->getPath('extension_root'));

        foreach ($manifest->plugins->plugin as $plugin)
        {
            plgSystemRokExtender::registerExtenderPlugin($basepath . (string)$plugin);
        }
    }
}