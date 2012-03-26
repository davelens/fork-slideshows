{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSlideshows|ucfirst}: {$lblImages}</h2>

	{option:showSlideshowsAddImage}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_image'}&amp;slideshow_id={$slideshow.id}" class="button icon iconAdd" title="{$lblAddImage|ucfirst}">
			<span>{$lblAddImage|ucfirst}</span>
		</a>
	</div>
	{/option:showSlideshowsAddImage}
</div>

{option:dataGrid}
	<div class="dataGridHolder">
		<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massAction">
		<fieldset>
			<input type="hidden" name="slideshow_id" value="{$slideshow.id}" />
			{$dataGrid}
		</fieldset>
		</form>
	</div>
{/option:dataGrid}
{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}

<div class="fullwidthOptions">
	<a href="{$var|geturl:'index'}" class="button">
		<span>{$lblBackToOverview|ucfirst}</span>
	</a>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}