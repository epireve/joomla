<?php
/**
 * @category	Libraries
 * @package	JomSocial
 * @copyright	(C) 2010 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	GNU/GPL, see LICENSE.php
 */
 
defined('_JEXEC') or die('Restricted access');

class CLike 
{

	public function addLike( $element, $itemId )
	{
		$my		=   CFactory::getUser();

		$like		=&  JTable::getInstance( 'Like' , 'CTable' );
		$like->loadInfo( $element, $itemId );

		$like->element	=   $element;
		$like->uid	=   $itemId;

		// Check if user already like
		$likesInArray	=   explode( ',', trim( $like->like, ',' ) );
		array_push( $likesInArray, $my->id );
		$likesInArray	=   array_unique( $likesInArray );
		$like->like	=   ltrim( implode( ',', $likesInArray ), ',' );

		// Check if the user already dislike
		$dislikesInArray    =   explode( ',', trim( $like->dislike, ',' ) );
		if( in_array( $my->id, $dislikesInArray ) )
		{
			// Remove user dislike from array
			$key	=   array_search( $my->id, $dislikesInArray  );
			unset( $dislikesInArray [$key] );

			$like->dislike	    =   implode( ',', $dislikesInArray  );
		}

		$like->store();
	}

	public function addDislike( $element, $itemId )
	{
		$my		=   CFactory::getUser();

		$dislike	=&  JTable::getInstance( 'Like' , 'CTable' );
		$dislike->loadInfo( $element, $itemId );

		$dislike->element   =   $element;
		$dislike->uid	    =   $itemId;

		$dislikesInArray    =   explode( ',', $dislike->dislike );
		array_push( $dislikesInArray, $my->id );
		$dislikesInArray    =   array_unique( $dislikesInArray );
		$dislike->dislike   =   ltrim( implode( ',', $dislikesInArray ), ',' );

		// Check if the user already like
		$likesInArray	=   explode( ',', $dislike->like );
		if( in_array( $my->id, $likesInArray ) )
		{
			// Remove user like from array
			$key	=   array_search( $my->id, $likesInArray );
			unset( $likesInArray[$key] );

			$dislike->like	    =   implode( ',', $likesInArray );			
		}

		$dislike->store();
	}

	public function unlike( $element, $itemId )
	{
		$my	=   CFactory::getUser();

		$like	=&  JTable::getInstance( 'Like' , 'CTable' );
		$like->loadInfo( $element, $itemId );

		// Check if the user already like
		$likesInArray	    =   explode( ',', $like->like );
		if( in_array( $my->id, $likesInArray ) )
		{
			// Remove user like from array
			$key	=   array_search( $my->id, $likesInArray );
			unset( $likesInArray[$key] );

			$like->like =   implode( ',', $likesInArray );

		}
		
		// Check if the user already dislike
		$dislikesInArray    =   explode( ',', $like->dislike );
		if( in_array( $my->id, $dislikesInArray ) )
		{
			// Remove user dislike from array
			$key	=   array_search( $my->id, $dislikesInArray );
			unset( $dislikesInArray[$key] );

			$like->dislike =   implode( ',', $dislikesInArray );

		}
		
		$like->store();
	}

	// Check if the user like this
	// Returns:
	// -1	- Unlike
	// 1	- Like
	// 0	- Dislike
	public function userLiked( $element, $itemId, $userId )
	{
		$like	=&  JTable::getInstance( 'Like' , 'CTable' );
		$like->loadInfo( $element, $itemId );

		// Check if user already like
		$likesInArray	=   explode( ',', trim( $like->like, ',' ));

		if( in_array( $userId, $likesInArray ) )
		{
			// Return 1, the user is liked
			return COMMUNITY_LIKE;
		}

		// Check if user already dislike
		$dislikesInArray	=   explode( ',', trim( $like->dislike, ',' ) );

		if( in_array( $userId, $dislikesInArray ) )
		{
			// Return 0, the user is disliked
			return COMMUNITY_DISLIKE;
		}

		// Return -1 as neutral
		return COMMUNITY_UNLIKE;
	}

	/**
	 * Can current $my user 'like' an item ?
	 * - rule: friend can like friend's item (photos/vidoes/event)
	 * @return bool
	 */
	public function canLike()
	{
		$my =	CFactory::getInstance();

		return ( $my->id != 0 );
	}
	
	/**
	 * Return number of likes
	 */
	public function getLikeCount($element, $itemId)
	{
		$like	=&  JTable::getInstance( 'Like' , 'CTable' );
		$like->loadInfo( $element, $itemId );
		$count = 0;
		
		if( !empty ($like->like) )
		{
			$likesInArray	=   explode( ',', trim( $like->like, ',' ) );
			$count		=	count( $likesInArray );
		}
		
		return $count;
	}
	
	/**
	 * Return an array of user who likes the element
	 * @return CUser objects
	 */
	public function getWhoLikes( $element, $itemId )
	{
		$like	=&  JTable::getInstance( 'Like' , 'CTable' );
		$like->loadInfo( $element, $itemId );
		
		$users = array();
		$likesInArray = array();
		
		if( !empty ($like->like) )
		{
			$likesInArray	=   explode( ',', trim( $like->like, ',' ) );
		}
		
		foreach($likesInArray as $row)
		{
			$user = CFactory::getUser($row);
			$users[] = $user;
		}
		
		return $users;
	}

	/**
	 *
	 * @return bool True if element can be liked
	 */
	public function enabled($element)
	{
		$config		=& CFactory::getConfig();
		
		// Element can also contain sub-element. eg:// photos.album
		// for enable/disable configuration, we only check the first component
		$elements = explode( '.', $element );
		return ( $config->get( 'likes_' . $elements[0] ) );

	}

	/**
	 *
	 * @return string
	 */
	public function getHTML( $element, $itemId, $userId )
	{
		// @rule: Only display likes html codes when likes is allowed.
		$config		=& CFactory::getConfig();

		if( !$this->enabled($element) )
		{
			return;
		}

		$like	=&  JTable::getInstance( 'Like' , 'CTable' );
		$like->loadInfo( $element, $itemId );

		$userLiked	    =	COMMUNITY_UNLIKE;
		$likesInArray	    =	array();
		$dislikesInArray    =	array();
		$likes		    =	0;
		$dislikes	    =	0;

		if( !empty ($like->like) )
		{
			$likesInArray	=   explode( ',', trim( $like->like, ',' ) );
			$likes		=	count( $likesInArray );
		}

		if( !empty ($like->dislike) )
		{
			$dislikesInArray    =   explode( ',', trim( $like->dislike, ',' ) );
			$dislikes	    =	count( $dislikesInArray );
		}

		$userLiked  =	$this->userLiked( $element, $itemId, $userId );

		$tmpl	= new CTemplate();

		// For rendering, we need to replace . with _ since it is not
		// a valid id
		$element = str_replace( '.', '_', $element );
		$tmpl->set( 'likeId' ,      'like'.'-'.$element.'-'.$itemId );
		$tmpl->set( 'likes',	    $likes );
		$tmpl->set( 'dislikes',	    $dislikes );
		$tmpl->set( 'userLiked',    $userLiked );
		
		if(!COwnerHelper::isRegisteredUser())
		{
			return $this->getHtmlPublic( $element, $itemId );
		}
		else
		{
			return $tmpl->fetch( 'like.html' );
		}       		
	}

	/**
	 * Display like/dislike for public
	 * @return string
	 */
	public function getHtmlPublic( $element, $itemId )
	{   
		$config		= CFactory::getConfig();     

		if( !$config->get( 'likes_' . $element ) )
		{
			return;
		}
		
		$like	=&  JTable::getInstance( 'Like' , 'CTable' );
		$like->loadInfo( $element, $itemId );

		$likesInArray	    =	array();
		$dislikesInArray    =	array();
		$likes		    =	0;
		$dislikes	    =	0;

		if( !empty ($like->like) )
		{
			$likesInArray	    =   explode( ',', trim( $like->like, ',' ) );
			$likes	    =	count( $likesInArray );
		}

		if( !empty ($like->dislike) )
		{
			$dislikesInArray    =   explode( ',', trim( $like->dislike, ',' ) );
			$dislikes   =	count( $dislikesInArray );
		}

		$tmpl	= new CTemplate();
		$tmpl->set( 'likes',	    $likes );
		$tmpl->set( 'dislikes',	    $dislikes );
		
		if( $config->get('show_like_public') )
		{
			return $tmpl->fetch( 'like.public' );
		}
		
	}
	
}