<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget that loads in a new slideshow based on its settings.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendSlideshowsWidgetIndex extends FrontendBaseWidget
{
	/**
	 * The dataset to populate the slideshow with
	 *
	 * @var	array
	 */
	private $items = array();

	/**
	 * The slideshow record
	 *
	 * @var	array
	 */
	private $slideshow;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();

		$this->loadTemplate();

		// If we succesfully gotten data, we can load the slideshow type
		if($this->getData())
		{
			$this->loadSlideshowType();
		}
		// no images yet
		else
		{
			// @todo show a friendly message stating there are no images in the slideshow right now.
		}

		$this->parse();
	}

	/**
	 * Fetches the dataset we need based on the slideshow's settings.
	 *
	 * @return bool	Returns true if the data loaded succesfully.
	 */
	private function getData()
	{
		$this->slideshow = FrontendSlideshowsModel::get($this->data['id']);

		if(empty($this->slideshow)) return false;

		// both the module and the callable method should be set
		if($this->slideshow['module'] !== null && $this->slideshow['dataset_id'] !== null)
		{
			$this->loadEngineFiles();

			// get the dataset method, so we can call it
			$method = FrontendSlideshowsModel::getDataSetMethod($this->slideshow['dataset_id']);

			/*
			 * PHP versions < 5.2.17 (my local dev) doesn't take too kindly with
			 * call_user_func() or call_user_func_array taking Class::Method as a parameter.
			 * We have to separate them in separate class/method vars and push them along in
			 * an array as parameter.
			 */
			list($class, $method) = explode('::', $method);

			$this->items = call_user_func_array(array($class, $method), array());
		}

		// this is a regular slideshow, and the user manages the images.
		else
		{
			$this->items = FrontendSlideshowsModel::getImages($this->data['id']);
		}

		// return true when data was set
		return !empty($this->items);
	}

	/**
	 * Loads all engine files of the module linked to this slideshow.
	 */
	private function loadEngineFiles()
	{
		$enginePath = FRONTEND_MODULES_PATH . '/' . $this->slideshow['module'] . '/engine';

		// the regex used here filters out all chars beginning with a dot (such as .swp files in vim)
		$engineFiles = SpoonFile::getList($enginePath, '/^[^\.]/');

		if(empty($engineFiles))
		{
			throw new Exception('You specified a module, but it has no model files!');
		}

		// get the dataset method, so we can call it
		$method = FrontendSlideshowsModel::getDataSetMethod($this->slideshow['dataset_id']);

		foreach($engineFiles as $file)
		{
			require_once $enginePath . '/' . $file;
		}

		// check if the method is callable/exists
		if(SPOON_DEBUG && !is_callable($method))
		{
			throw new Exception($method . ' is not a valid callable method!');
		}
	}

	/**
	 * Loads the slideshow type's related files and settings
	 */
	private function loadSlideshowType()
	{
		// load the general flexslider styles
		$this->addCSS('flexslider.css');

		// load type-specific css-file
		$frontendThemePath = '/frontend/modules/slideshows/layout';
		$cssFile = FrontendTheme::getPath($frontendThemePath . '/css/' . $this->slideshow['type'] . '.css');
		$templateFile = FrontendTheme::getPath($frontendThemePath . '/templates/' . $this->slideshow['type'] . '.tpl');

		// add the CSS file for the active type
		if(SpoonFile::exists(PATH_WWW . $cssFile)) $this->addCSS($cssFile, true);

		// add the javascript for the active type
		$this->addJS('flexslider.js');
		$this->addJS($this->slideshow['type'] . '.js');

		$templatePath = PATH_WWW . $templateFile;

		// set the slideshow template
		$this->slideshow['template'] = $templatePath;
	}

	/**
	 * Caches the widget and parses stuff into the template
	 */
	private function parse()
	{
		$cacheID = FRONTEND_LANGUAGE . '_slideshowCache_' . $this->slideshow['id'];

		// we will cache this widget for 15minutes
		$this->tpl->cache($cacheID, (24 * 60 * 60));

		// always assign the slideshow options
		$this->tpl->assign('slideshow', $this->slideshow);

		// if the widget isn't cached, assign the images
		if(!$this->tpl->isCached($cacheID)) $this->tpl->assign('items', $this->items);

		// parse the first item
		$this->tpl->assign('defaultImage', (isset($this->items[0])) ? $this->items[0] : false);
	}
}
