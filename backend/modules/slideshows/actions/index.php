<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action, it will display the overview of slideshows
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendSlideshowsIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the datagrids
	 */
	protected function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(BackendSlideshowsModel::QRY_DATAGRID_BROWSE, BL::getWorkingLanguage());
		$this->dataGrid->setSortingColumns(array('name'), 'name');
		$this->dataGrid->setSortParameter('desc');
		$this->dataGrid->addColumn('images', null, BL::lbl('Images'));
		$this->dataGrid->setColumnFunction(array(__CLASS__, 'setImagesLink'), array('[module]', '[id]'), 'images');
		$this->dataGrid->setColumnAttributes('images', array('style' => 'width: 1%;'));
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
		$this->dataGrid->setColumnAttributes('name', array('data-id' => '{id:[id]}'));
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}

	/**
	 * Datagrid method, sets a link to the images overview for the slideshow if
	 * a module was not specified
	 *
	 * @param string $module The module string (which shouldn't be an empty one).
	 * @param int $slideshowID The slideshow ID used in the URL parameters.
	 * @return string
	 */
	public static function setImagesLink($module, $slideshowID)
	{
		if($module == '')
		{
			$imagesLink = BackendModel::createURLForAction('images') . '&slideshow_id=' . $slideshowID;

			return '<a class="button icon iconEdit linkButton" href="' . $imagesLink . '">
						<span>' . BL::lbl('ManageImages') . '</span>
					</a>';
		}
	}
}
