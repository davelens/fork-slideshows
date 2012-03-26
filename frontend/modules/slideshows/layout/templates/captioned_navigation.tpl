{*
	The CSS classes "cpSlides" and "cpSlidesContainer" should be kept to keep the JS from breaking.
*}

<div class="mod">
	<div class="inner">
		<div class="bd">
			<div class="cpSlides">
				<div class="cpSlidesContainer col col-9">
					{iteration:items}
					<div id="cp-{$items.index}" class="slide"{option:!items.first} style="display:none;"{/option:!items.first}>
						<img src="{$items.image_url}" alt="{$items.title}" />
					</div>
					{/iteration:items}
				</div>

				{* The slideshow's pagination *}
				<div class="col col-3 lastCol">
					<ul class="slidesPagination">
						{iteration:items}

						<li rel="#{$items.index}">
							{$items.caption}
							<a class="hidden" href="#"></a>
						</li>
						{/iteration:items}
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>