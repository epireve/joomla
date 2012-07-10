<?php
/**
 * @package RokStats - RocketTheme
 * @version 1.7.0 November 1, 2011
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

class rokUserStatsHelper
{
	function getRows(&$params) {

		$session =& JFactory::getSession();

		$sessiontime				= intval($params->get('sessiontime', $session->getExpire()));
		$showcurrentactiveusers		= $params->get('showcurrentactiveusers',1);
		$showactiveguests			= $params->get('showactiveguests',1);
		$showactiveregistered		= $params->get('showactiveregistered',1);
		$showregisteredusernames	= $params->get('showregisteredusernames',1);
		$showuniqueviststoday 		= $params->get('showuniqueviststoday',1);
		$showuniquevistsyesterday 	= $params->get('showuniquevistsyesterday',1);
		$showvisitsthisweek	 		= $params->get('showvisitsthisweek',1);
		$showvisitspreviousweek		= $params->get('showvisitspreviousweek',1);
		$showtotalarticles 			= $params->get('showtotalarticles',0);
		$shownewarticlesthisweek	= $params->get('shownewarticlesthisweek',0);
	
		$rows = array();
		$db = JFactory::getDBO();

		// check to make sure table exists
		if (!in_array($db->getPrefix().'rokuserstats', $db->getTableList())) {
			$rows[] = array('error',JTEXT::_('MC_RS_NO_TABLE'),'');
			return $rows;
		} 

		// get users stats
		if ($showcurrentactiveusers or $showactiveguests or $showactiveregistered) {

			// get admin activity
			$query = 'SELECT * from #__rokuserstats WHERE timestamp >= date_sub(current_timestamp,interval '.$sessiontime.' second) GROUP BY ip, user_id';
			$db->setQuery($query);
			$results = $db->loadResultArray(1);
			
			$total = 0;
			$guests = 0;
			$registered = 0;
			
			// add list of users
			if (is_array($results)) {
				foreach ($results as $user) {
					if ($user == 0) $guests++;
					else $registered++;
					$total++;
				}
			}
			// currently active users
			if ($showcurrentactiveusers) {
				$rows[] = array('none',JTEXT::_('MC_RS_CURRENT_ACTIVE_USERS'),$total);
			}
			// active guests
			if ($showactiveguests) {
				$rows[] = array('none',JTEXT::_('MC_RS_ACTIVE_GUESTS'),$guests);
			}
			// active registered
			if ($showactiveregistered) {
				$rows[] = array('none',JTEXT::_('MC_RS_ACTIVE_REGISTERED'),$registered);
			}
		}

		// get registered users
		if ($showregisteredusernames) {
			$where = 'timestamp >= date_sub(current_timestamp,interval '.$session->getExpire().' second)';
			$query = 'SELECT u.name FROM #__rokuserstats AS s, #__users AS u WHERE s.user_id = u.id AND '.$where.' GROUP BY username';
			$db->setQuery($query);
			$usernames = $db->loadResultArray();
			// add data
			$rows[] = array('full',$usernames);

		}

		// get daily visit stats
		if ($showuniqueviststoday or $showuniquevistsyesterday) {
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

			// get trends
			$currentday_trend = $currentday >= $yesterday ? 'up' : 'down';
			$yesterday_trend = $yesterday >= $prevday ? 'up' : 'down';

			// add data
			if ($showuniqueviststoday) {
				$rows[] = array($currentday_trend,JTEXT::_('MC_RS_UNIQUE_VISITS_TODAY'),$currentday);
			}
			if ($showuniquevistsyesterday) {
				$rows[] = array($yesterday_trend,JTEXT::_('MC_RS_UNIQUE_VISITS_YESTERDAY'),$yesterday);
			}
		}
		
		// get weekly visit stats
		if ($showvisitsthisweek or $showvisitspreviousweek) {
			
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

			// get trends
			$currentweek_trend = $currentweek >= $pastweek ? 'up' : 'down';
			$pastweek_trend = $pastweek >= $prevweek ? 'up' : 'down';

			// add data
			if ($showvisitsthisweek) {
				$rows[] = array($currentweek_trend,JTEXT::_('MC_RS_VISITS_THIS_WEEK'),$currentweek);
			}
			if ($showvisitspreviousweek) {
				$rows[] = array($pastweek_trend,JTEXT::_('MC_RS_VISITS_PREVIOUS_WEEK'),$pastweek);
			}
		}

		// get article status
		if ($showtotalarticles or $shownewarticlesthisweek) {

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

			// get trends
			$article_trend = $newarticles >= $pastarticles ? 'up' : 'down';

			// add data
			if ($showtotalarticles) {
				$rows[] = array('none',JTEXT::_('MC_RS_TOTAL_ARTICLES'),$totalarticles);
			}
			if ($shownewarticlesthisweek) {
				$rows[] = array($article_trend,JTEXT::_('MC_RS_NEW_ARTICLES_THIS_WEEK'),$newarticles);
			}
		}

		// return results		
		return $rows;
	}
}
