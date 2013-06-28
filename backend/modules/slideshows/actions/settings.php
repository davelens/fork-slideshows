<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general slideshows settings
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->header->addJS('settings.js');

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{

		$modules = BackendModel::getModules();

		// loop the modules and build the multi-checkbox values
		foreach($modules as $key => $module)
		{
			// the slideshows module shouldn't be in the list ^^
			if($module['value'] === 'slideshows') unset($modules[$key]);

			// build the values for this checkbox
			$label = ucfirst(BL::lbl(SpoonFilter::toCamelCase($module)));
			$modules[$key] = array('label' => $label, 'value' => $module);
		}

		// get all the currently stored modules
		$storedModules = BackendModel::getModuleSetting('slideshows', 'modules');

		$this->frm = new BackendForm('settings');
		$this->frm->addMultiCheckbox('modules', $modules, $storedModules);
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			// shorten fields
			$modules = $this->frm->getField('modules')->getValue();

			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting('slideshows', 'modules', $modules);

				if(!empty($modules))
				{
					foreach($modules as $module)
					{
						BackendSlideshowsHelper::writeHelperFile($module);
					}
				}

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
