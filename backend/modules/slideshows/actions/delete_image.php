<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a slideshow image
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsDeleteImage extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		if($this->id !== null && BackendSlideshowsModel::existsImage($this->id))
		{
			parent::execute();

			$this->record = (array) BackendSlideshowsModel::getImage($this->id);

			// delete item
			BackendSlideshowsModel::deleteImage($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete_image', array('item' => $this->record));

			// build redirect URL
			$this->redirect(BackendModel::createURLForAction('images') . '&slideshow_id=' . $this->record['slideshow_id'] . '&report=deleted&var=' . urlencode($this->record['title']));
		}
		// the image does not exist
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
