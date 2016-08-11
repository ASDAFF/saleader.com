<?
namespace Bitrix\Sale\Delivery\Pecom;

use Bitrix\Main\Error;
use Bitrix\Sale\Result;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Delivery\ExternalLocationMap;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sale/delivery/delivery_pecom_loc.php');

class Location extends ExternalLocationMap
{
	const EXTERNAL_SERVICE_CODE = 'PECOM';
	const CSV_FILE_PATH = '/bitrix/modules/sale/ru/delivery/pecom/location.csv';

	/**
	 * @return Result
	 */
	protected static function getAllLocations()
	{
		$result = new Result();
		$http = new \Bitrix\Main\Web\HttpClient(array(
			"version" => "1.1",
			"socketTimeout" => 30,
			"streamTimeout" => 30,
			"redirect" => true,
			"redirectMax" => 5,
		));

		$jsnData = $http->get("http://www.pecom.ru/ru/calc/towns.php");
		$errors = $http->getError();

		if (!$jsnData && !empty($errors))
		{
			foreach($errors as $errorCode => $errMes)
				$result->addError(new Error($errMes, $errorCode));

			return $result;
		}

		$data = json_decode($jsnData, true);

		if(is_array($data))
		{
			// City MOSKVA  Region MOSKVA
			$cityRegionSame = array();
			//City name contains (temeryazevskiy r-n)
			$precised = array();
			$emptyRegions = array();
			$other = array();

			foreach($data as $regionName => $city)
			{
				foreach($city as $cityId => $cityName)
				{
					if(strtolower(SITE_CHARSET) != 'utf-8')
					{
						$cityName = trim(static::utfDecode($cityName));
						$regionName = trim(static::utfDecode($regionName));
					}

					if(strpos($cityName, '(') !== false && strpos($cityName, ')') !== false)
						$precised[] = array($cityName, $regionName, $cityId);
					elseif($cityName == $regionName)
						$cityRegionSame[] = array($cityName, $regionName, $cityId);
					elseif(strlen($regionName) <= 0 || $regionName == '-' || $regionName == '--')
						$emptyRegions = array($cityName, $regionName, $cityId);
					else
						$other[] = array($cityName, $regionName, $cityId);
				}
			}

			$result->addData(
				array_merge(
					$precised,
					$other,
					$cityRegionSame,
					$emptyRegions
				)
			);
		}
		else
		{
			$result->addError(new Error("Can decode pecom cities data!"));
		}

		return $result;
	}

	/**
	 * SOKOLOVSKOE (GULKELIVICHSKIY R-N) => GULKELIVICHSKIY
	 * @param $cityNameUpper
	 * @return string
	 */
	protected static function extractDistrict($cityNameUpper)
	{
		$result = '';
		$matches = array();

		if(preg_match('/\s*\((.*)\)/i'.BX_UTF_PCRE_MODIFIER, $cityNameUpper, $matches))
		{
			if(isset($matches[1]))
			{
				$result = str_replace(
					array(
						' '.Loc::getMessage('SALE_DH_PECOM_LOC_DIST_1'),
						Loc::getMessage('SALE_DH_PECOM_LOC_DIST_2').'. '
					),
					'',
					trim($matches[1])
				);
			}
		}

		return trim($result);
	}

	/**
	 * ZELENGA S. => ZELENGA
	 * @param $cityNameUpper
	 * @return string
	 */
	protected static function prepareCityName($cityNameUpper)
	{
		$result = preg_replace('/\s*\(.*\)/i'.BX_UTF_PCRE_MODIFIER, '', $cityNameUpper);
		$replace = array();

		for($i = 0; $i < 8; $i++)
			$replace[] = Loc::getMessage('SALE_DH_PECOM_LOC_CN_'.strval($i));

		$result = str_replace($replace, '', $result );
		$result = str_replace(array('.', ','), '', $result);

		return trim($result);
	}

	/**
	 * @param string $cityNameUpper
	 * @return array Location with parents.
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected static function getLocationsFromDb($cityNameUpper)
	{
		if(empty($cityNameUpper))
			return array();

		$res = LocationTable::getList(array(
			'filter' => array(
				'=NAME.NAME_UPPER' => $cityNameUpper,
				'=NAME.LANGUAGE_ID' => LANGUAGE_ID,
				'=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID
			),
			'select' => array(
				'ID',
				'PID' => 'PARENTS.ID',
				'NAME_UPPER' => 'NAME.NAME_UPPER',
				'PARENTS_TYPE_CODE' => 'PARENTS.TYPE.CODE' ,
				'PARENTS_NAME_UPPER' => 'PARENTS.NAME.NAME_UPPER'
			)
		));

		return $res->fetchAll();
	}

	/**
	 * Check if where are shared parents between region and city parents chains
	 * @param $regionNameUpper
	 * @param array $parentIds
	 * @return array
	 */
	protected static function extractLocationIdByParentsChain($regionNameUpper, array $parentIds)
	{
		$result = array();

		if(strlen($regionNameUpper) <= 0 || empty($parentIds))
			return array();

		//Region parents chain
		$regLocatons = static::getLocationsFromDb($regionNameUpper);
		$rpids = array();
		$npids = array();
		$hasSameParent = array();

		foreach($regLocatons as $locaton)
		{
			if(!isset($npids[$locaton['ID']]))
				$npids[$locaton['ID']] = $parentIds;

			if(!isset($rpids[$locaton['ID']]))
				$rpids[$locaton['ID']] = array();

			if(!isset($hasSameParent[$locaton['ID']]))
				$hasSameParent[$locaton['ID']] = array();

			foreach($npids[$locaton['ID']] as $locId => $chain)
			{
				if(!is_array($rpids[$locaton['ID']][$locId]))
					$rpids[$locaton['ID']][$locId] = array();

				if(in_array($locaton['PID'], $chain))
				{
					$hasSameParent[$locaton['ID']][$locId] = true;
					$key = array_search($locaton['PID'], $chain);
					unset($npids[$locaton['ID']][$locId][$key]);
				}
				else
				{
					if(!isset($hasSameParent[$locaton['ID']][$locId]))
						$hasSameParent[$locaton['ID']][$locId] = false;

					$rpids[$locaton['ID']][$locId][] = $locaton['PID'];
				}
			}
		}

		$winnerId = 0;
		$winnerDistance = 0;

		foreach($rpids as $pid => $lids)
		{
			foreach($lids as $locId => $chain)
			{
				if(!isset($npids[$pid][$locId]))
					continue;

				if(!$hasSameParent[$pid][$locId])
					continue;

				//unshared parents in chain
				$distance = count($chain) + count($npids[$pid][$locId]);

				//The shortest distance wins
				if($winnerDistance <= 0 || $winnerDistance > $distance)
				{
					$winnerDistance = $distance;
					$winnerId = $locId;
				}
			}
		}

		if(intval($winnerId) > 0 )
			$result[] = $winnerId;

		return $result;
	}

	/**
	 * @param array $locations
	 * @param $regionName
	 * @param $district
	 * @return array
	 */
	protected static function extractLocationsIds(array $locations, $regionName, $district)
	{
		if(empty($locations))
			return array();

		//locations ids
		$ids = array();
		//locations chains with parents
		$pids = array();
		static $extractedIds = array();

		foreach($locations as $loc)
		{
			$locId = $loc['ID'];

			if(in_array($locId, $extractedIds))
				continue;

			if(!in_array($loc['ID'], $ids))
				$ids[] = $locId;

			//DROGICHINSKIY RAYON => DROGICHINSKIY
			$parentsName = str_replace(
				array(
					' '.Loc::getMessage('SALE_DH_PECOM_LOC_PNU_1'),
					Loc::getMessage('SALE_DH_PECOM_LOC_PNU_2').' '
					),
				'',
				$loc['PARENTS_NAME_UPPER']
			);

			// KOSTROMSKAYA != OMSK
			if(
				(
					strlen($regionName) > 0
					&& 	strpos($parentsName, ToUpper($regionName)) === 0
				)
				|| (
						strlen($district) > 0
						&& strpos($parentsName, ToUpper($district)) === 0
				)
				||(
					 strlen($regionName) <= 0
					 && strlen($district) <= 0
					 && $loc['PARENTS_NAME_UPPER'] == $loc['NAME_UPPER']
				)
			)
			{
				$extractedIds[] = $locId;
				return array($locId);
			}

			if(!isset($pids[$locId]))
				$pids[$locId] = array();

			if(!in_array($loc['PID'], $pids[$locId]))
				$pids[$locId][] = $loc['PID'];
		}

		if(count($ids) != 1)
		{
			$pIds = static::extractLocationIdByParentsChain($regionName, $pids);

			if(count($ids) <= 0)
			{
				$ids = $pIds;
			}
			else  //(count($ids) > 1))
			{
				$itersect = array_intersect($ids, $pIds);

				if(count($itersect) == 1)
					$ids = $itersect;
			}
		}

		if(!empty($ids))
			if(!in_array(current($ids), $extractedIds))
				$extractedIds[] = current($ids);

		return $ids;
	}

	/**
	 * @param string $regionName
	 * @return string
	 */
	protected static function prepareRegionName($regionName)
	{
		if(strlen($regionName) <= 0 || trim($regionName == '-') || trim($regionName == '--'))
			return '';

		$result = ToUpper(str_replace('.', '', trim($regionName)));

		for($i=0; $i<4; $i++)
			if($result == Loc::getMessage('SALE_DH_PECOM_LOC_RN_'.strval($i)))
				$result = Loc::getMessage('SALE_DH_PECOM_LOC_RN_'.strval($i).'_R');

		return $result;
	}

	/**
	 * // BISKAZ => BISKAZ SELO, BISKAZ DEREVNYA, ...
	 * @param $cityNameUpper
	 * @return array
	 */
	protected static function getNamesArrayForDb($cityNameUpper)
	{
		$result  = array($cityNameUpper);

		for($i = 1; $i <= 19; $i++)
			$result[] = $cityNameUpper.' '.Loc::getMessage('SALE_DH_PECOM_LOC_GNA_'.strval($i));

		$unHyphenName = str_replace('-', ' ', $cityNameUpper);

		if($unHyphenName != $cityNameUpper)
			$result[] = $unHyphenName;

		return $result;
	}

	/**
	 * @param string $cityNameIn
	 * @param string $regionNameIn
	 * @param string $district
	 * @return int Location Id.
	 */
	protected static function getLocationIdByNames($cityNameIn, $regionNameIn, $district = '')
	{
		$result = 0;
		$cityNameUpper = ToUpper(trim($cityNameIn));
		$district = static::extractDistrict($cityNameUpper);
		$cityNameUpper = static::prepareCityName($cityNameUpper);

		if(strlen($cityNameUpper) <= 0)
			return array();

		$regionName = static::prepareRegionName($regionNameIn);

		$ids = static::extractLocationsIds(
			static::getLocationsFromDb(
				static::getNamesArrayForDb(
					$cityNameUpper
				)
			),
			$regionName,
			$district
		);

		if(count($ids) == 1)
		{
			$result = $ids[0];
		}
		elseif(count($ids) > 1)
		{
			reset($ids);
			$result = current($ids);
		}

		return $result;
	}
}