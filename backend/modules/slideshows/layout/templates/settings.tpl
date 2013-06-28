{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblSlideshows|ucfirst}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$msgEnableSlideshowsForModules|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong horizontal" id="slideshowSettings">
			<ul id="moduleList" class="inputList">
				{iteration:modules}
					<li class="module">
						{$modules.chkModules}

						<a href="#" class="icon iconCollapsed container" title="open">
							<span>
								<label for="modules{$modules.label}">{$modules.label}</label>
							</span>
						</a>

						<div id="module-{$modules.value}" class="configureDataSet hidden">
							<div class="heading">
								<h3>{$msgConfigureDataSet}</h3>
							</div>
							<div class="box">
								<div id="datagrid-{$modules.value}" class="options hidden">
									<div class="dataGridHolder">
										<table class="dataGrid" cellpadding="0" cellspacing="0">
											<thead>
												<tr>
													<th width="200"><span>method</span></th>
													<th><span>label</span></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
								<div class="options">
									<p>
										<label for="method-{$modules.value}">{$msgChooseMethod|ucfirst}</label>
										<select name="method-{$modules.value}" id="method-{$modules.value}">
										</select>
									</p>
									<p>
										<label for="method-{$modules.value}-label">{$msgChooseMethodLabel|ucfirst}</label>
										<input name="method-{$modules.value}-label" id="method-{$modules.value}-label" class="inputText" type="text"  value="" />
									</p>
									<div class="buttonHolder">
										<a id="submit-{$modules.value}" class="button inputButton saveMethod" href="#save">
											<span>{$lblAdd|ucfirst}</span>
										</a>
									</div>
								</div>
							</div>
						</div>

						<div class="noMethods hidden">
							<div class="heading">
								<h3>{$msgNoMethodsHeading}</h3>
							</div>
							<div class="box">
								<div class="options">
									{$msgNoMethodsMessage}
								</div>
							</div>
						</div>
					</li>
				{/iteration:modules}
			</ul>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
