<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

/**
 * Method to add menu's item.
 *
 * @return boolean true on success false on failure.
 */
function addMenuItems()
{
	$db		= JFactory::getDBO();
	
	// Get new component id.
	$component		= JComponentHelper::getComponent('com_community');
	$component_id	= 0;
	if (is_object($component) && isset($component->id)){
		$component_id 	= $component->id;
	}
	
	$column_name	= JOOMLA_MENU_NAME;
	$column_cid		= JOOMLA_MENU_COMPONENT_ID;
	
	// Get the default menu type
	// 2 Joomla bugs occur in /Administrator mode
	// Bug 1: JFactory::getApplication('site') failed. It always return id = 'administrator'.
	// Bug 2: JMenu::getDefault('*') failed. JAdministrator::getLanguageFilter() doesn't exist.
	// If these 2 bugs are fixed, we can call the following syntax:
	// $defaultMenuType	= JFactory::getApplication('sites')->getMenu()->getDefault()->menutype;
	jimport('joomla.application.application');
	$defaultMenuType	= JApplication::getInstance('site')->getMenu()->getDefault('workaround_joomla_bug')->menutype;
	
	$query 	= 'SELECT ' . $db->nameQuote( 'ordering' ) . ' '
			. 'FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'ORDER BY ' . $db->nameQuote( 'ordering' ) . ' DESC LIMIT 1';
	$db->setQuery( $query );
	$order 	= $db->loadResult() + 1;
		
	// Update the existing menu items.
	$row		= JTable::getInstance ( 'menu', 'JTable' );
	
	$row->menutype = $defaultMenuType;
	$row->$column_name = 'JomSocial';
	$row->alias = 'JomSocial';
	$row->link = 'index.php?option=com_community&view=frontpage';
	$row->type = 'component';
	$row->published = '1';
	$row->$column_cid = $component_id;
	$row->ordering = $order;
	$row->id = null; //new item
	
	if (!JOOMLA_LEGACY_VERSION)
	{
		$row->language	= '*';
	}
	
	$row->check();
	
	if(!$row->store())
	{
		// $row->getError()
		return false;
	}
	
	//for version 1.6 only. The parent_id is not updated correctly via JTable
	if (!JOOMLA_LEGACY_VERSION)
	{
		$query = 'UPDATE '. $db->nameQuote( '#__menu' ) 
				 . ' SET `parent_id` = ' .$db->quote(1)
				 . ', `level` = ' . $db->quote(1) 
				 . ' WHERE `id` = ' . $db->quote($row->id) ;
		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			return false;
		}
	}
	
	if (!addDefaultToolbarMenus())
	{
		return false;
	}
	
	// update memu items with component id
	if (!updateMenuItems())
	{
		return false;
	}
	
	return true;
}

function addDefaultCustomFields()
{
	$db		=& JFactory::getDBO();
	$query	= 'INSERT INTO ' . $db->nameQuote('#__community_fields') . ' (' . $db->nameQuote('id') . ', ' . $db->nameQuote('type') . ', ' . $db->nameQuote('ordering') . ', ' . $db->nameQuote('published') . ', ' . $db->nameQuote('min') . ', ' . $db->nameQuote('max') . ', ' . $db->nameQuote('name') . ', ' . $db->nameQuote('tips') . ', ' . $db->nameQuote('visible') . ', ' . $db->nameQuote('required') . ', ' . $db->nameQuote('searchable') . ', ' . $db->nameQuote('options') . ', ' . $db->nameQuote('fieldcode') . ') VALUES '.
				'(' . $db->Quote('1') . ', ' . $db->Quote('group') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('10') . ', ' . $db->Quote('100') . ', ' . $db->Quote('Basic Information') . ', ' . $db->Quote('Basic information for user') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('') . ', ' . $db->Quote('') . '),' .
				'(' . $db->Quote('2') . ', ' . $db->Quote('select') . ', ' . $db->Quote('2') . ', ' . $db->Quote('1') .', ' . $db->Quote('10') . ', ' . $db->Quote('100') . ', ' . $db->Quote('Gender') . ', ' . $db->Quote('Select gender') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('Male'."\n".'Female') . ', ' . $db->Quote('FIELD_GENDER') . '),' .
				'(' . $db->Quote('3') . ', ' . $db->Quote('birthdate') .', ' . $db->Quote('3') .', ' . $db->Quote('1') .', ' . $db->Quote('10') .', ' . $db->Quote('100') .', ' . $db->Quote('Birthdate') .', ' . $db->Quote('Enter your date of birth so other users can know when to wish you happy birthday ') .', ' . $db->Quote('1') .', ' . $db->Quote('0') .', ' . $db->Quote('1') .', ' . $db->Quote('') .', ' . $db->Quote('FIELD_BIRTHDATE') .'),' .
				'(' . $db->Quote('4') . ', ' . $db->Quote('textarea') . ', ' . $db->Quote('4') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('800') . ', ' . $db->Quote('About me') . ', ' . $db->Quote('Tell us more about yourself') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('') . ', ' . $db->Quote('FIELD_ABOUTME') . '),' .
				'(' . $db->Quote('5') . ', ' . $db->Quote('group') . ', ' . $db->Quote('5') . ', ' . $db->Quote('1') . ', ' . $db->Quote('10') . ', ' . $db->Quote('100') . ', ' . $db->Quote('Contact Information') . ', ' . $db->Quote('Specify your contact details') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('') . ', ' . $db->Quote('') . '),' .
				'(' . $db->Quote('6') . ', ' . $db->Quote('text') . ', ' . $db->Quote('6') . ', ' . $db->Quote('1') . ', ' . $db->Quote('10') . ', ' . $db->Quote('100') . ', ' . $db->Quote('Mobile phone') .', ' . $db->Quote('Mobile carrier number that other users can contact you.') . ', ' . $db->Quote('1') . ', ' . $db->Quote('0') . ', ' . $db->Quote('1') . ', ' . $db->Quote('') . ', ' . $db->Quote('FIELD_MOBILE') . '),' .
				'(' . $db->Quote('7') . ', ' . $db->Quote('text') . ', ' . $db->Quote('7') . ', ' . $db->Quote('1') . ', ' . $db->Quote('10') . ', ' . $db->Quote('100') . ', ' . $db->Quote('Land phone') . ', ' . $db->Quote('Contact number that other users can contact you.') . ', ' . $db->Quote('1') . ', ' . $db->Quote('0') . ', ' . $db->Quote('1') . ', ' . $db->Quote('') . ', ' . $db->Quote('FIELD_LANDPHONE') . '),' .
				'(' . $db->Quote('8') . ', ' . $db->Quote('textarea') . ', ' . $db->Quote('8') . ', ' . $db->Quote('1') . ', ' . $db->Quote('10') . ', ' . $db->Quote('100') . ', ' . $db->Quote('Address') .', ' . $db->Quote('Your Address') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('') . ', ' . $db->Quote('FIELD_ADDRESS') . '),' .
				'(' . $db->Quote('9') . ', ' . $db->Quote('text') . ', ' . $db->Quote('9') . ', ' . $db->Quote('1') . ', ' . $db->Quote('10') . ', ' . $db->Quote('100') . ', ' . $db->Quote('State') . ', ' . $db->Quote('Your state') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('1') . ', ' . $db->Quote('') . ', ' . $db->Quote('FIELD_STATE') . '),' .
				'(' . $db->Quote('10') . ', ' . $db->Quote('text') . ', ' . $db->Quote('10') .', ' . $db->Quote('1') .', ' . $db->Quote('10') .', ' . $db->Quote('100') .', ' . $db->Quote('City / Town') .', ' . $db->Quote('Your city or town name') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('') .', ' . $db->Quote('FIELD_CITY') . '),' .
				'(' . $db->Quote('11') . ', ' . $db->Quote('country') .', ' . $db->Quote('11') .', ' . $db->Quote('1') .', ' . $db->Quote('10') .', ' . $db->Quote('100') .', ' . $db->Quote('Country') .', ' . $db->Quote('Your country') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('Afghanistan'."\n".'Albania'."\n".'Algeria'."\n".'American Samoa'."\n".'Andorra'."\n".'Angola'."\n".'Anguilla'."\n".'Antarctica'."\n".'Antigua and Barbuda'."\n".'Argentina'."\n".'Armenia'."\n".'Aruba'."\n".'Australia'."\n".'Austria'."\n".'Azerbaijan'."\n".'Bahamas'."\n".'Bahrain'."\n".'Bangladesh'."\n".'Barbados'."\n".'Belarus'."\n".'Belgium'."\n".'Belize'."\n".'Benin'."\n".'Bermuda'."\n".'Bhutan'."\n".'Bolivia'."\n".'Bosnia and Herzegovina'."\n".'Botswana'."\n".'Bouvet Island'."\n".'Brazil'."\n".'British Indian Ocean Territory'."\n".'Brunei Darussalam'."\n".'Bulgaria'."\n".'Burkina Faso'."\n".'Burundi'."\n".'Cambodia'."\n".'Cameroon'."\n".'Canada'."\n".'Cape Verde'."\n".'Cayman Islands'."\n".'Central African Republic'."\n".'Chad'."\n".'Chile'."\n".'China'."\n".'Christmas Island'."\n".'Cocos (Keeling) Islands'."\n".'Colombia'."\n".'Comoros'."\n".'Congo'."\n".'Cook Islands'."\n".'Costa Rica'."\n".'Cote D\'Ivoire (Ivory Coast)'."\n".'Croatia (Hrvatska)'."\n".'Cuba'."\n".'Cyprus'."\n".'Czechoslovakia (former)'."\n".'Czech Republic'."\n".'Denmark'."\n".'Djibouti'."\n".'Dominica'."\n".'Dominican Republic'."\n".'East Timor'."\n".'Ecuador'."\n".'Egypt'."\n".'El Salvador'."\n".'Equatorial Guinea'."\n".'Eritrea'."\n".'Estonia'."\n".'Ethiopia'."\n".'Falkland Islands (Malvinas)'."\n".'Faroe Islands'."\n".'Fiji'."\n".'Finland'."\n".'France'."\n".'France, Metropolitan'."\n".'French Guiana'."\n".'French Polynesia'."\n".'French Southern Territories'."\n".'Gabon'."\n".'Gambia'."\n".'Georgia'."\n".'Germany'."\n".'Ghana'."\n".'Gibraltar'."\n".'Great Britain (UK)'."\n".'Greece'."\n".'Greenland'."\n".'Grenada'."\n".'Guadeloupe'."\n".'Guam'."\n".'Guatemala'."\n".'Guinea'."\n".'Guinea-Bissau'."\n".'Guyana'."\n".'Haiti'."\n".'Heard and McDonald Islands'."\n".'Honduras'."\n".'Hong Kong'."\n".'Hungary'."\n".'Iceland'."\n".'India'."\n".'Indonesia'."\n".'Iran'."\n".'Iraq'."\n".'Ireland'."\n".'Israel'."\n".'Italy'."\n".'Jamaica'."\n".'Japan'."\n".'Jordan'."\n".'Kazakhstan'."\n".'Kenya'."\n".'Kiribati'."\n".'Korea, North'."\n".'South Korea'."\n".'Kuwait'."\n".'Kyrgyzstan'."\n".'Laos'."\n".'Latvia'."\n".'Lebanon'."\n".'Lesotho'."\n".'Liberia'."\n".'Libya'."\n".'Liechtenstein'."\n".'Lithuania'."\n".'Luxembourg'."\n".'Macau'."\n".'Macedonia'."\n".'Madagascar'."\n".'Malawi'."\n".'Malaysia'."\n".'Maldives'."\n".'Mali'."\n".'Malta'."\n".'Marshall Islands'."\n".'Martinique'."\n".'Mauritania'."\n".'Mauritius'."\n".'Mayotte'."\n".'Mexico'."\n".'Micronesia'."\n".'Moldova'."\n".'Monaco'."\n".'Mongolia'."\n".'Montserrat'."\n".'Morocco'."\n".'Mozambique'."\n".'Myanmar'."\n".'Namibia'."\n".'Nauru'."\n".'Nepal'."\n".'Netherlands'."\n".'Netherlands Antilles'."\n".'Neutral Zone'."\n".'New Caledonia'."\n".'New Zealand'."\n".'Nicaragua'."\n".'Niger'."\n".'Nigeria'."\n".'Niue'."\n".'Norfolk Island'."\n".'Northern Mariana Islands'."\n".'Norway'."\n".'Oman'."\n".'Pakistan'."\n".'Palau'."\n".'Panama'."\n".'Papua New Guinea'."\n".'Paraguay'."\n".'Peru'."\n".'Philippines'."\n".'Pitcairn'."\n".'Poland'."\n".'Portugal'."\n".'Puerto Rico'."\n".'Qatar'."\n".'Reunion'."\n".'Romania'."\n".'Russian Federation'."\n".'Rwanda'."\n".'Saint Kitts and Nevis'."\n".'Saint Lucia'."\n".'Saint Vincent and the Grenadines'."\n".'Samoa'."\n".'San Marino'."\n".'Sao Tome and Principe'."\n".'Saudi Arabia'."\n".'Senegal'."\n".'Seychelles'."\n".'S. Georgia and S. Sandwich Isls.'."\n".'Sierra Leone'."\n".'Singapore'."\n".'Slovak Republic'."\n".'Slovenia'."\n".'Solomon Islands'."\n".'Somalia'."\n".'South Africa'."\n".'Spain'."\n".'Sri Lanka'."\n".'St. Helena'."\n".'St. Pierre and Miquelon'."\n".'Sudan'."\n".'Suriname'."\n".'Svalbard and Jan Mayen Islands'."\n".'Swaziland'."\n".'Sweden'."\n".'Switzerland'."\n".'Syria'."\n".'Taiwan'."\n".'Tajikistan'."\n".'Tanzania'."\n".'Thailand'."\n".'Togo'."\n".'Tokelau'."\n".'Tonga'."\n".'Trinidad and Tobago'."\n".'Tunisia'."\n".'Turkey'."\n".'Turkmenistan'."\n".'Turks and Caicos Islands'."\n".'Tuvalu'."\n".'Uganda'."\n".'Ukraine'."\n".'United Arab Emirates'."\n".'United Kingdom'."\n".'United States'."\n".'Uruguay'."\n".'US Minor Outlying Islands'."\n".'USSR (former)'."\n".'Uzbekistan'."\n".'Vanuatu'."\n".'Vatican City State (Holy Sea)'."\n".'Venezuela'."\n".'Viet Nam'."\n".'Virgin Islands (British)'."\n".'Virgin Islands (U.S.)'."\n".'Wallis and Futuna Islands'."\n".'Western Sahara'."\n".'Yemen'."\n".'Yugoslavia'."\n".'Zaire'."\n".'Zambia'."\n".'Zimbabwe') .', ' . $db->Quote('FIELD_COUNTRY'). '),' .
				'(' . $db->Quote('12') . ', ' . $db->Quote('url') . ', ' . $db->Quote('12') .', ' . $db->Quote('1') .', ' . $db->Quote('10') .', ' . $db->Quote('100') .', ' . $db->Quote('Website') .', ' . $db->Quote('Your website') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('') .', ' . $db->Quote('FIELD_WEBSITE') . '),' .
				'(' . $db->Quote('13') . ', ' . $db->Quote('group') . ', ' . $db->Quote('13') .', ' . $db->Quote('1') . ', ' . $db->Quote('10') .', ' . $db->Quote('100') .', ' . $db->Quote('Education') .', ' . $db->Quote('Educations') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('') . ', ' . $db->Quote('') .'),' .
				'(' . $db->Quote('14') . ', ' . $db->Quote('text') .', ' . $db->Quote('14') .', ' . $db->Quote('1') .', ' . $db->Quote('10') .', ' . $db->Quote('200') .', ' . $db->Quote('College / University') .', ' . $db->Quote('Your college or university name') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('') .', ' . $db->Quote('FIELD_COLLEGE') . '),' .
				'(' . $db->Quote('15') . ', ' . $db->Quote('text') . ', ' . $db->Quote('15') .', ' . $db->Quote('1') .', ' . $db->Quote('5') .', ' . $db->Quote('100') .', ' . $db->Quote('Graduation Year') .', ' . $db->Quote('Graduation year') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('1') .', ' . $db->Quote('') .', ' . $db->Quote('FIELD_GRADUATION') .')';

	$db->setQuery( $query );
	$db->query();
	
	if($db->getErrorNum())
	{
		return false;
	}	
	return true;
}

function addDefaultGroupCategories()
{
	$db 	=& JFactory::getDBO();
	
	$query 	= 'INSERT INTO ' . $db->nameQuote('#__community_groups_category') . ' (' . $db->nameQuote('id') .', ' . $db->nameQuote('name') .', ' . $db->nameQuote('description') .') VALUES ' .
				'(' . $db->Quote('1') .', ' . $db->Quote('General') .', ' . $db->Quote('General group category.') .'),'.
				'(' . $db->Quote('2') .', ' . $db->Quote('Internet') .', ' . $db->Quote('Internet group category.') . '),'.
				'(' . $db->Quote('3') .', ' . $db->Quote('Business') .', ' . $db->Quote('Business groups category') .'),'.
				'(' . $db->Quote('4') .', ' . $db->Quote('Automotive') .', ' . $db->Quote('Automotive groups category') .'),'.
				'(' . $db->Quote('5') .', ' . $db->Quote('Music') .', ' . $db->Quote('Music groups category') .')';
	
	$db->setQuery( $query );
	
	$db->query();
	
	if($db->getErrorNum())
	{
		return false;
	}	
	return true;
}

function addDefaultVideosCategories()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'INSERT INTO '.$db->nameQuote('#__community_videos_category') .'(' . $db->nameQuote('id') .', ' . $db->nameQuote('name') .', ' . $db->nameQuote('description') .', ' . $db->nameQuote('published') .' )'.
				' VALUES ( NULL , ' . $db->Quote('General') .', ' . $db->Quote('General video channel') .', ' . $db->Quote('1') .')';
	
	$db->setQuery( $query );
	
	$db->query();
	
	if($db->getErrorNum())
	{
		return false;
	}	
	return true;
}

function addDefaultEventsCategories()
{
	$db		=& JFactory::getDBO();

	$query	= 'INSERT INTO '.$db->nameQuote("#__community_events_category").'(' . $db->nameQuote('name') .', ' . $db->nameQuote('description') .' ) VALUES ' .
				'( ' . $db->Quote('General') .', ' . $db->Quote('General events') .'),'.
				'( ' . $db->Quote('Birthday') .', ' . $db->Quote('Birthday events') .'),'.
				'( ' . $db->Quote('Party') .', ' . $db->Quote('Party events') .')';

	$db->setQuery( $query );

	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}
	return true;
}

function addDefaultUserPoints()
{
	$db 	=& JFactory::getDBO();

	$query = "INSERT INTO `#__community_userpoints` (`rule_name`, `rule_description`, `rule_plugin`, `action_string`, `component`, `access`, `points`, `published`, `system`) VALUES
				('Add Application', 'Give points when registered user add new application.', 'com_community', 'application.add', '', 1, 0, 0, 1),
				('Remove Application', 'Deduct points when registered user remove application.', 'com_community', 'application.remove', '', 1, 0, 0, 1),
				('Upload Photo', 'Give points when registered user upload photos.', 'com_community', 'photo.upload', '', 1, 0, 1, 1),
				('Add New Group', 'Give points when registered user created new group.', 'com_community', 'group.create', '', 1, 3, 1, 1),
				('Add New Group''s Discussion', 'Give points when registered user created new discussion on group.', 'com_community', 'group.discussion.create', '', 1, 2, 1, 1),
				('Leave Group', 'Deduct points when registered user leave a group.', 'com_community', 'group.leave', '', 1, -1, 1, 1),
				('Approve Friend Request', 'Give points when registered user approved friend request.', 'com_community', 'friends.request.approve', '', 1, 1, 1, 1),
				('Add New Photo Album', 'Give points when registered user added new photo album.', 'com_community', 'album.create', '', 1, 1, 1, 1),
				('Post Group Wall', 'Give points when registered user post wall on group.', 'com_community', 'group.wall.create', '', 1, 1, 1, 1),
				('Join Group', 'Give points when registered user joined a group.', 'com_community', 'group.join', '', 1, 1, 1, 1),
				('Reply Group''s Discussion', 'Give points when registered user replied on group''s discussion.', 'com_community', 'group.discussion.reply', '', 1, 1, 1, 1),
				('Post Wall', 'Give points when registered user post a wall on profile.', 'com_community', 'profile.wall.create', '', 1, 1, 1, 1),
				('Profile Status Update', 'Give points when registered user update their profile status.', 'com_community', 'profile.status.update', '', 1, 1, 1, 1),
				('Profile Update', 'Give points when registered user update their profile.', 'com_community', 'profile.save', '', 1, 1, 1, 1),
				('Update group', 'Give points when registered user update the group.', 'com_community', 'group.updated', '', 1, 1, 1, 1),
				('Upload group avatar', 'Give points when registered user upload avatar to group.', 'com_community', 'group.avatar.upload', '', 1, 0, 1, 1),
				('Create Group''s News', 'Give points when registered user add group''s news.', 'com_community', 'group.news.create', '', 1, 1, 1, 1),
				('Post Wall for photos', 'Give points when registered user post wall at photos.', 'com_community', 'photos.wall.create', '', 1, 1, 1, 1),
				('Remove Friend', 'Deduct points when registered user remove a friend.', 'com_community', 'friends.remove', '', 1, 0, 1, 1),
				('Upload profile avatar', 'Give points when registered user upload profile avatar.', 'com_community', 'profile.avatar.upload', '', 1, 0, 1, 1),
				('Update privacy', 'Give points when registered user updated privacy.', 'com_community', 'profile.privacy.update', '', 1, 0, 1, 1),
				('Reply Messages', 'Give points when registered user reply a message.', 'com_community', 'inbox.message.reply', '', 1, 0, 1, 1),
				('Send Messages', 'Give points when registered user send a message.', 'com_community', 'inbox.message.send', '', 1, 0, 1, 1),
				('Remove Group member', 'Deduct points when registered user remove a group memeber.', 'com_community', 'group.member.remove', '', 1, 0, 1, 1),
				('Delete news', 'Deduct points when registered user remove a news.', 'com_community', 'group.news.remove', '', 1, 0, 1, 1),
				('Remove Wall', 'Deduct points to original poster when registered user remove a wall.', 'com_community', 'wall.remove', '', 1, 0, 1, 1),
				('Remove Photo album', 'Deduct points when registered user remove a photo album.', 'com_community', 'album.remove', '', 1, 0, 1, 1),
				('Remove photos', 'Deduct points when registered user remove a photo.', 'com_community', 'photo.remove', '', 1, 0, 1, 1),
                                ('Update Event', 'Give points when registered user update the event.', 'com_community', 'events.update', '', 1, 1, 1, 1)";

	$db->setQuery( $query );
	$db->query();
	
	if($db->getErrorNum())
	{
		return false;
	}	
	return true;
}

function menuExist()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' .  $db->Quote( '%option=com_community%') . ' '
			. 'AND ' . $db->nameQuote('menutype') . ' != ' . $db->Quote('main');

	$db->setQuery( $query );
	
	$needUpdate	= ( $db->loadResult() >= 1 ) ? true : false;
	
	return $needUpdate;
}

function menuTypesExist()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu_types' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'menutype' ) . ' = ' .  $db->Quote( 'jomsocial') . ' '
			. 'AND ' . $db->nameQuote('title') . ' = ' . $db->Quote('JomSocial toolbar');

	$db->setQuery( $query );
	
	$needUpdate	= ( $db->loadResult() >= 1 ) ? true : false;
	
	return $needUpdate;
}

/**
 * Method to update menu's component id.
 *
 * @return boolean true on success false on failure.
 */
function updateMenuItems()
{
	// Get new component id.
	$component		= JComponentHelper::getComponent('com_community');
	$component_id	= 0;
	if (is_object($component) && isset($component->id)){
		$component_id 	= $component->id;
	}
	
	if ($component_id > 0)
	{
		// Update the existing menu items.
		$db 	= JFactory::getDBO();
		
		$query 	= 'UPDATE ' . $db->nameQuote( '#__menu' ) . ' '
				. 'SET '.$db->nameQuote(JOOMLA_MENU_COMPONENT_ID).'=' . $db->Quote( $component_id ) . ' '
				. 'WHERE ' . $db->nameQuote('link') .' LIKE ' . $db->Quote('%option=com_community%');
	
		$db->setQuery( $query );
		$db->query();
		
		if($db->getErrorNum())
		{
			return false;
		}
	}
	return true;
}

function needsDefaultCustomFields()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_fields' );
	$db->setQuery( $query );

	$needUpdate	= ( $db->loadResult() > 0 ) ? false : true;
	
	return $needUpdate;
}

function needsDefaultGroupCategories()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups_category' );
	$db->setQuery( $query );

	$needUpdate	= ( $db->loadResult() > 0 ) ? false : true;
	
	return $needUpdate;
}

function needsDefaultVideosCategories()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_videos_category' );
	$db->setQuery( $query );

	$needUpdate	= ( $db->loadResult() > 0 ) ? false : true;
	
	return $needUpdate;
}

function needsDefaultEventsCategories()
{
	$db		=& JFactory::getDBO();

	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_events_category' );
	$db->setQuery( $query );

	$needUpdate	= ( $db->loadResult() > 0 ) ? false : true;

	return $needUpdate;
}

function needsDefaultUserPoints()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_userpoints' );
	$query	.= ' WHERE ' . $db->nameQuote('system') .' = 1';
	$db->setQuery( $query );

	$needUpdate	= ( $db->loadResult() > 0 ) ? false : true;
	
	return $needUpdate;
}

function addDefaultToolbarMenus()
{
	$db		= JFactory::getDBO();
	$file	= JPATH_ROOT . DS . 'administrator' .  DS . 'components' . DS . 'com_community' . DS . 'toolbar.xml';
	$menu_name = JOOMLA_MENU_NAME;
	$menu_parent = JOOMLA_MENU_PARENT;
	$menu_level = JOOMLA_MENU_LEVEL;
	$parser =& JFactory::getXMLParser('Simple');
	$parser->loadFile( $file );
	
	$items	= $parser->document->getElementByPath( 'items' );

	$i	= 1;
	foreach( $items->children() as $item )
	{
		$obj		= new stdClass();

		// Retrieve the menu name
		$element	= $item->getElementByPath( 'name' );
		$obj->$menu_name	= !empty( $element ) ? $element->data() : '';

		// Retrieve the menu alias
		$element	= $item->getElementByPath( 'alias' );
		$obj->alias	= !empty( $element ) ? $element->data() : '';

		// Retrieve the menu link
		$element	= $item->getElementByPath( 'link' );
		$obj->link	= !empty( $element ) ? $element->data() : '';
		
		$obj->menutype	= 'jomsocial';
		$obj->type		= 'component';
		$obj->published	= 1;
		$obj->$menu_parent	= JOOMLA_MENU_ROOT_PARENT;
		$obj->$menu_level	= JOOMLA_MENU_LEVEL_PARENT;
		$obj->ordering	= $i;
		

		$childs			= $item->getElementByPath( 'childs' );
		
		if (!JOOMLA_LEGACY_VERSION)
		{
			$obj->language	= '*';

			//J1.6: menu item ordering follow lft and rgt
			$query 	= 'SELECT ' . $db->nameQuote( 'rgt' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'ORDER BY ' . $db->nameQuote( 'rgt' ) . ' DESC LIMIT 1';
			$db->setQuery( $query );
			$obj->lft 	= $db->loadResult() + 1;
			$totalchild = $childs?count($childs->children()):0;
			$obj->rgt	= $obj->lft + $totalchild * 2 + 1;
		}
			
		$db->insertObject( '#__menu' , $obj );
		if ($db->getErrorNum()) {
			return false;
		}
		$parentId		= $db->insertid();
		
		if( $childs )
		{	
			$x	= 1;
			
			foreach( $childs->children() as $child )
			{
				$childObj		= new stdClass();
				
				// Retrieve the menu name
				$element	= $child->getElementByPath( 'name' );
				$childObj->$menu_name	= !empty( $element ) ? $element->data() : '';

				// Retrieve the menu alias
				$element	= $child->getElementByPath( 'alias' );
				$childObj->alias	= !empty( $element ) ? $element->data() : '';
		
				// Retrieve the menu link
				$element	= $child->getElementByPath( 'link' );
				$childObj->link	= !empty( $element ) ? $element->data() : '';
				
				$childObj->menutype	= 'jomsocial';
				$childObj->type		= 'component';
				$childObj->published	= 1;
				$childObj->$menu_parent	= $parentId;
				$childObj->$menu_level	= JOOMLA_MENU_LEVEL_PARENT + 1;
				$childObj->ordering	= $x;
				
				if (!JOOMLA_LEGACY_VERSION)
				{
					$childObj->language	= '*';
					//J1.6: menu item ordering follow lft and rgt
					$childObj->lft 	= $obj->lft + ($x - 1)* 2 + 1;
					$childObj->rgt	= $childObj->lft + 1;
				}

				$db->insertObject( '#__menu' , $childObj );
				if ($db->getErrorNum()) {
					return false;
				}
				
				$x++;
			}
		}
		$i++;
	}
	return true;
}

function addDefaultMenuTypes()
{
	$db		= JFactory::getDBO();
	$query	= 'INSERT INTO ' . $db->nameQuote( '#__menu_types' ) . ' (' . $db->nameQuote('menutype') .',' . $db->nameQuote('title') .',' . $db->nameQuote('description') .') VALUES '
	    			. '( ' . $db->Quote( 'jomsocial' ) . ',' . $db->Quote( 'JomSocial toolbar' ) . ',' . $db->Quote( 'Toolbar items for JomSocial toolbar') . ')';
	$db->setQuery( $query );
	$db->Query();
	if ($db->getErrorNum())
	{
		return false;
	}
	return true;
}
