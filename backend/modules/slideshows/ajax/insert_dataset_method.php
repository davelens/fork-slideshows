<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will save a selected method + label for the given module.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsAjaxInsertDatasetMethod extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$module = SpoonFilter::getPostValue('m', null, '');
		$method = SpoonFilter::getPostValue('method', null, '');
		$label = SpoonFilter::getPostValue('label', null, '');

		// validate
		if($module == '') $this->output(self::BAD_REQUEST, null, 'module-parameter is missing.');
		if($label == '') $this->output(self::BAD_REQUEST, null, 'label-parameter is missing.');

		// build record
		$item['module'] = $module;
		$item['method'] = $method;
		$item['label'] = $label;

		if(!BackendSlideshowsModel::existsDataSetMethod($item))
		{
			$item['id'] = BackendSlideshowsModel::insertDataSetMethod($item);

			$this->output(self::OK, $item['id']);
		}
		// the dataset method already exists
		else $this->output(self::ERROR, BL::err('AlreadyExists'));
	}
}
