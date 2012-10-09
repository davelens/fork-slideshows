if(!jsSlideshows) { var jsSlideshows = new Object(); }

jsSlideshows.basic =
{
	init: function()
	{
		// options: https://github.com/woothemes/FlexSlider/blob/master/jquery.flexslider.js#L804
		$('.flexslider').flexslider({
			selector: '.basic > .slide'
		});
	},

	eoo: true
}

$(document).ready(jsSlideshows.basic.init);

