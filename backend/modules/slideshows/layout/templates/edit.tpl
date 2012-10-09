{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSlideshows|ucfirst}: {$lblEdit}</h2>
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
								<h3>{$lblGeneral|ucfirst}</h3>
							</div>
							<div class="options horizontal">

								<p>
									<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									{$txtName} {$txtNameError}
								</p>

								<p>
									<label for="type">{$lblType|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									{$ddmType} {$ddmTypeError}
								</p>

								{option:modules}
								<p>
									<label for="module">{$lblModule|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									{$ddmModule} <span id="methodsHolder">{$ddmMethods}</span>
								</p>
								{/option:modules}
							</div>
						</div>
					</td>

					<td id="sidebar">
						<div id="measurements" class="box">
							<div class="heading">
								<h3>{$lblMeasurements|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
							</div>
							<div class="options">
								<p>
									<label for="width">{$lblWidth|ucfirst}</label>
									{$txtWidth} {$txtWidthError}
								</p>

								<p>
									<label for="height">{$lblHeight|ucfirst}</label>
									{$txtHeight} {$txtHeightError}
								</p>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showSlideshowsDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item.name}}
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