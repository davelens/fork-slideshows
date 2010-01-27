<?php

/**
 * BackendLocaleIndex
 *
 * This is the index-action, it will display the overview of language labels
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendLocaleIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrids
	 *
	 * @return void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendLocaleModel::QRY_DATAGRID_BROWSE, BL::getWorkingLanguage());

		// header labels
//		$this->datagrid->setHeaderLabels(array('name' => ucfirst(BL::getLabel('Category')), 'num_posts' => ucfirst(BL::getLabel('PostsInThisCategory'))));

		// sorting columns
//		$this->datagrid->setSortingColumns(array('name', 'num_posts'), 'name');

		// add column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]', BL::getLabel('Edit'));
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>