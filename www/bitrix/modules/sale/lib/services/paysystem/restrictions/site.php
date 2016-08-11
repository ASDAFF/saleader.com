<?php
namespace Bitrix\Sale\Services\PaySystem\Restrictions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Delivery\Restrictions;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaymentCollection;

Loc::loadMessages(__FILE__);

/**
 * Class Site
 * @package Bitrix\Sale\Services\PaySystem\Restrictions
 */
class Site extends Restrictions\BySite
{
	/**
	 * @param Payment $payment
	 * @return null|string
	 */
	protected static function extractParams(Payment $payment)
	{
		/** @var PaymentCollection $collection */
		$collection = $payment->getCollection();

		/** @var Order $order */
		$order = $collection->getOrder();

		return $order->getSiteId();
	}
} 