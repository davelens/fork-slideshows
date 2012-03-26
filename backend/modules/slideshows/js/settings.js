/**
 * Interaction for the slideshows settings action
 *
 * @author	Dave Lens <dave@netlash.com>
 */
jsBackend.slideshows.settings =
{
		init: function()
		{
			$(window).keydown(function(event)
			{
				if(event.keyCode == 13)
				{
					event.preventDefault();
					return false;
				}
			});

			$('.hidden').hide();
			$('.container').live('click', jsBackend.slideshows.settings.clickHandler);
			$('.saveMethod').live('click', jsBackend.slideshows.settings.submitHandler);
			$('.actionDelete a').live('click', jsBackend.slideshows.settings.deleteHandler);
			$('.container span label').each(jsBackend.slideshows.settings.mouseHandler);
			$('#slideshowSettings select').each(jsBackend.slideshows.settings.selectHandler);
		},


		hide: function()
		{
			$(this).hide();
		},

		deleteHandler: function(event)
		{
			var self = $(this);
			jsBackend.slideshows.deleteMethod(self.data('id'), self.data('module'));
		},

		selectHandler: function(event)
		{
			var module = $(this).parents('.module').find('input').val()
			var methodsDropdown = $('#method-'+ module);

			jsBackend.slideshows.loadMethods(module, methodsDropdown);
			jsBackend.slideshows.buildDatagrid(module);
		},


		submitHandler: function(event)
		{
			event.preventDefault();

			var self = $(this);
			var module = self.parents('.module').find('input').val();
			var method = $('#method-'+ module).val();
			var label = $('#method-'+ module +'-label').val();

			jsBackend.slideshows.saveMethod(module, method, label);
		},


		clickHandler: function(event)
		{
			event.preventDefault();

			var self = $(this);

			// the action is currently closed, open it
			if(self.hasClass('iconCollapsed'))
			{
				// load the methods
				var module = self.prev('input').val();
				var methodsDropdown = $('#method-'+ module);

				jsBackend.slideshows.loadMethods(module, methodsDropdown);

				// show the method label form and select the relevant checkbox
				self.prev('input').attr('checked', true);

				// change title
				self.attr('title', 'close');

				// change css
				self.addClass('iconExpanded');
				self.removeClass('iconCollapsed');

				if(methodsDropdown.val() == null)
				{
					self.siblings('.noMethods').show();
				}

				else
				{
					self.next('.configureDataSet').show();
				}

				// toggle the datagrid
				jsBackend.slideshows.toggleDataGrid(module);
			}

			// the action is currently open, close it
			else
			{
				// hide the method label form and deselect the relevant checkbox
				self.next('.configureDataSet').hide();
				self.siblings('.noMethods').hide();
				self.prev('input').attr('checked', false);

				// change title
				self.attr('title', 'open');

				// change css
				self.addClass('iconCollapsed');
				self.removeClass('iconExpanded');
			}
		},


		mouseHandler: function()
		{
			$(this).mouseover(function()
			{
				$(this).css('cursor', 'pointer');
				$(this).css('cursor', 'hand');
			});
		},


		// end
		eoo: true
}
