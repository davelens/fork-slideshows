if(!jsSlideshows) { var jsSlideshows = new Object(); }

jsSlideshows.basic =
{
	init: function()
	{
		// options: https://github.com/woothemes/FlexSlider/blob/master/jquery.flexslider.js#L804
		$('.basic-holder').flexslider({
			selector: '.basic > .slide',
			animation: "slide"
		});
	},

	eoo: true
}

$(document).ready(jsSlideshows.basic.init);

