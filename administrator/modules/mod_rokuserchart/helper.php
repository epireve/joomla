<?php
/**
 * @package RokUserChart - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

class rokUserChartHelper
{

	
	function renderChart(&$params) {
	
		// get data
		$db = JFactory::getDBO();
		
		$value_data = array();
		$history = intval($params->get('history',7));
		// currently active users
		$query = 'select count(tdate) as unique_visitors, tdate from (SELECT date(timestamp) as tdate from #__rokuserstats WHERE timestamp >= date_sub(curdate(),interval '.$history.' day) group by  ip, user_id, tdate order by tdate) as foo group by tdate';
		$db->setQuery($query);
		$data = $db->loadObjectList();
		
		if (is_array($data)) {
			foreach($data as $row) {
				$value_data[] = $row->unique_visitors;
			}
		}

        if (empty($value_data)){
            $value_data[] = 0;
        }
        $max = max($value_data);
	
		require_once('googlechartlib/GoogleChart.php');
		
		$chart = new GoogleChart('lc',$params->get('width',285),$params->get('height',120));
		$chart->setTitle(JTEXT::sprintf('MC_RUC_TITLE',intval($history)));
		$chart->setTitleColor('666666')->setTitleSize(13);
		
		$data = new GoogleChartData($value_data);
		$data->setColor('4F9BD8');
		$data->setThickness(2);
			
		$chart->addData($data);
		
		$y_axis = new GoogleChartAxis('y');
		$y_axis->setRange(0,$max);
		$y_axis->setTickMarks(2);
		
		$x_axis = new GoogleChartAxis('x');
		$x_axis->setRange(0,count($value_data)-1);
		$x_axis->setTickMarks(2);
		
		$chart->addAxis($y_axis);
		$chart->addAxis($x_axis);
		
		return $chart->toHtml();
	
	}
	
}
