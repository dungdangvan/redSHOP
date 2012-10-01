<?php
/**
 * @package     redSHOP
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die ('Restricted access');

require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redshop' . DS . 'helpers' . DS . 'xmlhelper.php');
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'core' . DS . 'controller' . DS . 'detail.php';

class xmlexport_detailController extends RedshopCoreControllerDetail
{
    public $redirectViewName = 'xmlexport';

    public function __construct($default = array())
    {
        parent::__construct($default);
        $this->registerTask('add', 'edit');
    }

    public function xmlexport()
    {
        $this->save(1);
    }

    public function save($export = 0)
    {
        $session = JFactory::getSession();

        $post   = $this->input->getArray($_POST);
        $option = $this->input->getString('option', '');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        $post['xmlexport_id'] = $cid [0];
        $model                = $this->getModel('xmlexport_detail');

        if ($post['xmlexport_id'] == 0)
        {
            $post['xmlexport_date'] = time();
        }

        $childelement = $session->get('childelement');

        if (isset($childelement['orderdetail']))
        {
            $post['element_name']      = ($childelement['orderdetail'][0]) ? $childelement['orderdetail'][0] : "orderdetail";
            $post['xmlexport_filetag'] = $childelement['orderdetail'][1];
        }
        elseif (isset($childelement['productdetail']))
        {
            $post['element_name']      = ($childelement['productdetail'][0]) ? $childelement['productdetail'][0] : "productdetail";
            $post['xmlexport_filetag'] = $childelement['productdetail'][1];
        }

        if (isset($childelement['billingdetail']))
        {
            $post['billing_element_name'] = ($childelement['billingdetail'][0]) ? $childelement['billingdetail'][0] : "billingdetail";
            $post['xmlexport_billingtag'] = $childelement['billingdetail'][1];
        }
        if (isset($childelement['shippingdetail']))
        {
            $post['shipping_element_name'] = ($childelement['shippingdetail'][0]) ? $childelement['shippingdetail'][0] : "shippingdetail";
            $post['xmlexport_shippingtag'] = $childelement['shippingdetail'][1];
        }
        if (isset($childelement['orderitem']))
        {
            $post['orderitem_element_name'] = ($childelement['orderitem'][0]) ? $childelement['orderitem'][0] : "orderitem";
            $post['xmlexport_orderitemtag'] = $childelement['orderitem'][1];
        }
        if (isset($childelement['stockdetail']))
        {
            $post['stock_element_name'] = ($childelement['stockdetail'][0]) ? $childelement['stockdetail'][0] : "stockdetail";
            $post['xmlexport_stocktag'] = $childelement['stockdetail'][1];
        }
        if (isset($childelement['prdextrafield']))
        {
            $post['prdextrafield_element_name'] = ($childelement['prdextrafield'][0]) ? $childelement['prdextrafield'][0] : "prdextrafield";
            $post['xmlexport_prdextrafieldtag'] = $childelement['prdextrafield'][1];
        }

        $row = $model->store($post, $export);
        if ($row)
        {
            if ($export == 1)
            {
                $msg = JText::_('COM_REDSHOP_XMLEXPORT_FILE_SUCCESSFULLY_SYNCHRONIZED');
            }
            else
            {
                $msg = JText::_('COM_REDSHOP_XMLEXPORT_DETAIL_SAVED');
            }
        }
        else
        {
            if ($export == 1)
            {
                $msg = JText::_('COM_REDSHOP_ERROR_XMLEXPORT_FILE_SYNCHRONIZED');
            }
            else
            {
                $msg = JText::_('COM_REDSHOP_ERROR_SAVING_XMLEXPORT_DETAIL');
            }
        }

        $session->set('childelement', NULL);
        $this->setRedirect('index.php?option=' . $option . '&view=xmlexport', $msg);
    }

    public function setChildElement()
    {
        JHTMLBehavior::modal();

        $xmlhelper    = new xmlHelper();
        $post         = $this->input->getArray($_POST);
        $session      = JFactory::getSession();
        $childelement = $session->get('childelement');

        $model = $this->getModel('xmlexport_detail');

        $resarray = array();
        $uarray   = array();
        $columns  = $xmlhelper->getSectionColumnList($post['section_type'], $post['parentsection']);

        for ($i = 0; $i < count($columns); $i++)
        {
            if (trim($post[$columns[$i]->Field]) != "")
            {
                $xmltag     = str_replace(" ", "_", strtolower(trim($post[$columns[$i]->Field])));
                $uarray[]   = $xmltag;
                $resarray[] = $columns[$i]->Field . "=" . $xmltag;
            }
        }
        $firstlen = count($uarray);
        $uarray1  = array_unique($uarray);
        sort($uarray1);
        $seclen = count($uarray1);

        if ($seclen != $firstlen)
        {
            echo $msg = JText::_('COM_REDSHOP_DUPLICATE_FIELDNAME');
            return;
        }

        $childelement[$post['parentsection']] = array($post['element_name'], implode(";", $resarray));
        $session->set('childelement', $childelement);    ?>
    <script language="javascript">
        window.parent.SqueezeBox.close();
    </script>
    <?php
    }

    public function removeIpAddress()
    {
        $xmlexport_ip_id = $this->input->get('xmlexport_ip_id', 0);

        $model = $this->getModel('xmlexport_detail');
        $model->deleteIpAddress($xmlexport_ip_id);
        die();
    }

    public function cancel()
    {
        $option  = $this->input->getString('option', '');
        $session = JFactory::getSession();
        $session->set('childelement', NULL);
        $msg = JText::_('COM_REDSHOP_XMLEXPORT_DETAIL_EDITING_CANCELLED');
        $this->setRedirect('index.php?option=' . $option . '&view=xmlexport', $msg);
    }

    /**
     * logic for auto synchronize
     *
     * @access public
     * @return void
     */
    public function auto_syncpublish()
    {
        $option = $this->input->get('option');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            throw new RuntimeException(JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_AUTO_SYNCHRONIZE'));
        }
        $model = $this->getModel('xmlexport_detail');
        if (!$model->auto_syncpublish($cid, 1))
        {
            echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
        }
        $msg = JText::_('COM_REDSHOP_AUTO_SYNCHRONIZE_ENABLE_SUCCESSFULLY');
        $this->setRedirect('index.php?option=' . $option . '&view=xmlexport', $msg);
    }

    /**
     * logic for disable auto sync
     *
     * @access public
     * @return void
     */
    public function auto_syncunpublish()
    {
        $option = $this->input->get('option');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            throw new RuntimeException(JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_AUTO_SYNCHRONIZE'));
        }
        $model = $this->getModel('xmlexport_detail');
        if (!$model->auto_syncpublish($cid, 0))
        {
            echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
        }
        $msg = JText::_('COM_REDSHOP_AUTO_SYNCHRONIZE_DISABLE_SUCCESSFULLY');
        $this->setRedirect('index.php?option=' . $option . '&view=xmlexport', $msg);
    }

    /**
     * logic for use to all user
     *
     * @access public
     * @return void
     */
    public function usetoallpublish()
    {
        $option = $this->input->get('option');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            throw new RuntimeException(JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_USE_EXPORTFILE_TO_ALL'));
        }
        $model = $this->getModel('xmlexport_detail');
        if (!$model->usetoallpublish($cid, 1))
        {
            echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
        }
        $msg = JText::_('COM_REDSHOP_EXPORTFILE_USE_TO_ALL_ENABLE_SUCCESSFULLY');
        $this->setRedirect('index.php?option=' . $option . '&view=xmlexport', $msg);
    }

    /**
     * logic for disable use to all user
     *
     * @access public
     * @return void
     */
    public function usetoallunpublish()
    {
        $option = $this->input->get('option');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            throw new RuntimeException(JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_USE_EXPORTFILE_TO_ALL'));
        }
        $model = $this->getModel('xmlexport_detail');
        if (!$model->usetoallpublish($cid, 0))
        {
            echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
        }
        $msg = JText::_('COM_REDSHOP_EXPORTFILE_USE_TO_ALL_DISABLE_SUCCESSFULLY');
        $this->setRedirect('index.php?option=' . $option . '&view=xmlexport', $msg);
    }
}

