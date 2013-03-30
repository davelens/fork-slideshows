<?php

/**
 * In this file we store all generic functions that we will be using in the slideshows module
 *
 * @author Dave Lens <github-slideshows@davelens.be>
 */
class BackendSlideshowsModel
{
	const QRY_DATAGRID_BROWSE =
		'SELECT a.id, a.name, a.module, b.type
		 FROM slideshows AS a
		 INNER JOIN slideshows_types AS b ON b.id = a.type_id
		 WHERE a.language = ?
		 GROUP BY a.id';

	const QRY_DATAGRID_BROWSE_IMAGES =
		'SELECT a.id, a.slideshow_id, a.filename, a.title, a.caption, a.sequence
		 FROM slideshows_images AS a
		 WHERE a.slideshow_id = ?
		 GROUP BY a.id';

	/**
	 * @param array $ids
	 */
	public static function delete(array $ids)
	{
		$db = BackendModel::getContainer()->get('database');
		foreach($ids as $key => $id)
		{
			// only handle ids that are not linked to any page
			if(self::existsPageBlock((int) $id)) unset($ids[$key]);
			else $ids[$key] = (int) $id;
		}

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
		if(!empty($imageIDs))
		{
			self::deleteImage($imageIDs);
		}

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

			// delete extra from linked page
			$db->delete(
				'pages_blocks',
				'extra_id = ?',
				array($extra['id'])
			);

			foreach($ids as $key => $id)
			{
				$ids[$key] = (int) $id;
			}

			// create an array with an equal amount of questionmarks as ids provided
			$idPlaceHolders = array_fill(0, count($ids), '?');

			$db->delete('slideshows', 'id IN (' . implode(',', $idPlaceHolders) . ')', $ids);
		}

		BackendModel::invalidateFrontendCache('slideshowCache');
	}

	/**
	 * @param array $ids
	 * @return int The number of affected rows
	 */
	public static function deleteDatasetMethod(array $ids)
	{
		if(empty($ids)) return;
		return (int) BackendModel::getContainer()->get('database')->delete(
			'slideshows_datasets',
			'id IN('. implode(',', $ids) .')'
		);
	}

	/**
	 * @param array $ids
	 */
	public static function deleteImage(array $ids)
	{
		if(empty($ids)) return;

		foreach($ids as $id)
		{
			$item = self::getImage($id);
			$slideshow = self::get($item['slideshow_id']);

			// delete image reference from db
			BackendModel::getContainer()->get('database')->delete('slideshows_images', 'id = ?', array($id));

			// delete image from disk
			$basePath = FRONTEND_FILES_PATH . '/slideshows/' . $item['slideshow_id'];
			SpoonFile::delete($basePath . '/source/' . $item['filename']);
			SpoonFile::delete($basePath . '/64x64/' . $item['filename']);
			SpoonFile::delete($basePath . '/' . $slideshow['format'] . '/' . $item['filename']);
		}

		BackendModel::invalidateFrontendCache('slideshowCache');
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM slideshows AS a
			 WHERE a.id = ?',
			array((int) $id)
		);
	}

	/**
	 * @param array $item The dataset method record to check against
	 * @return bool
	 */
	public static function existsDataSetMethod(array $item)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM slideshows_datasets AS a
			 WHERE a.module = ? AND a.method = ? AND a.label = ?',
			array($item['module'], $item['method'], $item['label'])
		);
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public static function existsImage($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM slideshows_images AS a
			 WHERE a.id = ?',
			array((int) $id)
		);
	}

	/**
	 * @param int $slideshowId
	 * @return bool
	 */
	public static function existsPageBlock($slideshowId)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM slideshows AS a
			 INNER JOIN pages_blocks AS b ON b.extra_id = a.extra_id
			 INNER JOIN pages AS c ON c.revision_id = b.revision_id
			 WHERE b.visible="Y" AND c.hidden="N" AND c.status="active" AND a.id = ?',
			array((int) $slideshowId)
		);
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT a.*, CONCAT(a.width, "x", a.height) AS format
			 FROM slideshows AS a
			 WHERE a.id = ?',
			array((int) $id)
		);
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public static function getDataSet($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT a.*
			 FROM slideshows_datasets AS a
			 WHERE a.id = ?',
			array((int) $id)
		);
	}

	/**
	 * @param string $module
	 * @return array
	 */
	public static function getDataSetMethods($module)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT a.*
			 FROM slideshows_datasets AS a
			 WHERE a.module = ?',
			array($module),
			'id'
		);
	}

	/**
	 * @param string $module
	 * @return array
	 */
	public static function getDataSetMethodsAsPairs($module)
	{
		return (array) BackendModel::getContainer()->get('database')->getPairs(
			'SELECT a.id, a.label
			 FROM slideshows_datasets AS a
			 WHERE a.module = ?',
			array($module)
		);
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public static function getImage($id)
	{
		$item = (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT a.*, a.filename AS image, b.width, b.height
			 FROM slideshows_images AS a
			 INNER JOIN slideshows AS b ON b.id = a.slideshow_id
			 WHERE a.id = ?',
			array((int) $id)
		);

		$basePath = FRONTEND_FILES_URL . '/slideshows/' . $item['slideshow_id'];
		$format = $item['width'] . 'x' . $item['height'];
		$item['image_url'] = $basePath . '/' . $format . '/' . $item['image'];
		return $item;
	}

	/**
	 * @param int $slideshowID
	 * @return array
	 */
	public static function getImages($slideshowID)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			self::QRY_DATAGRID_BROWSE_IMAGES,
			array((int) $slideshowID)
		);
	}

	/**
	 * @return array
	 */
	public static function getInternalLinks()
	{
		return (array) BackendModel::getContainer()->get('database')->getPairs(
			'SELECT p.id AS value, p.title
			 FROM pages AS p
			 WHERE p.status = ? AND p.hidden = ? AND p.language = ?',
			array('active', 'N', BL::getWorkingLanguage())
		);
	}

	/**
	 * @return array
	 */
	public static function getTypesAsPairs()
	{
		return (array) BackendModel::getContainer()->get('database')->getPairs(
			'SELECT id, type
			 FROM slideshows_types'
		);
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public static function getTypeSettings($id)
	{
		$settings = BackendModel::getContainer()->get('database')->getVar(
			'SELECT settings
			 FROM slideshows_types
			 WHERE id = ?',
			array((int) $id)
		);

		return unserialize($settings);
	}

	/**
	 * @param string $item
	 * @return int
	 */
	private static function insert($item)
	{
		$db = BackendModel::getContainer()->get('database');

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
	 * @param string $item
	 * @return int
	 */
	public static function insertDataSetMethod($item)
	{
		return (int) BackendModel::getContainer()->get('database')->insert('slideshows_datasets', $item);
	}

	/**
	 * @param string $item
	 * @return int
	 */
	private static function insertImage($item)
	{
		return (int) BackendModel::getContainer()->get('database')->insert('slideshows_images', $item);
	}

	/**
	 * @param array $item The record to save.
	 * @return int
	 */
	public static function save(array $item)
	{
		if(isset($item['id']) && self::exists($item['id']))
		{
			self::update($item);
		}
		else
		{
			$item['id'] = self::insert($item);
		}

		BackendModel::invalidateFrontendCache('slideshowCache');
		return (int) $item['id'];
	}

	/**
	 * @param array $item
	 * @return int
	 */
	public static function saveImage(array $item)
	{
		if(isset($item['id']) && self::existsImage($item['id']))
		{
			self::updateImage($item);
		}
		else
		{
			$item['id'] = self::insertImage($item);
		}

		BackendModel::invalidateFrontendCache('slideshowCache');
		return (int) $item['id'];
	}

	/**
	 * Remark: $slideshow['id'] should be available.
	 *
	 * @param array $item
	 * @return int
	 */
	private static function update(array $item)
	{
		$db = BackendModel::getContainer()->get('database');
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
	 * @param array $item
	 * @return int
	 */
	public static function updateDataSetMethod(array $item)
	{
		BackendModel::getContainer()->get('database')->update(
			'slideshows_datasets',
			$item,
			'id = ?',
			array($item['id'])
		);

		BackendModel::invalidateFrontendCache('slideshowCache');
		return (int) $item['id'];
	}

	/**
	 * @param array $item
	 * @return int
	 */
	private static function updateImage(array $item)
	{
		BackendModel::invalidateFrontendCache('slideshowCache');
		return (int) BackendModel::getContainer()->get('database')->update(
			'slideshows_images',
			$item,
			'id = ?',
			array($item['id'])
		);
	}
}
