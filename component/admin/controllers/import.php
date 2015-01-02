<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


class RedshopControllerImport extends RedshopController
{
	public function cancel()
	{
		$this->setRedirect('index.php');
	}

	public function importdata()
	{
		ob_clean();
		$model = $this->getModel('import');
		$model->importdata();
	}
}


