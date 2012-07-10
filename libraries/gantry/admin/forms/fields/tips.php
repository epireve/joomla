<?php
/**
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.config.gantryformfield');
/**
 * @package     gantry
 * @subpackage  admin.elements
 */
class GantryFormFieldTips extends GantryFormField {

    protected $type = 'tips';
    protected $basetype = 'none';

	public function getInput(){

		global $gantry;
		
        $gantry->addScript($gantry->gantryUrl.'/admin/widgets/tips/js/tips.js');
		
		$xmlist = $gantry->templatePath.'/admin/tips/'.$this->element['tab'].'.xml';
		if (!file_exists($xmlist)) die($xmlist . ' file not found');
		
		$xml = simplexml_load_file($xmlist);
		$count = count($xml);
		$random = rand(0, $count - 1);
		
		
		$output = "
		<div class=\"gantrytips\">\n
			<div class=\"gantry-pin\"></div>\n
			<div class=\"gantrytips-controller\">\n
				<div class=\"gantrytips-arrow gantrytips-left\">&#x25c0;</div>\n
				<div class=\"gantrytips-middle\"><span><span class=\"current-tip\">" . ($random + 1) . "</span> / " . $count . "</span></div>\n
				<div class=\"gantrytips-arrow gantrytips-right\">&#x25b6;</div>\n
			</div>\n
			<div class=\"gantrytips-desc\">\n
				<div class=\"gantrytips-wrapper\">\n";
					
			for($i = 0; $i < $count; $i++) {
				$tip_title = ($xml->tip[$i]['label']);
				$tip_id = (isset($xml->tip[$i]['id'])) ? $xml->tip[$i]['id'] : false;
				$cls = ($i != $random) ? ' style="visibility: hidden; opacity: 0;"' : ' style="visibility: visible; opacity: 1;"';
				
				if (!$tip_id) $outputID = '';
				else $outputID = 'id="tip-' . str_replace('-', '_', $tip_id) . '"';
				
				$output .= "<div ".$outputID." class=\"gantrytips-tip\"".$cls.">\n";
				$output .= "<div class=\"gantrytips-bar h2bar\">\n
					<span>" . $tip_title  . "</span>\n
				</div>\n";
				$output .= $xml->tip[$i]."</div>\n";
			}
					
		$output .= "
				</div>\n
			</div>\n
		</div>\n";
		
		return $output;

		
	}
	
	public function getLabel(){
		return "";
    }
}