joms.$(document).ready(function()
{
	joms.$('body').delegate('a', 'click', function(event)
	{
		var link = joms.$(event.currentTarget);

		if (link.attr('target')!='page')
		{
			joms.mobile.content.load(link.attr('href'));
		} else {
			document.location.href=link.attr('href');
		}
		event.preventDefault();
	});

	joms.mobile.init();
});

joms.extend({
	mobile: {
		settings: {
			content: '#community-content'
		},
				
		init: function(settings)
		{
			// TODO: merge settings.
		},

		switchWidth: function(w)
		{
			if (w!='auto')
				w = parseInt(w);
			
			joms.$('#community-wrap').css('width', w);
		},

		content: {
			elem: function()
			{
				return joms.$(joms.mobile.settings.content);
			},

			load: function(url)
			{
				var settings = {
					url: url,
					type: 'POST',
					data: {
						'section': 'content',
						'screen' : 'mobile'
					},
					dataType: 'text',
					beforeSend: function()
					{
						joms.mobile.content.elem().empty();
						joms.mobile.content.loading(true);
					},
					success: function(data, status, xhr)
					{
						joms.mobile.content.display(data.content);
					},
					complete: function()
					{
						joms.mobile.content.loading(false);
					},
					error: function()
					{
					}					
				};

				joms.ajax.execute(settings);
			},

			loading: function(toggle)
			{
				this.elem().toggleClass('loading', toggle);
			},

			display: function(data)
			{
				this.elem().html(data);
			}
		}
	}
});