<?php
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');
?>
	<?php 
	
	// Jom Social Profile field comfiguration
			
	echo $this->pane->startPanel( '1. Jom Social Profile Field Configuration', 'jsprofilefield' );
	?>
	<ol>
		<?php if($this->field->published == 0){?><li>This field is <span style="color:red;">Not Published</span> in Jom Social Custom Profiles. Please Publish it.</li><?php }?>
		<?php if($this->field->required == 0){?><li>This field is set to <span style="color:red;">Not Required</span> in Jom Social Custom Profiles. Please make it Required.</li><?php }?>
		<?php if($this->field->visible == 0){?><li>This field is set to <span style="color:red;">Not Visible</span> for user in Jom Social Custom Profiles. Please make it Visible.</li><?php }?>
		<?php if($this->field->published == 1 
				&& $this->field->required == 1
				&& $this->field->visible == 1){
				?><li> All the settings for Profile Fields have been configured correctly.</li><?php 
				}?>
	</ol>
	<?php 
	echo $this->pane->endPanel();
	
	echo $this->pane->startPanel( '2. Allow', 'allow' );
	?>
	<ol>
		<li>This field must be published and visible in Jom Social Custom Profile.</li>
		<li>This field will only be allowed to the users of seleted Profile Type.</li>
		<li>If you are configuring this first time. This field will be allowed to all user.</li>
	</ol>
	<?php 
	echo $this->pane->endPanel();
	
	
	echo $this->pane->startPanel( '3. Required', 'required' );
	?>
		<ol>
		<li>This field must be published, required and visible in Jom Social Custom Profile.</li>
		<li>This field will only be required to the users of seleted Profile Type.</li>
		<li>If you are configuring this first time. This field will be required to all user.</li>
	</ol>
	<?php 
	echo $this->pane->endPanel();
	
	
	echo $this->pane->startPanel( '4. Visible', 'visible' );
	?>
		<ol>
		<li>This field must be published and visible in Jom Social Custom Profile.</li>
		<li>This field will be visible at the time of editing profile and viewing self's profile, for seleted profile type.</li>
		<li>For other users it will only be visible at the time of editing profile. When any user viewing profile, if this field is not visible for profile owner's profile type, this field will not be shown.</li>
		<li>If you are configuring this first time. This field will be visible to all user.</li>
	</ol>
	<?php	
	echo $this->pane->endPanel();
	
	
	echo $this->pane->startPanel( '5. Editable After Registration', 'editableAfterRegistration' );
	?>
		<ol>
		<li>This field must be published and visible in Jom Social Custom Profile.</li>
		<li>This field will be visible after registration, when editing profile, for selected profile type.</li>
		<li>For other users it will only be visible at the time registration and viewing self's profile, but not be shown when editing profile.</li>
		<li>If you are configuring this first time. This field will be editable after registration to all user.</li>
	</ol>
	<?php 	
	echo $this->pane->endPanel();
	
	
	echo $this->pane->startPanel( '6. Editable During Registration', 'editableDurinRegistration' );
	?>
		<ol>
		<li>This field must be published and visible in Jom Social Custom Profile and set to visible in this configuration.</li>
		<li>This field will be visible during registration for selected profile type.</li>
		<li>For other users it will not be visible at the time registration and but after registration it will be visible when editing profile or viewing self's profile.</li>
		<li>If you are configuring this first time. This field will be editable during registration to all user.</li>
	</ol>
	<?php 	
	echo $this->pane->endPanel();
	
	echo $this->pane->startPanel( '7. Advance Searchable', 'advanceSearchable' );
	?>
		<ol>
		<li>This field will be visible during advance search.</li>
	</ol>
	<?php 	
	echo $this->pane->endPanel();