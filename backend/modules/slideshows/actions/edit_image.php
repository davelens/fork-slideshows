<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit action, it will display a form to edit an existing slideshow image.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsEditImage extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');
		if($this->id !== null && BackendSlideshowsModel::existsImage($this->id))
		{
			parent::execute();

			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}
		// the item does not exist
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	protected function getData()
	{
		$this->record = BackendSlideshowsModel::getImage($this->id);
		$this->record['data'] = unserialize($this->record['data']);
		$this->record['link'] = $this->record['data']['link'];
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		$internalLinks = BackendSlideshowsModel::getInternalLinks();

		$internalLink = ($this->record['link']['type'] == 'internal') ? $this->record['link']['id'] : '';
		$externalLink = ($this->record['link']['type'] == 'external') ? $this->record['link']['url'] : '';

		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title']);
		$this->frm->addEditor('caption', $this->record['caption']);
		$this->frm->addImage('image');
		$this->frm->addCheckbox('delete_image');
		$this->frm->addCheckbox('external_link', ($this->record['link']['type'] == 'external'));
		$this->frm->addText('external_url', $externalLink);
		$this->frm->addDropdown('internal_url', $internalLinks, $internalLink,
			false,
			'chzn-select'
		)->setAttribute('style', 'width:800px')->setDefaultElement('--');
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('id', $this->id);
		$this->tpl->assign('item', $this->record);
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$image = $this->frm->getField('image');
			$caption = $this->frm->getField('caption');

			$this->frm->getField('title')->isFilled(BL::err('NameIsRequired'));
			if($this->record['image'] === null) $image->isFilled(BL::err('FieldIsRequired'));

			// validate url if one is given
			if($this->frm->getField('external_link')->getChecked())
			{
				$this->frm->getField('external_url')->isURL(BL::err('InvalidURL'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build image record to insert
				$item['id'] = $this->id;
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['caption'] = (!$caption->isFilled()) ? null : $caption->getValue();
				$item['filename'] = $this->record['image'];

				// the extra data
				$data = array('link' => null);

				// links
				if($this->frm->getField('internal_url')->isFilled())
				{
					$data['link'] = array(
						'type' => 'internal',
						'id' => $this->frm->getField('internal_url')->getValue()
					);
				}

				// external links
				if($this->frm->getField('external_link')->getChecked())
				{
					$data['link'] = array(
						'type' => 'external',
						'url' => $this->frm->getField('external_url')->getValue()
					);
				}

				$item['data'] = serialize($data);

				// set events files path for this record
				$path = FRONTEND_FILES_PATH . '/slideshows/' . $this->record['slideshow_id'];
				$format = $this->record['width'] . 'x' . $this->record['height'];

				// delete_image checkbox was checked
				if($this->frm->getField('delete_image')->getChecked())
				{
					SpoonFile::delete($path . '/source/' . $this->record['image']);
					SpoonFile::delete($path . '/64x64/' . $this->record['image']);
					SpoonFile::delete($path . '/' . $format . '/' . $this->record['image']);

					$item['filename'] = null;
				}

				if($image->isFilled())
				{
					// set formats
					$formats = array();
					$formats[] = array('size' => '64x64', 'force_aspect_ratio' => false);
					$formats[] = array('size' => $format, 'force_aspect_ratio' => false);

					// overwrite the filename
					if($item['filename'] === null)
					{
						$item['filename'] = time() . '.' . $image->getExtension();
					}

					// add images
					BackendSlideshowsHelper::addImages($image, $path, $item['filename'], $formats);
				}

				// save the item
				$id = BackendSlideshowsModel::saveImage($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_image', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('images') . '&slideshow_id=' . $this->record['slideshow_id'] . '&report=edited&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
