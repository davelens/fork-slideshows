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
class BackendSlideshowsAjaxDeleteDatasetMethod extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$id = SpoonFilter::getPostValue('id', null, '0', 'int');

		if($id === 0) $this->output(self::BAD_REQUEST, null, 'ID-parameter is missing.');

		if(BackendSlideshowsModel::deleteDatasetMethod(array($id)) > 0)
		{
			return $this->output(self::OK, $id);
		}

		// no relevant records deleted
		$this->output(self::ERROR, BL::err('NoDatasetsDeleted'));
	}
}
