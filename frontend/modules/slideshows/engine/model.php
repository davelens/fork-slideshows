<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic DB functions that we will be using in the slideshows module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendSlideshowsModel
{
	/**
	 * Returns the slideshow record
	 *
	 * @param int $id The ID of the slideshow to return.
	 * @return array
	 */
	public static function get($id)
	{
		$db = FrontendModel::getDB();

		$item = $db->getRecord(
			'SELECT a.*, b.type, b.settings
			 FROM slideshows AS a
			 INNER JOIN slideshows_types AS b ON b.id = a.type_id
			 WHERE a.id = ?',
			array((int) $id)
		);

		$item['settings'] = unserialize($item['settings']);

		return $item;
	}

	/**
	 * Returns the dataset method
	 *
	 * @param int $id The ID of the slideshow to get the method from.
	 * @return string
	 */
	public static function getDataSetMethod($id)
	{
		$db = FrontendModel::getDB();

		return $db->getVar(
			'SELECT i.method
			 FROM slideshows_datasets AS i
			 WHERE i.id = ?',
			array($id)
		);
	}

	/**
	 * Returns the images for a given slideshow ID
	 *
	 * @param int $slideshowID The ID of the slideshow to fetch the images for.
	 * @return array
	 */
	public static function getImages($slideshowID)
	{
		$db = FrontendModel::getDB();

		$records = $db->getRecords(
			'SELECT a.*
			 FROM slideshows_images AS a
			 WHERE a.slideshow_id = ?
			 ORDER BY a.sequence',
			array((int) $slideshowID)
		);

		if(empty($records)) return array();

		// fetch the slideshow so we know the measurements
		$slideshow = self::get($slideshowID);

		// define the format and the slideshow image folder
		$format = $slideshow['width'] . 'x' . $slideshow['height'];
		$slideshowImageURI = SITE_URL . '/' . FRONTEND_FILES_URL . '/slideshows/' . $slideshowID;

		foreach($records as $key => $record)
		{
			$records[$key]['index'] = $key;
			$records[$key]['image_url'] = $slideshowImageURI . '/' . $format . '/' . $record['filename'];
			$records[$key]['data'] = unserialize($record['data']);

			// is there a link given?
			if($records[$key]['data']['link'] !== null)
			{
				// set the external option. This allows us to link to external sources
				$external = ($records[$key]['data']['link']['type'] == 'external');
				$records[$key]['data']['link']['external'] = $external;

				// if this is an internal page, we need to build the url since we have the id
				if(!$external)
				{
					$records[$key]['data']['link']['url'] = FrontendNavigation::getURL($records[$key]['data']['link']['id']);
				}
			}
		}

		return $records;
	}
}
