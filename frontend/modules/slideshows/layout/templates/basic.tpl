{*
	The CSS classes "flexslider", "basic" and "slide" are needed in JS.
*}

<div class="flexslider">
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
