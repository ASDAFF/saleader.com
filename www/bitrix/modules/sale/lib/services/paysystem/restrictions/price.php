<?php

namespace Bitrix\Sale\Services\PaySystem\Restrictions;

use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Services\Base;
use Bitrix\Sale\Payment;

Loc::loadMessages(__FILE__);

class Price extends Base\Restriction
{
	/**
	 * @param $entityParams
	 * @param array $params
	 * @param int $paymentId
	 * @return bool
	 */
	protected static function check($entityParams, array $params, $paymentId = 0)
	{
		if ($entityParams['PRICE_PAYMENT'] === null)
			return true;

		$maxValue = static::getPrice($entityParams, $params['MAX_VALUE']);
		$minValue = static::getPrice($entityParams, $params['MIN_VALUE']);
		$price = (float)$entityParams['PRICE_PAYMENT'];

		if ($maxValue > 0 && $minValue > 0)
			return ($maxValue >= $price) && ($minValue <= $price);

		if ($maxValue > 0)
			return $maxValue >= $price;

		if ($minValue > 0)
			return $minValue <= $price;

		return false;
	}

	/**
	 * @param Payment $entity
	 * @return array
	 */
	protected static function extractParams(Payment $entity)
	{
		return array(
			'PRICE_PAYMENT' => $entity->getField('SUM')
		);
	}

	/**
	 * @param $entityParams
	 * @param $paramValue
	 * @return float
	 */
	protected static function getPrice($entityParams, $paramValue)
	{
		return (float)$paramValue;
	}

	/**
	 * @return mixed
	 */
	public static function getClassTitle()
	{
		return Loc::getMessage('SALE_PS_RESTRICTIONS_BY_PRICE');
	}

	/**
	 * @return mixed
	 */
	public static function getClassDescription()
	{
		return Loc::getMessage('SALE_PS_RESTRICTIONS_BY_PRICE_DESC');
	}

	/**
	 * @param $paySystemId
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public static function getParamsStructure($paySystemId = 0)
	{
		return array(
			"MIN_VALUE" => array(
				'TYPE' => 'NUMBER',
				'DEFAULT' => 0,
				'LABEL' => Loc::getMessage("SALE_PS_RESTRICTIONS_BY_PRICE_TYPE_MORE")
			),
			"MAX_VALUE" => array(
				'TYPE' => 'NUMBER',
				'DEFAULT' => 0,
				'LABEL' => Loc::getMessage("SALE_PS_RESTRICTIONS_BY_PRICE_TYPE_LESS")
			)
		);
	}

	/**
	 * @param Payment $payment
	 * @param $params
	 * @return array
	 * @throws ArgumentTypeException
	 */
	public static function getRange(Payment $payment, $params)
	{
		if ($payment instanceof Payment)
		{
			$p = static::extractParams($payment);
			return array(
				'MAX' => static::getPrice($p, $params['MAX_VALUE']),
				'MIN' => static::getPrice($p, $params['MIN_VALUE']),
			);
		}

		throw new ArgumentTypeException('');
	}

	/**
	 * @param $mode
	 * @return int
	 */
	public static function getSeverity($mode)
	{
		return Manager::SEVERITY_SOFT;
	}
}