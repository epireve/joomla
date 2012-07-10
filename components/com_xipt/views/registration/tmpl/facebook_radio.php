<?php /**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
	
foreach($allProfileTypes as $pType ):
	$selected = '';
	// check if selected
	if ($selectedPT == $pType->id)
		$selected = 'checked="true"';
	?>		
	<div class="singlePT">
		<div id="Name">
			<input  type="radio" 
				id="profiletypes<?php echo $pType->id?>" 
				name="profiletypes" 
				value="<?php echo $pType->id;?>" <?php echo $selected; ?> />
			<?php echo $pType->name; ?>
		</div>

		<div id="Details">
			<div id="Avatar">
				<img src="<?php echo JURI::root().XiptHelperUtils::getUrlpathFromFilePath($pType->avatar); ?>" 
				     height="60px" 
 			  	     width="60px"/>
			</div>
			<p id="Description"> <?php echo $pType->tip; ?> </p>
		</div>
	</div>
<?php endforeach; ?>
<?php 
