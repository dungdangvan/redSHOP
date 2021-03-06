<?php

/**
 */
class ShopperGroupCest
{
	public function __construct()
	{
		$this->faker = Faker\Factory::create();

		$this->shopperName = $this->faker->bothify(' Testing shopper ##??');

		$this->shopperNameSaveClose = $this->faker->bothify(' Name Close ##??');

		$this->shopperType = 'Default Private';

		$this->categoryName = 'Testing Category ' . $this->faker->randomNumber();

		$this->customerType = 'Company customer';

		$this->shippingRate = $this->faker->numberBetween(1, 100);

		$this->shippingCheckout = $this->faker->numberBetween(1, 100);

		$this->catalog = 'Yes';

		$this->showPrice = 'Yes';

		$this->nameShopperEdit = $this->shopperType . 'edit';

		$this->idShopperChange = '1';

		$this->shipping = 'no';

		$this->enableQuotation = 'yes';

		$this->showVat = 'no';

		$this->shopperGroupPortal = 'no';


	}

    /**
     * @param AcceptanceTester $I
     */
	public function _before(AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
	}

	/**
	 * @param AcceptanceTester $I
	 * @param                  $scenario
	 * @throws Exception
	 */
	public function createCategory(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Test Category Save creation in Administrator');
		$I = new AcceptanceTester\CategoryManagerJoomla3Steps($scenario);
		$I->wantTo('Create a Category Save button');
		$I->addCategorySave($this->categoryName);

	}

	/**
	 *
	 * Function create new Shopper groups
	 *
	 * @param AcceptanceTester $I
	 * @param $scenario
	 *
	 * @depends createCategory
	 *
	 * @throws Exception
	 */
	public function createShopperGroup(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Test Shopper Group Save creation in Administrator');
		$I = new AcceptanceTester\ShopperGroupManagerJoomla3Steps($scenario);
		$I->wantTo('Create Shopper Group Save button');
		$I->addShopperGroups($this->shopperName, $this->shopperType, $this->customerType, $this->shopperGroupPortal,$this->categoryName, $this->shipping,$this->shippingRate, $this->shippingCheckout, $this->catalog,$this->showVat, $this->showPrice, $this->enableQuotation,'save');
		$I->addShopperGroups($this->shopperNameSaveClose, $this->shopperType, $this->customerType, $this->shopperGroupPortal,$this->categoryName,$this->shipping, $this->shippingRate, $this->shippingCheckout, $this->catalog, $this->showVat,$this->showPrice,$this->enableQuotation, 'saveclose');
	}

	/**
	 * Function change status os shopper groups is unpublish
	 * Change Shopper groups have name is Default Private
	 *
	 * @param AcceptanceTester $I
	 * @param $scenario
	 *
	 */
	public function changeStateShopperGroup(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Test Unpublish Shopper groups');
		$I = new AcceptanceTester\ShopperGroupManagerJoomla3Steps($scenario);
		$I->wantTo('Test Unpublish Shopper groups');
		$I->changeStateShopperGroup('unpublished');
		$I->changeStateShopperGroup('published');
	}

	/**
	 * Function edit name of shopper groups
	 *
	 * @param AcceptanceTester $I
	 * @param $scenario
	 *
	 * @depends changeStateShopperGroup
	 */
	public function editNameShopper(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Edit Name of Shopper groups');
		$I = new AcceptanceTester\ShopperGroupManagerJoomla3Steps($scenario);
		$I->wantTo('Edit Name of Shopper groups');
		$I->editShopperGroups($this->shopperType, $this->idShopperChange, $this->nameShopperEdit);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param $scenario
	 */
	public function deleteShopperGroupsNo(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Check delete Shopper groups');
		$I = new AcceptanceTester\ShopperGroupManagerJoomla3Steps($scenario);
		$I->wantTo('Edit Name of Shopper groups');
		$I->deleteShopperGroupsNo();
	}

	public function changeStatusAllShopperGroups(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Publish all  Shopper groups');
		$I = new AcceptanceTester\ShopperGroupManagerJoomla3Steps($scenario);
		$I->changStatusAllShopperGroups('unpublished');
		$I->changStatusAllShopperGroups('published');
	}

	public function checkButtons(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Test to validate different buttons on Gift Card Views');
		$I = new AcceptanceTester\ShopperGroupManagerJoomla3Steps($scenario);
		$I->checkButtons('edit');
		$I->checkButtons('cancel');
		$I->checkButtons('publish');
		$I->checkButtons('unpublish');
		$I->checkButtons('Delete');
	}

	public function addShopperGroupsMissingName(AcceptanceTester $I, $scenario)
	{
		$I->wantTo('Check delete Shopper groups');
		$I = new AcceptanceTester\ShopperGroupManagerJoomla3Steps($scenario);
		$I->wantTo('Edit Name of Shopper groups');
		$I->addShopperGroupsMissingName();
	}
}