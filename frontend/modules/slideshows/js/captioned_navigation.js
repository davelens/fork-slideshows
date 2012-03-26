if(!jsSlideshows) { var jsSlideshows = new Object(); }

jsSlideshows.captionedNavigation = 
{
	init: function()
	{
		$('.cpSlides').slides(
		{
			play: 5000,
			pause: 2500,
			hoverPause: true,
			container: 'cpSlidesContainer',
			preload: true,
			preloadImage: 'img/loading.gif',
			generatePagination: false,
			paginationClass: 'slidesPagination',
			crossfade: true,
			effect: 'fade'
		});

		
		/**
		 * Clicking the listitem should trigger the anchor click event, so slidesJS knows we
		 * want to view the selected slide.
		 */
		$('.cpSlides .slidesPagination li').live('click', function()
		{
			$('li[rel="'+ $(this).attr('rel') +'"] a').trigger('click');
		});
		

		/**
		 * This bit of code makes it possible to simulate multiple anchor links within the pagination.
		 * Dirty, but it works.
		 */
		$('.followLink').live('click', function()
		{
			if($(this).parents('li').attr('class') == 'current')
			{
				window.location = $(this).attr('rel');
			}
			else
			{	
				$('li[rel="'+ $(this).parents('li').attr('rel') +'"] a').trigger('click');
			}
		});
	},
	
	eoo: true
}

$(document).ready(jsSlideshows.captionedNavigation.init); 