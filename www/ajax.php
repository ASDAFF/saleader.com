<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?error_reporting(0);?>
<?if(!empty($_GET["act"])){
	if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")){
		if($_GET["act"] == "getAvailableWindow"){
			if(!empty($_GET["product_id"])){
				$APPLICATION->IncludeComponent(
					"bitrix:catalog.store.amount", 
					"fastView", 
					array(
						"COMPONENT_TEMPLATE" => "fastView",
						"ELEMENT_ID" => intval($_GET["product_id"]),
						"STORES" => array(
						),
						"ELEMENT_CODE" => "",
						"STORE_PATH" => "/stores/#store_id#/",
						"CACHE_TYPE" => "N",
						"CACHE_TIME" => "36000",
						"MAIN_TITLE" => "",
						"USER_FIELDS" => array(
							0 => "",
							1 => "",
						),
						"FIELDS" => array(
							0 => "TITLE",
							1 => "ADDRESS",
							2 => "DESCRIPTION",
							3 => "PHONE",
							4 => "EMAIL",
							5 => "IMAGE_ID",
							6 => "COORDINATES",
							7 => "SCHEDULE",
							8 => "",
						),
						"SHOW_EMPTY_STORE" => "Y",
						"USE_MIN_AMOUNT" => "Y",
						"SHOW_GENERAL_STORE_INFORMATION" => "N",
						"MIN_AMOUNT" => "0"
					),
					false
				);
			}
		}elseif($_GET["act"] == "getPricesWindow"){
			if(!empty($_GET["product_id"])){
				$APPLICATION->IncludeComponent(
					"dresscode:catalog.prices.view", 
					".default", 
					array(
						"COMPONENT_TEMPLATE" => ".default",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "360000",
						"PRODUCT_ID" => intval($_GET["product_id"])
					),
					false
				);
			}
		}elseif($_GET["act"] == "getFastView"){
			if(!empty($_GET["product_id"])){
				$APPLICATION->IncludeComponent(
					"dresscode:catalog.fast.view", 
					".default", 
					array(
						"COMPONENT_TEMPLATE" => ".default",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "360000",
						"PRODUCT_ID" => intval($_GET["product_id"]),
						'HIDE_MEASURES'=>'Y'
					),
					false
				);
			}
		}
		elseif($_GET["act"] == "selectSku"){
			if(!empty($_GET["params"]) &&
			   !empty($_GET["iblock_id"]) &&
			   !empty($_GET["prop_id"]) &&
			   !empty($_GET["product_id"]) &&
			   !empty($_GET["level"]) &&
			   !empty($_GET["props"])
			){

				$OPTION_ADD_CART = COption::GetOptionString("catalog", "default_can_buy_zero");
				$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

				$arTmpFilter = array(
 					"ACTIVE" => Y,
					"IBLOCK_ID" => intval($_GET["iblock_id"]),
					"PROPERTY_".intval($_GET["prop_id"]) => intval($_GET["product_id"])
 				);

				// if($OPTION_ADD_CART == N){
				// 	$arTmpFilter[">CATALOG_QUANTITY"] = 0;
				// }

				$arProps = array();
				$arParams =  array();
				$arTmpParams = array();
				$arCastFilter = array();
				$arProperties = array();
				$arPropActive = array();
				$arAllProperties = array();
				$arPropCombination = array();
				$arHighloadProperty = array();

				$PROPS = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["props"]) : $_GET["props"];
				$PARAMS = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["params"]) : $_GET["params"];
				$HIGHLOAD = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["highload"]) : $_GET["highload"];

				//normalize property
				$exProps = explode(";", trim($PROPS, ";"));
				$exParams = explode(";", trim($PARAMS, ";"));
				$exHighload = explode(";", trim($HIGHLOAD, ";"));

				if(empty($exProps) || empty($exParams))
					die("error #1 | Empty params or propList _no valid data");

				if(!empty($exHighload)){
					foreach ($exHighload as $ihl => $nextHighLoad) {
						$arHighloadProperty[$nextHighLoad] = "Y";
					}
				}

				foreach ($exProps as $ip => $sProp) {
					$msp = explode(":", $sProp);
					$arProps[$msp[0]][$msp[1]] = D;
				}

				foreach ($exParams as $ip => $pProp) {
					$msr = explode(":", $pProp);
					$arParams[$msr[0]] = $msr[1];
					if(empty($arHighloadProperty[$msr[0]])){
						$arTmpParams["PROPERTY_".$msr[0]."_VALUE"] = $msr[1];
					}else{
						$arTmpParams["PROPERTY_".$msr[0]] = $msr[1];
					}
				}

				$arFilter = array_merge($arTmpFilter, array_slice($arTmpParams, 0, $_GET["level"]));

				$rsOffer = CIBlockElement::GetList(
					array(),
					$arFilter, false, false,
					array(
						"ID",
						"NAME",
						"IBLOCK_ID",
						"CATALOG_MEASURE"
					)
				);

				while($obOffer = $rsOffer->GetNextElement()){
					$arOfferParams = $obOffer->GetFields();
					$arFilterProp = $obOffer->GetProperties();
					foreach ($arFilterProp as $ifp => $arNextProp) {
						if($arNextProp["PROPERTY_TYPE"] == "L" && !empty($arNextProp["VALUE"])
							|| $arNextProp["PROPERTY_TYPE"] == "S" && !empty($arNextProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]) && !empty($arNextProp["VALUE"])
						){
							$arProps[$arNextProp["CODE"]][$arNextProp["VALUE"]] = N;
							$arProperties[$arNextProp["CODE"]] = $arNextProp["VALUE"];
							$arPropCombination[$arOfferParams["ID"]][$arNextProp["CODE"]][$arNextProp["VALUE"]] = "Y";
						}
					}
				}

				if(!empty($arParams)){
					foreach ($arParams as $propCode => $arField) {
						if($arProps[$propCode][$arField] == "N"){
						 	$arProps[$propCode][$arField] = Y;
						}else{
							if(!empty($arProps[$propCode])){
								foreach ($arProps[$propCode] as $iCode => $upProp) {
									if($upProp == "N"){
										$arProps[$propCode][$iCode] = Y;
										break(1);
									}
								}
							}
						}
					}
				}

				if(!empty($arProps)){
					foreach ($arProps as $ip => $arNextProp) {
						foreach ($arNextProp as $inv => $arNextPropValue) {
							if($arNextPropValue == Y){
								$arPropActive[$ip] = $inv;
								$arPropActiveIndex[$activeIntertion++] = $inv;
							}
						}
					}
				}

				if(!empty($arProps)){
					$arPrevLevelProp = array();
					$levelIteraion = 0;
					foreach ($arProps as $inp => $arNextProp){ //level each
						if($levelIteraion > 0){
							foreach ($arNextProp as $inpp => $arNextPropEach) {
								if($arNextPropEach == "N" && !empty($arPrevLevelProp)){
									$seachSuccess = false;
									foreach ($arPropCombination as $inc => $arNextCombination) {
										if($arNextCombination[$inp][$inpp] == "Y" && $arNextCombination[$arPrevLevelProp["INDEX"]][$arPrevLevelProp["VALUE"]] == "Y"){
											$seachSuccess = true;
											break(1);
										}
									}
									if($seachSuccess == false){
										$arProps[$inp][$inpp] = "D";
									}
								}
							}
						}$levelIteraion++;
						$arPrevLevelProp = array("INDEX" => $inp, "VALUE" => $arPropActive[$inp]);
					}
				}

				$arLastFilter = array(
					"ACTIVE" => Y,
					"IBLOCK_ID" => intval($_GET["iblock_id"]),
					"PROPERTY_".intval($_GET["prop_id"]) => intval($_GET["product_id"])
				);

				// if($OPTION_ADD_CART == "N"){
				// 	$arTmpFilter[">CATALOG_QUANTITY"] = 0;
				// }

				foreach ($arPropActive as $icp => $arNextProp) {
					if(empty($arHighloadProperty[$icp])){
						$arLastFilter["PROPERTY_".$icp."_VALUE"] = $arNextProp;
					}else{
						$arLastFilter["PROPERTY_".$icp] = $arNextProp;
					}
				}

				$arLastOffer = getLastOffer($arLastFilter, $arProps, $_GET["product_id"], $OPTION_CURRENCY, !empty($_GET["product-more-pictures"]));

				if(!empty($arLastOffer["PRODUCT"]["CATALOG_MEASURE"])){
					//коэффициент еденица измерения 
					$rsMeasure = CCatalogMeasure::getList(
						array(),
						array(
							"ID" => $arLastOffer["PRODUCT"]["CATALOG_MEASURE"]
						),
						false,
						false,
						false
					);
					
					while($arNextMeasure = $rsMeasure->Fetch()) {
						$arLastOffer["PRODUCT"]["MEASURE"] = $arNextMeasure;
					}
				}

				if(!empty($_GET["product-change-prop"])){
					ob_start();
					$APPLICATION->IncludeComponent(
						"dresscode:catalog.properties.list", 
						htmlspecialchars($_GET["product-change-prop"]), 
						array(
							"PRODUCT_ID" => $arLastOffer["PRODUCT"]["ID"],
							"COUNT_PROPERTIES" => 10
						),
						false
					);
					$arLastOffer["PRODUCT"]["RESULT_PROPERTIES"] = ob_get_contents();
					ob_end_clean();
				}

				//price count
				$dbPrice = CPrice::GetList(
			        array(),
			        array("PRODUCT_ID" => $arLastOffer["PRODUCT"]["ID"]),
			        false,
			        false,
			        array("ID")
			    );
				$arLastOffer["PRODUCT"]["COUNT_PRICES"] = $dbPrice->SelectedRowsCount();

				//Информация о складах
				$rsStore = CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arLastOffer["PRODUCT"]["ID"]), false, false, array("ID", "AMOUNT")); 
				while($arNextStore = $rsStore->GetNext()){
					$arLastOffer["PRODUCT"]["STORES"][] = $arNextStore;
				}

				$arLastOffer["PRODUCT"]["STORES_COUNT"] = count($arLastOffer["PRODUCT"]["STORES"]);

				if(!empty($arProps)){
					echo jsonMultiEn(
						array(
							array("PRODUCT" => $arLastOffer["PRODUCT"]),
							array("PROPERTIES" => $arLastOffer["PROPERTIES"])
						)
					);
				}

			}
		}

		elseif($_GET["act"] == "addCart"){
					
			if(!empty($_GET["multi"]) && !empty($_GET['id'])){
				
				$error = false;
				$addElements = explode(";", $_GET["id"]);
				
				foreach ($addElements as $x => $nextID) {
					$addBasketQuantity = 1;
					$rsMeasureRatio = CCatalogMeasureRatio::getList(
						array(), 
						array("PRODUCT_ID" => $nextID), 
						false, 
						false, 
						array()
					);
					
					if($arProductMeasureRatio = $rsMeasureRatio->Fetch()){
						if(!empty($arProductMeasureRatio["RATIO"])){
							$addBasketQuantity = $arProductMeasureRatio["RATIO"];
						}
					}

					if(!Add2BasketByProductID(intval($nextID), $addBasketQuantity, false)){
						$error = true;
					}

				}

				if(!$error){
					echo '{"error" : "false"}';
				}

			}else{
				
				//коэффициент еденица измерения
			
				$addBasketQuantity = 1;
				$rsMeasureRatio = CCatalogMeasureRatio::getList(
					array(), 
					array("PRODUCT_ID" => intval($_GET["id"])), 
					false, 
					false, 
					array()
				);
				
				if($arProductMeasureRatio = $rsMeasureRatio->Fetch()){
					if(!empty($arProductMeasureRatio["RATIO"])){
						$addBasketQuantity = $arProductMeasureRatio["RATIO"];
					}
				}

				// Проверка на комплект
				
				$arComplectID = array();
				$arResult["COMPLECT"] = array();
				
				$rsComplect = CCatalogProductSet::getList(
					array("SORT" => "ASC"),
					array(
						"TYPE" => 1,
						"OWNER_ID" => intval($_GET["id"]),
						"!ITEM_ID" => intval($_GET["id"])
					),
					false,
					false,
					array("*")
				);

				while ($arComplectItem = $rsComplect->Fetch()){
					$arResult["COMPLECT"]["ITEMS"][$arComplectItem["ITEM_ID"]] = $arComplectItem;
					$arComplectID[$arComplectItem["ITEM_ID"]] = $arComplectItem["ITEM_ID"];
				}

				if(!empty($arComplectID)){
			
					$arResult["COMPLECT"]["WEIGHT"] = 0;
					$arResult["COMPLECT"]["RESULT_PRICE"] = 0;
					$arResult["COMPLECT"]["RESULT_BASE_DIFF"] = 0;
					$arResult["COMPLECT"]["RESULT_BASE_PRICE"] = 0;

					$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_MEASURE", "CATALOG_AVAILABLE", "CATALOG_WEIGHT");
					$arFilter = Array("ID" => $arComplectID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
					$rsComplectProducts = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
					while($obComplectProducts = $rsComplectProducts->GetNextElement()){
						
						$complectProductFields = $obComplectProducts->GetFields();
						$complectProductFields["PRICE"] = CCatalogProduct::GetOptimalPrice($complectProductFields["ID"], 1, $USER->GetUserGroupArray());
						$complectProductFields["PRICE"]["DISCOUNT_PRICE"] = $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
						$complectProductFields["PRICE"]["DISCOUNT_PRICE"] -= $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["DISCOUNT_PERCENT"] / 100;
						$complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]  * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
						$complectProductFields["PRICE"]["PRICE_DIFF"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] - $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
						$complectProductFields["PRICE"]["BASE_PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);
						$complectProductFields["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
						
						$arResult["COMPLECT"]["WEIGHT"] += $complectProductFields["CATALOG_WEIGHT"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
						$arResult["COMPLECT"]["RESULT_PRICE"] += $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
					}

					$rsComplectOwnerProduct = CIBlockElement::GetList(array(), array("ID" => intval($_GET["id"]), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y"), false, false, $arSelect);
					if($obComplectOwnerProduct = $rsComplectOwnerProduct->GetNextElement()){
						
						$complectOwnerProduct = $obComplectOwnerProduct->GetFields();
						$complectOwnerProduct["PRICE"] = CCatalogProduct::GetOptimalPrice($complectOwnerProduct["ID"], 1, $USER->GetUserGroupArray());

						$arCartFields = array(
							"PRODUCT_ID" => intval($_GET["id"]),
							"PRODUCT_PRICE_ID" => $complectOwnerProduct["PRICE"]["PRICE"]["ID"],
							"PRICE" => $arResult["COMPLECT"]["RESULT_PRICE"],
							"CURRENCY" => $complectOwnerProduct["PRICE"]["PRICE"]["CURRENCY"],
							"WEIGHT" => $arResult["COMPLECT"]["WEIGHT"],
							"PRODUCT_PROVIDER_CLASS" =>  false,
							"QUANTITY" => $addBasketQuantity,
							"LID" => $_GET["site_id"],
							"DELAY" => "N",
							"CAN_BUY" => $complectOwnerProduct["CATALOG_AVAILABLE"],
							"NAME" => $complectOwnerProduct["NAME"],
							"IGNORE_CALLBACK_FUNC" => "Y",
							"CALLBACK_FUNC" => "", 
							"MODULE" => "sale",
							"NOTES" => "",
							"DETAIL_PAGE_URL" => $complectOwnerProduct["DETAIL_PAGE_URL"],
							"FUSER_ID" => CSaleBasket::GetBasketUserID()
						);

						$addCartResult = CSaleBasket::Add($arCartFields);

					}

				}else{
					$addCartResult = Add2BasketByProductID(intval($_GET["id"]), $addBasketQuantity, false);
				}

				if($addCartResult){
					
					GLOBAL $USER;
					
					$OPTION_ADD_CART = COption::GetOptionString("catalog", "default_can_buy_zero");
					$mStr = $gStr = "";
					
					$getList = CIBlockElement::GetList(
						Array(),
						array(
							"ID" => intval($_GET['id'])
						),
						false,
						false,
						array(
							"ID",
							"NAME",
							"DETAIL_PICTURE",
							"DETAIL_PAGE_URL",
							"CATALOG_QUANTITY",
							"CATALOG_MEASURE"
						)
					);

					$obj = $getList->GetNextElement();
					$arResult = $obj->GetFields();
					$arResult["PROPERTIES"] = $obj->GetProperties();
					$arResult["NAME"] = str_replace("&quot;","", $arResult["NAME"]);
					
					if(empty($arResult["DETAIL_PICTURE"])){
						$productSkuInfo = CCatalogSku::GetProductInfo($arResult["ID"]);
						if (is_array($productSkuInfo)){
							$getListSkuProductInfo = CIBlockElement::GetList(
								Array(),
								array(
									"ID" => $productSkuInfo["ID"]
								),
								false,
								false,
								array(
									"DETAIL_PICTURE",
								)
							)->GetNextElement();
							$arSkuProductResult = $getListSkuProductInfo->GetFields();
							$arResult["DETAIL_PICTURE"] = $arSkuProductResult["DETAIL_PICTURE"];
						}			
					}
					
					$arResult["DETAIL_PICTURE"] = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 280, "height" => 280), BX_RESIZE_IMAGE_PROPORTIONAL, false);
					$arResult["DETAIL_PICTURE"] = !empty($arResult["DETAIL_PICTURE"]["src"]) ? $arResult["DETAIL_PICTURE"]["src"] : SITE_TEMPLATE_PATH."/images/empty.png";

					foreach ($arResult as $index => $arValues) {
						$arJsn[] = '"'.$index.'":"'.addslashes(str_replace(array("'", "\r", "\n", "\r\n"), "", trim($arValues))).'"';
					}

					$dbBasketItems = CSaleBasket::GetList(
						false,
						array(
							"FUSER_ID" => CSaleBasket::GetBasketUserID(),
							"ORDER_ID" => "NULL",
							"PRODUCT_ID" => intval($_GET["id"])
						),
						false,
						false,
						array(
							"ID",
							"QUANTITY",
							"PRICE",
							"PRODUCT_ID",
							"CURRENCY",
							"DISCOUNT_PRICE"
						)
					);

					$basketQty = $dbBasketItems->Fetch();
					$basketQty["~DISCOUNT_PRICE"] = !empty($basketQty["DISCOUNT_PRICE"]) && $basketQty["DISCOUNT_PRICE"] > 0 ? CCurrencyLang::CurrencyFormat($basketQty["PRICE"] + $basketQty["DISCOUNT_PRICE"], $basketQty["CURRENCY"], true) : $basketQty["DISCOUNT_PRICE"];
					$basketQty["DISCOUNT_SUM"] = !empty($basketQty["DISCOUNT_PRICE"]) && $basketQty["DISCOUNT_PRICE"] > 0 ? CCurrencyLang::CurrencyFormat(($basketQty["PRICE"] + $basketQty["DISCOUNT_PRICE"]) * round($basketQty["QUANTITY"]), $basketQty["CURRENCY"], true) : $basketQty["DISCOUNT_PRICE"];
					$basketQty["OLD_PRICE"] = round($basketQty["~DISCOUNT_PRICE"]) > 0 ? $basketQty["PRICE"] + $basketQty["DISCOUNT_PRICE"] : 0;
					$arResult["CAN_BUY"] = $OPTION_ADD_CART == "Y" ? true : false;
					$arResult["MEASURE_SYMBOL_RUS"] = "";

					if(!empty($arResult["CATALOG_MEASURE"])){
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
							$arResult["MEASURE"] = $arNextMeasure;
						}
					}

					if(!empty($arResult["MEASURE"])){
						$arResult["MEASURE_SYMBOL_RUS"] = $arResult["MEASURE"]["SYMBOL_RUS"];
					}

					$jStr = '
						"PRODUCT_ID":"'.intval($basketQty["PRODUCT_ID"]).'",
						"CART_ID":"'.intval($basketQty["ID"]).'",
						"QUANTITY":"'.doubleval($basketQty["QUANTITY"]).'",
						"~PRICE":"'.round($basketQty["PRICE"]).'",
						"OLD_PRICE": "'.$basketQty["OLD_PRICE"].'",
						"SUM":"'.addslashes(CCurrencyLang::CurrencyFormat(round($basketQty["PRICE"]) * doubleval($basketQty["QUANTITY"]), $basketQty["CURRENCY"], true)).'",
						"PRICE":"'.addslashes(CCurrencyLang::CurrencyFormat($basketQty["PRICE"], $basketQty["CURRENCY"], true)).'",
						"DISCOUNT_PRICE":"'.$basketQty["~DISCOUNT_PRICE"].'",
						"DISCOUNT_SUM":"'.$basketQty["DISCOUNT_SUM"].'",
						"CAN_BUY":"'.$arResult["CAN_BUY"].'",
						"MEASURE_SYMBOL_RUS":"'.$arResult["MEASURE_SYMBOL_RUS"].'",
						"ADDBASKET_QUANTITY_RATIO":"'.$addBasketQuantity.'"
					';
					
					if(!empty($arResult["PROPERTIES"]["RATING"])){
						$jStr .= ',"RATING": "'.$arResult["PROPERTIES"]["RATING"]["VALUE"].'"';
					}

					if(!empty($arResult["PROPERTIES"]["OFFERS"]["VALUE"])){
						
						foreach ($arResult["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker){
							$background = strstr($arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242";
							$mStr .= '<div class=\"marker\" style=\"background-color: '.$background .'\">'.$marker.'</div>';
						}					   

						$jStr .= ',"MARKER": "'.$mStr.'"';
					
					}

					$arJsn[] = $jStr;
					echo "{".implode($arJsn, ",")."}";
				}
			}
		}
		elseif($_GET["act"] == "del"){
			echo CSaleBasket::Delete(intval($_GET["id"]));
		}elseif($_GET["act"] == "upd"){
			
			$OPTION_QUANTITY_TRACE = COption::GetOptionString("catalog", "default_quantity_trace");
			
			if(!empty($_GET["id"])){
				$getList = CIBlockElement::GetList(
					Array(),
					array(
						"ID" => intval($_GET['id'])
					),
					false,
					false,
					array(
						"ID",
						"NAME",
						"DETAIL_PICTURE",
						"DETAIL_PAGE_URL",
						"CATALOG_QUANTITY"
					)
				);

				$obj = $getList->GetNextElement();
				$arProduct = $obj->GetFields();
				if(!empty($arProduct)){
					$dbBasketItems = CSaleBasket::GetList(
						false, 
						array(
							"FUSER_ID" => CSaleBasket::GetBasketUserID(),
							"ORDER_ID" => "NULL",
							"PRODUCT_ID" => intval($_GET["id"])
						), 
						false, 
						false, 
						array("ID")
					);
					
					$basketRES = $dbBasketItems->Fetch();
					if(!empty($basketRES)){
							
						if($OPTION_QUANTITY_TRACE == "Y"){
							if($arProduct["CATALOG_QUANTITY"] < doubleval($_GET["q"])){
								$quantityError = true;
							}
						}

						if(!$quantityError){
							if(CSaleBasket::Update($basketRES["ID"], array("QUANTITY" => doubleval($_GET["q"])))){
								echo '{"success" : "true"}';
							}else{
								echo '{"error" : "basketUpdateError"}';
							}
						}else{
							echo '{"error" : "quantityError", "currentQuantityValue": "'.$arProduct["CATALOG_QUANTITY"].'"}';
						}

					}else{
						echo '{"error" : "productCartError"}';
					}
				}else{
					echo '{"error" : "productNotFoundError"}';
				}
			}else{
				echo '{"error" : "empty product id"}';
			}
		}
		elseif($_GET["act"] == "skuADD"){ 
			if(!empty($_GET["id"]) && !empty($_GET["ibl"])){

				$PRODUCT_ID = intval($_GET["id"]);				
				$IBLOCK_ID  = intval($_GET["ibl"]);
				$SKU_INFO   = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
				$PRODUCT_INFO = CIBlockElement::GetByID($PRODUCT_ID)->GetNext();
				$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
				$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

				$dbPriceType = CCatalogGroup::GetList(
			        array("SORT" => "ASC"),
			        array("BASE" => Y)
				);

				while ($arPriceType = $dbPriceType->Fetch()){
				    $OPTION_BASE_PRICE = $arPriceType["ID"];
				}

				if (is_array($SKU_INFO)){  
					
					$arResult   = array();
					$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $PRODUCT_ID), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
					while($ob = $rsOffers->GetNextElement()){ 
						$arFields = $ob->GetFields();  
						$arProps = $ob->GetProperties();
						$dbPrice = CPrice::GetList(
					        array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
					        array(
					        	"PRODUCT_ID" => $arFields["ID"],
					        	"CATALOG_GROUP_ID" => $OPTION_BASE_PRICE
					        ),
					        false,
					        false,
					        array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
						);
					
						while ($arPrice = $dbPrice->Fetch()){
						    $arDiscounts = CCatalogDiscount::GetDiscountByPrice(
					            $arPrice["ID"],
					            $USER->GetUserGroupArray(),
					            "N",
					            SITE_ID
						    );
						    $arFields["PRICE"] = CCatalogProduct::CountPriceWithDiscount(
					            $arPrice["PRICE"],
					            $arPrice["CURRENCY"],
					            $arDiscounts
						    );
						
							$arFields["DISCONT_PRICE"] = $arFields["PRICE"] != $arPrice["PRICE"] ? CurrencyFormat(CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY) : 0;
							$arFields["PRICE"] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arFields["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY);

						}
					
						$picture = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array('width' => 220, 'height' => 200), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						$arFields["DETAIL_PICTURE"] = !empty($picture["src"]) ? $picture["src"] : SITE_TEMPLATE_PATH."/images/empty.png";
						$arFields["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
						$arResult[] = array_merge($arFields, array("PROPERTIES" => $arProps));

					}

					foreach ($arResult[0]["PROPERTIES"] as $i => $arProp) {
						$propVisible = false;
						if(empty($arProp["VALUE"])){
							if(empty($propDelete[$i])){
								foreach ($arResult as $x => $arElement) {
									if(!empty($arElement["PROPERTIES"][$i]["VALUE"])){
										$propVisible = true;
										break;
									}
								}
							
								if($propVisible === false){
									$propDelete[$i] = true;
								}
							}
						}
					}
	
					foreach ($arResult as $i => $arElement) {
						foreach ($propDelete as $x => $val) {
							unset($arResult[$i]["PROPERTIES"][$x]);
						}
					}

					if(!empty($arResult)){
						echo jsonMultiEn($arResult);
					}

				} 

			}
		}
		elseif($_GET["act"] == "addWishlist"){
			if(!empty($_GET["id"])){
				$_SESSION["WISHLIST_LIST"]["ITEMS"][$_GET["id"]] = $_GET["id"];
				echo intval($_SESSION["WISHLIST_LIST"]["ITEMS"][$_GET["id"]]);
			}
		}elseif($_GET["act"] == "removeWishlist"){
			if(!empty($_GET["id"])){
				unset($_SESSION["WISHLIST_LIST"]["ITEMS"][$_GET["id"]]);
				echo true;
			}
		}
		elseif($_GET["act"] == "addCompare"){
			if(!empty($_GET["id"])){
				$_SESSION["COMPARE_LIST"]["ITEMS"][$_GET["id"]] = $_GET["id"];
				echo intval($_SESSION["COMPARE_LIST"]["ITEMS"][$_GET["id"]]);
			}
		}elseif($_GET["act"] == "compDEL"){
			if(!empty($_GET["id"])){
				foreach ($_SESSION["COMPARE_LIST"]["ITEMS"] as $key => $arValue){
					if($arValue == $_GET["id"]){
						echo true;
						unset($_SESSION["COMPARE_LIST"]["ITEMS"][$key]);
						break;
					}
				}
			}
		}elseif($_GET["act"] == "search"){
			$_GET["name"] = BX_UTF !== 1 ? htmlspecialcharsbx(iconv("UTF-8", "CP1251//IGNORE", $_GET["name"])) : $_GET["name"];
			
			$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
			$OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
			$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

			$dbPriceType = CCatalogGroup::GetList(
		        array("SORT" => "ASC"),
		        array("BASE" => Y)
			);

			while ($arPriceType = $dbPriceType->Fetch()){
			    $OPTION_BASE_PRICE = $arPriceType["ID"];
			}
			
			if(!empty($_GET["name"]) && !empty($_GET["iblock_id"])){
				$section = !empty($_GET["section"]) ? intval($_GET["section"]) : 0;
				$arSelect = Array("ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_QUANTITY");
				$arFilter = Array("ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "IBLOCK_ID" => intval($_GET["iblock_id"]));
				$arFilter[] =  array("LOGIC" => "OR", "?NAME" => $_GET["name"], "PROPERTY_ARTICLE" => $_GET["name"]);
				if($section){
					 $arFilter["SECTION_ID"] = $section;
				}
				$res = CIBlockElement::GetList(Array("shows" => "DESC"), $arFilter, false, Array("nPageSize" => 4), $arSelect);
				while($ob = $res->GetNextElement()){ 
					$arFields = $ob->GetFields(); 
					$dbPrice = CPrice::GetList(
				        array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
				        array(
				        	"PRODUCT_ID" => $arFields["ID"],
				        	"CATALOG_GROUP_ID" => $OPTION_BASE_PRICE
				        ),
				        false,
				        false,
				        array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
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
					    $arFields["DISCONT_PRICE"] = $arFields["PRICE"] != $arPrice["PRICE"] ? CurrencyFormat(CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY) : 0;
					    $arFields["PRICE"] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arFields["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY);
					}

					if(empty($arFields["TMP_PRICE"])){
						$arFields["SKU"] = CCatalogSKU::IsExistOffers($arFields["ID"]);
						if($arFields["SKU"]){
							$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arFields["IBLOCK_ID"]);
							if (is_array($SKU_INFO)){  
								$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arFields["ID"]), false, false, array("ID","IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME")); 
								while($arSku = $rsOffers->GetNext()){
									$arSkuPrice = CCatalogProduct::GetOptimalPrice($arSku["ID"], 1, $USER->GetUserGroupArray());
									if(!empty($arSkuPrice)){
										$arFields["SKU_PRODUCT"][] = $arSku + $arSkuPrice;
									}
									$arFields["PRICE"] = ($arFields["PRICE"] > $arSkuPrice["DISCOUNT_PRICE"] || empty($arFields["PRICE"])) ? $arSkuPrice["DISCOUNT_PRICE"] : $arFields["PRICE"];
								}
								$arFields["DISCONT_PRICE"] = null;
								$arFields["PRICE"] = "от ".CurrencyFormat($arFields["PRICE"], $OPTION_CURRENCY);
							}
						}
					}
					
					$arFields["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
					$picture = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					$arFields["DETAIL_PICTURE"] = !empty($picture["src"]) ? $picture["src"] : SITE_TEMPLATE_PATH."/images/empty.png";
					foreach ($arFields as $key => $arProp){
						$arJsn[] = '"'.$key.'" : "'.addslashes(trim(str_replace("'", "", $arProp))).'"';
					}
					$arReturn[] = '{'.implode($arJsn, ",").'}';
				}

				echo "[".implode($arReturn, ",")."]";
			}
		}elseif($_GET["act"] == "flushCart"){
		   ?>
		   <ul>
			   <li class="dl">      
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line", 
	"topCart", 
	array(
		"HIDE_ON_BASKET_PAGES" => "N",
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/",
		"PATH_TO_PROFILE" => SITE_DIR."personal/",
		"PATH_TO_REGISTER" => SITE_DIR."login/",
		"POSITION_FIXED" => "N",
		"SHOW_AUTHOR" => "Y",
		"SHOW_EMPTY_VALUES" => "Y",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_PERSONAL_LINK" => "N",
		"SHOW_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "Y",
		"COMPONENT_TEMPLATE" => "topCart"
	),
	false
);?>
				</li>
				<li class="dl">
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line", 
	"bottomCart", 
	array(
		"HIDE_ON_BASKET_PAGES" => "N",
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/",
		"PATH_TO_PROFILE" => SITE_DIR."personal/",
		"PATH_TO_REGISTER" => SITE_DIR."login/",
		"POSITION_FIXED" => "N",
		"SHOW_AUTHOR" => "N",
		"SHOW_EMPTY_VALUES" => "Y",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_PERSONAL_LINK" => "N",
		"SHOW_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "Y",
		"COMPONENT_TEMPLATE" => "topCart"
	),
	false
);?>				
				</li>
				<li class="dl">
					<?$APPLICATION->IncludeComponent("dresscode:favorite.line", ".default", Array(
						),
						false
					);?>			
				</li>
				<li class="dl">
					<?$APPLICATION->IncludeComponent("dresscode:compare.line", ".default", Array(
						
						),
						false
					);?>			
				</li>
			</ul><?
		}elseif($_GET["act"] == "rating"){
			global $USER;
			if ($USER->IsAuthorized()){
				if(!empty($_GET["id"])){
					$arUsers[] = $USER->GetID();
					$res = CIBlockElement::GetList(Array(), Array("ID" => intval($_GET["id"]), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"), false, false, Array("ID", "IBLOCK_ID", "PROPERTY_USER_ID", "PROPERTY_GOOD_REVIEW", "PROPERTY_BAD_REVIEW"));
					while($ob = $res->GetNextElement()){ 
						$arFields = $ob->GetFields();  
						if($arFields["PROPERTY_USER_ID_VALUE"] == $arUsers[0]){
							$result = array(
								"result" => false,
								"error" => "Вы уже голосовали!",
								"heading" => "Ошибка"
							);
							break;
						}
					}
					if(!$result){
						$propCODE = $_GET["trig"] ? "GOOD_REVIEW" : "BAD_REVIEW";
						$propVALUE = $_GET["trig"] ? $arFields["PROPERTY_GOOD_REVIEW_VALUE"] + 1 : $arFields["PROPERTY_BAD_REVIEW_VALUE"] + 1;
						$db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("CODE" => "USER_ID"));
						if($arProps = $db_props->Fetch()){
							$arUsers[] = $arProps["VALUE"];
						}
						CIBlockElement::SetPropertyValuesEx($arFields["ID"], $arFields["IBLOCK_ID"], array($propCODE => $propVALUE, "USER_ID" => $arUsers));
						$result = array(
							"result" => true
						);
					}
				}else{
					$result = array(
						"result" => false,
						"error" => "Элемент не найден",
						"heading" => "Ошибка"
					);
				}
			}
			else{
				$result = array(
					"error" => "Для голосования вам необходимо авторизаваться",
					"result" => false,
					"heading" => "Ошибка"
				);
			}
			echo jsonEn($result);
		
		}elseif($_GET["act"] == "newReview"){
			global $USER;
			if ($USER->IsAuthorized()){
				if(!empty($_GET["DIGNITY"])      && 
				   !empty($_GET["SHORTCOMINGS"]) && 
				   !empty($_GET["COMMENT"])      && 
				   !empty($_GET["NAME"])         && 
				   !empty($_GET["USED"])         && 
				   !empty($_GET["RATING"])       && 
				   !empty($_GET["PRODUCT_NAME"]) && 
				   !empty($_GET["PRODUCT_ID"])
				  ){
					$arUsers = array($USER->GetID());
					$res = CIBlockElement::GetList(
						Array(), 
						Array(
							"ID" => intval($_GET["PRODUCT_ID"]),
							"ACTIVE_DATE" => "Y",
							"ACTIVE" => "Y"
						), 
						false, 
						false, 
						Array(
							"ID", 
							"IBLOCK_ID", 
							"PROPERTY_USER_ID", 
							"PROPERTY_VOTE_SUM", 
							"PROPERTY_VOTE_COUNT"
						)
					);
					while($ob = $res->GetNextElement()){
						$arFields = $ob->GetFields();
						if($arFields["PROPERTY_USER_ID_VALUE"] == $arUsers[0]){
							$result = array(
								"heading" => "Ошибка",
								"message" => "Вы уже оставляли отзыв к этому товару."
							);
							break;
						}
						$arUsers[] = $arFields["PROPERTY_USER_ID_VALUE"];
					}
					if(empty($result)){
						$newElement = new CIBlockElement;

						// DIGNITY - достоинства
						// SHORTCOMINGS - недостатки
						// RATING - рейтинг
						// EXPERIENCE - опыт использования
						// NAME - Имя

						$PROP = array(
							"DIGNITY" => (BX_UTF == 1) ? htmlspecialchars($_GET["DIGNITY"]) : iconv("UTF-8","windows-1251//IGNORE", htmlspecialchars($_GET["DIGNITY"])),
							"SHORTCOMINGS" => (BX_UTF == 1) ? htmlspecialchars($_GET["SHORTCOMINGS"]) :  iconv("UTF-8","windows-1251//IGNORE", htmlspecialchars($_GET["SHORTCOMINGS"])),
							"NAME" => (BX_UTF == 1) ? htmlspecialchars($_GET["NAME"]) : iconv("UTF-8","windows-1251//IGNORE", htmlspecialchars($_GET["NAME"])),
							"EXPERIENCE" => intval($_GET["USED"]),
							"RATING" => intval($_GET["RATING"])
						);

						$arLoadProductArray = Array(
							"MODIFIED_BY"    => $USER->GetID(),
							"IBLOCK_SECTION_ID" => false,
							"IBLOCK_ID"      => intval($_GET["iblock_id"]),
							"PROPERTY_VALUES"=> $PROP,
							"NAME"           => (BX_UTF == 1) ? htmlspecialchars($_GET["PRODUCT_NAME"]) : iconv("UTF-8","windows-1251//IGNORE", htmlspecialchars($_GET["PRODUCT_NAME"])),
							"ACTIVE"         => "N",
							"DETAIL_TEXT"    => (BX_UTF == 1) ? htmlspecialchars($_GET["COMMENT"]) : iconv("UTF-8","windows-1251//IGNORE", htmlspecialchars($_GET["COMMENT"])),
							"CODE"           => intval($_GET["PRODUCT_ID"])
						);

						if($PRODUCT_ID = $newElement->Add($arLoadProductArray)){
							$result = array(
								"heading" => "Отзыв добавлен",
								"message" => "Ваш отзыв будет опубликован после модерации.",
								"reload" => true
							);

							$VOTE_SUM   = $arFields["PROPERTY_VOTE_SUM_VALUE"] + intval($_GET["RATING"]);
							$VOTE_COUNT = $arFields["PROPERTY_VOTE_COUNT_VALUE"] + 1;
							$RATING = ($VOTE_SUM / $VOTE_COUNT);

							CIBlockElement::SetPropertyValuesEx(
								intval($_GET["PRODUCT_ID"]),
								$arFields["IBLOCK_ID"], 
								array(
									"VOTE_SUM" => $VOTE_SUM,
									"VOTE_COUNT" => $VOTE_COUNT,
									"RATING" => $RATING,
									"USER_ID" => $arUsers
								)
							);

						}
						else{
							$result = array(
								"heading" => "Ошибка",
								"message" => "error(1)"
							);
						}
					}
				}else{
					$result = array(
						"heading" => "Ошибка",
						"message" => "Заполните все поля!"
					);
				}
			}else{
				$result = array(
					"heading" => "Ошибка",
					"message" => "Ошибка авторизации"
				);
			}

			echo jsonEn($result);

		}elseif($_GET["act"] == "getFastBuy"){
		
			if(!empty($_GET["id"])){
				
				$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();
				$arResult = array();
				
				$res = CIBlockElement::GetList(array(), array("ID" => intval($_GET["id"])), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
				while($arRes = $res->GetNextElement()){ 
					$arResult["PRODUCT"] = $arRes->GetFields();
					$arResult["PRODUCT"]["PROPERTIES"] = $arRes->GetProperties();
					$arTmpPrice = CCatalogProduct::GetOptimalPrice($arResult["PRODUCT"]["ID"], 1, $USER->GetUserGroupArray());
					
					$arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResult["PRODUCT"]["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
					$arResult["PRODUCT"]["PICTURE"]["src"] = !empty($arResult["PRODUCT"]["PICTURE"]["src"]) ? $arResult["PRODUCT"]["PICTURE"]["src"] : SITE_TEMPLATE_PATH."/images/empty.png";
					$arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($arTmpPrice["DISCOUNT_PRICE"], $OPTION_CURRENCY);

					if(empty($arResult["PRODUCT"]["DETAIL_PICTURE"])){
						$skuProductInfo = CCatalogSKU::getProductList($arResult["PRODUCT"]["ID"]);
						if(!empty($skuProductInfo)){
							foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
								$productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
								if(!empty($productBySku)){
									if($arResProductSku = $productBySku->GetNextElement()){
										$arResProductSkuFields = $arResProductSku->GetFields();
										if(!empty($arResProductSkuFields["DETAIL_PICTURE"])){
											$arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResProductSkuFields["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);									
										}
									}
								}
							}
						}
					}

					if($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"] != $arTmpPrice["RESULT_PRICE"]["DISCOUNT_PRICE"]){
						$arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"].= ' <s class="discount">'.CurrencyFormat($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY).'</s>';
					}

					if(!empty($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"])){
						$mSt = ''; foreach ($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker){
							$background = strstr($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242";
							$mStr .= '<div class="marker" style="background-color: '.$background .'">'.$marker.'</div>';
						}					   

						$arResult["PRODUCT"]["MARKER"] = $mStr;
					}

					//комплекты
					$arResult["COMPLECT"] = array();
					$arComplectID = array();

					$rsComplect = CCatalogProductSet::getList(
						array("SORT" => "ASC"),
						array(
							"TYPE" => 1,
							"OWNER_ID" => $arResult["PRODUCT"]["ID"],
							"!ITEM_ID" => $arResult["PRODUCT"]["ID"]
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
						$arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] = $arResult["COMPLECT"]["RESULT_PRICE_FORMATED"];
						if(!empty($arResult["COMPLECT"]["RESULT_BASE_DIFF"])){
							$arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"].= ' <s class="discount">'.$arResult["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"].'</s>';
						}

					}

				}

				if(!empty($arResult)){
					echo jsonMultiEn($arResult);
				}

			}
		
			}elseif($_GET["act"] === "fastBack"){
			
				if(!empty($_GET["phone"]) && !empty($_GET["id"])){

					if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale")){
						$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();
						$arElement = CIBlockElement::GetByID(intval($_GET["id"]))->GetNext();
						if(!empty($arElement)){
							
							$dbPrice = CPrice::GetList(
						        array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
						        array("PRODUCT_ID" => $arElement["ID"]),
						        false,
						        false,
						        array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
							);
							
							while ($arPrice = $dbPrice->Fetch()){
								
								$arDiscounts = CCatalogDiscount::GetDiscountByPrice(
									$arPrice["ID"],
									$USER->GetUserGroupArray(),
									"N",
									SITE_ID
								);
								
								$arElement["PRICE"] = CCatalogProduct::CountPriceWithDiscount(
									$arPrice["PRICE"],
									$arPrice["CURRENCY"],
									$arDiscounts
								);
								$arElement["~PRICE"] = $arElement["PRICE"];
								$arElement["PRICE"] = CurrencyFormat($arElement["PRICE"], $arPrice["CURRENCY"]);
							
							}

							$postMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE" => "SALE_DRESSCODE_FASTBACK_SEND"))->GetNext();

							if(empty($postMess)){
								
								$MESSAGE = "<h3>С сайта #SITE# поступил новый заказ в 1 клик. </h3> <p> Товар: <b>#PRODUCT#</b>  <br /> Имя: <b>#NAME#</b> <br /> Телефон: <b>#PHONE#</b> <br /> Комментарий: #COMMENT#";
								$FIELDS = "#SITE# \n #PRODUCT# \n #NAME# \n #PHONE# \n #COMMENT# \n";

								$et = new CEventType;
							    $et->Add(
							    	array(
								        "LID"           => "ru",
								        "EVENT_NAME"    => "SALE_DRESSCODE_FASTBACK_SEND",
								        "NAME"          => "Купить в один клик",
								        "DESCRIPTION"   => $FIELDS
							        )
							    );

								$arr["ACTIVE"] = "Y";
								$arr["EVENT_NAME"] = "SALE_DRESSCODE_FASTBACK_SEND";
								$arr["LID"] = SITE_ID;
								$arr["EMAIL_FROM"] = COption::GetOptionString('main', 'email_from', 'webmaster@webmaster.com');
								$arr["EMAIL_TO"] = COption::GetOptionString("sale", "order_email");
								$arr["BCC"] = COption::GetOptionString("main", 'email_from', 'webmaster@webmaster.com');
								$arr["SUBJECT"] = "Покупка товара в один клик";
								$arr["BODY_TYPE"] = "html";
								$arr["MESSAGE"] = $MESSAGE;

								$emess = new CEventMessage;
								$emess->Add($arr);

							}						

							$arMessage = array(
								"SITE" => SITE_SERVER_NAME,
								"PRODUCT" => $arElement["NAME"]." (ID:".$arElement["ID"]." )"." - ".$arElement["PRICE"],
								"NAME" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]),
								"PHONE" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"]),
								"COMMENT" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"])
							);

							CEvent::SendImmediate("SALE_DRESSCODE_FASTBACK_SEND", htmlspecialcharsbx($_GET["SITE_ID"]), $arMessage, "Y", false);

							// NEW ORDER

							$getPersonType = CSalePersonType::GetList(Array("SORT" => "ASC"), Array("LID" => htmlspecialcharsbx($_GET["SITE_ID"]))); 
							if ($arPersonItem = $getPersonType->Fetch()){
								$USER_ID = intval($USER->GetID());
		  						if($USER_ID == 0){
					  				$rsUser = CUser::GetByLogin("unregistered");
									$arUser = $rsUser->Fetch();
									if(!empty($arUser)){
										$USER_ID = $arUser["ID"];
									}else{

										$newUser = new CUser;
										$newPass = rand(0, 999999999);
										$arUserFields = Array(
										  "NAME"              => "unregistered",
										  "LAST_NAME"         => "unregistered",
										  "EMAIL"             => "unregistered@unregistered.com",
										  "LOGIN"             => "unregistered",
										  "LID"               => "ru",
										  "ACTIVE"            => "Y",
										  "GROUP_ID"          => array(),
										  "PASSWORD"          => $newPass,
										  "CONFIRM_PASSWORD"  => $newPass,
										);
										
										$USER_ID = $newUser->Add($arUserFields);
									}
								}

								//paysystem 

								$db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT" => "ASC", "PSA_NAME" => "ASC"), 
									Array("ACTIVE" => "Y", "PERSON_TYPE_ID" => $arPersonItem["ID"])
								);

								if ($ptype = $db_ptype->Fetch()){

									//delivery

									$db_dtype = CSaleDelivery::GetList(
									    array(
									            "SORT" => "ASC",
									            "NAME" => "ASC"
									        ),
									    array(
									            "LID" => htmlspecialcharsbx($_GET["SITE_ID"]),
									            "ACTIVE" => "Y",
									        ),
									    false,
									    false,
									    array()
									);
									
									if ($ar_dtype = $db_dtype->Fetch()){

										// CSaleBasket::GetBasketUserID()

										$arFields = array(
										   "LID" => htmlspecialcharsbx($_GET["SITE_ID"]),
										   "PERSON_TYPE_ID" => $arPersonItem["ID"],
										   "PAYED" => "N",
										   "CANCELED" => "N",
										   "STATUS_ID" => "N",
										   "PRICE" => $arElement["~PRICE"],
										   "CURRENCY" => $OPTION_CURRENCY,
										   "USER_ID" => $USER_ID,
										   "PAY_SYSTEM_ID" => $ptype["ID"],
										   "PRICE_DELIVERY" => 0,
										   "DELIVERY_ID" => $ar_dtype["ID"],
										   "DISCOUNT_VALUE" => 0,
										   "TAX_VALUE" => 0.0,
										   "USER_DESCRIPTION" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"])
										);

										$ORDER_ID = CSaleOrder::Add($arFields);
										$ORDER_ID = IntVal($ORDER_ID);


										$db_props = CSaleOrderProps::GetList(
										        array("SORT" => "ASC"),
										        array(
										                "PERSON_TYPE_ID" => $arPersonItem["ID"],
										                "UTIL" => "N"
										            ),
										        false,
										        false,
										        array()
										    );

										while ($props = $db_props->Fetch()){
											if($props["IS_PROFILE_NAME"] == "Y"){
												CSaleOrderPropsValue::Add(array(
												   "ORDER_ID" => $ORDER_ID,
												   "ORDER_PROPS_ID" => $props["ID"],
												   "NAME" => $props["NAME"],
												   "CODE" => $props["CODE"],
												   "VALUE" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"])
												));
											}else if(strtoupper($props["CODE"]) == "TELEPHONE" || strtoupper($props["CODE"]) == "PHONE" || $props["IS_PHONE"] == "Y"){
												CSaleOrderPropsValue::Add(array(
												   "ORDER_ID" => $ORDER_ID,
												   "ORDER_PROPS_ID" => $props["ID"],
												   "NAME" => $props["NAME"],
												   "CODE" => $props["CODE"],
												   "VALUE" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"])
												));											
											}
										}							
										
										CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID(), False);
										
										Add2BasketByProductID(
											$arElement["ID"], 
											1, 
											array("ORDER_ID" => $ORDER_ID), 
											array()
										);
										
										CSaleBasket::OrderBasket($ORDER_ID, $USER_ID, $_GET["SITE_ID"]);


									}else{
										$result = array(
											"heading" => "Ошибка",
											"message" => "Ошибка, служба доставки не создана!",
											"success" => false
										);
									}

								}else{
									$result = array(
										"heading" => "Ошибка",
										"message" => "Ошибка, платежная система не создана!",
										"success" => false
									);
								}

							}
							if(empty($result)){
								$result = array(
									"heading" => "Ваш заказ успешно отправлен",
									"message" => "В ближайшее время Вам перезвонит наш менеджер для уточнения деталей заказа.",
									"success" => true
								);
							}
						}else{

							$result = array(
								"heading" => "Ошибка",
								"message" => "Ошибка, товар не найден!",
								"success" => false
							);

						}

					}

				}else{
					$result = array(
						"heading" => "Ошибка",
						"message" => "Ошибка, заполните обязательные поля!",
						"success" => false
					);
				}
			
			echo jsonEn($result);

		}
	}
	else{
		die(false);
	}
}

function priceFormat($data, $str = ""){
	$price = explode(".", $data);
	$strLen = strlen($price[0]);
	for ($i = $strLen; $i > 0 ; $i--) {
		$str .=	(!($i%3) ? " " : "").$price[0][$strLen - $i];
	}
	return $str.($price[1] > 0 ? ".".$price[1] : "");
}

function jsonEn($data, $multi = false){
	if(!$multi){
		foreach ($data as $index => $arValue) {
			$arJsn[] = '"'.$index.'" : "'.addslashes($arValue).'"';
		}
		return  "{".implode($arJsn, ",")."}";
	}
}

function jsonMultiEn($data){
	if(is_array($data)){
		if(count($data) > 0){
			$arJsn = "[".implode(getJnLevel($data, 0), ",")."]";
		}else{
			$arJsn = implode(getJnLevel($data), ",");
		}
	}
	return str_replace(array("\t", "\r", "\n", "'"), "", trim($arJsn));
}

function getJnLevel($data, $level = 1, $arJsn = array()){
	foreach ($data as $i => $arNext) {
		if(!is_array($arNext)){
			$arJsn[] = '"'.$i.'":"'.addslashes(trim(str_replace("'", "", $arNext))).'"';
		}else{
			if($level === 0){
				$arJsn[] = "{".implode(getJnLevel($arNext), ",")."}";
			}else{
				$arJsn[] = '"'.$i.'":{'.implode(getJnLevel($arNext),",").'}';
			}
		}
	}
	return $arJsn;
}

function getLastOffer($arLastFilter, $arProps, $productID, $priceCurrency, $enableMorePictures = false){
	
	if(!empty($_GET["product_width"]) && !empty($_GET["product_height"])){
		$arProductImage = array("width" => $_GET["product_width"], "height" => $_GET["product_height"]);
	}else{
		$arProductImage = array("width" => 220, "height" => 200);
	}

	$rsLastOffer = CIBlockElement::GetList(
		array(),
		$arLastFilter, false, false,
		array(
			"ID",
			"NAME",
			"IBLOCK_ID",
			"DETAIL_PICTURE",
			"DETAIL_PAGE_URL",
			"CATALOG_QUANTITY",
			"CATALOG_AVAILABLE"
		)
	);
	if(!$rsLastOffer->SelectedRowsCount()){
		$st = array_pop($arLastFilter);
		$mt = array_pop($arProps);
		return getLastOffer($arLastFilter, $arProps, $productID, $priceCurrency, $enableMorePictures);
	}else{
		if($obReturnOffer = $rsLastOffer->GetNextElement()){
			
			$productFilelds = $obReturnOffer->GetFields();
			if($enableMorePictures){
				$productProperties = $obReturnOffer->GetProperties();
			}

			$productFilelds["IMAGES"] = array();
			$rsProductSelect = array("ID", "IBLOCK_ID", "DETAIL_PICTURE");

			if(!empty($productFilelds["DETAIL_PICTURE"])){
				
				$arImageResize = CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], $arProductImage, BX_RESIZE_IMAGE_PROPORTIONAL, false);
				$productFilelds["PICTURE"] = $arImageResize["src"];

				$productFilelds["IMAGES"][] = array(
					"SMALL_PICTURE" => CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
					"LARGE_PICTURE" => CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
					"SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
 				);	
			}

			if(!empty($productProperties["MORE_PHOTO"]["VALUE"])){
				foreach ($productProperties["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
					$productFilelds["IMAGES"][] = array(
						"SMALL_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
						"LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
						"SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
					);	
				}				
			}

			if(empty($productFilelds["DETAIL_PICTURE"]) || empty($productProperties["MORE_PHOTO"]["VALUE"])){
				if($rsProduct = CIBlockElement::GetList(array(), array("ID" => $productID), false, false, $rsProductSelect)->GetNextElement()){
					
					$rsProductFields = $rsProduct->GetFields();
					if($enableMorePictures){
						$rsProductProperties = $rsProduct->GetProperties();
					}

					if(!empty($rsProductFields["DETAIL_PICTURE"]) || !empty($rsProductProperties["MORE_PHOTO"]["VALUE"])){
						if(!empty($rsProductFields["DETAIL_PICTURE"]) && empty($productFilelds["DETAIL_PICTURE"])){
							
							$arImageResize = CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], $arProductImage, BX_RESIZE_IMAGE_PROPORTIONAL, false);
							$productFilelds["PICTURE"] = $arImageResize["src"];
							
							array_unshift($productFilelds["IMAGES"], array(
								"SMALL_PICTURE" => CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
								"LARGE_PICTURE" => CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
								"SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
							));

						}
						if(!empty($rsProductProperties["MORE_PHOTO"]["VALUE"]) && empty($productProperties["MORE_PHOTO"]["VALUE"])){
							foreach ($rsProductProperties["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
								if(!empty($nextPictureID)){
									$productFilelds["IMAGES"][] = array(
										"SMALL_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
										"LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
										"SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
									);	
								}
							}
						}
					}else{
						if(empty($productFilelds["IMAGES"])){
							$productFilelds["IMAGES"][0]["SMALL_PICTURE"] = array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png");  
							$productFilelds["IMAGES"][0]["LARGE_PICTURE"] = array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png");
							$productFilelds["IMAGES"][0]["SUPER_LARGE_PICTURE"] = array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png");   
						} 
					}
				}
			}

			if(empty($productFilelds["PICTURE"])){
				$productFilelds["PICTURE"] = SITE_TEMPLATE_PATH."/images/empty.png";
			}

			global $USER;
			$productFilelds["PRICE"] = CCatalogProduct::GetOptimalPrice($productFilelds["ID"], 1, $USER->GetUserGroupArray());
			$productFilelds["PRICE"]["DISCOUNT_PRICE"] = FormatCurrency($productFilelds["PRICE"]["DISCOUNT_PRICE"], $priceCurrency);
			$productFilelds["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = FormatCurrency($productFilelds["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $priceCurrency);
			$productFilelds["CAN_BUY"] = $productFilelds["CATALOG_AVAILABLE"];
			
			if(!empty($productFilelds["PRICE"]["DISCOUNT"])){
				unset($productFilelds["PRICE"]["DISCOUNT"]);
			}
			
			if(!empty($productFilelds["PRICE"]["DISCOUNT_LIST"])){
				unset($productFilelds["PRICE"]["DISCOUNT_LIST"]);
			}

			return array(
				"PRODUCT" => array_merge(
					$productFilelds, array(
						"PROPERTIES" => $obReturnOffer->GetProperties()
					)
				),
				"PROPERTIES" => $arProps
			);
		}
	}
}

?>