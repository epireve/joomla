<?php
class CProgressbarHelper{

	/**
	* Generate the HTML for a progress bar
	*
	* @param int max progress value 
	* @param int current progress value
	* @param string [optional] style class for custom styling on the bar container
	* @param string [optional] style class for custom styling for the bar filling
	* @return string HTML
	*/
	static public function getHTML( $max=100, $currval=0, $barclass='outerpgbar', $barfillclass='innerpgbar'){
		
		if(!is_numeric($max) || !is_numeric($currval) || $max < 0 || $currval < 0)
		return '<div>Progress bar cannot be generated properly</div>';
		
		$width = intval(($currval/$max) * 100) ;
		$barclass = (!empty($barclass)) 
					? 'class="'.$barclass.'"' 
					: 'style="background-color:white; height:.8em; border :1px solid #D0D0D0; margin-bottom:10px; padding:1px; "' ;
					
		$barfillclass = (!empty($barfillclass)) 
					? 'class="'.$barfillclass.'" style="width:'.$width.'%;"' 
					: 'style="width:'.$width.'%; height: 100%; background-color:green; position:relative;"' ;
		
		//class progressbarfill : inside of the progress bar
		$html = '<div '.$barclass.' >'.
					'<div '.$barfillclass.'></div>'.
				'</div>';

		return $html;
	}
}
