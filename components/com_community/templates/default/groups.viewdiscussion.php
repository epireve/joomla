<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	isAdmin		boolean is this group belong to me
 * @params	members		An array of member objects 
 * @params	title		A string that represents the title of the discussion 
 * @params	parentid	An integer value of the discussion parent. 
 * @params	groupid		An integer value of the discussion's group id. 
 */
defined('_JEXEC') or die();
?>
<div class="page-actions">
    <?php echo $reportHTML;?>
    <?php echo $bookmarksHTML;?>
    <div class="clr"></div>
</div>

<!-- COMMUNITY: START cLayout -->
<div class="cLayout">
	
	<!-- COMMUNITY: START Sidebar (Related Discussions) -->
	<div class="cSidebar">
		<?php
			$keywords = explode(' ',$discussion->title);
			echo $this->view('groups')->modRelatedDiscussion($keywords);
		?>
                
		<!-- THIS SHALL BE REMOVED ONCE THE BACKEND IS DONE -->
		<!--
<div class="cGroup-FileDiscussions">
			<div class="cModule">
				<h3>Available files</h3>
				<ul class="cResetList">
					<li><a href="">File 1.jpg [100kb]</a></li>
					<li><a href="">How ugly its gonna be if the file name is thiiiissss loonnng.pdf [387kb]</a></li>
					<li><a href="">File 3.jpg [164kb]</a></li>
					<li><a href="">File 4 [281kb]</a></li>
				</ul>
				<div class="app-box-footer">
				<a href="">view more</a>
				</div>
			</div>
		</div>
-->
		<!-- THIS SHALL BE REMOVED ONCE THE BACKEND IS DONE -->
                
	</div>
	<!-- COMMUNITY: END Sidebar (Related Discussions) -->
	
	
	<!-- COMMUNITY: START Discussion Area -->
	<div class="cMain">
		<div id="group-discussion-topic">
			<!--Discussion : Avatar-->
			<div class="author-avatar">
				<a href="<?php echo CUrlHelper::userLink($creator->id); ?>"><img class="cAvatar cAvatar-Large" src="<?php echo $creator->getThumbAvatar(); ?>" border="0" alt="" /></a>
			</div>
		    <!--Discussion : Avatar-->

		    <!--Discussion : Detail-->
			<div class="discussion-detail">
				<!--Discussion : Entry-->
				<div class="discussion-entry">
				<?php echo $discussion->message; ?>
				</div>
				<!--Discussion : Entry-->

				<!--Discussion : Author & Date-->
				<div class="small cMeta">
				<?php echo JText::sprintf('COM_COMMUNITY_GROUPS_DISCUSSION_CREATOR_TIME_LINK' , $creatorLink , $creator->getDisplayName() , $discussion->created);?>
				</div>
				<!--Discussion : Author & Date-->
			</div>
		    <!--Discussion : Detail-->
		</div>

		<div class="app-box">
			<?php if($config->get('group_discuss_order') == 'DESC'){ ?>
				<div id="wallForm"><?php echo $wallForm; ?></div>
				<div id="wallContent"><?php echo $wallContent; ?></div>
			<?php } else { ?>
				<div id="wallContent"><?php echo $wallContent; ?></div>
				<div id="wallForm"><?php echo $wallForm; ?></div>
			<?php } ?>
		</div>
		
	</div>
	<!-- COMMUNITY: END Discussion Area -->
	
</div>
<!-- COMMUNITY: END cLayout -->