<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Resequence a slideshow's images via ajax.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsAjaxSequence extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			$item['id'] = (int) $id;
			$item['sequence'] = $i + 1;

			// update sequence
			if(BackendSlideshowsModel::existsImage($item['id']))
			{
				BackendSlideshowsModel::saveImage($item);
			}
		}

		BackendModel::triggerEvent(
			$this->getModule(), 'after_sequence', $ids
		);

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}

