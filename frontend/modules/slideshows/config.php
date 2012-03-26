<?php

/**
 * This is the configuration-object
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
final class FrontendSlideshowsConfig extends FrontendBaseConfig
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
}
