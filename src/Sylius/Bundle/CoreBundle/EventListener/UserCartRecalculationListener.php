<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserCartRecalculationListener
{
    /**
     * @var CartProviderInterface
     */
    private $cartProvider;

    /**
     * @var OrderRecalculatorInterface
     */
    private $orderRecalculator;

    /**
     * @param CartProviderInterface $cartProvider
     * @param OrderRecalculatorInterface $orderRecalculator
     */
    public function __construct(CartProviderInterface $cartProvider, OrderRecalculatorInterface $orderRecalculator)
    {
        $this->cartProvider = $cartProvider;
        $this->orderRecalculator = $orderRecalculator;
    }

    /**
     * @param UserEvent $event
     */
    public function recalculateCartWhileLogin(UserEvent $event)
    {
        $this->recalculateCart();
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function recalculateCartWhileInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->recalculateCart();
    }

    private function recalculateCart()
    {
        if (!$this->cartProvider->hasCart()) {
            return;
        }

        $cart = $this->cartProvider->getCart();

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        $this->orderRecalculator->recalculate($cart);
    }
}
