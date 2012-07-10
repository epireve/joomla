<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		3.2.11 September 8, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureWebFonts extends GantryFeature {

    var $_feature_name = 'webfonts';

    var $_google_fonts = array("Aclonica", "Allan", "Allerta", "Allerta Stencil", "Amaranth", "Annie Use Your Telescope", "Anonymous Pro", "Anton", "Architects Daughter", "Arimo", "Artifika", "Arvo", "Asset", "Astloch", "Bangers", "Bentham", "Bevan", "Bigshot One", "Brawler", "Buda", "Cabin", "Cabin Sketch", "Calligraffitti", "Candal", "Cantarell", "Cardo", "Carter One", "Caudex", "Cedarville Cursive", "Cherry Cream Soda", "Chewy", "Coda", "Coming Soon", "Copse", "Corben", "Cousine", "Covered By Your Grace", "Crafty Girls", "Crimson Text", "Crushed", "Cuprum", "Damion", "Dancing Script", "Dawning of a New Day", "Didact Gothic", "Droid Sans", "Droid Sans Mono", "Droid Serif", "EB Garamond", "Expletus Sans", "Fontdiner Swanky", "Francois One", "Geo", "Goblin One", "Goudy Bookletter 1911", "Gravitas One", "Gruppo", "Hammersmith One", "Holtwood One SC", "Homemade Apple", "IM Fell", "Inconsolata", "Indie Flower", "Irish Grover", "Josefin Sans", "Josefin Slab", "Judson", "Jura", "Just Another Hand", "Just Me Again Down Here", "Kameron", "Kenia", "Kranky", "Kreon", "Kristi", "La Belle Aurore", "Lato", "League Script", "Lekton", "Limelight", "Lobster", "Lobster Two", "Lora", "Luckiest Guy", "Maiden Orange", "Mako", "Maven Pro", "Meddon", "MedievalSharp", "Megrim", "Merriweather", "Metrophobic", "Michroma", "Miltonian", "Molengo", "Monofett", "Mountains of Christmas", "Muli", "Neucha", "Neuton", "News Cycle", "Nixie One", "Nobile", "Nova", "Nunito", "OFL Sorts Mill Goudy TT", "Old Standard TT", "Open Sans", "Orbitron", "Oswald", "Over the Rainbow", "PT Sans", "PT Serif", "Pacifico", "Paytone One", "Permanent Marker", "Philosopher", "Play", "Playfair Display", "Podkova", "Puritan", "Quattrocento", "Quattrocento Sans", "Radley", "Raleway", "Redressed", "Reenie Beanie", "Rock Salt", "Rokkitt", "Ruslan Display", "Schoolbell", "Shadows Into Light", "Shanti", "Sigmar One", "Six Caps", "Slackey", "Smythe", "Sniglet", "Special Elite", "Sue Ellen Francisco", "Sunshiney", "Swanky and Moo Moo", "Syncopate", "Tangerine", "Tenor Sans", "Terminal Dosis Light", "The Girl Next Door", "Tinos", "Ubuntu", "Ultra", "UnifrakturCook", "UnifrakturMaguntia", "Unkempt", "VT323", "Varela", "Vibur", "Vollkorn", "Waiting for the Sunrise", "Wallpoet", "Walter Turncoat", "Wire One", "Yanone Kaffeesatz", "Zeyada");

    function init() {
        global $gantry;

        $font_family = $gantry->get('font-family');

       // Added to setect whether to use HTTP or HTTPS:
       $mode = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

       // Only Google at this point
       if ($this->get('source') == "google" && in_array($font_family,$this->_google_fonts)) {

       	// Modified to use the HTTP/HTTPS $mode defined earlier:
           $gantry->addStyle($mode.'://fonts.googleapis.com/css?family='.str_replace(" ","+",$font_family));
           $gantry->addInlineStyle("h1, h2 { font-family: '".$font_family."', 'Helvetica', arial, serif; }");
       }

    }

}