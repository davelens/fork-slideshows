<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the slideshows module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendSlideshowsModel
{
	/**
	 * Overview of all slideshows
	 *
	 * @var	string
	 */
	const QRY_DATAGRID_BROWSE =
		'SELECT i.id, i.name, i.module, a.type
		 FROM slideshows AS i
		 INNER JOIN slideshows_types AS a ON a.id = i.type_id
		 WHERE i.language = ?
		 GROUP BY i.id';

	/**
	 * Overview of all images for a slideshow
	 *
	 * @var	string
	 */
	const QRY_DATAGRID_BROWSE_IMAGES =
		'SELECT i.id, i.slideshow_id, i.filename, i.title, i.caption, i.sequence
		 FROM slideshows_images AS i
		 WHERE i.slideshow_id = ?
		 GROUP BY i.id';

	/**
	 * Deletes a slideshow.
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function delete($ids)
	{
		$db = BackendModel::getDB(true);

		$ids = (array) $ids;
		foreach($ids as $key => $id) $ids[$key] = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		$imageIDs = $db->getColumn(
			'SELECT id
			 FROM slideshows_images
			 WHERE
			 slideshow_id IN(' . implode(',', $idPlaceHolders) . ')',
			$ids
		);

		// delete the images if needed
		if(!empty($imageIDs)) self::deleteImage($imageIDs);

		foreach($ids as $id)
		{
			SpoonDirectory::delete(FRONTEND_FILES_PATH . '/slideshows/' . $id);

			$slideshow = self::get($id);

			// build extra
			$extra = array(
				'id' => $slideshow['extra_id'],
				'module' => 'slideshows',
				'type' => 'widget',
				'action' => 'index'
			);

			// delete extra
			$db->delete(
				'modules_extras',
				'id = ? AND module = ? AND type = ? AND action = ?',
				array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
			);

			// loop and cast to integers
			foreach($ids as $key => $id) $ids[$key] = (int) $id;

			// create an array with an equal amount of questionmarks as ids provided
			$idPlaceHolders = array_fill(0, count($ids), '?');

			// delete slideshow
			$db->delete('slideshows', 'id IN (' . implode(',', $idPlaceHolders) . ')', $ids);
		}

		BackendModel::invalidateFrontendCache('slideshowCache');
	}

	/**
	 * Deletes slideshow dataset method
	 *
	 * @param mixed $ids The ids to delete.
	 * @return int The number of affected rows
	 */
	public static function deleteDatasetMethod($ids)
	{
		$db = BackendModel::getDB(true);

		// make sure ids $is an array
		$ids = (array) $ids;

		if(empty($ids)) return;

		return (int) $db->delete('slideshows_datasets', 'id IN('. implode(',', $ids) .')');
	}

	/**
	 * Deletes slideshow images
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function deleteImage($ids)
	{
		$db = BackendModel::getDB(true);

		// make sure ids $is an array
		$ids = (array) $ids;

		if(empty($ids)) return;

		foreach($ids as $id)
		{
			$item = self::getImage($id);
			$slideshow = self::get($item['slideshow_id']);

			// delete image from disk
			$db->delete('slideshows_images', 'id = ?', array($id));

			// delete image reference from db
			SpoonFile::delete(FRONTEND_FILES_PATH . '/slideshows/' . $item['slideshow_id'] . '/source/' . $item['filename']);
			SpoonFile::delete(FRONTEND_FILES_PATH . '/slideshows/' . $item['slideshow_id'] . '/64x64/' . $item['filename']);
			SpoonFile::delete(FRONTEND_FILES_PATH . '/slideshows/' . $item['slideshow_id'] . '/' . $slideshow['format'] . '/' . $item['filename']);
		}

		BackendModel::invalidateFrontendCache('slideshowCache');
	}

	/**
	 * Check if a slideshow exists.
	 *
	 * @param int $id The id to check for existence.
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.id
			 FROM slideshows AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Check if a slideshow dataset method exists.
	 *
	 * @param array $item The dataset method record to check against
	 * @return bool
	 */
	public static function existsDataSetMethod($item)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.id
			 FROM slideshows_datasets AS i
			 WHERE i.module = ? AND i.method = ? AND i.label = ?',
			array($item['module'], $item['method'], $item['label'])
		);
	}

	/**
	 * Check if a slideshow image exists.
	 *
	 * @param int $id The id to check for existence.
	 * @return bool
	 */
	public static function existsImage($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.id
			 FROM slideshows_images AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get slideshow record.
	 *
	 * @param int $id The id of the record to get.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*, CONCAT(i.width, "x", i.height) AS format
			 FROM slideshows AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get dataset method record.
	 *
	 * @param int $id
	 * @return array
	 */
	public static function getDataSet($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*
			 FROM slideshows_datasets AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get dataset methods by module.
	 *
	 * @param string $module The module to fetch the dataset methods for.
	 * @return array
	 */
	public static function getDataSetMethods($module)
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT i.*
			 FROM slideshows_datasets AS i
			 WHERE i.module = ?',
			array($module),
			'id'
		);
	}

	/**
	 * Get dataset methods by module as pairs
	 *
	 * @param string $module
	 * @return array
	 */
	public static function getDataSetMethodsAsPairs($module)
	{
		return (array) BackendModel::getDB()->getPairs(
			'SELECT i.id, i.label
			 FROM slideshows_datasets AS i
			 WHERE i.module = ?',
			array($module)
		);
	}

	/**
	 * Get slideshow image record.
	 *
	 * @param int $id The id of the record to get.
	 * @return array
	 */
	public static function getImage($id)
	{
		$item = (array) BackendModel::getDB()->getRecord(
			'SELECT i.*, i.filename AS image, a.width, a.height
			 FROM slideshows_images AS i
			 INNER JOIN slideshows AS a ON a.id = i.slideshow_id
			 WHERE i.id = ?',
			array((int) $id)
		);

		$item['image_url'] = FRONTEND_FILES_URL . '/slideshows/' . $item['slideshow_id'] . '/' . $item['width'] . 'x' . $item['height'] . '/' . $item['image'];

		return $item;
	}

	/**
	 * Get images from slideshow
	 *
	 * @param int $slideshowID The ID of the slideshow to fetch the images for.
	 * @return array
	 */
	public static function getImages($slideshowID)
	{
		return (array) BackendModel::getDB()->getRecords(
			self::QRY_DATAGRID_BROWSE_IMAGES,
			array((int) $slideshowID)
		);
	}

	/**
	 * Fetches all the internal Urls
	 *
	 * @return array
	 */
	public static function getInternalLinks()
	{
		return (array) BackendModel::getDB()->getPairs(
			'SELECT p.id AS value, p.title
			 FROM pages AS p
			 WHERE p.status = ? AND p.hidden = ? AND p.language = ?',
			array('active', 'N', BL::getWorkingLanguage())
		);
	}

	/**
	 * Get the slideshow types as pairs
	 *
	 * @return array
	 */
	public static function getTypesAsPairs()
	{
		return (array) BackendModel::getDB()->getPairs(
			'SELECT id, type
			 FROM slideshows_types'
		);
	}

	/**
	 * Get the slideshow type settings for the given type ID
	 *
	 * @param int $id The ID of the type to fetch.
	 * @return array
	 */
	public static function getTypeSettings($id)
	{
		$settings = BackendModel::getDB()->getVar(
			'SELECT settings
			 FROM slideshows_types
			 WHERE id = ?',
			array((int) $id)
		);

		return unserialize($settings);
	}

	/**
	 * Insert a new slideshow
	 *
	 * @param string $item The data for the slideshow.
	 * @return int
	 */
	private static function insert($item)
	{
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array(
			'module' => 'slideshows',
			'type' => 'widget',
			'label' => 'Slideshows',
			'action' => 'index',
			'data' => null,
			'hidden' => 'N',
			'sequence' => $db->getVar(
				'SELECT MAX(i.sequence) + 1
				 FROM modules_extras AS i
				 WHERE i.module = ?',
				array('slideshows')
			)
		);

		// make sure a valid sequence is set
		if(is_null($extra['sequence']))
		{
			$extra['sequence'] = $db->getVar(
				'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
				 FROM modules_extras AS i'
			);
		}

		// insert extra
		$item['extra_id'] = $db->insert('modules_extras', $extra);
		$extra['id'] = $item['extra_id'];

		// insert and return the new id
		$item['id'] = $db->insert('slideshows', $item);

		// update extra (item id is now known)
		$extra['data'] = serialize(array(
			'id' => $item['id'],
			'extra_label' => ucfirst(BL::lbl('Slideshows', 'core')) . ': ' . $item['name'],
			'language' => $item['language'],
			'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])
		);

		$db->update(
			'modules_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?',
			array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
		);

		return $item['id'];
	}

	/**
	 * Insert a new dataset method
	 *
	 * @param string $item The data for the ... dataset record.
	 * @return int
	 */
	public static function insertDataSetMethod($item)
	{
		$db = BackendModel::getDB(true);

		return $db->insert('slideshows_datasets', $item);
	}

	/**
	 * Insert a new slideshow image
	 *
	 * @param string $item The data for the image.
	 * @return int
	 */
	private static function insertImage($item)
	{
		return (int) BackendModel::getDB(true)->insert('slideshows_images', $item);
	}

	/**
	 * Saves a slideshow record
	 *
	 * @param array $item The record to save.
	 * @return int
	 */
	public static function save($item)
	{
		// check if an entry already exists: update existing
		if(isset($item['id']) && self::exists($item['id']))
		{
			self::update($item);
		}
		// if no existing entry exist, insert a new one
		else $item['id'] = self::insert($item);

		BackendModel::invalidateFrontendCache('slideshowCache');

		return $item['id'];
	}

	/**
	 * Saves a slideshow image record
	 *
	 * @param array $item The image record to save.
	 * @return int
	 */
	public static function saveImage($item)
	{
		// check if an item already exists: update existing
		if(isset($item['id']) && self::existsImage($item['id']))
		{
			self::updateImage($item);
		}
		// if no existing entry exist, insert a new one
		else $item['id'] = self::insertImage($item);

		BackendModel::invalidateFrontendCache('slideshowCache');

		return $item['id'];
	}

	/**
	 * Update a slideshow
	 * Remark: $slideshow['id'] should be available.
	 *
	 * @param array $item The new data for the slideshow.
	 * @return int	The amount of updated records
	 */
	private static function update($item)
	{
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array(
			'id' => $item['extra_id'],
			'module' => 'slideshows',
			'type' => 'widget',
			'label' => 'Slideshows',
			'action' => 'index',
			'data' => serialize(array(
				'id' => $item['id'],
				'extra_label' => ucfirst(BL::lbl('Slideshows', 'core')) . ': ' . $item['name'],
				'language' => $item['language'],
				'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id']
			)),
			'hidden' => 'N'
		);

		// update extra
		$db->update(
			'modules_extras',
			$extra,
			'id = ? AND module = ? AND type = ? AND action = ?',
			array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
		);

		BackendModel::invalidateFrontendCache('slideshowCache');

		return $db->update('slideshows', $item, 'id = ?', $item['id']);
	}

	/**
	 * Update an existing dataset method
	 *
	 * @param string $item The data for the ... dataset record.
	 * @return int
	 */
	public static function updateDataSetMethod($item)
	{
		$db = BackendModel::getDB(true);

		$db->update('slideshows_datasets', $item, 'id = ?', array($item['id']));

		BackendModel::invalidateFrontendCache('slideshowCache');

		return $item['id'];
	}

	/**
	 * Update a slideshow image
	 *
	 * @param array $item The new data for the slideshow.
	 * @return int The amount of updated records
	 */
	private static function updateImage($item)
	{
		$db = BackendModel::getDB(true);

		BackendModel::invalidateFrontendCache('slideshowCache');

		return $db->update('slideshows_images', $item, 'id = ?', array($item['id']));
	}
}
