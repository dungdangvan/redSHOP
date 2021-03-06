<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$redTemplate = Redtemplate::getInstance();
$app         = JFactory::getApplication();

$Itemid  = $app->input->getInt('Itemid');
$return  = $app->input->getString('return');
$session = JFactory::getSession();
$cart    = $session->get('cart');

$detail     = $this->detail;
$user       = JFactory::getUser();

$quotation_template = RedshopHelperTemplate::getTemplate("quotation_request");

if (count($quotation_template) > 0 && $quotation_template[0]->template_desc != "")
{
	$templateDesc = $quotation_template[0]->template_desc;
}
else
{
	$templateDesc = "<fieldset class=\"adminform\"><legend>{order_detail_lbl}</legend> \r\n<table class=\"admintable\">\r\n<tbody>\r\n<tr>\r\n<td>{product_name_lbl}</td>\r\n<td>{quantity_lbl}</td>\r\n</tr>\r\n{product_loop_start}\r\n<tr>\r\n<td>{product_name}<br />{product_attribute}<br />{product_accessory}<br />{product_userfields}</td>\r\n<td>{update_cart}</td>\r\n</tr>\r\n{product_loop_end}\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n<p>{customer_note_lbl}:{customer_note}</p>\r\n<fieldset class=\"adminform\"><legend>{billing_address_information_lbl}</legend> {billing_address}{quotation_custom_field_list} </fieldset> \r\n<table border=\"0\">\r\n<tbody>\r\n<tr>\r\n<td align=\"center\">{cancel_btn}{request_quotation_btn}</td>\r\n</tr>\r\n</tbody>\r\n</table>";
}?>
<script type="text/javascript">
	function validateInfo() {
		var frm = document.adminForm;

		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

		if (frm.user_email.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_EMAIL_ADDRESS')?>");
			return false;
		}

		var email = frm.user_email.value;

		if (reg.test(email) == false) {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_VALID_EMAIL_ADDRESS')?>");
			return false;
		}

		if (validateExtrafield(frm) == false) {
			return false;
		}
		return true;
	}
</script>
<?php

if (strstr($templateDesc, "{product_loop_start}") && strstr($templateDesc, "{product_loop_end}"))
{
	$template_sdata  = explode('{product_loop_start}', $templateDesc);
	$template_start  = $template_sdata[0];
	$template_edata  = explode('{product_loop_end}', $template_sdata[1]);
	$template_end    = $template_edata[1];
	$template_middle = $template_edata[0];

	$template_middle = RedshopHelperCartTag::replaceCartItem($template_middle, $cart, 0, Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE'));
	$templateDesc   = $template_start . $template_middle . $template_end;
}

$templateDesc = Redshop\Cart\Render\Label::replace($templateDesc);

if ($user->id)
{
	$templateDesc = RedshopHelperBillingTag::replaceBillingAddress($templateDesc, $detail);
	$templateDesc .= '<input type="hidden" name="user_email" id="user_email" value="' . $detail->user_email . '"/>';
}
else
{
	$billing = '<div class="redshop-billingaddresses">';

	$emailField = new stdClass;
	$emailField->title = JText::_('COM_REDSHOP_EMAIL');
	$emailField->desc = '';
	$emailField->required = '';

	$inputField = '<input type="text" name="user_email" id="user_email" value=""/>';

	$billing .= RedshopLayoutHelper::render(
					'fields.html',
					array(
						'fieldHandle' => $emailField,
						'inputField'  => $inputField
					)
				);

	if (strstr($templateDesc, "{quotation_custom_field_list}"))
	{
		$billing      .= Redshop\Fields\SiteHelper::renderFields(RedshopHelperExtrafields::SECTION_QUOTATION, $detail->user_info_id, "tbl");
		$templateDesc = str_replace("{quotation_custom_field_list}", "", $templateDesc);
	}
	else
	{
		$templateDesc = RedshopHelperExtrafields::listAllField(RedshopHelperExtrafields::SECTION_QUOTATION, $detail->user_info_id, "", $templateDesc);
	}

	$billing .= '</div>';

	$templateDesc = str_replace("{billing_address_information_lbl}", JText::_('COM_REDSHOP_BILLING_ADDRESS_INFORMATION_LBL'), $templateDesc);
	$templateDesc = str_replace("{billing_address}", $billing, $templateDesc);
}

$cancel_btn = '<input type="submit" class="greenbutton btn btn-primary" name="cancel" value="' . JText::_("COM_REDSHOP_CANCEL") . '" onclick="javascript:document.adminForm.task.value=\'cancel\';"/>';
$quotation_btn = '<input type="submit" class="greenbutton btn btn-primary" name="addquotation" value="' . JText::_("COM_REDSHOP_REQUEST_QUOTATION") . '" onclick="return validateInfo();"/>';
$quotation_btn .= '<input type="hidden" name="option" value="com_redshop" />';
$quotation_btn .= '<input type="hidden" name="Itemid" value="' . $Itemid . '" />';
$quotation_btn .= '<input type="hidden" name="task" value="addquotation" />';
$quotation_btn .= '<input type="hidden" name="view" value="quotation" />';
$quotation_btn .= '<input type="hidden" name="return" value="' . $return . '" />';

$templateDesc = str_replace("{cancel_btn}", $cancel_btn, $templateDesc);
$templateDesc = str_replace("{request_quotation_btn}", $quotation_btn, $templateDesc);

$templateDesc = str_replace("{order_detail_lbl}", JText::_('COM_REDSHOP_ORDER_DETAIL_LBL'), $templateDesc);
$templateDesc = str_replace("{customer_note_lbl}", JText::_('COM_REDSHOP_CUSTOMER_NOTE_LBL'), $templateDesc);
$templateDesc = str_replace("{customer_note}", '<textarea name="quotation_note" id="quotation_note"></textarea>', $templateDesc);

$templateDesc = '<form action="' . JRoute::_($this->request_url) . '" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">' . $templateDesc . '</form>';

echo eval("?>" . $templateDesc . "<?php ");?>
