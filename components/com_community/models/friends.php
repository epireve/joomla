<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Friends
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ('Restricted access');

require_once (JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

class CommunityModelFriends extends JCCModel
implements CLimitsInterface, CNotificationsInterface
{
    var $_data = null;
    var $_profile;
    var $_pagination;

    public function CommunityModelFriends()
    {
        parent::JCCModel();
        global $option;
        $mainframe = & JFactory::getApplication();

        // Get pagination request variables
        $limit = ($mainframe->getCfg('list_limit') == 0)?5:$mainframe->getCfg('list_limit');
        $limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0?(floor($limitstart/$limit)*$limit):
            0);

            $this->setState('limit', $limit);
            $this->setState('limitstart', $limitstart);
        }
		
		/**
		 * Deprecated since 1.8
		 */	
        public function addFriendCount($userId)
        {
             $this->updateFriendCount($userId);
			 return $this;
        }
		
		/**
		 * Deprecated since 1.8
		 */		 		
        public function substractFriendCount($userId)
        {
            $this->updateFriendCount($userId);
        }
        
        public function updateFriendCount($userId)
        {
		$db = & $this->getDBO();
		$count = $this->getFriendsCount($userId);

		$user = CFactory::getUser( $userId );
		$user->updateFriendList(); 
		$user->save();

		$query =    'UPDATE '.$db->nameQuote('#__community_users')
			    .'SET '.$db->nameQuote('friendcount').'='.$db->Quote($count)
			    .'WHERE '.$db->nameQuote('userid').'='.$db->Quote($userId);
		
		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}
		
		return $this;
	}

        public function & getData()
        {
            if ( empty($this->_data))
            {
                $this->_data = array ();

                $this->_data['name'] = 'Testing';
                $this->_data['status'] = 'Alive';
            }

            return $this->_data;
        }

        public function & getFiltered($wheres = array ())
        {
            $db = & $this->getDBO();

            $wheres[] = 'block = 0';

            $query = "SELECT *"
            .' FROM '. $db->nameQuote('#__users')
            .' WHERE '.implode(' AND ', $wheres)
            .' ORDER BY '. $db->nameQuote('id').' DESC ';

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

            $result = $db->loadObjectList();
            return $result;
        }

        /**
         * Search for people
         * @param query	string	people's name to seach for
         */
        public function searchPeople($query)
        {
        	$db = & $this->getDBO();
            $filter = array ();
            $strict = true;
            $regex = $strict?
            '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i':
            '/^([*+!.&#$�\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i'
            ;
            if (preg_match($regex, JString::trim($query), $matches))
            {
                $query = array ($matches[1], $matches[2]);
                $filter = array ("`email`='{$matches[1]}@{$matches[2]}'");
            } 
			else
            {
                $filter = array ($db->nameQuote('username') ."=". $db->Quote($query));
            }

            $result = $this->getFiltered($filter);

            // for each one of these people, we need to load their relationship with
            // our current user
            // 		if(!empty($result)){
            // 			for($i = 0; $i = $result; $i++){
            //
            // 			}
            // 		}

            return $result;
        }


        /**
         * Save a friend request to stranger. Stranger will have to approve
         * @param	$id		int		stranger user id
         * @param   $fromid int     owner's id
         */
        public function addFriend($id, $fromid, $msg='', $status = 0)
        {
            $my = JFactory::getUser();
            $db = & $this->getDBO();
            $wheres[] = 'block = 0';

            if ($my->id == $id)
            {
                JError::raiseError(500, JText::_('COM_COMMUNITY_FRIEND_ADD_YOURSELF_ERROR'));
            }

            $date	=& JFactory::getDate(); //get the time without any offset!
            $query	= 'INSERT INTO '. $db->nameQuote('#__community_connection')
            	.' SET ' . $db->nameQuote('connect_from').' = '.$db->Quote($fromid)
            	. ', '. $db->nameQuote('connect_to').' = '.$db->Quote($id)
            	. ', '. $db->nameQuote('status').' = '. $db->Quote($status)
            	. ', '. $db->nameQuote('created').' = ' . $db->Quote($date->toMySQL())
				. ', '. $db->nameQuote('msg').' = ' . $db->Quote($msg);

            $db->setQuery($query);
            $db->query();
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
			
			return $this;
        }

        /**
         * Send a friend request to stranger. Stranger will have to approve
         * @param	$id		int		stranger user id
         */
        public function addFriendRequest($id, $fromid)
        {
            $my = JFactory::getUser();
            $db = & $this->getDBO();
            $wheres[] = 'block = 0';

            if ($my->id == $id)
            {
                JError::raiseError(500, JText::_('COM_COMMUNITY_FRIEND_ADD_YOURSELF_ERROR'));
            }

			$date	=& JFactory::getDate(); //get the time without any offset!
			
            $query = 'INSERT INTO '. $db->nameQuote('#__community_connection')
            	. ' SET '. $db->nameQuote('connect_from').'='.$db->Quote($fromid)
            	. ', '. $db->nameQuote('connect_to').'='.$db->Quote($id)
            	. ', '. $db->nameQuote('status').'='. $db->Quote(1)
            	. ', '. $db->nameQuote('created').' = ' . $db->Quote($date->toMySQL());

            $db->setQuery($query);
            $db->query();
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }


            //@todo escape code
            $query = 'INSERT INTO '. $db->nameQuote('#__community_connection')
            		.' SET '. $db->nameQuote('connect_from').'='.$db->Quote($id)
            		. ', '. $db->nameQuote('connect_to').'='.$db->Quote($fromid)
            		. ', '. $db->nameQuote('status').'='. $db->Quote(1)
            		. ', '. $db->nameQuote('created').' = ' . $db->Quote($date->toMySQL());

            $db->setQuery($query);
            $db->query();
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
			
			return $this;
        }

		
        /**
         *@param $id int user id
         *@param $groupid int group id
         *
         */
        public function deleteFriendGroup($id, $groupid)
        {
            $db = & $this->getDBO();
            $query = 'DELETE FROM '. $db->nameQuote('#__community_friendlist')
            		.' WHERE '. $db->nameQuote('user_id').'='.$db->Quote($id)
            		.' AND '. $db->nameQuote('group_id').'='.$db->Quote($groupid);
            $db->setQuery($query);
            $db->query();

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

        }


        /**
         *@param $id int user id
         *@param $groupid int group id
         *
         */
        public function deleteFriendsTag($id, $groupid)
        {
            $db = & $this->getDBO();
            $query = 'DELETE FROM '. $db->nameQuote('#__community_friendgroup')
            		.' WHERE '. $db->nameQuote('user_id').'='.$db->Quote($id)
            		.' AND '. $db->nameQuote('group_id').'='.$db->Quote($groupid);
            $db->setQuery($query);
            $db->query();


            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

            return true;

        }


        /**
         * Delete sent request
         */
        public function deleteSentRequest($from, $to)
        {
            $db = & $this->getDBO();

            $query = 'DELETE FROM '. $db->nameQuote('#__community_connection')
				  	.' WHERE '. $db->nameQuote('connect_from').' = '.$db->Quote($from)
				  	.' AND '. $db->nameQuote('connect_to').' = '.$db->Quote($to)
				  	.' AND '. $db->nameQuote('status').' = '. $db->Quote(0);

            $db->setQuery($query);
            $db->query();

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

            return true;
        }

        /**
         *delete friend connection
         *@param $conn_from int user_id should use JFactory::getUser() id
         *@param $conn_to int user_id
         *@return true when delete success
         */

        public function deleteFriend($conn_from, $conn_to)
        {
            $db = & $this->getDBO();
            //1- check connection exist or not
            $query = 'SELECT * FROM '. $db->nameQuote('#__community_connection')
	            		.' WHERE '. $db->nameQuote('connect_from').'= '.$db->Quote($conn_from)
	            		.' AND '. $db->nameQuote('connect_to').' = '.$db->Quote($conn_to)
	            		.' AND '. $db->nameQuote('status').'='. $db->Quote(1);

            $db->setQuery($query);

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
            $rows1 = $db->loadObject();

            $query = 'SELECT * FROM '. $db->nameQuote('#__community_connection')
	            	.' WHERE '. $db->nameQuote('connect_from').' = '.$db->Quote($conn_to)
	            	.' AND '. $db->nameQuote('connect_to').' = '.$db->Quote($conn_from)
	            	.' AND '. $db->nameQuote('status').'='. $db->Quote(1);

            $db->setQuery($query);

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
            $rows2 = $db->loadObject();

            //@todo avoid sql injection..
            //2- delete connection
            if (count($rows1) > 0 && count($rows2) > 0)
            {
                $query = 'DELETE FROM '. $db->nameQuote('#__community_connection')
			        	.' WHERE '. $db->nameQuote('connection_id').' in ('.$db->Quote($rows1->connection_id).','.$db->Quote($rows2->connection_id).')';
                $db->setQuery($query);
                $db->query();

                //@todo remove friend's tag too..

                if ($db->getErrorNum())
                {
                    JError::raiseError(500, $db->stderr());
                }

                return true;
            }

        }

        /**
         *Retrieve friend assigned tag
         *@param $filter array, where statement
         *@return $result obj, records
         */
        public function & getFriendsTag($filter = array ())
        {
            $db = & $this->getDBO();
            $query = 'SELECT * FROM '. $db->nameQuote('#__community_friendgroup');

            $wheres = array ();
            foreach ($filter as $column=>$value)
            {
                $wheres[] = $db->nameQuote($column).'='. $db->Quote($value);
            }


            if (count($wheres) > 0)
            {
                $query .= ' WHERE '.implode(' AND ', $wheres);
            }

            $db->setQuery($query);

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

            $result = $db->loadObjectList();
            return $result;

        }

        /**
         * Return friends with the given tag id
         */
        public function getFriendsWithTag($tagid)
        {
            $db = & $this->getDBO();

            // select all frinds with the given group
            // @todo: check and make sure the given tagid belog to current user
            $query = 'SELECT '. $db->nameQuote('user_id')
            			.' FROM '. $db->nameQuote('#__community_friendgroup')
            			.' WHERE '. $db->nameQuote('group_id').'='.$db->Quote($tagid);
            $db->setQuery($query);
            $result = $db->loadObjectList();

            foreach ($result as $row)
            {
                $userid[] = $row->user_id;
            }

            // With all the list of friends, we now need to load their info
            $userids = implode(',', $userid);
            $where = array ();
            $where[] = $db->nameQuote('id') .' IN ($userids)';
            $result = $this->getFiltered($where);

            return $result;
        }

        /**
         *Retrieve friend's tagsname and name
         *@param $user_id int, user id
         *@return $tagNames array, return tag names
         */
        public function getFriendsTagNames($user_id)
        {
            $db = & $this->getDBO();


            $query = 'SELECT fg.*,fl.'. $db->nameQuote('group_name').',u.'. $db->nameQuote('name')
            		.' FROM	'. $db->nameQuote('#__community_friendgroup').' fg'
					.' join '. $db->nameQuote('#__community_friendlist').' fl on (fg.'. $db->nameQuote('group_id').'=fl.'. $db->nameQuote('group_id').')'
					.' join '. $db->nameQuote('#__users').' u on (fg.'. $db->nameQuote('user_id').'=u.'. $db->nameQuote('id').')'
					.' WHERE fg.'. $db->nameQuote('user_id').'='.$db->Quote($user_id);


            $db->setQuery($query);

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

            $rows = $db->loadObjectList();

            if (count($rows) > 0)
            {
                $tagNames = array();
				foreach ($rows as $row)
                {
                    $tagNames[$row->group_id] = $row->group_name;

                }
                return $tagNames;
            }
            else
            {
                return array ();
            }
        }



        /**
         * Get all people what are waiting to get user's approval
         * @param	id	int		userid of the user responsible for approving it
         */
        public function getPending($id)
        {
			if($id == 0)
			{
				// guest obviouly hasn't send any request
				return null;
			}
			
            $db = & $this->getDBO();

            $wheres[] = 'block = 0';

            $limit = $this->getState('limit');
            $limitstart = $this->getState('limitstart');
			
            $total = $this->countPending($id);

            // Apply pagination
            if ( empty($this->_pagination))
            {
                jimport('joomla.html.pagination');
                $this->_pagination = new JPagination($total, $limitstart, $limit);
            }
			
			$query = 'SELECT b.*, a.'. $db->nameQuote('connection_id').', a.'. $db->nameQuote('msg')
            .' FROM '. $db->nameQuote('#__community_connection').' as a, '. $db->nameQuote('#__users').' as b'
            .' WHERE a.'. $db->nameQuote('connect_to').'='.$db->Quote($id)
            .' AND a.'. $db->nameQuote('status').'='. $db->Quote(0)
            .' AND a.'. $db->nameQuote('connect_from').'=b.'. $db->nameQuote('id')
            .' ORDER BY a.'. $db->nameQuote('connection_id').' DESC '
            ." LIMIT {$limitstart}, {$limit} ";

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
            $result = $db->loadObjectList();
            return $result;
        }
        
        /**
         * Count total pending request.
         **/
        public function countPending($id)
        {
        	$db = & $this->getDBO();
        	
        	$query = "SELECT count(*) "
            .' FROM '. $db->nameQuote('#__community_connection').' as a, '. $db->nameQuote('#__users').' as b'
            .' WHERE a.'. $db->nameQuote('connect_to').'='.$db->Quote($id)
            .' AND a.'. $db->nameQuote('status').'='. $db->Quote(0)
            .' AND a.'. $db->nameQuote('connect_from').'=b.'. $db->nameQuote('id')
            .' ORDER BY a.'. $db->nameQuote('connection_id').' DESC ';

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
            
            return $db->loadResult();
        }
        
        /**
         * Lets caller know if the request really belongs to the UserId
         **/
        public function isMyRequest($requestId, $userId)
        {
            $db = & $this->getDBO();

            $query = 'SELECT COUNT(*) FROM '
            .$db->nameQuote('#__community_connection')
            .'WHERE '.$db->nameQuote('connection_id').'='.$db->Quote($requestId).' '
            .'AND '.$db->nameQuote('connect_to').'='.$db->Quote($userId);

            $db->setQuery($query);
            $status = ($db->loadResult() > 0)?true:false;

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

            return $status;
        }

        /**
         * approve the requested friend connection
         * @param	id 	int		the connection request id
         * @return	true if everything is ok
         */
        public function approveRequest($id)
        {
            $connection = array ();
            $db = & $this->getDBO();
            //get connect_from and connect_to
            $query = 'SELECT '. $db->nameQuote('connect_from').','. $db->nameQuote('connect_to')
            .' FROM '. $db->nameQuote('#__community_connection')
            .' WHERE '. $db->nameQuote('connection_id').'='.$db->Quote($id);

            $db->setQuery($query);
            $conn = $db->loadObject();

            if (! empty($conn))
            {
                $connect_from = $conn->connect_from;
                $connect_to = $conn->connect_to;

                $connection[] = $connect_from;
                $connection[] = $connect_to;

                //delete connection id
                $query = 'DELETE FROM '. $db->nameQuote('#__community_connection')
                		.' WHERE '. $db->nameQuote('connection_id').'='.$db->Quote($id);

                $db->setQuery($query);
                $db->query();
                if ($db->getErrorNum())
                {
                    JError::raiseError(500, $db->stderr());
                }


				$date	=& JFactory::getDate(); //get the time without any offset!
                //do double entry                
                //@todo escape code
                $query = 'INSERT INTO '. $db->nameQuote('#__community_connection')
                	.' SET '. $db->nameQuote('connect_from').'='.$db->Quote($connect_from)
                	.', '. $db->nameQuote('connect_to').'='.$db->Quote($connect_to)
                	.', '. $db->nameQuote('status').'='. $db->Quote(1)
                	.', '. $db->nameQuote('created').' = ' . $db->Quote($date->toMySQL());

                $db->setQuery($query);
                $db->query();
                if ($db->getErrorNum())
                {
                    JError::raiseError(500, $db->stderr());
                }


                //@todo escape code
                $query = 'INSERT INTO '. $db->nameQuote('#__community_connection')
                	.' SET '. $db->nameQuote('connect_from').'='.$db->Quote($connect_to)
                	. ', '. $db->nameQuote('connect_to').'='.$db->Quote($connect_from)
                	. ', '. $db->nameQuote('status').'='. $db->Quote(1)
                	. ', '. $db->nameQuote('created').' = ' . $db->Quote($date->toMySQL());

                $db->setQuery($query);
                $db->query();
                if ($db->getErrorNum())
                {
                    JError::raiseError(500, $db->stderr());
                }

                return $connection;
            }
            else
            {
                // Return null is null
                return null;
            }
        }


        /**
         * reject the requested friend connection
         * @param	id 	int		the connection request id
         * @return	true if everything is ok
         */
        public function rejectRequest($id)
        {
            $db = & $this->getDBO();

            //validating the connection id to avoid injection
            $query = 'SELECT '. $db->nameQuote('connect_from').','. $db->nameQuote('connect_to')
		          		.' FROM '. $db->nameQuote('#__community_connection')
				  		.' WHERE '. $db->nameQuote('connection_id').' = '.$db->Quote($id);

            $db->setQuery($query);
            $conn = $db->loadObject();

            if (! empty($conn))
            {

                //delete connection id
                $query = 'DELETE FROM '. $db->nameQuote('#__community_connection')
                		.' WHERE '. $db->nameQuote('connection_id').'='.$db->Quote($id);

                $db->setQuery($query);
                $db->query();

                if ($db->getErrorNum())
                {
                    JError::raiseError(500, $db->stderr());
                }

                return true;
            } else
            {
                return false;
            }
        }
		
	public function getTotalToday($id)
	{
		// guest obviouly hasn't send any request
		if($id == 0)
		{
			return null;
		}
			
        $db 	= & $this->getDBO();
		$date	= & JFactory::getDate();
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote('#__community_connection').' AS a '
				. ' WHERE a.'. $db->nameQuote('connect_from').'=' . $db->Quote( $id )
				. ' AND TO_DAYS(' . $db->Quote( $date->toMySQL( true ) ) . ') - TO_DAYS( DATE_ADD( a.'. $db->nameQuote('created').' , INTERVAL ' . $date->getOffset() . ' HOUR ) ) = 0 ';
		$db->setQuery( $query );

		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}
		$total = $db->loadResult();
		
		return $total;
	}
        
        /**
         * Get all request that the user has send but not yet approved
         */
        public function getSentRequest($id)
        {
        	if($id == 0)
			{
				// guest obviouly hasn't send any request
				return null;
			}
			
            $db = & $this->getDBO();

			$wheres = array();
            $wheres[] = 'block = 0';

            $limit = $this->getState('limit');
            $limitstart = $this->getState('limitstart');

            $query = 'SELECT count(*) '
            .' FROM '. $db->nameQuote('#__community_connection').' as a, '. $db->nameQuote('#__users').' as b'
            .' WHERE a.'. $db->nameQuote('connect_from').'='.$db->Quote($id)
            .' AND a.'. $db->nameQuote('status').'='. $db->Quote(0)
            .' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
            .' ORDER BY a.'. $db->nameQuote('connection_id').' DESC ';

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
            $total = $db->loadResult();

            // Appy pagination
            if ( empty($this->_pagination))
            {
                jimport('joomla.html.pagination');
                $this->_pagination = new JPagination($total, $limitstart, $limit);
            }

            $query = CString::str_ireplace('count(*)', 'b.*', $query);
            $query .= " LIMIT {$limitstart}, {$limit} ";

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
            $result = $db->loadObjectList();
            return $result;
        }

        public function & getPagination()
        {
            return $this->_pagination;
        }

        /**
         * Return an array of friend id
         */
        public function getFriendIds($id)
        {
        	if($id == 0)
			{
				// guest obviously has no frinds
				$fid = array();
				return $fid;
			}
			
        	$db		= JFactory::getDBO();
			$query	= 'SELECT DISTINCT(a.'. $db->nameQuote('connect_to').') AS '. $db->nameQuote('id')
					.' FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
					.' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
					.' ON a.'. $db->nameQuote('connect_from').'=' . $db->Quote( $id ) . ' '
					.' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
					.' AND a.'. $db->nameQuote('status').'=' . $db->Quote( 1 );
            $db->setQuery( $query );
            
			$friends	= $db->loadResultArray();
			return $friends;
        }

        /**
         * Return an array of friend records
		 * This is a temporary solution to for the performance issue on photo page view. 
		 * Todo: need to support pagination on the cWindow
         */
        public function getFriendRecords($id)
        {
        	if($id == 0)
			{
				// guest obviously has no frinds
				$fid = array();
				return $fid;
			}
			
        	$db		= JFactory::getDBO();
			$query	= 'SELECT DISTINCT(a.'. $db->nameQuote('connect_to').') AS id , b.'. $db->nameQuote('name') .' , b.'.$db->nameQuote('username').' , u.'.$db->nameQuote('params') . 'AS _cparams'.' , b.'.$db->nameQuote('params') . 'AS params'
					  . ' FROM ' . $db->nameQuote('#__community_connection') .' AS a'
					  . ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b  '
					  . ' ON a.'. $db->nameQuote('connect_from') .'='. $db->Quote( $id )
					  . ' AND b.'. $db->nameQuote('block'). '=' . $db->Quote(0)
					  . ' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
					  . ' AND a.'. $db->nameQuote('status').'=' . $db->Quote(1)  
					  . ' LEFT JOIN ' . $db->nameQuote('#__community_users') .' u '
					  . ' ON a.' . $db->nameQuote('connect_to') .'=u.' . $db->nameQuote('userid') 
					  . ' WHERE NOT EXISTS ( SELECT d.'. $db->nameQuote('blocked_userid')
					  . ' FROM '. $db->nameQuote('#__community_blocklist')
					  . ' AS d  WHERE d.'. $db->nameQuote('userid').' = '. $db->Quote( $id )
					  . ' AND d.'. $db->nameQuote('blocked_userid').' = a.'. $db->nameQuote('connect_to').')'
					  . ' ORDER BY a.'. $db->nameQuote('connection_id').' DESC';
			
            $db->setQuery( $query );
            
			$friends	= $db->loadObjectList();
			
			$users = array();
            foreach ($friends as $friend)
            {
				$user 			= new CUser($friend->id);
				$isNewUser		= $user->init($friend);
                $users[] = $user;
            }
			
			return $users;
        }
        /**
         * Return the total number of friend for the user
         * @paran int id	the user id
         */
        public function getFriendsCount($id)
        {
            // For visitor with id=0, obviously he won't have any friend!
            if ( empty($id))
            return 0;

            $db = & $this->getDBO();

            // Search those we send connection
            $query = "SELECT count(distinct connect_to) "
            .' FROM '. $db->nameQuote('#__community_connection').' as a, '. $db->nameQuote('#__users').' as b'
            .' WHERE a.'. $db->nameQuote('connect_from').'='.$db->Quote($id)
	    .' AND b.block=0 '
            .' AND a.'. $db->nameQuote('status').'='. $db->Quote(1)
            .' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
            .' AND NOT EXISTS ( SELECT d.'. $db->nameQuote('blocked_userid')
            					.' FROM ' . $db->nameQuote( '#__community_blocklist' ) . ' AS d' 
            					.' WHERE d.'. $db->nameQuote('userid').' = ' . $db->Quote( $id ) 
            					.' AND d.'. $db->nameQuote('blocked_userid').' = a.'. $db->nameQuote('connect_to').') '
            .' ORDER BY a.'. $db->nameQuote('connection_id').' DESC ';

            $db->setQuery($query);
            $total = $db->loadResult();
            return $total;
        }

		public function getInviteListByName($namePrefix ,$userid, $cid, $limitstart = 0, $limit = 8){
			$db	=& $this->getDBO();

			$andName = '';
			$config = CFactory::getConfig();
			$nameField = $config->getString('displayname');
			if(!empty($namePrefix)){
				$andName	= ' AND b.' . $db->nameQuote( $nameField ) . ' LIKE ' . $db->Quote( '%'.$namePrefix.'%' ) ;
			}
			$query	=   'SELECT DISTINCT(a.'.$db->nameQuote('connect_to').') AS id  FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
					. ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
					. ' ON a.'.$db->nameQuote('connect_from').'=' . $db->Quote( $userid )
					. ' AND a.'.$db->nameQuote('connect_to').'=b.'.$db->nameQuote('id')
					. ' AND a.'.$db->nameQuote('status').'=' . $db->Quote( '1' )
					. ' AND b.'.$db->nameQuote('block').'=' .$db->Quote('0') 
					. ' WHERE NOT EXISTS ( SELECT d.'.$db->nameQuote('blocked_userid') . ' as id'
										. ' FROM '.$db->nameQuote('#__community_blocklist') . ' AS d  '
										. ' WHERE d.'.$db->nameQuote('userid').' = '.$db->Quote($userid)
										. ' AND d.'.$db->nameQuote('blocked_userid').' = a.'.$db->nameQuote('connect_to').')'
					. $andName
					. ' ORDER BY b.' . $db->nameQuote($nameField)
					. ' LIMIT ' . $limitstart.','.$limit
					;
			$db->setQuery( $query );
			$friends = $db->loadResultArray();
			//calculate total
			$query	=   'SELECT COUNT(DISTINCT(a.'.$db->nameQuote('connect_to').'))  FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
					. ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
					. ' ON a.'.$db->nameQuote('connect_from').'=' . $db->Quote( $userid )
					. ' AND a.'.$db->nameQuote('connect_to').'=b.'.$db->nameQuote('id')
					. ' AND a.'.$db->nameQuote('status').'=' . $db->Quote( '1' )
					. ' AND b.'.$db->nameQuote('block').'=' .$db->Quote('0') 
					. ' WHERE NOT EXISTS ( SELECT d.'.$db->nameQuote('blocked_userid') . ' as id'
										. ' FROM '.$db->nameQuote('#__community_blocklist') . ' AS d  '
										. ' WHERE d.'.$db->nameQuote('userid').' = '.$db->Quote($userid)
										. ' AND d.'.$db->nameQuote('blocked_userid').' = a.'.$db->nameQuote('connect_to').')'
					. $andName;
			
			$db->setQuery( $query );
			$this->total	=  $db->loadResult();
			
			return $friends;	
		}

        /**
         * return the list of friend from approved connections
         * controller need to set the id
         *
         * @param	id	int		user id of the person we want to searhc their friend
         * @param	bool do we need to randomize the result
         * @param	sorted	boolean	do we need sorting?
         * @return	CUser objects
         */
        public function & getFriends($id, $sorted = 'latest', $useLimit = true , $filter = 'all' , $maxLimit = 0 )
        {
            $cusers = array ();
			
			// Deprecated since 1.8 .
			// Earlier versions the default $filter is empty but since we will now need to handle character filter,
			// we need to set the default to 'all'
			if( empty($filter) )
			{
				$filter	= 'all';
			}
		
            // For visitor with id=0, obviously he won't have any friend!
            if ( empty($id))
            {
                return $cusers;
            }

            $db = & $this->getDBO();

            $wheres = array ();
            $wheres[] = 'block = 0';
            $limit = $this->getState('limit');
            $limitstart = $this->getState('limitstart');

			$query	= 'SELECT DISTINCT(a.'. $db->nameQuote('connect_to').') AS id ';
			if($filter == 'suggestion')
			{
				$query	= 'SELECT DISTINCT(b.'. $db->nameQuote('connect_to').') AS id ';
			}
			$query	.= ', CASE WHEN c.'. $db->nameQuote('userid').' IS NULL THEN 0 ELSE 1 END AS online';
			
			switch( $filter )
			{
                case 'mutual':
                	$user	= CFactory::getUser();
					
					$query	.= ' FROM ' . $db->nameQuote( '#__community_connection' ) . ' AS a '
							. ' INNER JOIN ' . $db->nameQuote( '#__community_connection' ) . ' AS b ON ( a.'. $db->nameQuote('connect_to').' = b.'. $db->nameQuote('connect_to').' ) '
							. ' AND a.'. $db->nameQuote('connect_from').'=' . $db->Quote( $id )
							. ' AND b.'. $db->nameQuote('connect_from').'=' . $db->Quote( $user->id )
							. ' AND a.'. $db->nameQuote('status').'=' . $db->Quote( 1 );
					$query	.= ' LEFT JOIN ' . $db->nameQuote('#__session') . ' AS c ON a.'. $db->nameQuote('connect_to').' = c.'. $db->nameQuote('userid');
					$query  .= ' WHERE NOT EXISTS ( SELECT d.'. $db->nameQuote('blocked_userid')
							.' FROM ' . $db->nameQuote( '#__community_blocklist' ) . ' AS d'
							.' WHERE d.'. $db->nameQuote('userid').' = ' . $db->Quote( $id ) 
							.' AND d.'. $db->nameQuote('blocked_userid').' = a.'. $db->nameQuote('connect_to').') ';

		            // Search those we send connection
			    $total = $this->getFriendsCount($id);
		
		            // Appy pagination
		            if ( empty($this->_pagination))
		            {
		                jimport('joomla.html.pagination');
		                $this->_pagination = new JPagination($total, $limitstart, $limit);
		            }
                	break;
                case 'suggestion':
                	$user	= CFactory::getUser();
					$query	.= ', COUNT(1) AS totalFriends, b.'. $db->nameQuote('connect_to').' AS id'
							. ' FROM ' . $db->nameQuote( '#__community_connection' ) . ' AS b'
							. ' LEFT JOIN '. $db->nameQuote('#__session') . ' AS c ON c.'. $db->nameQuote('userid').' = b.'. $db->nameQuote('connect_to')
							. ' WHERE b.'. $db->nameQuote('connect_to').' != ' . $db->Quote( $user->id )
							. ' AND b.'. $db->nameQuote('connect_from').' IN (SELECT a.'. $db->nameQuote('connect_to')
							. ' FROM ' . $db->nameQuote( '#__community_connection' ) . ' a WHERE a.'. $db->nameQuote('connect_from').' = ' . $db->Quote( $user->id ) 
							. ' AND a.'. $db->nameQuote('status').' = ' . $db->Quote( '1' ) . ')'
							. ' AND NOT EXISTS(SELECT d.'. $db->nameQuote('connect_to')
												.' FROM '. $db->nameQuote('#__community_connection').' d '
												.' WHERE (d.'. $db->nameQuote('connect_to').' = ' . $db->Quote( $user->id ) 
												.' AND d.'. $db->nameQuote('connect_from').' = b.'. $db->nameQuote('connect_to').')' 
												.' OR (d.'. $db->nameQuote('connect_to').' = b.'. $db->nameQuote('connect_to')
												.' AND d.'. $db->nameQuote('connect_from').' = ' . $db->Quote( $user->id ) . ') )'
					        . ' AND NOT EXISTS ( SELECT e.'. $db->nameQuote('blocked_userid')
					        					.' FROM ' . $db->nameQuote( '#__community_blocklist' ) . ' AS e '
					        					.' WHERE e.'. $db->nameQuote('userid').' = ' . $db->Quote( $id ) 
					        					.' AND e.'. $db->nameQuote('blocked_userid').' = b.'. $db->nameQuote('connect_to').') ';
		            
                    	    // Search those we send connection
			    $total = $this->getFriendsCount($id);
		
		            // Appy pagination
		            if ( empty($this->_pagination))
		            {
		                jimport('joomla.html.pagination');
		                $this->_pagination = new JPagination($total, $limitstart, $limit);
		            }
                	break;
                case 'all':
                	$query	.= ', b.name';
					$query	.= ' FROM ' . $db->nameQuote( '#__community_connection' ) . ' AS a '
							. ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
							. ' ON a.'. $db->nameQuote('connect_from').'=' . $db->Quote( $id )
							. ' AND b.block=0 '
							. ' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
							. ' AND a.'. $db->nameQuote('status').'=' . $db->Quote( '1' ) . ' '
					        . ' LEFT JOIN ' . $db->nameQuote('#__session') . ' AS c ON a.'. $db->nameQuote('connect_to').' = c.'. $db->nameQuote('userid')
					        . ' WHERE NOT EXISTS ( SELECT d.'. $db->nameQuote('blocked_userid')
					        						.' FROM ' . $db->nameQuote( '#__community_blocklist' ) . ' AS d '
					        						.' WHERE d.'. $db->nameQuote('userid').' = ' . $db->Quote( $id ) 
					        						.' AND d.'. $db->nameQuote('blocked_userid').' = a.'. $db->nameQuote('connect_to').') ';
					
		            // Search those we send connection
			    $total = $this->getFriendsCount($id);
		
		            // Appy pagination
		            if ( empty($this->_pagination))
		            {
		                jimport('joomla.html.pagination');
		                $this->_pagination = new JPagination($total, $limitstart, $limit);
		            }
                    break;
                default:
					$filterCount	= JString::strlen( $filter );
					
					$filterQuery	= '';
					
					if( $filter == 'others' )
					{
						$filterQuery	= ' AND b.name REGEXP "^[^a-zA-Z]."';
					}
					else
					{
					    $config         = CFactory::getConfig();
					    
						$filterQuery	= ' AND(';
						for( $i = 0; $i < $filterCount; $i++ )
						{
							$char			= $filter{$i};
							$filterQuery	.= $i != 0 ? ' OR ' : ' ';
							$nameField      = 'b.' . $db->nameQuote( $config->get('displayname') );
							$filterQuery	.= $nameField .' LIKE "' . JString::strtoupper($char) . '%" OR ' . $nameField . ' LIKE "' . JString::strtolower($char) . '%"';
						}
						$filterQuery	.= ')';
					}
					
                	$query	.= ', b.name';
					$query	.= ' FROM ' . $db->nameQuote( '#__community_connection' ) . ' AS a '
							. ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
							. ' ON a.'. $db->nameQuote('connect_from').'=' . $db->Quote( $id )
							. ' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
							. ' AND a.'. $db->nameQuote('status').'=' . $db->Quote( '1' );
					$query	.= $filterQuery;
					$query	.= ' LEFT JOIN ' . $db->nameQuote('#__session') . ' AS c ON a.connect_to = c.userid';
					$query  .= ' WHERE NOT EXISTS ( SELECT d.'. $db->nameQuote('blocked_userid')
											.' FROM ' . $db->nameQuote( '#__community_blocklist' ) . ' AS d '
											.' WHERE d.'. $db->nameQuote('userid').' = ' . $db->Quote( $id ) 
											.' AND d.'. $db->nameQuote('blocked_userid').' = a.'. $db->nameQuote('connect_to').') ';

		            // Search those we send connection
		            $pagingQuery = "SELECT count(*) "
		            .' FROM '. $db->nameQuote('#__community_connection').' as a, '. $db->nameQuote('#__users').' as b'
		            .' WHERE a.'. $db->nameQuote('connect_from').'='.$db->Quote($id)
		            .' AND a.'. $db->nameQuote('status').'='. $db->Quote(1)
		            .' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
		            . $filterQuery
		            .' AND NOT EXISTS ( SELECT d.'. $db->nameQuote('blocked_userid')
		            					.' FROM ' . $db->nameQuote( '#__community_blocklist' ) . ' AS d'
		            					.' WHERE d.'. $db->nameQuote('userid').' = ' . $db->Quote( $id ) 
		            					.' AND d.'. $db->nameQuote('blocked_userid').' = a.'. $db->nameQuote('connect_to').') '
		            .' ORDER BY a.'. $db->nameQuote('connection_id').' DESC ';
		
		            $db->setQuery($pagingQuery);
		            $total = $db->loadResult();
		
		            // Appy pagination
		            if ( empty($this->_pagination))
		            {
		                jimport('joomla.html.pagination');
		                $this->_pagination = new JPagination($total, $limitstart, $limit);
		            }
                	break;
			}
			
            switch($sorted)
            {
                // We only want the id since we use CFactory::getUser later to get their full details.
                case 'online':                		
						$query	.= ' ORDER BY '. $db->nameQuote('online').' DESC';
                    break;
                case 'suggestion':
						$query	.=	' GROUP BY (b.'. $db->nameQuote('connect_to').')'
								. ' HAVING (totalFriends >= ' . FRIEND_SUGGESTION_LEVEL . ')';
								
                    break;
                case 'name':
            		//sort by name only applicable to filter is not mutual and suggestion
            		if($filter != 'mutual' && $filter != 'suggestion')
            		{
            			$config	= CFactory::getConfig();
            			$query	.= ' ORDER BY b.' . $db->nameQuote( $config->get( 'displayname' ) ) . ' ASC';
					}
					break;	
                default:
						$query	.= ' ORDER BY a.'. $db->nameQuote('connection_id').' DESC';
                    break;
            }

            if ($useLimit)
            {
                $query .= " LIMIT {$limitstart}, {$limit} ";
            }
            else if ($maxLimit > 0)
            {
            	// we override the limit by specifying how many return need to be return.
            	$query .= " LIMIT 0, {$maxLimit} ";
            }

            $db->setQuery($query);

            $result = $db->loadObjectList();

            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }
			
			// preload all users
			$uids = array();
			foreach($result as $m)
			{
				$uids[] = $m->id;
			}
			CFactory::loadUsers($uids);
			
            for ($i = 0; $i < count($result); $i++)
            {

                $usr = CFactory::getUser($result[$i]->id);
                $cusers[] = $usr;
            }

            return $cusers;
        }

        /**
         * return the list of friends group
         * @param id int user id of that person we want to search for their friend group
         *
         */

        public function getFriendsGroup($id)
        {
            $db = & $this->getDBO();

            // Search those we send connection
            $query = 'SELECT *	FROM '. $db->nameQuote('#__community_friendlist')
            		.' WHERE '. $db->nameQuote('user_id').' = '.$db->Quote($id);

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());
            }

            $result = $db->loadObjectList();

            return $result;
        }

        /**
         *get Friend Connection
         *
         *@param connect_from int owner's id
         *@param connect_to stranger's id
         *return db object
         */

        public function getFriendConnection($connect_from, $connect_to)
        {

            $db = & $this->getDBO();

            $query = 'SELECT * FROM '. $db->nameQuote('#__community_connection')
		        		.' WHERE ('. $db->nameQuote('connect_from').' = '.$db->Quote($connect_from).' AND '. $db->nameQuote('connect_to').' ='.$db->Quote($connect_to).')'
						.' OR ( '. $db->nameQuote('connect_from').' = '.$db->Quote($connect_to)
						.' AND '. $db->nameQuote('connect_to').' ='.$db->Quote($connect_from).')';

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());

            }

            $result = $db->loadObjectList();

            return $result;
        }
        
        public function getPendingUserId($id)
        {
			$db = & $this->getDBO();

            $query = 'SELECT '. $db->nameQuote('connect_from')
		          		.' FROM '. $db->nameQuote('#__community_connection')
				  		.' WHERE '. $db->nameQuote('connection_id').' = '.$db->Quote($id);

            $db->setQuery($query);
            if ($db->getErrorNum())
            {
                JError::raiseError(500, $db->stderr());

            }

            $result = $db->loadObject();

            return $result;
		}

		/**
		 * Returns a list of pending friend requests for the user
		 *
		 * @param	int	$userId	The number of friend requests to lookup for this user.
		 * 
		 * @return	int Total number of friend requests.		 		 		 
		 **/	 	
		public function getTotalNotifications( $userId )
		{
			return (int) $this->countPending( $userId );
		}
    }//end of class
