<?php
/** $Id: default.php 10381 2008-06-01 03:35:53Z pasamio $ */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="panel">
<h3 class="jpane-toggler"><?php echo JText::_( 'Users Online' );?></h3>
<div class="jpane-slider">
<form method="post" action="index.php?option=com_users">
	<ul class="adminlist">
	<?php
		$i		= 0;
		$now	= time();
		foreach ($rows as $row) :
			$auth = $user->authorize( 'com_users', 'manage' );
			if ($auth) :
				$link 	= 'index.php?option=com_users&amp;task=edit&amp;cid[]='. $row->userid;
				$name 	= '<a href="'. $link .'" title="'. JText::_( 'Edit User' ) .'">'. $row->username .'</a>';
			else :
				$name 	= $row->username;
			endif;

			$clientInfo =& JApplicationHelper::getClientInfo($row->client_id);
			?>
			<li>
				<span class="name">
					<?php echo $name;?>
				</span>
				<span class="usertype">
					/ <?php echo $row->usertype;?>
				</span>
				<span class="logout">
				<?php if ($auth && $user->get('gid') > 24 && $row->userid != $user->get('id')) : ?>
					/ <input style="position:relative;top:4px;" type="image" src="images/publish_x.png" onclick="f=this.form;f.task.value='flogout';f.client.value=<?php echo (int) $row->client_id; ?>;f.cid_value.value=<?php echo (int) $row->userid ?>" />
				<?php endif; ?>
				</span>
			</li>
			<?php
			$i++;
		endforeach;
		?>
	</ul>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="client" value="" />
	<input type="hidden" name="cid[]" id="cid_value" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
</div>