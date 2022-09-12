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

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Repository\PaymentProviderRepositoryInterface as BasePaymentProviderRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface PaymentProviderRepositoryInterface extends BasePaymentProviderRepositoryInterface
{
    /**
     * @return PaymentProviderInterface[]
     */
    public function findActiveForStore(StoreInterface $store): array;
}
