<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<ul id="ap-quicklink">
	<li class="parent">
	<a id="ap-quickadd"><span class="quickadd-icon"></span><span class="quickadd-title"><?php echo JText::_( 'QUICK ADD' );?></span></a>
		<div class="ap-quickadd-div submenu">
			<ul>
				<li><?php echo JText::_( 'CORE' );?></li>
				<li><a href="index.php?option=com_content&task=add"><?php echo JText::_( 'NEW ARTICLE' )?></a></li>
				<li><a href="index.php?option=com_sections&scope=content&task=add"><?php echo JText::_( 'NEW SECTION' )?></a></li>
				<li><a href="index.php?option=com_categories&scope=content&task=add"><?php echo JText::_( 'NEW CATEGORY' )?></a></li>
				<?php if($this->_user->get('gid') > 23) : ?>
					<li><a href="index.php?option=com_menus&task=addMenu"><?php echo JText::_( 'NEW MENU' )?></a></li>
					<li><a href="index.php?option=com_modules&task=add"><?php echo JText::_( 'NEW MODULE' )?></a></li>
					<li><a href="index.php?option=com_users&task=add"><?php echo JText::_( 'NEW USER' )?></a></li>
					<li><a href="index.php?option=com_installer"><?php echo JText::_( 'NEW EXTENSION' )?></a></li>
				 <?php endif; ?> 
			</ul>
			<ul>
				<li><?php echo JText::_( 'EXTENSIONS' );?></li>
				<?php
				/*
				if (($this->_user->get('gid') >= $this->get('flexicontentAcl')) && $this->get('flexicontentAcl') != 0)  
					echo "<li><a href=\"index.php?option=com_content&task=add\">". JText::_( 'NEW CONTENT ITEM' ) ."</a></li>";
				*/
				
				if (($this->_user->get('gid') >= $this->get('k2Acl')) && $this->get('k2Acl') != 0) 
					echo "<li><a href=\"index.php?option=com_k2&view=item\">". JText::_( 'NEW CONTENT ITEM' ) ."</a></li>";
					
				/*	
				if (($this->_user->get('gid') >= $this->get('zooAcl')) && $this->get('zooAcl') != 0) 
					echo "<li><a href=\"index.php?option=com_content&task=add\">". JText::_( 'NEW CONTENT ITEM' ) ."</a></li>";
				*/
					
				if (($this->_user->get('gid') >= $this->get('jseblodAcl')) && $this->get('jseblodAcl') != 0) 
					echo "<li><a href=\"index.php?option=com_cckjseblod&controller=interface&act=-1&cck=1\">". JText::_( 'NEW CONTENT ITEM' ) ."</a></li>";
				
				if (($this->_user->get('gid') >= $this->get('joomailerAcl')) && $this->get('joomailerAcl') != 0)
					echo "<li><a href=\"index.php?option=com_joomailermailchimpintegration&view=create\">". JText::_( 'NEW NEWSLETTER' ) ."</a></li>";
					
				if (($this->_user->get('gid') >= $this->get('sobi2Acl')) && $this->get('sobi2Acl') != 0)
					echo "<li><a href=\"index2.php?option=com_sobi2&task=addItem\">". JText::_( 'NEW DIRECTORY ITEM' ) ."</a></li>";
					
				if (($this->_user->get('gid') >= $this->get('sobiproAcl')) && $this->get('sobiproAcl') != 0)
					echo "<li><a href=\"index.php?option=com_sobipro&task=entry.add&pid=1\">". JText::_( 'NEW DIRECTORY ITEM' ) ."</a></li>";
					
				if (($this->_user->get('gid') >= $this->get('kunenaAcl')) && $this->get('kunenaAcl') != 0)
					echo "<li><a href=\"index.php?option=com_kunena&task=new\">". JText::_( 'NEW FORUM' ) ."</a></li>";
					
				if (($this->_user->get('gid') >= $this->get('ninjaboardAcl')) && $this->get('ninjaboardAcl') != 0)
					echo "<li><a href=\"index.php?option=com_ninjaboard&view=forum\">". JText::_( 'NEW FORUM' ) ."</a></li>";	
				
				if (($this->_user->get('gid') >= $this->get('virtuemartAcl')) && $this->get('virtuemartAcl') != 0)
					echo "<li><a href=\"index.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart\">". JText::_( 'NEW PRODUCT' ) ."</a></li>";
					
				if (($this->_user->get('gid') >= $this->get('phocagalleryAcl')) && $this->get('phocagalleryAcl') != 0)
					echo "<li><a href=\"index.php?option=com_phocagallery&task=new\">". JText::_( 'NEW GALLERY' ) ."</a></li>";
				
				if (($this->_user->get('gid') >= $this->get('tiendaAcl')) && $this->get('tiendaAcl') != 0)
					echo "<li class=\"quickadd-first\"><a href=\"index.php?option=com_tienda&view=products&task=add\">". JText::_( 'NEW PRODUCT' ) ."</a></li><li class=\"quickadd-last\"><a href=\"index.php?option=com_tienda&view=categories&task=add\">". JText::_( 'NEW CATEGORY' ) ."</a></li>";
				
				if (($this->_user->get('gid') >= $this->get('projectforkAcl')) && $this->get('projectforkAcl') != 0)
					echo "<li class=\"quickadd-first\"><a href=\"index.php?option=com_projectfork&section=tasks&task=form_new_task\">". JText::_( 'NEW TASK' ) ."</a></li><li class=\"quickadd-first\"><a href=\"index.php?option=com_projectfork&section=tasks&task=form_new_milestone\">". JText::_( 'NEW MILESTONE' ) ."</a></li>";
					
				if ($this->_user->get('gid') >= 23)
					echo "<li><a href=\"index.php?option=com_banners&task=add\">". JText::_( 'NEW BANNER' ) ."</a></li>";
				if ($this->_user->get('gid') >= 23)
					echo "<li><a href=\"index.php?option=com_poll&task=add\">". JText::_( 'NEW POLL' ) ."</a></li>";
				?>
			</ul>
			<div class="clr"></div>
		</div>
	</li>
</ul>