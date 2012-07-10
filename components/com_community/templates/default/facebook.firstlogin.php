<?php
defined('_JEXEC') or die();
?>
<a href="<?php echo $userInfo['profile_url'];?>" target="_blank"><img src="<?php echo $userInfo['pic_square'];?>" border="0" style="border: 1px solid #000; float: left; margin: 5px;line-height:0;" /></a>
<p><?php echo JText::sprintf('COM_COMMUNITY_FACEBOOK_CONNECT_DESCRIPTION', $userInfo['name'] );?></p>
<div style="margin-top: 30px; padding: 10px 0px; font-size: 13px">
	<div style="font-weight:700;font-size: 14px;"><?php echo JText::_('COM_COMMUNITY_I_AM_CURRENTLY');?></div>
	<label class="lblradio-block" style="font-weight: 700; margin-top: 20px"><input type="radio" value="1" name="membertype" checked style="margin-right:8px" /><?php echo JText::_('COM_COMMUNITY_A_NEW_USER');?></label>
	<div style="margin-left: 20px;"><?php echo JText::_('COM_COMMUNITY_NEW_MEMBER_DESCRIPTION');?></div>
        
        <?php if( $config->get('enableterms') ) { ?>
        <span id="err_msg" style="color:red;display:none;margin-top:20px;margin-left: 20px"><?php echo JText::_('COM_COMMUNITY_FACEBOOK_ACCEPT_TNC_ERROR');?></span>
        <label id ="tnc" class="lblradio-block" style="font-weight: 700; margin-top: 20px;"><input type="checkbox" name="tnc" id="tnc" value="Y" class="inputbox required" /><?php echo JText::_('COM_COMMUNITY_I_HAVE_READ').' <a href="javascript:void(0);" onclick="joms.registrations.showTermsWindow(1);">'.JText::_('COM_COMMUNITY_TERMS_AND_CONDITION').'</a>.';?></label>

        <?php }?>
        <label class="lblradio-block" style="font-weight: 700; margin-top: 20px"><input type="radio" value="2" name="membertype" style="margin-right:8px" /><?php echo JText::_('COM_COMMUNITY_MEMBER_OF_SITE');?></label>
	<div style="margin-left: 20px;"><?php echo JText::_('COM_COMMUNITY_EXISTING_SITE_MEMBER_DESCRIPTION');?></div>
	<div style="color: red;margin-top:20px;"><?php echo JText::_('COM_COMMUNITY_LINKING_NOTICE');?></div>
</div>
<script type="text/javascript">
joms.jQuery('input:radio[name=membertype]').change(function(){

    var value = this.value;

    if(value == 2){
        joms.jQuery('label#tnc').hide()
    }else
    {
        joms.jQuery('label#tnc').show();
    }
});
</script>