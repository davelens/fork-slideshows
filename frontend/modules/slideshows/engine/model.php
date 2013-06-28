<?php

/**
 * In this file we store all generic DB functions that we will be using in the slideshows module
 *
 * @author Dave Lens <github-slideshows@davelens.be>
 */
class FrontendSlideshowsModel
{
	/**
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		$item = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT a.*, b.type, b.settings
			 FROM slideshows AS a
			 INNER JOIN slideshows_types AS b ON b.id = a.type_id
			 WHERE a.id = ?',
			array((int) $id)
		);

		$item['settings'] = unserialize($item['settings']);
		$item['hide_paging'] = ($item['hide_paging'] === 'Y');
		$item['hide_button_navigation'] = ($item['hide_button_navigation'] === 'Y');
		return $item;
	}

	/**
	 * @param int $id The ID of the slideshow
	 * @return string
	 */
	public static function getDataSetMethod($id)
	{
		return FrontendModel::getContainer()->get('database')->getVar(
			'SELECT a.method
			 FROM slideshows_datasets AS a
			 WHERE a.id = ?',
			array($id)
		);
	}

	/**
	 * @param int $id The ID of the slideshow
	 * @return array
	 */
	public static function getImages($id)
	{
		$records = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT a.*
			 FROM slideshows_images AS a
			 WHERE a.slideshow_id = ?
			 ORDER BY a.sequence',
			array((int) $id)
		);

		if(empty($records)) return array();

		// fetch the slideshow so we know the measurements
		$slideshow = self::get($id);

		// define the format and the slideshow image folder
		$format = $slideshow['width'] . 'x' . $slideshow['height'];
		$slideshowImageURI = SITE_URL . '/' . FRONTEND_FILES_URL . '/slideshows/' . $id;

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
					$extraId = $records[$key]['data']['link']['id'];
					$records[$key]['data']['link']['url'] = FrontendNavigation::getURL($extraId);
				}
			}
		}

		return (array) $records;
	}
}
