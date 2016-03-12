<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock")){
	
	$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
	$OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
	$arResult["BASE_LANG_CURRENCY"] = $OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

	$dbPriceType = CCatalogGroup::GetList(
	        array("SORT" => "ASC"),
	        array()
	);

	while ($arPriceType = $dbPriceType->Fetch()){
	    $PRICE_CODES[$arPriceType["NAME"]] = $arPriceType["ID"];
	}

	CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);

	$arID = array();
	$arBasketItems = array();
	$arBasketOrder = array("NAME" => "ASC", "ID" => "ASC");
	$arBasketUser = array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL");
	$arBasketSelect = array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY",
			"CAN_BUY", "PRICE", "WEIGHT", "NAME", "CURRENCY", "CATALOG_XML_ID", "VAT_RATE",
			"NOTES", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "DIMENSIONS", "TYPE", "SET_PARENT_ID", "DETAIL_PAGE_URL", "*"
	);
	$dbBasketItems = CSaleBasket::GetList($arBasketOrder, $arBasketUser, false, false, $arBasketSelect);

	$arResult["SUM"]          = 0;
	$arResult["ORDER_WEIGHT"] = 0;
	$arResult["SUM_DELIVERY"] = 0;

	$arResult["MAX_DIMENSIONS"] = array();
	$arResult["ITEMS_DIMENSIONS"] = array();

	while ($arItems = $dbBasketItems->Fetch()){

		CSaleBasket::UpdatePrice(
			$arItems["ID"],
			$arItems["CALLBACK_FUNC"],
			$arItems["MODULE"],
			$arItems["PRODUCT_ID"],
			$arItems["QUANTITY"],
			"N",
			$arItems["PRODUCT_PROVIDER_CLASS"]
		);

		array_push($arID, $arItems["ID"]);

		$arDim = $arItems["DIMENSIONS"] = $arItems["~DIMENSIONS"];

		if(is_array($arDim)){
			$arResult["MAX_DIMENSIONS"] = CSaleDeliveryHelper::getMaxDimensions(
				array(
					$arDim["WIDTH"],
					$arDim["HEIGHT"],
					$arDim["LENGTH"]
					),
				$arResult["MAX_DIMENSIONS"]);

			$arResult["ITEMS_DIMENSIONS"][] = $arDim;
		}

	}

	if (!empty($arID)){

		$dbBasketItems = CSaleBasket::GetList(
			$arBasketOrder,
			array(
				"ID" => $arID,
				"ORDER_ID" => "NULL"
			),
			false,
			false,
			$arBasketSelect
		);

		while ($arItems = $dbBasketItems->Fetch()){
		    $arResult["SUM"]    += ($arItems["PRICE"]  * $arItems["QUANTITY"]);
		    $arResult["ORDER_WEIGHT"] += ($arItems["WEIGHT"] * $arItems["QUANTITY"]);
		    $arResult["ITEMS"][$arItems["PRODUCT_ID"]] = $arItems;
		    $arResult["ID"][] = $arItems["PRODUCT_ID"];
		}
	 
	 $arOrder = array(
	      "SITE_ID" => SITE_ID,
	      "USER_ID" => $GLOBALS["USER"]->GetID(),
	      "ORDER_PRICE" => $arResult["SUM"],
	      "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
	      "BASKET_ITEMS" => $arResult["ITEMS"]
	   );
	   
	   $arOptions = array(
	      "COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
	   );
	   
	   $arErrors = array();
	   
	   CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

	   $PRICE_ALL = 0;
	   $DISCOUNT_PRICE_ALL = 0;
	   $QUANTITY_ALL = 0;
	      
	   foreach ($arOrder["BASKET_ITEMS"] as $arItem)
	   {
	      $arResult["ITEMS"][$arItem["PRODUCT_ID"]] = $arItem;
	      $PRICE_ALL += $arItem["PRICE"] * $arItem["QUANTITY"];
	      $DISCOUNT_PRICE_ALL += $arItem["DISCOUNT_PRICE"] * $arItem["QUANTITY"];      
	      $QUANTITY_ALL += $arItem['QUANTITY'];
	   }

	   $arResult["SUM"] = $PRICE_ALL;
	   $arResult["DISCOUNT_PRICE_ALL"] = $DISCOUNT_PRICE_ALL;
	   $arResult["QUANTITY_ALL"] = $QUANTITY_ALL;   

	}

	// add fields
	if(!empty($arResult["ID"])){

		$arSelect = Array(
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DETAIL_PAGE_URL",
			"DETAIL_PICTURE",
			"CATALOG_GROUP_1",
			"CATALOG_QUANTITY",
			"PROPERTY_*",
			"*"
		);

		$res = CIBlockElement::GetList(
			Array(),
			Array(
				"ID" => $arResult["ID"],
				"IBLOCK_ID" => $arResult["IBLOCK_ID"]
			),
			false,
			false,
			$arSelect
		);

		while($ob = $res->GetNextElement()){
			$arFields = $ob->GetFields();
			$arProductProperties = $ob->GetProperties();

			$skuProductInfo = CCatalogSKU::getProductList($arFields["ID"]);
			
			if(!empty($skuProductInfo)){
				foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
					$productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
					if(!empty($productBySku)){
						if($arResProductSku = $productBySku->GetNextElement()){
							$arResProductSkuProperties = $arResProductSku->GetProperties();
							$arProductProperties = array_merge($arResProductSkuProperties, $arProductProperties);
						}
					}
				}
			}

			foreach ($arProductProperties as $isp => $arProperty) {
				if($arProperty["PROPERTY_TYPE"] == "E" || $arProperty["PROPERTY_TYPE"] == "S" || $arProperty["PROPERTY_TYPE"] == "N"){
					$arProductProperties[$isp] = CIBlockFormatProperties::GetDisplayValue($arFields, $arProductProperties[$isp], "catalog_out");
				}
			}

			$arPrice = CCatalogProduct::GetOptimalPrice($arFields["ID"], 1, $USER->GetUserGroupArray());
			if($arPrice["PRICE"]["CURRENCY"] != $OPTION_CURRENCY){
				$arPrice["PRICE"]["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"]["PRICE"], $arPrice["PRICE"]["CURRENCY"], $OPTION_CURRENCY);
			}

			$arFields["OLD_PRICE"] = $arPrice["PRICE"]["PRICE"];
			$arFields["PICTURE"] = CFile::ResizeImageGet(
				$arFields["DETAIL_PICTURE"],
				array(
					"width"  => $arParams["BASKET_PICTURE_WIDTH"],
					"height" => $arParams["BASKET_PICTURE_HEIGHT"]
				),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				false
			);
			
			$arResult["ITEMS"][$arFields["ID"]]["INFO"] = array_merge($arFields, array("PROPERTIES" => $arProductProperties));
		
		}

	}

	if(!empty($arResult["ID"])){

		$arResult["ACCESSORIES"] = array();

		$arSelect = Array(
			"ID",
			"PROPERTY_SIMILAR_PRODUCT"
		);

		$arFilter = Array(
			"ID" => $arResult["ID"]
		);

		$res = CIBlockElement::GetList(
			Array(),
			$arFilter,
			false,
			false,
			$arSelect
		);

		while($ob = $res->GetNextElement()){
			$arFields = $ob->GetFields();
			if(!empty($arFields["PROPERTY_SIMILAR_PRODUCT_VALUE"])){
				array_push($arResult["ACCESSORIES"], $arFields["PROPERTY_SIMILAR_PRODUCT_VALUE"]);
			}
		}

		if(!empty($arResult["ACCESSORIES"])){

			shuffle($arResult["ACCESSORIES"]);
			$arSelect = Array(
				"ID",
				"NAME",
				"DETAIL_PAGE_URL",
				"DETAIL_PICTURE",
				"CATALOG_GROUP_1",
				"CATALOG_QUANTITY",
				"PROPERTY_RATING",
				"PROPERTY_MARKER"
			);

			$arFilter = Array(
				"ID" => $arResult["ACCESSORIES"]
			);

			$res = CIBlockElement::GetList(
				Array(),
				$arFilter,
				false,
				false,
				$arSelect
			);

			while($ob = $res->GetNextElement()){
				$arFields = $ob->GetFields();

				$dbPrice = CPrice::GetList(
			        array(
			        	"QUANTITY_FROM" => "ASC",
			        	"QUANTITY_TO"   => "ASC",
			        	"SORT"          => "ASC"
			        ),
			        array(
			        	"PRODUCT_ID" => $arFields["ID"]
			        ),
			        false,
			        false,
			        array(
			        	"ID",
			        	"CATALOG_GROUP_ID",
			        	"PRICE",
			        	"CURRENCY",
			        	"QUANTITY_FROM",
			        	"QUANTITY_TO"
			        )
				);

				while ($arPrice = $dbPrice->Fetch()){

				    $arDiscounts = CCatalogDiscount::GetDiscountByPrice(
			            $arPrice["ID"],
			            $USER->GetUserGroupArray(),
			            "N",
			            SITE_ID
				    );

				    $arFields["TMP_PRICE"] = $arFields["PRICE"] = CCatalogProduct::CountPriceWithDiscount(
			            $arPrice["PRICE"],
			            $arPrice["CURRENCY"],
			            $arDiscounts
				    );

					$arFields["OLD_PRICE"] = $arFields["PRICE"] != $arPrice["PRICE"] ? CurrencyFormat(CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY) : 0;
					$arFields["PRICE"] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arFields["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY);

				}

				$arFields["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
				$arFields["COMPARE"] = false; //!empty($_SESSION["COMPARE_LIST"]["ITEMS"][$arFields["ID"]]);

				if(empty($arFields["TMP_PRICE"])){
					$arFields["SKU"] = CCatalogSKU::IsExistOffers($arFields["ID"]);
					if($arFields["SKU"]){
						$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arFields["IBLOCK_ID"]);
						if (is_array($SKU_INFO)){  
							$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arFields["ID"]), false, array(), array("ID","IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME")); 
							while($arSku = $rsOffers->GetNext()){
								$arSkuPrice = CCatalogProduct::GetOptimalPrice($arSku["ID"], 1, $USER->GetUserGroupArray());
								$arFields["SKU_PRODUCT"][] = $arSku + $arSkuPrice;
								$arFields["PRICE"] = ($arFields["PRICE"] > $arSkuPrice["DISCOUNT_PRICE"] || empty($arFields["PRICE"])) ? $arSkuPrice["DISCOUNT_PRICE"] : $arFields["PRICE"];
							
								$arResult["SKU_PRICES"][] = $arSkuPrice["DISCOUNT_PRICE"];
									
								if($arSku["CATALOG_QUANTITY"] > 0){
									$arFields["CATALOG_QUANTITY"] = $arSku["CATALOG_QUANTITY"];
								}
							}
							
							$arFields["SKU_PRICE"] = CurrencyFormat($arFields["PRICE"], $OPTION_CURRENCY);
							
							if(min($arResult["SKU_PRICES"]) != max($arResult["SKU_PRICES"])){
								$arFields["SKU_SHOW_FROM"] = true;
							}

							$arFields["ADDSKU"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
						
						}
					}
				}

				$arFields["PICTURE"] = CFile::ResizeImageGet($arFields["DETAIL_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false);
				$arResult["ACCESSORIES"]["ITEMS"][] = $arFields;
			}
		}

	}

	$this->IncludeComponentTemplate();
}
?>
