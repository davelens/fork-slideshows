<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the configuration-object for the slideshow module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
final class BackendSlideshowsConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();

	/**
	 * The disabled AJAX-actions
	 *
	 * @var	array
	 */
	protected $disabledAJAXActions = array();

	/**
	 * Class constructor
	 *
	 * @var	array
	 * @param string $module The module we're loading.
	 */
	public function __construct($module)
	{
		parent::__construct($module);

		$this->requireAdditionalEngineFiles();
	}

	/**
	 * Requires additional model/helper files
	 */
	private function requireAdditionalEngineFiles()
	{
		require_once BACKEND_MODULE_PATH . '/engine/helper.php';
	}
}
