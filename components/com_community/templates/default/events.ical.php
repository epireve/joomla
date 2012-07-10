<?php
/**
 * @package	JomSocial
 * @subpackage Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>
BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
URL:<?php echo $url; ?>
 
DTSTART:<?php echo $dtstart; ?>

DTEND:<?php echo $dtend; ?>

SUMMARY:<?php echo $event->title; ?>

DESCRIPTION:<?php echo $event->description ?>

LOCATION:<?php echo $event->location; ?>

END:VEVENT
END:VCALENDAR