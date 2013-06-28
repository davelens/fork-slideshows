{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSlideshows|ucfirst}: {$lblEditImage}</h2>
</div>

{form:edit}
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">
						<div class="box">
							<div class="heading">
								<h3>{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
							</div>
							<div class="options">
								{$txtTitle} {$txtTitleError}
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3>{$lblImage|ucfirst}</h3>
							</div>
							<div class="options">
								<label for="image">{$lblImage|ucfirst}</label>
								{$fileImage} {$fileImageError}
							</div>
							{option:item.image}
							<div class="options">
								<img src="{$item.image_url}" alt="" />
								<ul class="inputList">
									<li>
										{$chkDeleteImage} <label for="deleteImage">{$lblDelete|ucfirst}</label>
									</li>
								</ul>
							</div>
							{/option:item.image}
						</div>

						<div class="box">
							<div class="heading">
								<h3>{$lblLink|ucfirst}</h3>
							</div>
							<div class="options">
								<p>
									<label for="externalLink">{$chkExternalLink|ucfirst} {$chkExternalLinkError}{$msgExternalLink|ucfirst}</label>
								</p>
								<p id="internalLinks">
									<label for="internalLink">{$lblInternalLink|ucfirst}</label>
									{$ddmInternalUrl} {$ddmInternalUrlError}
								</p>
								<p id="externalLinks">
									<label for="externalUrl">{$lblExternalLink|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									{$txtExternalUrl} {$txtExternalUrlError}
								</p>
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<div class="oneLiner">
									<h3>{$lblCaption|ucfirst}</h3>
									<abbr class="help">(?)</abbr>
									<div class="tooltip" style="display: none;">
										<p>{$msgHelpCaption}</p>
									</div>
								</div>
							</div>
							<div class="optionsRTE">
								{$txtCaption} {$txtCaptionError}
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showSlideshowsImages}
		<a href="{$var|geturl:'images'}&slideshow_id={$item.slideshow_id}" class="button">
			<span>{$lblBackToOverview|ucfirst}</span>
		</a>
		{/option:showSlideshowsImages}

		{option:showSlideshowsDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item.title}}
			</p>
		</div>
		{/option:showSlideshowsDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}