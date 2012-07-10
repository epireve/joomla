<?php
/**
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformfield');

require_once(dirname(__FILE__).'/selectbox.php');


class GantryFormFieldFonts extends GantryFormFieldSelectBox {
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'fonts';
    protected $basetype = 'select';

    var $_google_fonts = array("Aclonica", "Allan", "Allerta", "Allerta Stencil", "Amaranth", "Annie Use Your Telescope", "Anonymous Pro", "Anton", "Architects Daughter", "Arimo", "Artifika", "Arvo", "Asset", "Astloch", "Bangers", "Bentham", "Bevan", "Bigshot One", "Brawler", "Buda", "Cabin", "Cabin Sketch", "Calligraffitti", "Candal", "Cantarell", "Cardo", "Carter One", "Caudex", "Cedarville Cursive", "Cherry Cream Soda", "Chewy", "Coda", "Coming Soon", "Copse", "Corben", "Cousine", "Covered By Your Grace", "Crafty Girls", "Crimson Text", "Crushed", "Cuprum", "Damion", "Dancing Script", "Dawning of a New Day", "Didact Gothic", "Droid Sans", "Droid Sans Mono", "Droid Serif", "EB Garamond", "Expletus Sans", "Fontdiner Swanky", "Francois One", "Geo", "Goblin One", "Goudy Bookletter 1911", "Gravitas One", "Gruppo", "Hammersmith One", "Holtwood One SC", "Homemade Apple", "IM Fell", "Inconsolata", "Indie Flower", "Irish Grover", "Josefin Sans", "Josefin Slab", "Judson", "Jura", "Just Another Hand", "Just Me Again Down Here", "Kameron", "Kenia", "Kranky", "Kreon", "Kristi", "La Belle Aurore", "Lato", "League Script", "Lekton", "Limelight", "Lobster", "Lobster Two", "Lora", "Luckiest Guy", "Maiden Orange", "Mako", "Maven Pro", "Meddon", "MedievalSharp", "Megrim", "Merriweather", "Metrophobic", "Michroma", "Miltonian", "Molengo", "Monofett", "Mountains of Christmas", "Muli", "Neucha", "Neuton", "News Cycle", "Nixie One", "Nobile", "Nova", "Nunito", "OFL Sorts Mill Goudy TT", "Old Standard TT", "Open Sans", "Orbitron", "Oswald", "Over the Rainbow", "PT Sans", "PT Serif", "Pacifico", "Paytone One", "Permanent Marker", "Philosopher", "Play", "Playfair Display", "Podkova", "Puritan", "Quattrocento", "Quattrocento Sans", "Radley", "Raleway", "Redressed", "Reenie Beanie", "Rock Salt", "Rokkitt", "Ruslan Display", "Schoolbell", "Shadows Into Light", "Shanti", "Sigmar One", "Six Caps", "Slackey", "Smythe", "Sniglet", "Special Elite", "Sue Ellen Francisco", "Sunshiney", "Swanky and Moo Moo", "Syncopate", "Tangerine", "Tenor Sans", "Terminal Dosis Light", "The Girl Next Door", "Tinos", "Ubuntu", "Ultra", "UnifrakturCook", "UnifrakturMaguntia", "Unkempt", "VT323", "Varela", "Vibur", "Vollkorn", "Waiting for the Sunrise", "Wallpoet", "Walter Turncoat", "Wire One", "Yanone Kaffeesatz", "Zeyada");

    protected function getOptions() {
        global $gantry;
        $options = array();
        $options = parent::getOptions();

		if (!defined("GANTRY_FONTS")) {
			$gantry->addScript($gantry->gantryUrl.'/admin/widgets/fonts/js/fonts.js');
			$gantry->addDomReadyScript("GantryFonts.init('webfonts_enabled', 'webfonts_source', 'font_family');");
			define("GANTRY_FONTS", 1);
		}


		// only google right now
		if ($gantry->get('webfonts-source') == 'google') {
			$webfonts = $this->_google_fonts;
		}
		
		if ($gantry->get('webfonts-enabled')) $disabled = false;
		else $disabled = true;
		
		foreach ($webfonts as $webfont) {
			$webfontsData = $webfont;
			$webfontsValue = $webfont;

			$text = $webfontsData;
			
			// Create a new option object based on the <option /> element.
			$tmp = GantryHtmlSelect::option((string) $webfontsValue, JText::_(trim((string) $text)), 'value', 'text', $disabled);

			// adding reference source class
			if (in_array($webfont, $this->_google_fonts)) $option['class'] = 'google';
			else $option['class'] = 'native';
			
			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.

			$tmp->onclick = isset($option['onclick'])?(string) $option['onclick']:'';

			// Add the option object to the result set.
			$options[] = $tmp;
		}
		
        return $options;
    }
}
