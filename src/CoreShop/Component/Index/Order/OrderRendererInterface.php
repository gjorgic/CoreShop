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

namespace CoreShop\Component\Index\Order;

use CoreShop\Component\Index\Worker\WorkerInterface;

interface OrderRendererInterface
{
    /**
     * Renders the condition.
     *
     *
     * @return mixed
     */
    public function render(WorkerInterface $worker, OrderInterface $condition, string $prefix = null);
}
