if(!jsSlideshows) { var jsSlideshows = new Object(); }

jsSlideshows.basic = 
{
	init: function()
	{	
		$('.basicSlides').slides({
			play: 5000,
			pause: 2500,
			hoverPause: true,
			container: 'basicSlidesContainer',
			crossfade: true,
			effect: 'fade'
		});
	},
	
	eoo: true
}

$(document).ready(jsSlideshows.basic.init); 