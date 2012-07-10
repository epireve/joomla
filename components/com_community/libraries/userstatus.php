<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	user
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');


class CUserStatus {

	private $creators = null;
	public $target = '';
	private $type = '';
	
	/**
	 *
	 * @param type $target
	 * @param type $type
	 * @param type $creators 
	 */
	public function __construct($target='', $type='profile' , $creators = null)
	{
		$my = CFactory::getUser();
		$this->type = $type;
		$this->target  = (empty($target)) ? $my->id : $target;
		
		if(is_array($creators))
		{
			foreach($creators as $row)
			{
				$this->addCreator($row);
			}
		}
	}

	public function addCreator($creator)
	{
		$this->creators[] =& $creator;

		return $creator;
	}

	public function render()
	{
		CFactory::load('libraries', 'privacy');

		$my = CFactory::getUser();
		$config	= CFactory::getConfig();

		if ($my->id && is_array($this->creators)) {

			$tmpl = new CTemplate();
			$html = $tmpl	->set('my', $my)
						->set('target', $this->target)
						->set('type', $this->type)
						->set('creators', $this->creators)
						->set('maxStatusChar',$config->get('statusmaxchar'))
						->fetch('status.form');
			
			// Some of the creator might need custom url replacement
			// Take a look at status.photo.php template for example
			$group_url = ($this->type == 'groups') ? CRoute::_('index.php?option=com_community&view=photos&task=ajaxPreview&no_html=1&tmpl=component&groupid='.$this->target) : CRoute::_('index.php?option=com_community&view=photos&task=ajaxPreview&no_html=1&tmpl=component');
			$html = str_replace('{url}', $group_url, $html);
			echo $html;
		}
	}
}

class CUserStatusCreator {

	public $type='';
	public $class='';
	public $title='';
	public $html='';

	public function __construct($type=null)
	{
		$this->type = $type;
		$this->class = 'type-' . $type;
	}
	
	static function getPhotoInstance($groupid=null)
	{
		$template	=   new CTemplate();
		$creator        = new CUserStatusCreator('photo');
		$creator->title = JText::_('COM_COMMUNITY_PHOTOS');
		$template->set('groupid', $groupid);
		$creator->html  = $template->fetch('status.photo');
		return $creator;
	}
	
	static function getVideoInstance()
	{
		$template	=   new CTemplate();
		$creator        = new CUserStatusCreator('video');
			$creator->title = JText::_('COM_COMMUNITY_VIDEOS');
			$creator->html  = $template->fetch('status.video');
		return $creator;
	}

	
	static function getMessageInstance()
	{
		$template	=   new CTemplate();
		$creator        =   new CUserStatusCreator('message');
		$creator->title =   JText::_('COM_COMMUNITY_MESSAGE');
		$creator->html  =   $template->fetch('status.message');
		return $creator;
	}

        static function getEventInstance()
        {
            $template	=   new CTemplate();
            
            CFactory::load( 'helpers' , 'event' );
            $dateSelection = CEventHelper::getDateSelection();

            $model		= CFactory::getModel( 'events' );
            $categories	= $model->getCategories();

            // Load category tree
            CFactory::load('helpers','category');
            $cTree	= CCategoryHelper::getCategories($categories);
            $lists['categoryid']	=   CCategoryHelper::getSelectList( 'events', $cTree );

            $template->set( 'startDate'       , $dateSelection->startDate );
            $template->set( 'endDate'         , $dateSelection->endDate );
            $template->set( 'startHourSelect' , $dateSelection->startHour );
            $template->set( 'endHourSelect'   , $dateSelection->endHour );
            $template->set( 'startMinSelect'  , $dateSelection->startMin );
            $template->set( 'endMinSelect'    , $dateSelection->endMin );
            $template->set( 'startAmPmSelect' , $dateSelection->startAmPm );
            $template->set( 'endAmPmSelect'   , $dateSelection->endAmPm );
            $template->set( 'lists'           , $lists );

            
            $creator  = new CUserStatusCreator('event');
            $creator->title = JText::_('COM_COMMUNITY_EVENTS');
            $creator->html  = $template->fetch('status.event');

            return $creator;
        }

}


?>