{*
	The CSS classes "basicSlides" and "basicSlidesContainer" should be kept to keep the JS from breaking.
*}


<div id="slideshow" class="basicSlides">
	<div class="basicSlidesContainer">
		{iteration:items}
			<div id="basic-{$items.index}" class="slide"{option:!items.first} style="display:none;"{/option:!items.first}>
				{option:items.data.link}
					<a href="{$items.data.link.url}"{option:items.data.link.external} target="_blank"{/option:items.data.link.external} title="{$items.title}">
				{/option:items.data.link}
					<img src="{$items.image_url}" alt="{$items.title}" />
				{option:items.data.link}
					</a>
				{/option:items.data.link}
			</div>
		{/iteration:items}
	</div>

	{* No javascript enabled *}
	<noscript>
		{option:defaultImage}
		<div class="basicSlides">
			<div class="basicSlidesContainer col col-6">
				<img src="{$defaultImage.image_url}" alt="{$defaultImage.title}" />
			</div>
		</div>
		{/option:defaultImage}
	</noscript>
</div>