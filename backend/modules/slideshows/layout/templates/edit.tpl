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

						<div id="settings" class="box">
							<div class="heading">
								<h3>{$lblSettings|ucfirst}</h3>
							</div>
							<div class="options horizontal">
								<p>
									<label for="speed">{$lblSpeed|ucfirst}</label>
									{$txtSpeed} {$txtSpeedError}
								</p>
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

						<div class="box">
							<div class="heading">
								<h3>{$lblNavigation|ucfirst}</h3>
							</div>
							<div class="options">
								<ul class="inputList">
									<li>
										{$chkHideButtonNavigation}
										<label for="hideButtonNavigation">{$msgHideButtonNavigation}</label>
									</li>
									<li>
										{$chkHidePaging}
										<label for="hidePaging">{$msgHidePaging}</label>
									</li>
								</ul>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showSlideshowsDelete}
			{option:isDeletable}
				<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
					<span>{$lblDelete|ucfirst}</span>
				</a>
				<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
					<p>
						{$msgConfirmDelete|sprintf:{$item.name}}
					</p>
				</div>
			{/option:isDeletable}
		{/option:showSlideshowsDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
