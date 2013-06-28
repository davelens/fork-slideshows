/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the slideshows module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash,com>
 */
jsBackend.slideshows =
{
	previewSelector: '#previewImages',

	init: function()
	{
		var module = $('#module');

		jsBackend.slideshows.toggleMethods(module);
		jsBackend.slideshows.toggleLinks();

		if(module.length > 0)
		{
			module.live('change', function()
			{
				jsBackend.slideshows.toggleMethods(module);

				jsBackend.slideshows.loadDataSetMethods($(this).val(), $('#methods'));
			});
		}

		// load the settings
		if(jsBackend.slideshows.settings)
		{
			jsBackend.slideshows.settings.init();
		}

		// whenever an option is hovered in the methods dropdown, we should fetch a preview tooltip with images
		$('#methods option').hover(jsBackend.slideshows.selectMethodFocusHandler, jsBackend.slideshows.selectMethodBlurHandler);
	},

	selectMethodBlurHandler: function(e)
	{
		var preview = $(jsBackend.slideshows.previewSelector);

		preview.hide();
		preview.html('');
	},

	selectMethodFocusHandler: function(e)
	{
		var self = $(this);

		$.ajax(
		{
			data:
			{
				fork: { action: 'get_dataset_preview' },
				id: self.val()
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					// show error if needed
					if(jsBackend.debug) alert(textStatus);
				}
				else
				{
					/* @todo show preview on option hover
					var preview = $(jsBackend.slideshows.previewSelector);
					var methods = $('#methods');

					var relativeX = parseInt(methods.position().left);
					var relativeY = parseInt(methods.position().top);

					preview.css({left: relativeX, top: relativeY});

					for(i in json.data)
					{
						var file = json.data[i].replace('/600x280/', '/64x64/'); //@todo fix regex
						preview.append('<img class="thumbnail" src="'+ file +'" alt="" />');
						preview.show();
					}
					*/
				}
			}
		});
	},

	buildDatagrid: function(module)
	{
		$.ajax(
		{
			data:
			{
				fork: { action: 'get_dataset_methods' },
				m: module
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					// show error if needed
					if(jsBackend.debug) alert(textStatus);
				}
				else
				{
					// clear the datagrid bodyo first
					var dataGridBody = $('#datagrid-'+ module).find('tbody').html('');

					for(var key in json.data)
					{
						var item = json.data[key];
						var rowHTML = '<tr>'+
							'<td class="method">'+ item.method +'</td>'+
							'<td class="label" data-id="{id:\''+ item.id +'\', m:\''+ module +'\', method:\''+ item.method +'\'}">'+
								item.label +
							'</td>'+
							'<td class="action actionDelete">'+
								'<a class="button icon iconDelete linkButton" href="#" data-id="'+ item.id +'" data-module="'+ module  +'">'+
									'<span>{$lblDelete}</span>'+
								'</a>'+
							'</td>'+
						'</tr>';

						// add the HTML for the new row to the datagrid
						dataGridBody.append(rowHTML);
					}

					// reload the inline edits for all datagrids
					jsBackend.slideshows.reloadDataGridInlineEdits(module);

					// toggle the datagrid
					jsBackend.slideshows.toggleDataGrid(module);
				}
			}
		});
	},

	loadDataSetMethods: function(module, select)
	{
		$.ajax(
		{
			data:
			{
				fork: { action: 'get_dataset_methods_as_pairs' },
				m: module
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					// show error if needed
					if(jsBackend.debug) alert(textStatus);
				}
				else
				{
					// clear the select HTML first
					var dataGridBody = select.html('');

					for(var key in json.data)
					{
						var label = json.data[key];
						var optionHTML = '<option value="'+ key +'">'+
								label +
						'</option>';

						// add the HTML for the new row to the datagrid
						select.append(optionHTML);
					}
				}
			}
		});
	},

	loadMethods: function(module, select)
	{
		$.ajax(
		{
			data:
			{
				fork: { action: 'get_supported_methods' },
				m: module
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					// show error if needed
					if(jsBackend.debug) alert(textStatus);
				}
				else
				{
					select.html(json.data);
				}
			}
		});
	},

	reloadDataGridInlineEdits: function(module)
	{
		var label = $('#datagrid-'+ module +' td.label');

		if(label.length > 0)
		{
			label.inlineTextEdit(
			{
				params: { fork: { action: 'update_dataset_method' } },
				tooltip: '{$msgClickToEdit}'
			});
		}
	},

	deleteMethod: function(id, module)
	{
		$.ajax(
		{
			data:
			{
				fork: { action: 'delete_dataset_method' },
				id: id,
				m: module
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					// show error if needed
					if(jsBackend.debug) alert(textStatus);
				}
				else
				{
					// rebuild the datagrid for this module
					jsBackend.slideshows.buildDatagrid(module);

					// show a message stating the label was mapped
					jsBackend.messages.add('success', 'The selected method was successfully deleted');
				}
			}
		});
	},

	saveMethod: function(module, method, label)
	{
		$.ajax(
		{
			data:
			{
				fork: { action: 'insert_dataset_method' },
				m: module,
				method: method,
				label: label
			},
			success: function(json, textStatus)
			{
				if(json.code != 200)
				{
					// show error if needed
					if(jsBackend.debug) alert(textStatus);
				}
				else
				{
					// rebuild the datagrid for this module
					jsBackend.slideshows.buildDatagrid(module);

					// show a message stating the label was mapped
					jsBackend.messages.add('success', 'The selected method is now mapped to the label "'+ label +'"');
				}
			}
		});
	},

	toggleDataGrid: function(module)
	{
		// check if we should show/hide the datagrid
		var dataGrid = $('#datagrid-'+ module);
		var rows = dataGrid.find('tbody tr');

		if(rows.length > 0)
		{
			dataGrid.show();
		}
		else
		{
			dataGrid.hide();
		}
	},

	toggleLinks: function()
	{
		// checkbox checked?
		if(!$('#externalLink').is(':checked'))
		{
			$('#externalLinks').toggle();

			$('#externalLink').click(function(){
				$('#externalLinks').toggle();
				$('#internalLinks').toggle();
			});
		}
		// nope
		else
		{
			$('#internalLinks').toggle();

			$('#externalLink').click(function(){
				$('#externalLinks').toggle();
				$('#internalLinks').toggle();
			});
		}
	},

	toggleMethods: function(module)
	{
		if(module.val() == '0' || module.val() == undefined)
		{
			$('#methods').hide();
			$('#measurements').show();
		}

		else
		{
			$('#methods').show();
			$('#measurements').hide();
		}
	},
}


$(jsBackend.slideshows.init);
