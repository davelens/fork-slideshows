{*
	The CSS classes "flexslider", "basic" and "slide" are needed in JS.
*}

<div class="basic-holder" data-slideshow-speed="{option:slideshow.speed}{$slideshow.speed}{/option:slideshow.speed}{option:!slideshow.speed}5000{/option:!slideshow.speed}" data-control-nav="{option:slideshow.hide_paging}false{/option:slideshow.hide_paging}{option:!slideshow.hide_paging}true{/option:!slideshow.hide_paging}" data-direction-nav="{option:slideshow.hide_button_navigation}false{/option:slideshow.hide_button_navigation}{option:!slideshow.hide_button_navigation}true{/option:!slideshow.hide_button_navigation}">
	<div class="basic">
		{iteration:items}
			<div id="basic-{$items.index}" class="slide"{option:!items.first} style="display:none;"{/option:!items.first}>
				{option:items.data.link}
				<a href="{$items.data.link.url}"{option:items.data.link.external} target="_blank" rel="nofollow"{/option:items.data.link.external} title="{$items.title}">
				{/option:items.data.link}
					<img src="{$items.image_url}" alt="{$items.title}" />
				{option:items.data.link}
				</a>
				{/option:items.data.link}

				{option:items.caption}
				<div class="caption">
					{$items.caption}
				</div>
				{/option:items.caption}
			</div>
		{/iteration:items}
	</div>
</div>
