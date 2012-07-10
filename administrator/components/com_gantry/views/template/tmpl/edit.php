<?php
/**
 * @version	$Id$
 * @package Gantry
 * @copyright Copyright (C) 2009 RocketTheme. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author RocketTheme, LLC
 */
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

JRequest::setVar( 'hidemainmenu', 1 );

global $gantry;

JHtml::_('behavior.keepalive');
$user = JFactory::getUser();
$canDo = $this->getActions();

require_once(JPATH_LIBRARIES."/gantry/gantry.php");


gantry_import('core.gantrysingleton');
gantry_import('core.config.gantryform');
gantry_import('core.config.gantryformnaminghelper');

$gantryForm = $this->gantryForm;
$fieldSets = $gantryForm->getFieldsets();



$gantry->addStyle($gantry->gantryUrl."/admin/widgets/gantry.css");
$gantry->addScript($gantry->gantryUrl."/admin/widgets/gantry.js");
if ($this->override) $gantry->addScript($gantry->gantryUrl."/admin/widgets/assignments/js/assignments.js");
$gantry->addInlineScript("var GantryIsMaster = ".(($this->override) ? 'false' : 'true').";");

function gantry_admin_render_edit_item($element)
{
    $buffer = '';
    $buffer .= "				<div class=\"gantry-field " . $element->type . "-field\">\n";
    $label = '';
    if ($element->show_label) $label = $element->getLabel() . "\n";
    $buffer .= $label;
    $buffer .= $element->getInput() . "\n";
    $buffer .= "					<div class=\"clr\"></div>\n";
    $buffer .= "				</div>\n";
    return $buffer;
}

function  gantry_admin_render_edit_override_item($element)
{
    $buffer = '';
    $buffer .= "				<div class=\"gantry-field " . $element->type . "-field\">\n";
    $label = '';
    $checked = ($element->variance) ? ' checked="checked"' : '';
    if ($element->show_label)
    {
        if (!$element->setinoverride)
        {
            $label = $element->getLabel() . "\n";
        } else
        {
            $label = '<div class="field-label"><span class="inherit-checkbox"><input  name="overridden-' . $element->name . '" type="checkbox"' . $checked . '/></span><span class="base-label">' . $element->getLabel() . '</span></div>';
        }
    }
    $buffer .= $label;
    $buffer .= $element->getInput() . "\n";
    $buffer .= "					<div class=\"clr\"></div>\n";
    $buffer .= "				</div>\n";
    return $buffer;
}

function get_badges_layout($name, $override=0, $involved=0, $assignments=0) {
	if ($name == 'assignment'){
		return '<span class="menuitems-involved"><span>'.$assignments.'</span></span>';
	} else {
		if ($override) {
			return '
				<span class="badges-involved">'."\n".'
				<span class="presets-involved"> <span>0</span></span> '."\n".'
				<span class="overrides-involved"> <span>'.$involved.'</span></span>'."\n".'
			</span>';
		} else {
			return '<span class="presets-involved"><span>0</span></span>';
		}
	}
}

function get_version_update_info(){

    $buffer = '';
    gantry_import('core.gantryupdates');
    $gantry_updates = GantryUpdates::getInstance();
    $currentVersion =  $gantry_updates->getCurrentVersion();
    $latest_version = $gantry_updates->getLatestVersion();

    if (version_compare($latest_version,$currentVersion,'>')){
        $klass="update";
        $upd = JText::sprintf('COM_GANTRY_VERSION_UPDATE_OUTOFDATE',$latest_version,'index.php?option=com_installer&view=update');
    } else {
        $klass = "noupdate";
        jimport('joomla.utilities.date');
        $nextupdate = new JDate($gantry_updates->getLastUpdated()+(24*60*60));

        $upd = JText::sprintf('COM_GANTRY_VERSION_UPDATE_CURRENT',JHTML::_('date', $gantry_updates->getLastUpdated()+(24*60*60),JText::_('DATE_FORMAT_LC2'),true));
    }

    $buffer .= "
    <div id='updater' class='".$klass."'>
        <div id='updater-bar' class='h2bar'>Gantry <span>v".$currentVersion."</span></div>
        <div id='updater-desc'>".$upd."</div>
    </div>";

    return $buffer;
}

$this->gantryForm->initialize();
?>

<div class="gantry-wrap <?php echo (!$this->override) ? 'defaults-wrap' : 'override-wrap'; ?>">
	<?php if(!$this->override):?><div id="gantry-master"></div><?php endif;?>
	<form action="<?php echo JRoute::_('index.php?option=com_gantry&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="template-form" class="form-validate">
        <fieldset class="adminform gantry-details">
			<legend><?php echo JText::_('JDETAILS');?></legend>
			<div class="gantry-detail gantry-detail-title">
			<?php echo $this->form->getLabel('title'); ?>
			<?php echo $this->form->getInput('title'); ?>
			</div>
			<div class="gantry-detail gantry-detail-template">
			<?php echo $this->form->getLabel('template'); ?>
			<?php echo $this->form->getInput('template'); ?>
			</div>

			<div class="gantry-detail gantry-detail-home">
			<?php echo $this->form->getLabel('home'); ?>
			<?php echo $this->form->getInput('home'); ?>
			</div>
			<div class="gantry-detail gantry-detail-id">
			<?php if ($this->item->id) : ?>
				<?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?>
			<?php endif; ?>
			</div>

		</fieldset>

		<?php echo $this->form->getInput('client_id'); ?>


	<!--<input type="hidden" name="id" value="<?php /*echo $gantry->templateName; */?>" />-->
    <?php //settings_fields('theme-options-array'); ?>
        <div class="fltrt">
            <div class="submit-wrapper png">

			</div>
			
			<?php echo $this->loadTemplate('presets'); ?>
			
            <div class="gantry-wrapper">
				<div id="gantry-logo">Powered by</div>
                <ul id="gantry-tabs">
                <?php
					$panels = array();
					$positions = array(
						'hiddens' => array(),
						'top' => array(),
						'left' => array(),
						'right' => array(),
						'bottom' => array()
					);

                    $involvedCounts = array();
					foreach ($fieldSets as $name => $fieldSet) {
						if ($name == 'toolbar-panel') continue;
						$fields = $gantryForm->getFullFieldset($name);
                        $involved = 0;
						array_push($panels, array("name" => $name, "height" => (isset($fieldSet->height))?$fieldSet->height:null));
						foreach($fields as $fname => $field) {
							$position = $field->panel_position;
							
                            if ($field->type != 'hidden' && $field->setinoverride && $field->variance) $involved++;
							if ($field->type == 'hidden') $position = 'hiddens';
							if (!isset($positions[$position][$name])) $positions[$position][$name] = array();
							array_push(
								$positions[$position][$name],
                                $field
								//array("name" => $field->name, "label" => $field->label, "input" => $field->input, "show_label" => $field->show_label, 'type' => $field->type)
							);
						}
                        $involvedCounts[$name] = $involved;
					}


					foreach ($fieldSets as $name => $fieldSet):
						if ($name == 'toolbar-panel') continue;
						?>
                        <li class="<?php echo $this->tabs[$name];?>">
                        <span class="outer">
                            <span class="inner"><span style="float:left;"><?php echo JText::_($fieldSet->label);?></span> <?php echo get_badges_layout($name, $this->override, $involvedCounts[$name], $this->assignmentCount);?></span>
                        </span>
                        </li>

                    <?php endforeach;?>
                </ul>
                <?php
					$output = "";
					$output .= "<div id=\"gantry-panel\">\n";
					if (count($panels) > 0)
                    {
                        for($i = 0; $i < count($panels); $i++) {
                            $panel = $panels[$i]['name'];

                            $width = '';
                            if ((@count($positions['left'][$panels[$i]['name']]) && !@count($positions['right'][$panels[$i]['name']])) || (!@count($positions['left'][$panels[$i]['name']]) && @count($positions['right'][$panels[$i]['name']]))) {
                                $width = 'width-auto';
                            }

                            $activePanel = "";
                            if ($i == $this->activeTab - 1) $activePanel = " active-panel";
                            else $activePanel = "";

                            $output .= "	<div class=\"gantry-panel panel-".($i+1)." panel-".$panel." ".$width.$activePanel."\">\n";

                            $buffer = "";
                            foreach($positions as $name => $position) {
                                if (isset($positions[$name][$panel])) {
                                    $buffer .= "		<div class=\"gantry-panel-".$name."\">\n";
                                    $panel_name = $name == 'left' ? 'panelform' : 'paneldesc';

                                    $buffer .= "			<div class=\"".$panel_name."\">\n";

                                    if ($panel_name == 'paneldesc' && $panel == 'overview') {
                                        $buffer .= get_version_update_info();

                                    }
                                    foreach($positions[$name][$panel] as $element) {
                                        if (!$this->override){
                                            $buffer .= $element->render('gantry_admin_render_edit_item');
                                        }
                                        else{
                                            $buffer .= $element->render('gantry_admin_render_edit_override_item');
                                        }
                                    }

                                    $buffer .= "			</div>\n";
                                    $buffer .= "		</div>\n";
                                }
                            }
                            $output .= $buffer;

                            $output .= "	</div>";
                        }
					}
					$output .= "</div>\n";
					echo $output;
				?>
                </div>
                <div class="clr"></div>
            </div>
            <div class="clr"></div>
            <input type="hidden" name="task" value="" />
		    <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>

<?php
 // css overrides
	if ($gantry->browser->name == 'ie' && file_exists($gantry->gantryPath . DS . 'admin' . DS . 'widgets' . DS . 'gantry-ie.css')) {
		$gantry->addStyle($gantry->gantryUrl . '/admin/widgets/gantry-ie.css');
	}
	if ($gantry->browser->name == 'ie' && $gantry->browser->version == '7' && file_exists($gantry->gantryPath . DS . 'admin' . DS . 'widgets' . DS . 'gantry-ie7.css')) {
	    $gantry->addStyle($gantry->gantryUrl . '/admin/widgets/gantry-ie7.css');
	}

	if (($gantry->browser->name == 'firefox' && $gantry->browser->version < '3.7') || ($gantry->browser->name == 'ie' && $gantry->browser->version > '6')) {
	    $css = ".text-short, .text-medium, .text-long, .text-color {padding-top: 4px;height:19px;}";
	    $gantry->addInlineStyle($css);
	}

	if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion == '7') {
	    $css = "
	        .g-surround, .g-inner, .g-surround > div {zoom: 1;position: relative;}
	        .text-short, .text-medium, .text-long, .text-color {border:0 !important;}
	        .selectbox {z-index:500;position:relative;}
	        .group-fusionmenu, .group-splitmenu {position:relative;margin-top:0 !important;zoom:1;}
	        .scroller .inner {position:relative;}
	        .moor-hexLabel {display:inline-block;zoom:1;float:left;}
	        .moor-hexLabel input {float:left;}
	    ";
	    $gantry->addInlineStyle($css);
	}
	if ($gantry->browser->name == 'opera' && file_exists($gantry->gantryPath . DS . 'admin' . DS . 'widgets' . DS . 'gantry-opera.css')) {
		$gantry->addStyle($gantry->gantryUrl . '/admin/widgets/gantry-opera.css');
	}

    $this->gantryForm->finalize();
    $gantry->finalize();