<?php
/**
 * @package		JomSocial
 * @subpackage	Library 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

global $jaxFuncNames;
	if (!isset($jaxFuncNames) or !is_array($jaxFuncNames)) $jaxFuncNames = array();



$jaxFuncNames['community_entry'] = "";

$jaxFuncNames[] = 'system,ajaxReport';
$jaxFuncNames[] = 'system,ajaxSendReport';
$jaxFuncNames[]	= 'system,ajaxEditWall';
$jaxFuncNames[]	= 'system,ajaxUpdateWall';
$jaxFuncNames[]	= 'system,ajaxShowInvitationForm';
$jaxFuncNames[]	= 'system,ajaxShowFriendsForm';
$jaxFuncNames[]	= 'system,ajaxSubmitInvitation';
$jaxFuncNames[]	= 'system,ajaxLike';
$jaxFuncNames[]	= 'system,ajaxDislike';
$jaxFuncNames[]	= 'system,ajaxUnlike';
$jaxFuncNames[]	= 'system,ajaxGetLikeHTML';
$jaxFuncNames[]	= 'system,ajaxGetOlderWalls';
$jaxFuncNames[] = 'system,ajaxStreamAdd';
$jaxFuncNames[] = 'system,ajaxLoadFriendsList';


$jaxFuncNames[] = "status,ajaxUpdate";
$jaxFuncNames[] = "status,ajaxUpdateStatus";
$jaxFuncNames[] = "status,test";
$jaxFuncNames[] = "frontpage,ajaxTest";
$jaxFuncNames[] = "status,ajaxPreview";
$jaxFuncNames[] = "status,ajaxSubmit";


/** apps **/
$jaxFuncNames[] = 'apps,ajaxShowAbout';
$jaxFuncNames[] = 'apps,ajaxShowPrivacy';
$jaxFuncNames[] = 'apps,ajaxRemove';
$jaxFuncNames[] = 'apps,ajaxAdd';
$jaxFuncNames[] = 'apps,ajaxShowSettings';
$jaxFuncNames[] = 'apps,ajaxSaveSettings';
$jaxFuncNames[] = 'apps,ajaxSavePrivacy';
$jaxFuncNames[] = 'apps,ajaxSaveOrder';
$jaxFuncNames[] = 'apps,ajaxSavePosition';
$jaxFuncNames[] = 'apps,ajaxRefreshLayout';
$jaxFuncNames[] = 'apps,ajaxBrowse';
$jaxFuncNames[] = 'apps,ajaxAddApp';


/** inbox **/
$jaxFuncNames[] = 'inbox,ajaxAddReply';
$jaxFuncNames[] = 'inbox,ajaxCompose';
$jaxFuncNames[] = 'inbox,ajaxRemoveMessage';
$jaxFuncNames[] = 'inbox,ajaxRemoveFullMessages';
$jaxFuncNames[] = 'inbox,ajaxRemoveSentMessages';
$jaxFuncNames[] = 'inbox,ajaxMarkMessageAsRead';
$jaxFuncNames[] = 'inbox,ajaxMarkMessageAsUnread';
$jaxFuncNames[]	= 'inbox,ajaxIphoneInbox';
$jaxFuncNames[] = 'inbox,ajaxSend';
$jaxFuncNames[] = 'inbox,ajaxDeleteMessages';

/** friends**/
$jaxFuncNames[] = 'friends,ajaxFriendTagSave';
$jaxFuncNames[] = 'friends,ajaxAssignTag';
$jaxFuncNames[]	= 'friends,ajaxIphoneFriends';
//$jaxFuncNames[] = 'friends,ajaxAddGroup';
$jaxFuncNames[] = 'friends,ajaxAdd';
$jaxFuncNames[] = 'friends,ajaxSaveFriend';
$jaxFuncNames[] = 'friends,ajaxConnect';
$jaxFuncNames[]	= 'friends,ajaxCancelRequest';
$jaxFuncNames[]	= 'friends,ajaxApproveRequest';
$jaxFuncNames[]	= 'friends,ajaxRejectRequest';
$jaxFuncNames[] = 'friends,ajaxConfirmFriendRemoval';
$jaxFuncNames[] = 'friends,ajaxRemoveFriend';
$jaxFuncNames[] = 'friends,ajaxBlockFriend';

/** groups **/
$jaxFuncNames[]	= 'groups,ajaxSaveWall';
$jaxFuncNames[]	= 'groups,ajaxSaveDiscussionWall';
$jaxFuncNames[]	= 'groups,ajaxRemoveWall';
$jaxFuncNames[]	= 'groups,ajaxAddNews';
$jaxFuncNames[]	= 'groups,ajaxSaveNews';
$jaxFuncNames[]	= 'groups,ajaxSaveJoinGroup';
$jaxFuncNames[]	= 'groups,ajaxShowLeaveGroup';
$jaxFuncNames[]	= 'groups,ajaxLeaveGroup';
$jaxFuncNames[]	= 'groups,ajaxRemoveMember';
$jaxFuncNames[]	= 'groups,ajaxRemoveReply';
$jaxFuncNames[]	= 'groups,ajaxApproveMember';
$jaxFuncNames[]	= 'groups,ajaxUnpublishGroup';
$jaxFuncNames[]	= 'groups,ajaxShowRemoveDiscussion';
$jaxFuncNames[]	= 'groups,ajaxShowRemoveBulletin';
$jaxFuncNames[]	= 'groups,ajaxAddAdmin';
$jaxFuncNames[]	= 'groups,ajaxRemoveAdmin';
$jaxFuncNames[]	= 'groups,ajaxDeleteGroup';
$jaxFuncNames[]	= 'groups,ajaxWarnGroupDeletion';
$jaxFuncNames[]	= 'groups,ajaxAddFeatured';
$jaxFuncNames[]	= 'groups,ajaxRemoveFeatured';
$jaxFuncNames[]	= 'groups,ajaxRejectInvitation';
$jaxFuncNames[]	= 'groups,ajaxAcceptInvitation';  
$jaxFuncNames[]	= 'groups,ajaxShowLockDiscussion';
$jaxFuncNames[]	= 'groups,ajaxBanMember';
$jaxFuncNames[]	= 'groups,ajaxUnbanMember';
$jaxFuncNames[] = 'groups,ajaxConfirmMemberRemoval';
$jaxFuncNames[]	= 'groups,ajaxJoinGroup';
$jaxFuncNames[]	= 'groups,ajaxUpdateCount';

/** photos **/
$jaxFuncNames[]	= 'photos,ajaxShowWall';
$jaxFuncNames[]	= 'photos,ajaxSaveWall';
$jaxFuncNames[]	= 'photos,ajaxRemoveWall';
$jaxFuncNames[]	= 'photos,ajaxShowWallContents';
$jaxFuncNames[]	= 'photos,ajaxSaveCaption';
$jaxFuncNames[]	= 'photos,ajaxRemovePhoto';
$jaxFuncNames[]	= 'photos,ajaxSetDefaultPhoto';
$jaxFuncNames[]	= 'photos,ajaxPagination';
$jaxFuncNames[] = 'photos,ajaxRemoveAlbum';
$jaxFuncNames[]	= 'photos,ajaxSwitchPhotoTrigger';
$jaxFuncNames[]	= 'photos,ajaxAddFeatured';
$jaxFuncNames[]	= 'photos,ajaxRemoveFeatured';
$jaxFuncNames[]	= 'photos,ajaxAddPhotoTag';
$jaxFuncNames[]	= 'photos,ajaxRemovePhotoTag';
$jaxFuncNames[]	= 'photos,ajaxDisplayCreator';
$jaxFuncNames[]	= 'photos,ajaxLinkToProfile';
$jaxFuncNames[]	= 'photos,ajaxSaveOrdering';
$jaxFuncNames[]	= 'photos,ajaxConfirmRemovePhoto';
$jaxFuncNames[] = 'photos,ajaxAddPhotoHits';
$jaxFuncNames[] = 'photos,ajaxRotatePhoto';
$jaxFuncNames[] = 'photos,ajaxUpdateCounter';
$jaxFuncNames[] = 'photos,ajaxAlbumSaveWall';
$jaxFuncNames[] = 'photos,ajaxAlbumRemoveWall';
$jaxFuncNames[] = 'photos,ajaxUploadPhoto';
$jaxFuncNames[] = 'photos,ajaxGetAlbumURL';
$jaxFuncNames[] = 'photos,ajaxCreateAlbum';
$jaxFuncNames[] = 'photos,ajaxUploadAvatar';
$jaxFuncNames[] = 'photos,ajaxShowThumbnail';
$jaxFuncNames[] = 'photos,ajaxGotoOldUpload';
$jaxFuncNames[] = 'photos,ajaxUpdateThumbnail';
$jaxFuncNames[] = 'photos,ajaxSaveAlbumDesc';

/** register **/
$jaxFuncNames[] = 'register,ajaxShowTnc';
$jaxFuncNames[] = 'register,ajaxSetMessage';
$jaxFuncNames[] = 'register,ajaxCheckUserName';
$jaxFuncNames[] = 'register,ajaxCheckEmail';
$jaxFuncNames[] = 'register,ajaxGenerateAuthKey';
$jaxFuncNames[] = 'register,ajaxAssignAuthKey';

/** comment **/
$jaxFuncNames[]	= 'comment,ajaxAdd';

/** profile **/
$jaxFuncNames[] = 'profile,ajaxPlayProfileVideo';
$jaxFuncNames[] = 'profile,ajaxConfirmLinkProfileVideo';
$jaxFuncNames[] = 'profile,ajaxLinkProfileVideo';
$jaxFuncNames[] = 'profile,ajaxRemoveConfirmLinkProfileVideo';
$jaxFuncNames[] = 'profile,ajaxRemoveLinkProfileVideo';
$jaxFuncNames[] = 'profile,ajaxErrorFileUpload';
$jaxFuncNames[]	= 'profile,ajaxBanUser';
$jaxFuncNames[]	= 'profile,ajaxRemovePicture';
$jaxFuncNames[]	= 'profile,ajaxIphoneProfile';
$jaxFuncNames[]	= 'profile,ajaxUploadNewPicture';
$jaxFuncNames[]	= 'profile,ajaxUpdateThumbnail';
$jaxFuncNames[]	= 'profile,ajaxUpdateURL';
$jaxFuncNames[]	= 'profile,ajaxConfirmRemoveAvatar';

/** activities **/
$jaxFuncNames[]	= 'activities,ajaxConfirmDeleteActivity';
$jaxFuncNames[]	= 'activities,ajaxDeleteActivity';
$jaxFuncNames[]	= 'activities,ajaxHideActivity';
$jaxFuncNames[]	= 'activities,ajaxAddComment';
$jaxFuncNames[]	= 'activities,ajaxGetContent';
$jaxFuncNames[]	= 'activities,ajaxAddPredefined';
$jaxFuncNames[]	= 'activities,ajaxAddText';
$jaxFuncNames[]	= 'activities,ajaxGetActivities';

/** frontpage **/
$jaxFuncNames[]	= 'frontpage,ajaxGetNewestMember';
$jaxFuncNames[]	= 'frontpage,ajaxGetActiveMember';
$jaxFuncNames[]	= 'frontpage,ajaxGetPopularMember';
$jaxFuncNames[]	= 'frontpage,ajaxGetActivities';
$jaxFuncNames[]	= 'frontpage,ajaxGetFeaturedMember';
$jaxFuncNames[]	= 'frontpage,ajaxIphoneFrontpage';
$jaxFuncNames[]	= 'frontpage,ajaxGetNewestVideos';
$jaxFuncNames[]	= 'frontpage,ajaxGetFeaturedVideos';
$jaxFuncNames[]	= 'frontpage,ajaxGetPopularVideos';

/** notification **/
$jaxFuncNames[]	= 'notification,ajaxGetNotification';
$jaxFuncNames[]	= 'notification,ajaxRejectRequest';
$jaxFuncNames[]	= 'notification,ajaxApproveRequest';
$jaxFuncNames[]	= 'notification,ajaxJoinInvitation';
$jaxFuncNames[]	= 'notification,ajaxRejectInvitation';
$jaxFuncNames[] = 'notification,ajaxGroupJoinInvitation';
$jaxFuncNames[] = 'notification,ajaxGroupRejectInvitation';
$jaxFuncNames[] = 'notification,ajaxGroupJoinRequest';
$jaxFuncNames[] = 'notification,ajaxGroupRejectRequest';
$jaxFuncNames[] = 'notification,ajaxGetRequest';
$jaxFuncNames[] = 'notification,ajaxGetInbox';

/** connect **/
$jaxFuncNames[]	= 'connect,ajaxImportData';
$jaxFuncNames[]	= 'connect,ajaxUpdateEmail';
$jaxFuncNames[]	= 'connect,ajaxUpdate';
$jaxFuncNames[]	= 'connect,ajaxMergeNotice';
$jaxFuncNames[]	= 'connect,ajaxMerge';
$jaxFuncNames[]	= 'connect,ajaxCreateNewAccount';
$jaxFuncNames[]	= 'connect,ajaxCheckEmail';
$jaxFuncNames[]	= 'connect,ajaxCheckUsername';
$jaxFuncNames[]	= 'connect,ajaxCheckName';
$jaxFuncNames[]	= 'connect,ajaxValidateLogin';
$jaxFuncNames[] = 'connect,ajaxShowNewUserForm';
$jaxFuncNames[] = 'connect,ajaxShowExistingUserForm';
$jaxFuncNames[] = 'connect,ajaxInvite';

/** Bookmarks **/
$jaxFuncNames[]	= 'bookmarks,ajaxShowBookmarks';
$jaxFuncNames[]	= 'bookmarks,ajaxEmailPage';

/** events **/
$jaxFuncNames[]	= 'events,ajaxSaveWall';
$jaxFuncNames[]	= 'events,ajaxRemoveWall';
$jaxFuncNames[]	= 'events,ajaxWarnEventDeletion';
$jaxFuncNames[]	= 'events,ajaxDeleteEvent';
$jaxFuncNames[]	= 'events,ajaxRequestInvite';
$jaxFuncNames[]	= 'events,ajaxIgnoreEvent';
$jaxFuncNames[]	= 'events,ajaxApproveInvite';
$jaxFuncNames[]	= 'events,ajaxConfirmUnblockGuest';
$jaxFuncNames[]	= 'events,ajaxUnblockGuest';
$jaxFuncNames[]	= 'events,ajaxConfirmBlockGuest';
$jaxFuncNames[]	= 'events,ajaxBlockGuest';
$jaxFuncNames[]	= 'events,ajaxConfirmRemoveGuest';
$jaxFuncNames[]	= 'events,ajaxRemoveGuest';
$jaxFuncNames[]	= 'events,ajaxManageAdmin';
$jaxFuncNames[]	= 'events,ajaxJoinInvitation';
$jaxFuncNames[]	= 'events,ajaxRejectInvitation';
$jaxFuncNames[]	= 'events,ajaxDisplayNearbyEvents';
$jaxFuncNames[] = 'events,ajaxCreate';
$jaxFuncNames[] = 'events,ajaxUpdateStatus';
$jaxFuncNames[] = 'events,ajaxShowMap';
$jaxFuncNames[] = 'events,ajaxGetCalendar';
$jaxFuncNames[] = 'events,ajaxGetEvents';
$jaxFuncNames[] = 'events,ajaxAddFeatured';
$jaxFuncNames[] = 'events,ajaxRemoveFeatured';

/** Backend **/
$jaxFuncNames[]	= 'admin,profiles,ajaxEditField';
$jaxFuncNames[]	= 'admin,profiles,ajaxTogglePublish';
$jaxFuncNames[]	= 'admin,profiles,ajaxSaveField';
$jaxFuncNames[]	= 'admin,groups,ajaxTogglePublish';
$jaxFuncNames[]	= 'admin,groupcategories,ajaxEditCategory';
$jaxFuncNames[]	= 'admin,groupcategories,ajaxSaveCategory';
$jaxFuncNames[]	= 'admin,videoscategories,ajaxTogglePublish';
$jaxFuncNames[]	= 'admin,videoscategories,ajaxEditCategory';
$jaxFuncNames[]	= 'admin,videoscategories,ajaxSaveCategory';
$jaxFuncNames[]	= 'admin,templates,ajaxChangeTemplate';
$jaxFuncNames[]	= 'admin,templates,ajaxLoadTemplateFile';
$jaxFuncNames[]	= 'admin,templates,ajaxSaveTemplateFile';
$jaxFuncNames[]	= 'admin,about,ajaxCheckVersion';
$jaxFuncNames[]	= 'admin,groups,ajaxEditGroup';
$jaxFuncNames[]	= 'admin,groups,ajaxChangeGroupOwner';
$jaxFuncNames[]	= 'admin,maintenance,ajaxPatch';
$jaxFuncNames[]	= 'admin,maintenance,ajaxPatchGroup';
$jaxFuncNames[]	= 'admin,maintenance,ajaxPatchTable';
$jaxFuncNames[]	= 'admin,maintenance,ajaxPatchFriendTable';
$jaxFuncNames[]	= 'admin,maintenance,ajaxPatchFriend';
$jaxFuncNames[]	= 'admin,maintenance,ajaxPatchPrivacy';
$jaxFuncNames[]	= 'admin,reports,ajaxPerformAction';
$jaxFuncNames[]	= 'admin,userpoints,ajaxRuleScan';
$jaxFuncNames[]	= 'admin,userpoints,ajaxTogglePublish';
$jaxFuncNames[]	= 'admin,userpoints,ajaxEditRule';
$jaxFuncNames[]	= 'admin,userpoints,ajaxSaveRule';
$jaxFuncNames[]	= 'admin,users,ajaxTogglePublish';
$jaxFuncNames[]	= 'admin,messaging,ajaxSendMessage';
$jaxFuncNames[]	= 'admin,users,ajaxRemoveAvatar';
$jaxFuncNames[]	= 'admin,profiles,ajaxEditGroup';
$jaxFuncNames[]	= 'admin,profiles,ajaxSaveGroup';
$jaxFuncNames[]	= 'admin,groups,ajaxAssignGroup';
$jaxFuncNames[]	= 'admin,profiles,ajaxGroupTogglePublish';
$jaxFuncNames[]	= 'admin,events,ajaxTogglePublish';
$jaxFuncNames[]	= 'admin,events,ajaxEditEvent';
$jaxFuncNames[]	= 'admin,eventcategories,ajaxEditCategory';
$jaxFuncNames[]	= 'admin,eventcategories,ajaxSaveCategory';
$jaxFuncNames[]	= 'admin,multiprofile,ajaxTogglePublish';
$jaxFuncNames[]	= 'admin,configuration,ajaxResetPrivacy';
$jaxFuncNames[]	= 'admin,profiles,ajaxGetFieldParams';

/** search **/
$jaxFuncNames[]	= 'search,ajaxGetFieldCondition';
$jaxFuncNames[]	= 'search,ajaxAddFeatured';
$jaxFuncNames[]	= 'search,ajaxRemoveFeatured';

/** video **/
$jaxFuncNames[]	= 'videos,ajaxShowWallContents';
$jaxFuncNames[]	= 'videos,ajaxSaveWall';
$jaxFuncNames[]	= 'videos,ajaxRemoveWall';
$jaxFuncNames[]	= 'videos,ajaxRemoveVideo';
$jaxFuncNames[]	= 'videos,ajaxEditVideo';
$jaxFuncNames[]	= 'videos,ajaxAddVideo';
$jaxFuncNames[] = 'videos,ajaxLinkVideo';
$jaxFuncNames[] = 'videos,ajaxLinkVideoPreview';
$jaxFuncNames[] = 'videos,ajaxUploadVideo';
$jaxFuncNames[]	= 'videos,ajaxAddFeatured';
$jaxFuncNames[]	= 'videos,ajaxRemoveFeatured';
$jaxFuncNames[]	= 'videos,ajaxFetchThumbnail';
$jaxFuncNames[]	= 'videos,ajaxSetVideoCategory';
$jaxFuncNames[] = 'videos,ajaxShowVideoWindow';

$jaxFuncNames[]	= 'autousersuggest,ajaxAutoUserSuggest';

/** tag **/
$jaxFuncNames[]	= 'system,ajaxAddTag';
$jaxFuncNames[]	= 'system,ajaxRemoveTag';
$jaxFuncNames[]	= 'system,ajaxShowTagged';

/** ban **/
$jaxFuncNames[]	= 'profile,ajaxBlockUser';
$jaxFuncNames[]	= 'profile,ajaxUnblockUser';

/** Comment **/
$jaxFuncNames[]	= 'system,ajaxStreamAddComment';
$jaxFuncNames[]	= 'system,ajaxStreamShowComments';
$jaxFuncNames[]	= 'system,ajaxStreamRemoveComment';

$jaxFuncNames[]	= 'system,ajaxStreamAddLike';
$jaxFuncNames[]	= 'system,ajaxStreamShowLikes';
$jaxFuncNames[]	= 'system,ajaxStreamUnlike';

/**
 * Memberlist
 * @since 2.0 
 **/
$jaxFuncNames[]	= 'memberlist,ajaxShowSaveForm';

/** Like/Dislike **/
$jaxFuncNames[]	= 'system,ajaxLike';
$jaxFuncNames[]	= 'system,ajaxDislike';

/** Zencoder **/
$jaxFuncNames[]	= 'admin,zencoder,ajaxShowForm';
$jaxFuncNames[]	= 'admin,zencoder,ajaxSubmitForm';

// Dont process other plugin ajax definitions for back end
if(!JString::stristr(JPATH_COMPONENT, 'administrator' . DS . 'components' . DS . 'com_community' ))
{
	// Include CAppPlugins library
	require_once( JPATH_COMPONENT . DS . 'libraries' . DS . 'apps.php');

	// Load Ajax plugins jax file.
	CAppPlugins::loadAjaxPlugins();
}