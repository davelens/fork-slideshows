{*
	The CSS classes "flexslider" and "basic" should be kept to keep the JS from breaking.
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
			</div>
		{/iteration:items}
	</div>
</div>
