<?php
/**
 * @package     RedShop
 * @subpackage  Step Class
 * @copyright   Copyright (C) 2008 - 2018 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace AcceptanceTester;
/**
 * Class ProductManagerJoomla3Steps
 *
 * @package  AcceptanceTester
 *
 * @link     http://codeception.com/docs/07-AdvancedUsage#StepObjects
 *
 * @since    2.1
 */
class CheckoutProductQuantityChangeSteps extends AdminManagerJoomla3Steps
{
    public function checkoutChangeQuantity($category, $userName, $password)
    {
        $I = $this;
        $I->amOnPage(\CheckoutProductChangeQuantityPage::$url);
        $I->click(\CheckoutProductChangeQuantityPage::$fillUserName);
        $I->fillField(\CheckoutProductChangeQuantityPage::$fillUserName, $userName);
        $I->click(\CheckoutProductChangeQuantityPage::$fillPassWord);
        $I->fillField(\CheckoutProductChangeQuantityPage::$fillPassWord, $password);
        $I->click(\CheckoutProductChangeQuantityPage::$submitButton);
        $I->waitForElement(\CheckoutProductChangeQuantityPage::$categoryTitle, 30);
        $I->click($category);
        $I->click(\CheckoutProductChangeQuantityPage::$addToCart);
        $I->amOnPage(\CheckoutProductChangeQuantityPage::$cartPageUrL);
        $I->click(\CheckoutProductChangeQuantityPage::$quantityField);
        $I->pressKey(\CheckoutProductChangeQuantityPage::$quantityField, \Facebook\WebDriver\WebDriverKeys::BACKSPACE);
        $quantities = 10;
        $quantity = str_split($quantities);
        foreach ($quantity as $char) {
            $I->pressKey(\CheckoutProductChangeQuantityPage::$quantityField, $char);
        }
        $I->waitForElement(\CheckoutProductChangeQuantityPage::$updateCartButton, 30);
        $I->click(\CheckoutProductChangeQuantityPage::$updateCartButton);
        $I->click(\CheckoutProductChangeQuantityPage::$checkoutButton);
        $I->click(\CheckoutProductChangeQuantityPage::$bankTransfer);
        $I->waitForElement(\CheckoutProductChangeQuantityPage::$termAndConditions);
        $I->click(\CheckoutProductChangeQuantityPage::$termAndConditions);
        $I->click(\CheckoutProductChangeQuantityPage::$checkoutFinalStep);
    }
}
