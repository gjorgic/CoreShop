<?php

namespace CoreShop\Test\Models;

use CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleActionType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleConditionType;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;

class Carrier extends RuleTest
{
    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistryName()
    {
        return 'coreshop.form_registry.shipping_rule.conditions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionValidatorName()
    {
        return 'coreshop.shipping_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return ShippingRuleConditionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormRegistryName()
    {
        return 'coreshop.form_registry.shipping_rule.actions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionProcessorName()
    {
        return 'coreshop.shipping_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return ShippingRuleActionType::class;
    }

    /**
     * @return CarrierPriceCalculatorInterface
     */
    protected function getPriceCalculator()
    {
        return $this->get('coreshop.carrier.price_calculator.shipping_rules');
    }

    /**
     * @return ShippingRuleInterface
     */
    protected function createRule()
    {
        /**
         * @var $shippingRule ShippingRuleInterface
         */
        $shippingRule = $this->getFactory('shipping_rule')->createNew();
        $shippingRule->setName('test-rule');

        return $shippingRule;
    }

    /**
     * Test Carrier Creation
     */
    public function testCarrierCreation()
    {
        $this->printTodoTestName();

        $carrier = $this->createResourceWithForm('carrier', CarrierInterface::class, [
            'label' => 'Test',
            'name' => 'Test',
            'rangeBehaviour' => 'deactivate'
        ]);

        $this->assertNull($carrier->getId());

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        $this->assertNotNull($carrier->getId());
    }

    /**
     * Test Carrier Price
     */
    public function testCarrierPrice()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var $carrier CarrierInterface
         */
        $carrier = $this->createResourceWithForm('carrier', CarrierInterface::class, [
            'label' => 'Test',
            'name' => 'Test',
            'rangeBehaviour' => 'deactivate',
            'taxRule' => Data::$taxRuleGroup->getId()
        ]);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        /**
         * @var $shippingRule ShippingRuleInterface
         */
        $shippingRule = $this->createResourceWithForm('shipping_rule', ShippingRuleInterface::class, [
            'name' => 'test->true'
        ]);
        $shippingRule->addAction($this->createActionWithForm('price', [
            'price' => 10
        ]));

        $this->getEntityManager()->persist($shippingRule);
        $this->getEntityManager()->flush();

        /**
         * @var $shippingRuleGroup ShippingRuleGroupInterface
         */
        $shippingRuleGroup = $this->createResourceWithForm('shipping_rule_group', ShippingRuleGroupInterface::class, [
            'carrier' => $carrier->getId(),
            'priority' => 1,
            'shippingRule' => $shippingRule->getId()
        ]);

        $this->getEntityManager()->persist($shippingRuleGroup);
        $this->getEntityManager()->flush();

        $carrier->addShippingRule($shippingRuleGroup);

        $price = $this->getPriceCalculator()->getPrice($carrier, $cart, Data::$customer1->getAddresses()[0], false);
        $priceWithTax = $this->getPriceCalculator()->getPrice($carrier, $cart, Data::$customer1->getAddresses()[0], true);

        $this->assertEquals(10, $price);
        $this->assertEquals(12, $priceWithTax);
    }

    /**
     * Test Carrier Taxes
     */
    public function testCarrierTax()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCartWithProducts();

        $tax = Data::$carrier2->getTaxAmount($cart);

        $this->assertEquals(4, $tax);*/
        //TODO
    }

    /**
     * Test Carrier Carts
     */
    public function testCarriersForCart()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCartWithProducts();
        $carriersForCart = \CoreShop\Model\Carrier::getCarriersForCart($cart);

        $this->assertEquals(1, count($carriersForCart));
        $this->assertEquals(Data::$carrier2->getId(), $carriersForCart[0]->getId());*/
        //TODO
    }
}
