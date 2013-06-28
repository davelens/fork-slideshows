<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit action, it will display a form to edit an existing slideshow.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');
		if($this->id !== null && BackendSlideshowsModel::exists($this->id))
		{
			parent::execute();

			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}
		// item does not exist
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * This will reformat the images
	 *
	 * @param int $width
	 * @param int $height
	 */
	protected function generateNewImages($width, $height)
	{
		$images = BackendSlideshowsModel::getImages($this->id);

		if(!empty($images))
		{
			// define the old and the new format folders (ie. '600x280')
			$newFormat = $width . 'x' . $height;
			$oldFormat = $this->record['width'] . 'x' . $this->record['height'];

			// define the path to the slideshow
			$slideshowPath = FRONTEND_FILES_PATH . '/slideshows/' . $this->id;

			// set formats
			$formats = array();
			$formats[] = array('size' => $newFormat, 'force_aspect_ratio' => false);

			foreach($images as $image)
			{
				BackendSlideshowsHelper::generateImages($slideshowPath, $image['filename'], $formats);
			}

			// delete the old format
			SpoonDirectory::delete($slideshowPath . '/' . $oldFormat);
		}
	}

	/**
	 * Get the data
	 */
	protected function getData()
	{
		$this->record = BackendSlideshowsModel::get($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addDropdown('type', BackendSlideshowsModel::getTypesAsPairs(), $this->record['type_id']);
		$this->frm->addDropdown('module', BackendSlideshowsHelper::getSupportedModules(), $this->record['module']);
		$this->frm->addDropdown('methods', BackendSlideshowsModel::getDataSetMethodsAsPairs($this->record['module']), $this->record['dataset_id']);

		$this->frm->addText('width', $this->record['width']);
		$this->frm->addText('height', $this->record['height']);
		$this->frm->addText('speed', $this->record['speed']);

		$hideButtonNavigation = $this->record['hide_button_navigation'] === 'Y';
		$hidePaging = $this->record['hide_paging'] === 'Y';

		$this->frm->addCheckbox('hide_button_navigation', $hideButtonNavigation);
		$this->frm->addCheckbox('hide_paging', $hidePaging);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('id', $this->id);
		$this->tpl->assign('item', $this->record);

		$this->tpl->assign('isDeletable', !BackendSlideshowsModel::existsPageBlock($this->id));

		if(BackendSlideshowsHelper::getModules())
		{
			$this->tpl->assign('modules', true);
		}
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

			// shorten fields
			$module = $this->frm->getField('module');
			$width = $this->frm->getField('width');
			$height = $this->frm->getField('height');
			$hideButtonNavigation = $this->frm->getField('hide_button_navigation')->getChecked();
			$hidePaging = $this->frm->getField('hide_paging')->getChecked();

			// validate fields
			$this->frm->getField('name')->isFilled(BL::err('NameIsRequired'));
			if($width->isFilled())
			{
				$width->isNumeric(BL::err('NumericCharactersOnly'));
			}
			if($width->isFilled())
			{
				$width->isNumeric(BL::err('NumericCharactersOnly'));
			}

			// the method is filled by javascript, so we have to fetch it from POST
			$method = isset($_POST['methods']) ? $_POST['methods'] : null;

			// no errors?
			if($this->frm->isCorrect())
			{
				// build slideshow record to insert
				$item['id'] = $this->id;
				$item['extra_id'] = $this->record['extra_id'];
				$item['language'] = BL::getWorkingLanguage();
				$item['name'] = $this->frm->getField('name')->getValue();
				$item['type_id'] = $this->frm->getField('type')->getValue();
				$item['module'] = ($module->getValue() == '0') ? null : $module->getValue();
				$item['hide_button_navigation'] = $hideButtonNavigation ? 'Y' : 'N';
				$item['hide_paging'] = $hidePaging ? 'Y' : 'N';
				$item['speed'] = (int) $this->frm->getField('speed')->getValue();

				if($item['speed'] === 0)
				{
					$item['speed'] = 5000;
				}

				if($item['module'] !== null)
				{
					$item['dataset_id'] = $method;
				}
				else
				{
					$item['width'] = $width->getValue();
					$item['height'] = $height->getValue();

					/*
						if the width/height differ from the one in $this->record, we resize
						all images in this slideshow to the new measurements.
					*/
					if($item['width'] != $this->record['width'] || $item['height'] != $this->record['height'])
					{
						$this->generateNewImages($item['width'], $item['height']);
					}
				}

				// save the item
				$id = BackendSlideshowsModel::save($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited' . '&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
