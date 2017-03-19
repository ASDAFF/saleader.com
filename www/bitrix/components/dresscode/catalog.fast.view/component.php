<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
		die();

	if (CModule::IncludeModule("sale") &&
		CModule::IncludeModule("catalog") &&
		CModule::IncludeModule("iblock") &&
		CModule::IncludeModule("dw.deluxe") &&
		CModule::IncludeModule("highloadblock")){

		if(!empty($arParams["PRODUCT_ID"])){
			
			if (!isset($arParams["CACHE_TIME"])){
				$arParams["CACHE_TIME"] = 1285912;
			}

			if ($this->StartResultCache($arParams["CACHE_TIME"], $arParams["PRODUCT_ID"])){
				
				$arParams["~PRODUCT_ID"] = 0;
				$arProductSkuExist = CCatalogSku::GetProductInfo(intval($arParams["PRODUCT_ID"]));
				if (is_array($arProductSkuExist)){
					$arParams["~PRODUCT_ID"] = $arParams["PRODUCT_ID"];
					$arParams["PRODUCT_ID"] = $arProductSkuExist["ID"];
				}

				$arSelect = Array("ID", "IBLOCK_ID", "CATALOG_AVAILABLE", "*");
				$arFilter = Array("ID" => intval($arParams["PRODUCT_ID"]), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
				$rsProduct = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount" => 1), $arSelect);
				if($obProduct = $rsProduct->GetNextElement()){ 
					$arResult = $obProduct->GetFields();  
					$arResult["PROPERTIES"] = $obProduct->GetProperties();
				}

				// если у товара есть sku
				if(CCatalogSKU::IsExistOffers($arParams["PRODUCT_ID"])){
					
					//get sku by dw.deluxe module
					$arSkuProperties = DwSKU::getSkuPropertiesByIblockID($arResult["IBLOCK_ID"]);
					$arOffers = DwSKU::getSkuByProductID($arParams["PRODUCT_ID"], $arResult["IBLOCK_ID"], array(
							"HIDE_NOT_AVAILABLE" => "N",
							"OPTION_ADD_CART" => "N"
						)
					);

					$arActiveProperties = DwSKU::setSkuActiveProperties($arOffers["OFFERS"], $arSkuProperties, $arParams["~PRODUCT_ID"]);
					$arActiveOffer = DwSKU::setSkuActiveOffer($arOffers["OFFERS"], $arActiveProperties["CLEAN_PROPERTIES"], $arParams["~PRODUCT_ID"]);
					$arResult = array_merge($arResult, $arActiveProperties, $arActiveOffer);

					//set price by sku
					if(!empty($arActiveOffer["SKU_ACTIVE_OFFER"])){
						
						$arResult["~ID"] = $arResult["ID"];
						$arResult["ID"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["ID"];
						$arResult["NAME"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["NAME"];
						$arResult["PRICE"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["PRICE"];
						$arResult["COUNT_PRICES"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["COUNT_PRICES"];
						$arResult["CATALOG_AVAILABLE"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["CATALOG_AVAILABLE"];
						$arResult["CATALOG_QUANTITY"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["CATALOG_QUANTITY"];
						$arResult["CATALOG_MEASURE"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["CATALOG_MEASURE"];

						if(!empty($arActiveOffer["SKU_ACTIVE_OFFER"]["DETAIL_PICTURE"])){
							$arResult["DETAIL_PICTURE"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["DETAIL_PICTURE"];
						}

						if(!empty($arActiveOffer["SKU_ACTIVE_OFFER"]["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
							$arResult["PROPERTIES"]["MORE_PHOTO"] = $arActiveOffer["SKU_ACTIVE_OFFER"]["PROPERTIES"]["MORE_PHOTO"];
						}

						$arResult["SKU_INFO"] = CCatalogSKU::GetInfoByProductIBlock($arResult["IBLOCK_ID"]);

					}

				}else{
					
					$arResult["PRICE"] = CCatalogProduct::GetOptimalPrice($arResult["ID"], 1, $USER->GetUserGroupArray());
					$dbPrice = CPrice::GetList(
				        array(),
				        array("PRODUCT_ID" => $arResult["ID"], "CAN_ACCESS" => "Y"),
				        false,
				        false,
				        array("ID")
				    );
					$arResult["COUNT_PRICES"] = $dbPrice->SelectedRowsCount();

				}

				//комплекты
				$arResult["COMPLECT"] = array();
				$arComplectID = array();

				$rsComplect = CCatalogProductSet::getList(
					array("SORT" => "ASC"),
					array(
						"TYPE" => 1,
						"OWNER_ID" => $arResult["ID"],
						"!ITEM_ID" => $arResult["ID"]
					),
					false,
					false,
					array("*")
				);

				while ($arComplectItem = $rsComplect->Fetch()) {
					$arResult["COMPLECT"]["ITEMS"][$arComplectItem["ITEM_ID"]] = $arComplectItem;
					$arComplectID[$arComplectItem["ITEM_ID"]] = $arComplectItem["ITEM_ID"];
				}

				if(!empty($arComplectID)){

					$arResult["COMPLECT"]["RESULT_PRICE"] = 0;
					$arResult["COMPLECT"]["RESULT_BASE_DIFF"] = 0;
					$arResult["COMPLECT"]["RESULT_BASE_PRICE"] = 0;

					$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_MEASURE");
					$arFilter = Array("ID" => $arComplectID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
					$rsComplectProducts = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
					while($obComplectProducts = $rsComplectProducts->GetNextElement()){
						
						$complectProductFields = $obComplectProducts->GetFields();
						$complectProductFields["PRICE"] = CCatalogProduct::GetOptimalPrice($complectProductFields["ID"], 1, $USER->GetUserGroupArray());
						$complectProductFields["PRICE"]["DISCOUNT_PRICE"] = $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
						$complectProductFields["PRICE"]["DISCOUNT_PRICE"] -= $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["DISCOUNT_PERCENT"] / 100;
						$complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
						$complectProductFields["PRICE"]["PRICE_DIFF"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] - $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
						$complectProductFields["PRICE"]["BASE_PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);
						$complectProductFields["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
						$arResult["COMPLECT"]["RESULT_PRICE"] += $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
						$arResult["COMPLECT"]["RESULT_BASE_PRICE"] += $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"];
						$arResult["COMPLECT"]["RESULT_BASE_DIFF"] += $complectProductFields["PRICE"]["PRICE_DIFF"];

						$complectProductFields = array_merge(
							$arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]], 
							$complectProductFields
						);
						
						$arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]] = $complectProductFields;

					}

					$arResult["COMPLECT"]["RESULT_PRICE_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_PRICE"], $OPTION_CURRENCY);
					$arResult["COMPLECT"]["RESULT_BASE_DIFF_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_BASE_DIFF"], $OPTION_CURRENCY);
					$arResult["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_BASE_PRICE"], $OPTION_CURRENCY); 

					//set price
					$arResult["PRICE"]["DISCOUNT_PRICE"] = $arResult["COMPLECT"]["RESULT_PRICE"];
					if($arResult["COMPLECT"]["RESULT_BASE_DIFF"] > 0){
						$arResult["PRICE"]["DISCOUNT"] = $arResult["COMPLECT"]["RESULT_BASE_DIFF"];
						$arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $arResult["COMPLECT"]["RESULT_BASE_PRICE"];
					}

				}

				//IMAGES
				if(!empty($arResult["DETAIL_PICTURE"])){
					$arResult["IMAGES"][] = array(
						"SMALL_PICTURE" => array_change_key_case(CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER),
						"LARGE_PICTURE" => array_change_key_case(CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER),
						"SUPER_LARGE_PICTURE" => array_change_key_case(CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER)
					);
				}

				if(!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
					foreach ($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $imf => $nextMorePhotoID) {
						$arResult["IMAGES"][] = array(
							"SMALL_PICTURE" => array_change_key_case(CFile::ResizeImageGet($nextMorePhotoID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER),
							"LARGE_PICTURE" => array_change_key_case(CFile::ResizeImageGet($nextMorePhotoID, array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER),
							"SUPER_LARGE_PICTURE" => array_change_key_case(CFile::ResizeImageGet($nextMorePhotoID, array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER)
						);
					}
				}

				if(empty($arResult["IMAGES"])){
					$arResult["IMAGES"][] = array(
						"SMALL_PICTURE" => SITE_TEMPLATE_PATH."/images/empty.png",
						"LARGE_PICTURE" => SITE_TEMPLATE_PATH."/images/empty.png",
						"SUPER_LARGE_PICTURE" => SITE_TEMPLATE_PATH."/images/empty.png",
					);					
				}

				//коэффициент еденица измерения 
				$rsMeasure = CCatalogMeasure::getList(
					array(),
					array(
						"ID" => $arResult["CATALOG_MEASURE"]
					),
					false,
					false,
					false
				);
				
				while($arNextMeasure = $rsMeasure->Fetch()) {
					$arResult["MEASURES"][$arNextMeasure["ID"]] = $arNextMeasure;
				}

				//Информация о складах
				if(empty($arResult["COMPLECT"])){
					$rsStore = CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arResult["ID"]), false, false, array("ID", "AMOUNT")); 
					while($arNextStore = $rsStore->GetNext()){
						$arResult["STORES"][] = $arNextStore;
					}
				}

				$arResult["CURRENCY"] = CCurrency::GetBaseCurrency();

				$this->setResultCacheKeys(array());
				$this->IncludeComponentTemplate();

			}
		}
	}

?>