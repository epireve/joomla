<?php
/**
 * @package RokUserStats - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

class rokUserStatsHelper
{
	function getRows() {
	
		$rows = array();
		$db = JFactory::getDBO();
		$session =& JFactory::getSession();
		
		// currently active users
		$query = 'SELECT * from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval '.$session->getExpire().' second) group by ip, user_id';
		$db->setQuery($query);
		$results = $db->loadResultArray(1);
		
		$total = 0;
		$guests = 0;
		$registered = 0;
		
		if (is_array($results)) {
			foreach ($results as $user) {
				if ($user == 0) $guests++;
				else $registered++;
				$total++;
			}
		}

		
		// unique visits today
		$query = 'select count(total) from (SELECT ip as total from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval 1 day) group by ip, user_id) as foo';
		$db->setQuery($query);
		$currentday = intval($db->loadResult());
		
		// unique visits yesterday
		$query = 'select count(total) from (SELECT ip as total from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval 2 day) AND timestamp < date_sub(curdate(),interval 1 day) group by ip, user_id) as foo';
		$db->setQuery($query);
		$yesterday = intval($db->loadResult());
		
		// previous day visits
		$query = 'select count(total) from (SELECT ip as total from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval 3 day) AND timestamp < date_sub(curdate(),interval 2 day) group by ip, user_id) as foo';
		$db->setQuery($query);
		$prevday = intval($db->loadResult());
		
		// visits past 7 days
		$query = 'select count(total) from (SELECT ip as total from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval 7 day) group by ip, user_id) as foo';
		$db->setQuery($query);
		$currentweek = intval($db->loadResult());
		
		// unique visits past week
		$query = 'select count(total) from (SELECT ip as total from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval 14 day) AND timestamp < date_sub(curdate(),interval 7 day) group by ip, user_id) as foo';
		$db->setQuery($query);
		$pastweek = intval($db->loadResult());
		
		// previous week visits
		$query = 'select count(total) from (SELECT ip as total from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval 21 day) AND timestamp < date_sub(curdate(),interval 14 day) group by ip, user_id) as foo';
		$db->setQuery($query);
		$prevweek = intval($db->loadResult());
		
		// total articles
		$query = 'select count(id) as total from #__content WHERE state = 1';
		$db->setQuery($query);
		$totalarticles = intval($db->loadResult());
		
		// new articles
		$query = 'select count(id) as total from #__content WHERE state = 1 and created >= date_sub(current_timestamp,interval 7 day)';
		$db->setQuery($query);
		$newarticles = intval($db->loadResult());
		
		// past articles
		$query = 'select count(id) as total from #__content WHERE state = 1 and created >= date_sub(current_timestamp,interval 14 day) AND publish_up < date_sub(curdate(), interval 7 day)';
		$db->setQuery($query);
		$pastarticles = intval($db->loadResult());
		
		

		
		$currentday_trend = $currentday >= $yesterday ? 'up' : 'down';
		$yesterday_trend = $yesterday >= $prevday ? 'up' : 'down';
		
		$currentweek_trend = $currentweek >= $pastweek ? 'up' : 'down';
		$pastweek_trend = $pastweek >= $prevweek ? 'up' : 'down';
		
		$article_trend = $newarticles >= $pastarticles ? 'up' : 'down';
		
		$rows[] = array('none',JTEXT::_('MC_RUS_CURRENT_ACTIVE_USERS'),$total);
		$rows[] = array('none',JTEXT::_('MC_RUS_ACTIVE_GUESTS'),$guests);
		$rows[] = array('none',JTEXT::_('MC_RUS_ACTIVE_REGISTERED'),$registered);
		$rows[] = array($currentday_trend,JTEXT::_('MC_RUS_UNIQUE_VISITS_TODAY'),$currentday);
		$rows[] = array($yesterday_trend,JTEXT::_('MC_RUS_UNIQUE_VISITS_YESTERDAY'),$yesterday);
		$rows[] = array($currentweek_trend,JTEXT::_('MC_RUS_VISITS_THIS_WEEK'),$currentweek);
		$rows[] = array($pastweek_trend,JTEXT::_('MC_RUS_VISITS_PREVIOUS_WEEK'),$pastweek);
		$rows[] = array('none',JTEXT::_('MC_RUS_TOTAL_ARTICLES'),$totalarticles);
		$rows[] = array($article_trend,JTEXT::_('MC_RUS_NEW_ARTICLES_THIS_WEEK'),$newarticles);
		
		
		
		return $rows;

	}
	
}
