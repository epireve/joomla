<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Jom Social Profile Controller
 */
class CommunityControllerZencoder extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * AJAX method to display a form to create a Zencoder account
	 * 
	 * @param	int	fieldId	The fieldId that we are editing
	 * @param	boolean	isGroup	Determines whether the current field is a group
	 * 
	 * @return	JAXResponse object	Azrul's AJAX Response object
	 **/
	public function ajaxShowForm($form = null)
	{
		$user	=& JFactory::getUser();
		
		if ( $user->get('guest')) {
			JError::raiseError( 403, JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN') );
			return;
		}
		
		if (!$form)
		{
			$form['email'] = '';
			$form['_error'] = '';
		}

		$response	= new JAXResponse();

		$windowTitle    = JText::_('COM_COMMUNITY_ZENCODER_REGISTRATION_FORM');
		
		ob_start();
?>
<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;">
	<span><?php echo JText::_('COM_COMMUNITY_ZENCODER_INTEGRATION_DESCRIPTION_LINE_1');?></span><br /><br />
	<span><?php echo JText::_('COM_COMMUNITY_ZENCODER_INTEGRATION_DESCRIPTION_LINE_2');?></span>
</div>
<div id="error-notice" style="color: red; font-weight:700;"></div>
<div style="clear: both;"></div>
<form action="#" method="post" name="registerZencoderAccount" id="registerZencoderAccount">
<table cellspacing="0" class="paramlist admintable" border="0" width="100%">
	<tbody>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_EMAIL');?></td>
			<td>:</td>
			<td>
				<input type="text" size="50" value="<?php echo $form['email']; ?>" name="email" />
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_PASSWORD');?></td>
			<td>:</td>
			<td>
				<input type="password" size="35" value="" name="password" />
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_ZENDCODER_RETYPE_PASSWORD');?></td>
			<td>:</td>
			<td>
				<input type="password" size="35" value="" name="password2" />
			</td>
		</tr>
		<tr>
			<td class="key"></td>
			<td></td>
			<td>
				<input type="checkbox" name="terms_of_service" value="1" />
				<span><?php echo JText::sprintf('COM_COMMUNITY_ZENDCODER_AGREE_ZENCODER_TERMS', 'http://zencoder.com/terms'); ?></span>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
				<span style="color:red;"><?php echo $form['_error']; ?></span>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?php
		$contents	= ob_get_contents();
		ob_end_clean();

		$buttons	= '<input type="button" class="button" onclick="javascript:azcommunity.submitZencoderAccount();return false;" value="' . JText::_('COM_COMMUNITY_ZENCODER_SUBMIT_BUTTON') . '"/>';
		$buttons	.= '&nbsp;&nbsp;<input type="button" class="button" onclick="javascript:cWindowHide();" value="' . JText::_('COM_COMMUNITY_CANCEL') . '"/>';
		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );
		$response->addAssign( 'cwin_logo' , 'innerHTML' , $windowTitle );
		$response->addScriptCall( 'cWindowActions' , $buttons );
		
		return $response->sendResponse();
	}
	
	public function ajaxSubmitForm($form=null)
	{
		if (!is_array($form)) return false;
		
		// validation
		if (!($form['email']))
		{
			$form['_error'] = 'You need to enter an email';
			$this->ajaxShowForm($form);
		}
		if ($form['password'] == '' || ($form['password']!==$form['password2']))
		{
			$form['_error'] = 'Password empty or do not match';
			$this->ajaxShowForm($form);
		}
		if (!isset($form['terms_of_service']))
		{
			$form['_error'] = 'You did not agree to the Term of Service';
			$this->ajaxShowForm($form);
		}
		
		
		$data	= array(
			'terms_of_service' => 0,
			'email' => '',
			'password' => '',
			'affiliate_code' => 'jomsocial',
			'newsletter' => 0
		);
		
		// something is weird with the returning value of jax.getFormValues
		// we can't use array_merge here :( //array_merge($form, $values);
		$data['email'] = $form['email'];
		$data['password'] = $form['password2'];
		$data['terms_of_service'] = $form['terms_of_service'];
		$data	= json_encode($data);

		CFactory::load('libraries', 'zencoder');
		$curl	= new CZenCoderCURL;
		if (!$curl->post('https://app.zencoder.com/api/account', $data))
		{
			$this->ajaxShowSuccss($curl->getError());
		}
		
		$content	= '';
		$code		= $curl->getStatusCode();
		$result		= $curl->getResults();
		$result		= json_decode($result);
		
		if (isset($result->errors))
		{
			foreach ($result->errors as $error)
			{
				$content .= $error.'<br />';
			}
		}
		
		if (isset($result->api_key))
		{
			$content .= 'Your API key: '.$result->api_key.'<br />';
			$content .= 'Password: '.$result->password;
			
			// Store the API key
			$config		=& JTable::getInstance( 'configuration' , 'CommunityTable' );
			$config->load( 'config' );
			$params		= new CParameter($config->params);
			$params->set('zencoder_api_key', $result->api_key);
			$config->params = $params->toString();
			$config->store();
		}
		
		if (!isset($result->errors) && !isset($result->api_key))
		{
			$content = 'Something is wrong here...';
		}
		
		$this->ajaxShowSuccss($content);
	}
	
	public function ajaxShowSuccss($content = '')
	{
		$windowTitle	= JText::_('COM_COMMUNITY_ZENCODER_REGISTRATION_FORM');
		//$buttons		= '<input type="button" class="button" onclick="javascript:cWindowHide();" value="' . JText::_('COM_COMMUNITY_ZENDCODER_OK_BUTTON') . '"/>';
		$buttons		= '<input type="button" class="button" onclick="javascript:window.location.reload();" value="' . JText::_('COM_COMMUNITY_ZENDCODER_OK_BUTTON') . '"/>';
		$response		= new JAXResponse();
		$response->addAssign( 'cWindowContent' , 'innerHTML' , $content );
		$response->addAssign( 'cwin_logo' , 'innerHTML' , $windowTitle );
		$response->addScriptCall( 'cWindowActions' , $buttons );
		return $response->sendResponse();
	}
}