<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will get a list of preview images for the selected dataset.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsAjaxGetDatasetPreview extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$id = SpoonFilter::getPostValue('id', null, '');
		if($id == '') $this->output(self::BAD_REQUEST, null, 'module-parameter is missing.');

		$record = BackendSlideshowsModel::getDataSet($id);
		if(empty($record)) $this->output(self::BAD_REQUEST, null, 'no matching dataset found.');

		// load module slideshow info
		require_once FRONTEND_MODULES_PATH . '/' . $record['module'] . '/engine/slideshows.php';

		// check if method is available
		if(is_callable($record['method']))
		{
			$results = call_user_func($record['method']);

			if(!empty($results))
			{
				foreach($results as $result)
				{
					$images[] = $result['image_url'];
				}
			}
		}

		// output
		$this->output(self::OK, $images);
	}
}
