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
class BackendSlideshowsAjaxUpdateDatasetMethod extends BackendBaseAJAXAction
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
		$label = SpoonFilter::getPostValue('value', null, '');
		$id = SpoonFilter::getPostValue('id', null, '0', 'int');

		// validate
		if($id === 0) $this->output(self::BAD_REQUEST, null, 'ID-parameter is missing.');
		if($module == '') $this->output(self::BAD_REQUEST, null, 'module-parameter is missing.');
		if($label == '') $this->output(self::BAD_REQUEST, null, 'label-parameter is missing.');

		// build record
		$item['id'] = $id;
		$item['module'] = $module;
		$item['method'] = $method;
		$item['label'] = $label;

		$methods = BackendSlideshowsModel::getDataSetMethods($module);

		if(!BackendSlideshowsModel::existsDataSetMethod($item) && $methods[$item['id']]['label'] !== $item['label'])
		{
			$item['id'] = BackendSlideshowsModel::updateDataSetMethod($item);

			$this->output(self::OK, $item['id']);
		}
		// the dataset method already exists
		else $this->output(self::ERROR, BL::err('AlreadyExists'));
	}
}
