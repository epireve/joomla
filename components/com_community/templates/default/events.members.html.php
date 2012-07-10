<?php
/**
 * @package	JomSocial
 * @subpackage 	Template
 * @copyright	(C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die();
?>
<div id="community-event-members" class="cModule">

                                    <h3><?php echo JText::sprintf('COM_COMMUNITY_EVENTS_CONFIRMED_GUESTS'); ?></h3>
                                      <?php if($eventMembersCount>0){ ?>

                                            <div class="app-box-content">
                                                            <ul class="cResetList cThumbList clrfix">
                                                            <?php
                                                            if($eventMembers) {
                                                                            foreach($eventMembers as $member) {
                                                            ?>
                                                                            <li>
                                                                                            <a href="<?php echo CUrlHelper::userLink($member->id); ?>">
                                                                                                            <img border="0" height="45" width="45" class="cAvatar  jomTips" src="<?php echo $member->getThumbAvatar(); ?>" title="<?php echo cAvatarTooltip($member);?>" alt="" />
                                                                                            </a>
                                                                            </li>
                                                            <?php
                                                                            }
                                                            }
                                                            ?>
                                                            </ul>
                                            </div>
                                            <div class="app-box-footer">
                                                            <a href="<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=viewguest&eventid=' . $eventId . '&type='.COMMUNITY_EVENT_STATUS_ATTEND );?>">
                                                                            <?php echo JText::_('COM_COMMUNITY_VIEW_ALL');?> (<?php echo $eventMembersCount; ?>)
                                                            </a>
                                            </div>
                                <?php }
                                    else
                                        echo JText::_('COM_COMMUNITY_EVENTS_NO_USER_ATTENDING_MESSAGE')
                                    ?>
</div>