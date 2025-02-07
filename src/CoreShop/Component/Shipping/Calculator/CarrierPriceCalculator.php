<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use CoreShop\Component\Shipping\Exception\NoShippingPriceFoundException;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

final class CarrierPriceCalculator implements CarrierPriceCalculatorInterface
{
    public function __construct(private PrioritizedServiceRegistryInterface $shippingCalculatorRegistry)
    {
    }

    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $context): int
    {
        $price = 0;

        /**
         * @var CarrierPriceCalculatorInterface $calculator
         */
        foreach ($this->shippingCalculatorRegistry->all() as $calculator) {
            try {
                $price = $calculator->getPrice($carrier, $shippable, $address, $context);
            } catch (NoShippingPriceFoundException) {
                continue;
            }
        }

        return $price;
    }
}
