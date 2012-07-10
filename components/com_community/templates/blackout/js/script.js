/* joms.filters.bind
   An alternative approach joms.filters.bind. May find its way back
   to joms class in script.js if it's good.
 */
 
 // joms.filters.option['']();
var joms_filters_option = {
	newestMember: function()
	{
		jax.call('community', 'frontpage,ajaxGetNewestMember', frontpageUsers)
	},
	activeMember: function()
	{
		jax.call('community', 'frontpage,ajaxGetActiveMember', frontpageUsers)
	},
	popularMember: function()
	{
		jax.call('community', 'frontpage,ajaxGetPopularMember', frontpageUsers)
	},
	featuredMember: function()
	{
		jax.call('community', 'frontpage,ajaxGetFeaturedMember', frontpageUsers)
	},
	allActivity: function()
	{
		jax.call('community', 'frontpage,ajaxGetActivities', 'all')
	},
	meAndFriendsActivity: function()
	{
		jax.call('community', 'frontpage,ajaxGetActivities', 'me-and-friends')
	},
	activeProfileAndFriendsActivity: function()
	{
		jax.call('community', 'frontpage,ajaxGetActivities', 'active-profile-and-friends', joms.user.getActive())
	},
	activeProfileActivity: function()
	{
		jax.call('community', 'frontpage,ajaxGetActivities', 'active-profile', joms.user.getActive());
	},
	newestVideos: function()
	{
		jax.call('community', 'frontpage,ajaxGetNewestVideos', frontpageVideos);
	},
	popularVideos: function()
	{
		jax.call('community', 'frontpage,ajaxGetPopularVideos', frontpageVideos);
	},
	featuredVideos: function()
	{
		jax.call('community', 'frontpage,ajaxGetFeaturedVideos', frontpageVideos);
	}
}

// joms.filters.activate()
function joms_filters_activate(e)
{	 
	var filterOption      = joms.jQuery(e);
	    filterOption.name = filterOption.attr('id').split('_')[1];

	if (!filterOption.hasClass('active'))
	{
		filterOption.addClass('active loading')
		            .siblings()
		            .removeClass('active');

		joms_filters_option[filterOption.name]();
	}
}

jQuery(document).ready(function()
{
	// Override joms.filters.hideLoading
	// Another reason why jax.call() needs callback param!
	joms.filters.hideLoading = function()
	{		
		jQuery( '.loading' ).hide();	
		jQuery('.filterOption').removeClass('loading').show();
		jQuery('.jomTipsJax').addClass('jomTips');
		joms.tooltip.setup();
	}
});