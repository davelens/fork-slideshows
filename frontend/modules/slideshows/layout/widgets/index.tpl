{*
	variables that are available:
	- {$widgetEventsArchive}:
*}
{option:slideshow.template}
{cache:{$LANGUAGE}_slideshowCache_{$slideshow.id}}
	{include:{$slideshow.template}}
{/cache:{$LANGUAGE}_slideshowCache_{$slideshow.id}}
{/option:slideshow.template}