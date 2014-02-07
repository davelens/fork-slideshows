<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a slideshow
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		if($this->id !== null && BackendSlideshowsModel::exists($this->id))
		{
			parent::execute();

			// get data
			$this->record = (array) BackendSlideshowsModel::get($this->id);

			// delete item
			BackendSlideshowsModel::delete(array($this->id));

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('item' => $this->record));

			// build redirect URL
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['name']));
		}
		// the image does not exist
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
