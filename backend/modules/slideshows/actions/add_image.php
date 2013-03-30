<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add action, it will display a form to add an image to a slideshow.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendSlideshowsAddImage extends BackendBaseActionEdit
{
	/**
	 * The slideshow record
	 *
	 * @var	array
	 */
	private $slideshow;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('slideshow_id', 'int');
		if($this->id !== null && BackendSlideshowsModel::exists($this->id))
		{
			parent::execute();

			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}
	}

	/**
	 * Get the necessary data
	 */
	private function getData()
	{
		$this->slideshow = BackendSlideshowsModel::get($this->getParameter('slideshow_id', 'int'));
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$internalLinks = BackendSlideshowsModel::getInternalLinks();

		$this->frm = new BackendForm('add');
		$this->frm->addText('title');
		$this->frm->addEditor('caption');
		$this->frm->addImage('image');
		$this->frm->addCheckbox('external_link');
		$this->frm->addText('external_url');
		$this->frm->addDropdown('internal_url', $internalLinks, '',
			false,
			'chzn-select'
		)->setAttribute('style', 'width:800px')->setDefaultElement('--');;
	}

	/**
	 * Parses stuff into the template
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('slideshow', $this->slideshow);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$image = $this->frm->getField('image');
			$caption = $this->frm->getField('caption');

			$this->frm->getField('title')->isFilled(BL::err('NameIsRequired'));
			$image->isFilled(BL::err('FieldIsRequired'));

			// validate url if one is given
			if($this->frm->getField('external_link')->getChecked())
			{
				$this->frm->getField('external_url')->isURL(BL::err('InvalidURL'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build image record to insert
				$item['slideshow_id'] = $this->slideshow['id'];
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['caption'] = (!$caption->isFilled()) ? null : $caption->getValue();

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

				// set files path for this record
				$path = FRONTEND_FILES_PATH . '/slideshows/' . $item['slideshow_id'];
				$format = $this->slideshow['width'] . 'x' . $this->slideshow['height'];

				// set formats
				$formats = array();
				$formats[] = array('size' => '64x64', 'force_aspect_ratio' => false);
				$formats[] = array('size' => $format, 'force_aspect_ratio' => false);

				// set the filename
				$item['filename'] = time() . '.' . $image->getExtension();

				// add images
				BackendSlideshowsHelper::addImages($image, $path, $item['filename'], $formats);

				// save the item
				$item['id'] = BackendSlideshowsModel::saveImage($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add_image', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('images') . '&slideshow_id=' . $item['slideshow_id'] . '&report=added&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
