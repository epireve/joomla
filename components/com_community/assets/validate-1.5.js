function cValidate()
{    
	/**
	 * Attach event to all form element with 'required' class
	 */	
	this.message 	 = '';
	this.REM	    = 'info is required. Make sure it contains a valid value!'; //required enty missing.	 
	this.noticeTitle = 'Notice';
	this.errorField  = new Array();
	this.customMessage = '';
	this.JOINTEXT	=   ',';
	
	this.init = function(){	
		
			joms.jQuery('#community-wrap form.community-form-validate :input.required').blur(			
				function(){
					if( ! joms.jQuery(this).hasClass('validate-custom-date') && ! joms.jQuery(this).hasClass('validate-country') )
					{
						if(cvalidate.validateElement(this))
							cvalidate.markValid(this);
						else					
							cvalidate.markInvalid(this);
					}
				}
			);

			joms.jQuery('#community-wrap form.community-form-validate :input.validate-profile-email').blur(
				function(){					
					if((joms.jQuery.trim(joms.jQuery(this).val()) != ''))
					{				
						if(cvalidate.validateElement(this))
							cvalidate.markValid(this);
						else					
							cvalidate.markInvalid(this);
					}	
				}
			);
			
			joms.jQuery('#community-wrap form.community-form-validate :input.validate-profile-url').blur(
				function(){					
					if((joms.jQuery.trim(joms.jQuery(this).val()) != ''))
					{				
						if(cvalidate.validateElement(this))
							cvalidate.markValid(this);
						else					
							cvalidate.markInvalid(this);
					}	
				}
			);
			
			joms.jQuery('#community-wrap form.community-form-validate :input.validate-country').change(
				function(){
					if(joms.jQuery(this).hasClass('required') )
					{
						if(cvalidate.validateElement(this))
							cvalidate.markValid(this);
						else					
							cvalidate.markInvalid(this);
					}	
				}
			);			
			
			joms.jQuery('#community-wrap form.community-form-validate :input.validate-custom-date').blur(
				function(){
					if(cvalidate.validateElement(this))
						cvalidate.markValid(this);
					else					
						cvalidate.markInvalid(this);
				}
			);
			
			
			joms.jQuery('#community-wrap form.community-form-validate :input.validateSubmit').click(			
				function(){
					if(cvalidate.validateForm()){
						return true;
					} else {						
						var message = (cvalidate.REM == 'undefined' || cvalidate.REM == '') ? 'info is required. Make sure it contains a valid value!' : cvalidate.REM;

						if (cvalidate.errorField.length > 1) {
							lastField   = cvalidate.errorField.pop();
							var joinText	= (cvalidate.JOINTEXT == 'undefined' || cvalidate.JOINTEXT == '') ? ' and ' : cvalidate.JOINTEXT;
							strErrField = cvalidate.errorField.join(', ') + ' ' + joinText + ' ' + lastField;
						} else {
							strErrField = cvalidate.errorField;
						}
						
						message = strErrField + ' ' + message;
						
						if (cvalidate.customMessage != ''){
							message = cvalidate.customMessage;
						}
						
						message	= message.replace(/\n/g,'');
						cWindowShow('joms.jQuery(\'#cWindowContent\').html("'+message+'")' , cvalidate.noticeTitle , 450 , 70 , 'warning');
						joms.jQuery("#community-wrap form.community-form-validate :input.required[value='']").each(
							function(i){cvalidate.markInvalid(this);}
						);
						return false;
					}
				}
			);
			
						
				
	}
	/**
	 * Sets a specific textarea element to certain character limit given the element id and max char.
	 **/	 	
	this.setMaxLength = function( element , maxChar ){

		joms.jQuery( element ).keyup(function(){
			var max = parseInt( maxChar );

			if( joms.jQuery(this).val().length > max)
			{
				joms.jQuery(this).val( joms.jQuery(this).val().substr(0, maxChar ));
			}
		});

	}
	
	this.markInvalid= function(el){
	    var fieldName = el.name;
	    
        if(joms.jQuery(el).hasClass('validate-custom-date')){
	       //since we knwo custom date come from an array. so we have to invalid all.
	       joms.jQuery("#community-wrap form.community-form-validate input[name='"+fieldName+"']").addClass('invalid');
	       joms.jQuery("#community-wrap form.community-form-validate select[name='"+fieldName+"']").addClass('invalid');
	    } else {
           joms.jQuery(el).addClass('invalid');
	    }
	}
	
	this.markValid= function(el){
	    var fieldName = el.name;
	    	    
	    if(joms.jQuery(el).hasClass('validate-custom-date')){
	       //since we knwo custom date come from an array. so we have to valid all.
	       joms.jQuery("#community-wrap form.community-form-validate input[name='"+fieldName+"']").removeClass('invalid');
	       joms.jQuery("#community-wrap form.community-form-validate select[name='"+fieldName+"']").removeClass('invalid');	       
	       
	    } else {	
		    joms.jQuery(el).removeClass('invalid');
		}

	    //hide error only for those custom fields
	    if(fieldName != null){
			fieldName = fieldName.replace('[]','');
		    joms.jQuery('#err'+fieldName+'msg').hide();	          
			joms.jQuery('#err'+fieldName+'msg').html('&nbsp');
		}		
		
	}
	
	/**
	 *
	 */	
	this.validateElement = function(el){
	    var isValid = true;
	    var fieldName = el.name;
	    
	    if(joms.jQuery(el).attr('type') == 'text' || joms.jQuery(el).attr('type') == 'password' || joms.jQuery(el).attr('type') == 'textarea'){
	    
	       if(joms.jQuery.trim(joms.jQuery(el).val()) == '') {
		  if(joms.jQuery(el).hasClass('required')){
		      isValid = false;
		      //show error only for those custom fields
		      fieldName = fieldName.replace('[]','');

		      lblName   = joms.jQuery('#lbl'+fieldName).html();

		      if(lblName == null){
			      lblName = 'Field';
		      } else {
			  lblName = lblName.replace('*','');
		      }

			  this.setMessage(fieldName, lblName, 'COM_COMMUNITY_REGISTER_INVALID_VALUE');
	          }

		   } else {
		   		
		       if(joms.jQuery(el).hasClass('validate-name')){
		           //checking the string length
		           if(joms.jQuery(el).val().length < 3){
			           this.setMessage(fieldName, '', 'COM_COMMUNITY_NAME_TOO_SHORT');
		               isValid = false;
		           } else {
		               joms.jQuery('#err' + fieldName + 'msg').hide();
					   joms.jQuery('#err' + fieldName + 'msg').html('&nbsp');
		               isValid = true;		           
		           }
		       }		   
		   
		       if(joms.jQuery(el).hasClass('validate-username')){
		           //use ajax to check the pages.
		           if(joms.jQuery('#usernamepass').val() != joms.jQuery(el).val()){
		               isValid = cvalidate.ajaxValidateUserName(joms.jQuery(el));
		           }//end if
		       }		   
				if(joms.jQuery(el).hasClass('validate-email')){
					//regex=/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
					regex=/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i;
					isValid = regex.test(joms.jQuery(el).val());
					
					if(isValid == false){
						this.setMessage(fieldName, '', 'COM_COMMUNITY_INVALID_EMAIL');
					} else {
						joms.jQuery('#err' + fieldName + 'msg').hide();
						joms.jQuery('#err' + fieldName + 'msg').html('&nbsp');
						
						//use ajax to check the pages.
						if(joms.jQuery('#emailpass').val() != joms.jQuery(el).val()){
							isValid = cvalidate.ajaxValidateEmail(joms.jQuery(el));
						}//end if
					}
				}

		       if(joms.jQuery(el).hasClass('validate-profile-email')){
		   		   //regex=/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
		   		   regex=/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,6})$/i;
		   		   
			       isValid = regex.test(joms.jQuery(el).val());
			       
			       if(isValid == false){
					   this.setMessage(fieldName, '', 'COM_COMMUNITY_INVALID_EMAIL');
			       } else {
		               joms.jQuery('#err' + fieldName + 'msg').hide();
					   joms.jQuery('#err' + fieldName + 'msg').html('&nbsp');
				   }				          
		       }
			   
		       if(joms.jQuery(el).hasClass('validate-profile-url')){		   		   
					
					var url = joms.jQuery(el).val();

					if (url.match('http://'))
					{
						url = url.replace('http://', '');
						joms.jQuery(el)
							.prev('select')
							.find('option')
							.removeAttr('selected')
							.filter('[value="http://"]')
							.attr('selected', 'selected');

					}

					if (url.match('https://'))
					{
						url = url.replace('https://', '');

						joms.jQuery(el)
							.prev('select')
							.find('option')
							.removeAttr('selected')
							.filter('[value="https://"]')
							.attr('selected', 'selected');
					}
					
					joms.jQuery(el).val(url);

					fieldName = fieldName.replace('[]','');
					regex = /^(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,6}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
						  		   		
			       	isValid = regex.test(joms.jQuery(el).val());
   
			       	if(isValid == false){
					   this.setMessage(fieldName, '', 'COM_COMMUNITY_INVALID_URL');
			       	} else {
		               joms.jQuery('#err' + fieldName + 'msg').hide();
					   joms.jQuery('#err' + fieldName + 'msg').html('&nbsp');
				   	}				          
		       }			   		       
		       
		       if(joms.jQuery(el).hasClass('validate-password') && el.name == 'jspassword'){
		           if(joms.jQuery(el).val().length < 6){
					   this.setMessage(fieldName, '', 'COM_COMMUNITY_PASSWORD_TOO_SHORT');
		               isValid = false;
		           } else {
		               joms.jQuery('#err' + fieldName + 'msg').hide();
					   joms.jQuery('#err' + fieldName + 'msg').html('&nbsp');					   
		               isValid = true;		           
		           }
		       }		       
		       
		       if(joms.jQuery(el).hasClass('validate-passverify') && el.name == 'jspassword2'){
		           isValid = (joms.jQuery('#jspassword').val() == joms.jQuery(el).val());
		           
		           if(isValid == false){
					   this.setMessage('jspassword2', '', 'COM_COMMUNITY_PASSWORD_NOT_SAME');
		           } else {
		               joms.jQuery('#errjspassword2msg').hide();
					   joms.jQuery('#errjspassword2msg').html('&nbsp');		           
		           }
		       }
		       
		       //now check for any custom field validation
		       if(joms.jQuery(el).hasClass('validate-custom-date')){
		           isValid = this.checkCustomDate(el);
		       }
		       
           }//end if else
	       
	    } else if(joms.jQuery(el).attr('type') == 'checkbox'){
	       if(joms.jQuery(el).hasClass('validate-custom-checkbox')){
			   if(joms.jQuery("#community-wrap form.community-form-validate input[name='"+fieldName+"']:checked").size() == 0)
			   {
			   		isValid = false;
			   }
			   
			   if(isValid == false){
		          fieldName = fieldName.replace('[]','');
		          lblName   = joms.jQuery('#lbl'+fieldName).html();		      
		          if(lblName == null){
			          lblName = 'Field';
		          } else {	              
		              lblName = lblName.replace('*','');
		          }
		          		          
		          this.setMessage(fieldName, lblName, 'COM_COMMUNITY_REGISTER_INVALID_VALUE');
			   }//end if
	       
	       } else {
              if(! joms.jQuery(el).attr('checked')) isValid = false;
	       }	    	    	       	       
	       
	       
	    } else if(joms.jQuery(el).attr('type') == 'radio'){
	       if(joms.jQuery(el).hasClass('validate-custom-radio')){
			   if(joms.jQuery("#community-wrap form.community-form-validate input[name='"+fieldName+"']:checked").size() == 0)
			   {
			   		isValid = false;
			   }
			   
			   if(isValid == false){
		          //fieldName = fieldName.replace('[]','');
		          lblName   = joms.jQuery('#lbl'+fieldName).html();		      
		          if(lblName == null){
			          lblName = 'Field';
		          } else {	              
		              lblName = lblName.replace('*','');
		          }
		          		          
		          this.setMessage(fieldName, lblName, 'COM_COMMUNITY_REGISTER_INVALID_VALUE');
			   }//end if
	       
	       } else {
              if(! joms.jQuery(el).attr('checked')) isValid = false;
	       }	    	    	       	       
	       
	       
	    } else if(joms.jQuery(el).attr('type') == 'select-one'){	       
	    
	       if(joms.jQuery(el).children(':selected').length == 0)
		   {
		   		isValid = false;
		   }
		   else
		   {
		    	joms.jQuery(el).children(':selected').each(
					function(){
		    			if(joms.jQuery(el).val() == '') isValid = false;	
		    		}
		    	);
		   }
		   
		   if(joms.jQuery(el).hasClass('validate-country')){
				if(joms.jQuery(el).val() == 'selectcountry') isValid = false;
		   }
		   
	       //now check for any custom field validation
	       if(joms.jQuery(el).hasClass('validate-custom-date')){
	           isValid = this.checkCustomDate(el);
	       } else if(isValid == false){
		          fieldName = fieldName.replace('[]','');	    		   
		               	           
		          lblName   = joms.jQuery('#lbl'+fieldName).html();
		      
		          if(lblName == null){
			          lblName = 'Field';
		          } else {
		              lblName = lblName.replace('*','');
		          }
                  this.setMessage(fieldName, lblName, 'COM_COMMUNITY_REGISTER_INVALID_VALUE');
	       }
		   	       	       
        } else if(joms.jQuery(el).attr('type') == 'select-multiple') {
              if(joms.jQuery(el).children(':selected').length == 0) isValid = false;
                            
              if(isValid == false){
		          fieldName = fieldName.replace('[]','');
		          lblName   = joms.jQuery('#lbl'+fieldName).html();
		      
		          if(lblName == null){
			          lblName = 'Field';
		          } else {	              
		              lblName = lblName.replace('*','');
		          }
		          this.setMessage(fieldName, lblName, 'COM_COMMUNITY_REGISTER_INVALID_VALUE');
              }              
        }
        
		return isValid;   
	} 	
	
	/**
	 * Check & validate form elements
	 */	 	
	this.validateForm = function(){
	    var isValid 	= true;
	    this.errorField = new Array();
	    
		joms.jQuery('#community-wrap form.community-form-validate :input.required').each(		    
			function(i){
				if(! cvalidate.validateElement(this)) isValid = false;
			}
		);
		
		joms.jQuery('#community-wrap form.community-form-validate :input.validate-profile-email').each(
			function(){					
				if((joms.jQuery.trim(joms.jQuery(this).val()) != ''))
				{				
					if(! cvalidate.validateElement(this)) isValid = false;
				}	
			}
		);
		
		joms.jQuery('#community-wrap form.community-form-validate :input.[class*=minmax]').each(
			function(){
				if (this.className.indexOf("minmax") > -1) {
					// do something
					var classattr = joms.jQuery(this).attr('class').split(" ");
					for(var i = 0; i < classattr.length; i++)
					{
						if(classattr[i].indexOf('minmax') == 0){
							var min = classattr[i].split('_')[1];
							var max = classattr[i].split('_')[2];
							var fieldlength = joms.jQuery.trim(joms.jQuery(this).val()).length;
							if(!(fieldlength >= min && fieldlength <= max)){
								cvalidate.setMessage(joms.jQuery(this).attr('id'), '', 'COM_COMMUNITY_REGISTER_INVALID_CHAR_COUNT',min,max);	
								isValid = false;
							}
							break;
						}
					}
					
				}
			}
		);
		
		joms.jQuery('#community-wrap form.community-form-validate :input.validate-profile-url').each(
			function(){					
				if((joms.jQuery.trim(joms.jQuery(this).val()) != ''))
				{				
					if(! cvalidate.validateElement(this)) isValid = false;
				}	
			}
		);		
		
		return isValid;
	}
	 
	/**
	 * Check the username whether already exisit or not.
	 */	 
	 this.ajaxValidateUserName = function(el){		 
	     jax.call('community', 'register,ajaxCheckUserName',joms.jQuery(el).val());	      	     	 
	 }
	 
	/**
	 * Check the email whether already exisit or not.
	 */
	 this.ajaxValidateEmail = function(el){
	     jax.call('community', 'register,ajaxCheckEmail',joms.jQuery(el).val());	 	     	     		 
	 }
	 
	 /**
	  * check custom date
	  */
	  this.checkCustomDate = function(el){
	      var isValid = true;
	      var fieldName = el.name;
	       //now check for any custom field validation
	       if(joms.jQuery(el).hasClass('validate-custom-date')){
	           //we know this field is an array type.
	           fieldId = fieldName.replace('[]','');
	           var dateObj = joms.jQuery("#community-wrap form.community-form-validate input[name='"+fieldName+"']");
	           
	           for(var i=0; i < dateObj.length; i++){
	               if (!/^-?\d+$/.test(dateObj[i].value)){
					    isValid = false;
				   }//end if
	           }//end for
	           
	           //now check whether the date is valid or not.
	           var dateObj2 = joms.jQuery("#community-wrap form.community-form-validate select[name='"+fieldName+"']");
	           
	           //dd / mm/ yyyy
	           var dd = dateObj[0].value;
	           var mm = dateObj2[0].value;
	           var yy = dateObj[1].value;		           

	           var dayobj = new Date(yy, eval(mm-1), dd);
	           
	           if ((dayobj.getMonth()+1!=mm)||(dayobj.getDate()!=dd)||(dayobj.getFullYear()!=yy)){
	               isValid = false;
	           }
	           
	           if(isValid == false){
                   this.setMessage(fieldId, '', 'COM_COMMUNITY_INVALID_DATE');				   		           
	           } else {
                   joms.jQuery('#err'+fieldId+'msg').hide();
		           joms.jQuery('#err'+fieldId+'msg').html('&nbsp');
	           }
	       }
		   return isValid;
	  
	  }
	  
	  /*
	   * Get the message text from langauge file using ajax
	   */
	  this.setMessage = function(fieldName, txtLabel, msgStr, param1, param2){
	  	  
	  	  errorLabel  = joms.jQuery('label[for="' + fieldName + '"]').html().replace('*', '');
	  	  
		  if (joms.jQuery.inArray(errorLabel, this.errorField) == -1)
		  {
		  	this.errorField.push(errorLabel);
		  }
	  	  
	      jax.call('community', 'register,ajaxSetMessage',fieldName, txtLabel, msgStr, param1, param2);	      
	  }
	  
	  //this.setREMText = function(text){
	  this.setSystemText = function(key, text){
	  	eval('cvalidate.' + key + ' = "' + text + '"');
	  }

}

var cvalidate = new cValidate();


// JavaScript Document
/* 
 * Password Strength (0.1.1)
 * by Sagie Maoz (n0nick.net)
 * n0nick@php.net
 *
 * This plugin will check the value of a password field and evaluate the
 * strength of the typed password. This is done by checking for
 * the diversity of character types: numbers, lowercase and uppercase
 * letters and special characters.
 *
 * Copyright (c) 2010 Sagie Maoz <n0nick@php.net>
 * Licensed under the GPL license, see http://www.gnu.org/licenses/gpl-3.0.html 
 *
 *
 * NOTE: This script requires jQuery to work.  Download jQuery at www.jquery.com
 *
 */
(function(jQuery){

var passwordStrength = new function()
{
	this.countRegexp = function(val, rex)
	{
		var match = val.match(rex);
		return match ? match.length : 0;
	}
	
	this.getStrength = function(val, minLength)
	{	
		var len = val.length;
		
		// too short =(
		if (len < minLength)
		{
			return 0;
		}
		
		var nums = this.countRegexp(val, /\d/g),
			lowers = this.countRegexp(val, /[a-z]/g),
			uppers = this.countRegexp(val, /[A-Z]/g),
			specials = len - nums - lowers - uppers;
		
		// just one type of characters =(
		if (nums == len || lowers == len || uppers == len || specials == len)
		{
			return 1;
		}
		
		var strength = 0;
		if (nums)	{ strength+= 2; }
		if (lowers)	{ strength+= uppers? 4 : 3; }
		if (uppers)	{ strength+= lowers? 4 : 3; }
		if (specials) { strength+= 5; }
		if (len > 10) { strength+= 1; }
		
		return strength;
	}
	
	this.getStrengthLevel = function(val, minLength)
	{
		var strength = this.getStrength(val, minLength);
		switch (true)
		{
			case (strength <= 0):
				return 1;
				break;
			case (strength > 0 && strength <= 4):
				return 2;
				break;
			case (strength > 4 && strength <= 8):
				return 3;
				break;
			case (strength > 8 && strength <= 12):
				return 4;
				break;
			case (strength > 12):
				return 5;
				break;
		}
		
		return 1;
	}
}

jQuery.fn.password_strength = function(options)
{
	var settings = jQuery.extend({
		'container' : null,
		'minLength' : 6,
		'texts' : {
			1 : 'Too weak',
			2 : 'Weak password',
			3 : 'Normal strength',
			4 : 'Strong password',
			5 : 'Very strong password'
		}
	}, options);
	
	return this.each(function()
	{
		if (settings.container)
		{
			var container = jQuery(settings.container);
		}
		else
		{
			var container = jQuery('<span/>').attr('class', 'jsPasswordStrength');
			jQuery(this).after(container);
		}
		
		jQuery(this).keyup(function()
		{
			var val = jQuery(this).val();
			if (val.length > 0)
			{
				var level = passwordStrength.getStrengthLevel(val, settings.minLength);
				var _class = 'jsPasswordStrength_' + level;
				
				if (!container.hasClass(_class) && level in settings.texts)
				{
					container.text(settings.texts[level]).attr('class', 'jsPasswordStrength ' + _class);
				}
			}
			else
			{
				container.text('').attr('class', 'jsPasswordStrength');
			}
		});
	});
};

})(joms.jQuery);



