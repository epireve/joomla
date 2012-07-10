<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" id="application-form" method="post" name="adminForm" enctype="multipart/form-data">
<div id="config-document">
	<div id="page-main" class="tab">
		<table class="noshow">
			<tr>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'reportings.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'advance_search.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'cronprocess.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'registrations.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'activity.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'filtering.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'likes.php' ); ?>
				</td>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'frontpage.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'bookmarkings.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'featured.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'messaging.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'walls.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'time_offset.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'email.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'status.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'profile.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'location.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="page-antispam" class="tab">
		<table class="noshow">
			<tr>
				<td width="50%">
					<?php require_once( dirname(__FILE__) . DS . 'akismet.php' ); ?>
				</td>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'limits.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="page-media" class="tab">
		<table class="noshow">
			<tr>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'photos.php' ); ?>
				</td>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'videos.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="page-groups" class="tab">
		<table class="noshow">
			<tr>
				<td width="50%">
					<?php require_once( dirname(__FILE__) . DS . 'groups.php' ); ?>
					
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table>
	</div>
	<div id="page-events" class="tab">
		<table class="noshow">
			<tr>
				<td width="50%">
					<?php require_once( dirname(__FILE__) . DS . 'events.php' ); ?>

				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table>
	</div>
	<div id="page-layout" class="tab">
		<table class="noshow">
			<tr>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'karma.php' ); ?>
				</td>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'display.php' ); ?>
					<?php require_once( dirname(__FILE__) . DS . 'frontpage_options.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="page-privacy" class="tab">
		<table class="noshow">
			<tr>
				<td width="50%">
					<?php require_once( dirname(__FILE__) . DS . 'privacy.php' ); ?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table>
	</div>
	<div id="page-network" class="tab">
		<table class="noshow">
			<tr>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'network.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="page-facebook-connect" class="tab">
		<table class="noshow">
			<tr>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'facebook_api.php' ); ?>
				</td>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'facebook_imports.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="page-remote-storage" class="tab">
		<table class="noshow">
			<tr>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'remote_storage_methods.php' ); ?>
				</td>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'remote_storage_s3.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="page-integrations" class="tab">
		<table class="noshow">
			<tr>
				<td width="50%">
					<?php require_once( dirname(__FILE__) . DS . 'myblog.php' ); ?>
				</td>
				<td>
					<?php require_once( dirname(__FILE__) . DS . 'google.php' ); ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="saveconfig" />
<input type="hidden" name="view" value="configuration" />
<input type="hidden" name="option" value="com_community" />
</form>
