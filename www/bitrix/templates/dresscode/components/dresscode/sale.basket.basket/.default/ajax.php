<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?include("lang/".LANGUAGE_ID."/template.php");?>
<?if(!empty($_GET["act"]) && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale") && CModule::IncludeModule("iblock")){

	if($_GET["act"] == "upd"){
		echo CSaleBasket::Update(intval($_GET['id']), array(
		   "QUANTITY" => intval($_GET["q"]),
		   "DELAY" => "N"
		));
	}elseif($_GET["act"] == "del"){
		echo CSaleBasket::Delete(intval($_GET['id']));
	}
	elseif($_GET["act"] == "emp"){
		echo CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
	}elseif($_GET["act"] == "coupon" && $_GET["value"]){
		$couponResult = CCatalogDiscountCoupon::SetCoupon($_GET["value"]);
		echo $couponResult === false ? CCatalogDiscountCoupon::ClearCoupon() : $couponResult;
	}

	// re calc delivery

	elseif ($_GET["act"] == "getProductPrices"){
	
		global $USER;

		$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

		$arID = array();
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
			"SITE_ID" => $_GET["SITE_ID"],
			"USER_ID" => $USER->GetID(),
			"ORDER_PRICE" => $arResult["SUM"],
			"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
			"BASKET_ITEMS" => $arResult["ITEMS"],
			"PERSON_TYPE_ID" => $_GET["PERSON_TYPE_ID"],
			"PAY_SYSTEM_ID" => $_GET["PAY_SYSTEM_ID"],
			"DELIVERY_ID" => $_GET["DELIVERY_ID"]
	   );
	   
	   $arOptions = array(
	      "COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
	   );
	   
	   $arErrors = array();
	   $allSum = 0;

		CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
		if(!empty($arOrder["BASKET_ITEMS"])){ 
			foreach ($arOrder["BASKET_ITEMS"] as $arItem){
				$arItem["~PRICE"] = round($arItem["PRICE"]);
				$arItem["~BASE_PRICE"] = $arItem["BASE_PRICE"];
				$arItem["SUM"] = FormatCurrency($arItem["PRICE"] * $arItem["QUANTITY"], $OPTION_CURRENCY);
				$arItem["PRICE"] = FormatCurrency($arItem["PRICE"], $OPTION_CURRENCY);
				$arItem["BASE_PRICE"] = FormatCurrency($arItem["BASE_PRICE"], $OPTION_CURRENCY);
				$arReturnItems[$arItem["ID"]] = $arItem;

			}
		}

		echo jsonMultiEn($arReturnItems);
	}

	elseif ($_GET["act"] == "reCalcDelivery") {
		
		global $USER;
		
		$FUSER_ID = CSaleBasket::GetBasketUserID();
		$OPTION_CURRENCY  = $arResult["BASE_LANG_CURRENCY"] = CCurrency::GetBaseCurrency();
		$SITE_ID = $_GET["SITE_ID"];


		CSaleBasket::UpdateBasketPrices($FUSER_ID, $SITE_ID);

		$res = CSaleBasket::GetList(
			array(
				"ID" => "ASC"
			),
			array(
					"FUSER_ID" => CSaleBasket::GetBasketUserID(),
					"LID" => $SITE_ID,
					"ORDER_ID" => "NULL"
				),
			false,
			false,
			array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY",
				"CAN_BUY", "PRICE", "WEIGHT", "NAME", "CURRENCY", "CATALOG_XML_ID", "VAT_RATE",
				"NOTES", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "DIMENSIONS", "TYPE", "SET_PARENT_ID", "DETAIL_PAGE_URL", "*"
			)
		);

		if($res->SelectedRowsCount() <= 0){
			exit(
				jsonEn(
					array(
						"ERROR" => GetMessage("ORDER_EMPTY")
					)
				)
			);
		}

		while ($arRes = $res->GetNext()){

			$ORDER_DISCOUNT  += ($arRes["QUANTITY"] * $arRes["DISCOUNT_PRICE"]);
			$ORDER_WEIGHT    += ($arRes["WEIGHT"] * $arRes["QUANTITY"]);
			$ORDER_PRICE     += ($arRes["PRICE"] * $arRes["QUANTITY"]);
			$ORDER_QUANTITY  += $arRes["QUANTITY"];
			$ORDER_MESSAGE   .= "<tr><td>".$arRes["NAME"]."</td><td>".$arRes["QUANTITY"]."</td><td>".SaleFormatCurrency($arRes["PRICE"], $arRes["CURRENCY"])." ".$arRes["CURRENCY"]."</td></tr>";

			if (!CSaleBasketHelper::isSetItem($arRes))
				$arResult["BASKET_ITEMS"][$arRes["ID"]] = $arRes;

			$arDim = $arRes["DIMENSIONS"] = $arRes["~DIMENSIONS"];

			if(is_array($arDim)){
				$arResult["MAX_DIMENSIONS"] = CSaleDeliveryHelper::getMaxDimensions(
					array(
						$arRes["WIDTH"],
						$arRes["HEIGHT"],
						$arRes["LENGTH"]
						),
					$arResult["MAX_DIMENSIONS"]);

				$arResult["ITEMS_DIMENSIONS"][] = $arDim;
			}

		}

		if(!empty($_GET["LOCATION_ID"])){
			
			$dbLoc = CSaleLocation::GetList(array(), array("ID" => $_GET["LOCATION_ID"]), false, false, array("*"));
			if($arLoc = $dbLoc->Fetch()){
				$arResult["LOCATION"] = $arLoc;
				$arUserResult["DELIVERY_LOCATION"] = $arLoc["ID"];
				$arUserResult["DELIVERY_LOCATION_BCODE"] = $arLoc["CODE"];
			}

			$arLocs = CSaleLocation::GetLocationZIP($arUserResult["DELIVERY_LOCATION"]); 
			if(!empty($arLocs)){
				$arLocs = $arLocs->Fetch();
			}

			$locFrom = COption::GetOptionString("sale", "location", false, $SITE_ID);


			$dbPay = CSalePaySystem::GetList(
				$arOrder = Array(
					"SORT" => "ASC",
					"PSA_NAME" => "ASC"
				),
				Array(
					"ACTIVE" => "Y",
					"PERSON_TYPE_ID" => $_GET["PERSON_TYPE"]
				)
			);

			while ($arPay = $dbPay->Fetch()){
			
				if(empty($arResult["PAYSYSTEM"]["FIRST_ID"])){
					$arResult["PAYSYSTEM"]["FIRST_ID"] = $arPay["ID"];
				}
				
				$arResult["PAYSYSTEM"][$arPay["ID"]] = $arPay;
			
			}

			$_GET["PAYSYSTEM_ID"] = !empty($arResult["PAYSYSTEM"][$_GET["PAYSYSTEM_ID"]]) ? $_GET["PAYSYSTEM_ID"] : $arResult["PAYSYSTEM"]["FIRST_ID"];

			$arFilter = array(
				"COMPABILITY" => array(
					"WEIGHT" => $ORDER_WEIGHT,
					"PRICE" => $ORDER_PRICE,
					"LOCATION_FROM" => $locFrom,
					"LOCATION_TO" => $arUserResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => !empty($arLocs["ZIP"]) ? $arLocs["ZIP"] : false,
					"MAX_DIMENSIONS" => $arResult["MAX_DIMENSIONS"],
					"ITEMS" => $arResult["BASKET_ITEMS"]
				)
			);

			$bFirst = true;
			$bFound = false;

			if(!$bFound && !empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") !== false){
				$arUserResult["DELIVERY_ID"] = "";
				$arResult["DELIVERY_PRICE"] = 0;
				$arResult["DELIVERY_PRICE_FORMATED"] = "";
			}

			//select delivery to paysystem
			$arUserResult["PERSON_TYPE_ID"] = $_GET["PERSON_TYPE"];
			$arUserResult["PAY_SYSTEM_ID"] = IntVal($_GET["PAYSYSTEM_ID"]);
			$bShowDefaultSelected = True;
			$arD2P = array();
			$arP2D = array();
			$delivery = "";
			$bSelected = false;

			$dbRes = CSaleDelivery::GetDelivery2PaySystem(array());
			while ($arRes = $dbRes->Fetch()){
				if($arRes["PAYSYSTEM_ID"] == $_GET["PAYSYSTEM_ID"]){
					$arRes["DELIVERY_ID"] = $arRes["DELIVERY_ID"];

					if(!empty($arRes["DELIVERY_PROFILE_ID"])){
						$arRes["DELIVERY_ID"] = $arRes["DELIVERY_ID"].":".$arRes["DELIVERY_PROFILE_ID"];
					}

					$arD2P[$arRes["DELIVERY_ID"]][$arRes["PAYSYSTEM_ID"]] = $arRes["PAYSYSTEM_ID"];
					$arP2D[$arRes["PAYSYSTEM_ID"]][$arRes["DELIVERY_ID"]] = $arRes["DELIVERY_ID"];
					$bShowDefaultSelected = False;
				}
			}

			$bFirst = True;
			$bFound = false;
			$bSelected = false;

			$shipment = CSaleDelivery::convertOrderOldToNew(array(
				"SITE_ID" => $SITE_ID,
				"WEIGHT" => $ORDER_WEIGHT,
				"PRICE" =>  $ORDER_PRICE,
				"LOCATION_TO" => isset($arUserResult["DELIVERY_LOCATION_BCODE"]) ? $arUserResult["DELIVERY_LOCATION_BCODE"] : $arUserResult["DELIVERY_LOCATION"],
				"LOCATION_ZIP" => $arLocs["ZIP"],
				"ITEMS" =>  $arResult["BASKET_ITEMS"],
				"CURRENCY" => $OPTION_CURRENCY
			));


			$arResult["TMP_DELIVERY"] = CSaleDelivery::DoLoadDelivery(isset($arUserResult["DELIVERY_LOCATION_BCODE"]) ? $arUserResult["DELIVERY_LOCATION_BCODE"] : $arUserResult["DELIVERY_LOCATION"], $arLocs["ZIP"], $arResult["ORDER_WEIGHT"], $arResult["SUM"], $arResult["BASE_LANG_CURRENCY"], $_GET["SITE_ID"]);
			if(!empty($arResult["TMP_DELIVERY"])){
				foreach ($arResult["TMP_DELIVERY"] as $did => $arNextDelivery) {
					$arResult["DELIVERY_PERSON_ARRAY_ID"][$arPersonType["ID"]][$arNextDelivery["ID"]] = true;
					$arNextDelivery["PRICE_FORMATED"] = str_replace("-", ".", CCurrencyLang::CurrencyFormat($arNextDelivery["PRICE"], $arNextDelivery["CURRENCY"]));
					$arResult["DELIVERY"][$arNextDelivery["ID"]] = $arNextDelivery;
				}
			}

			if ($arUserResult["PAY_SYSTEM_ID"] > 0 || strlen($arUserResult["DELIVERY_ID"]) > 0)
				if (strlen($arUserResult["DELIVERY_ID"]) > 0 && $arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
					$delivery = intval($arUserResult["DELIVERY_ID"]);

			if(DoubleVal($arResult["DELIVERY_PRICE"]) > 0)
				$arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $OPTION_CURRENCY);
			
			if(!empty($_GET["DELISYSTEM_ID"]) && !empty($arResult["DELIVERY"][intval($_GET["DELISYSTEM_ID"])])){
				$arDumpActiveDeli = $arResult["DELIVERY"][intval($_GET["DELISYSTEM_ID"])];
				unset($arResult["DELIVERY"][intval($_GET["DELISYSTEM_ID"])]);
				array_unshift($arResult["DELIVERY"], $arDumpActiveDeli);
			}

			echo jsonMultiEn($arResult["DELIVERY"]);

		}else{
			exit(
				jsonEn(
					array(
						"ERROR" => "Delivery error (3); Check field IS_LOCATION!!!."
					)
				)
			);
		}
	
	}

	##### ORDER #####

	elseif($_GET["act"] == "location" && !empty($_GET["q"])){

		$LOCATIONS = array();
		$CITY_NAME = (BX_UTF == 1) ? $_GET["q"] : iconv("UTF-8", "CP1251//IGNORE", $_GET["q"]);

		$dbLocations = CSaleLocation::GetList(
			array(
				"SORT" => "ASC",
				"COUNTRY_NAME_LANG" => "ASC",
				"CITY_NAME_LANG" => "ASC"
			),
			array(
				"LID" => LANGUAGE_ID,
				"%CITY_NAME" => $CITY_NAME
			),
			false,
			Array(
				"nPageSize" => 5,
			),
			array("*")
		);
		while ($arLoc = $dbLocations->Fetch()){
			if(!empty($arLoc["CITY_NAME"])){
				$arLoc["REGION_NAME"] = !empty($arLoc["REGION_NAME"]) ? $arLoc["REGION_NAME"].", " : "";
				$LOCATIONS[$arLoc["ID"]] = $arLoc["COUNTRY_NAME"].", ".$arLoc["REGION_NAME"].$arLoc["CITY_NAME"];
			}
		}
		echo jsonEn($LOCATIONS);
	}

	##### ORDER MAKE #####

	elseif ($_GET["act"] == "orderMake") {
		
		global $USER;
		
		$FUSER_ID = CSaleBasket::GetBasketUserID();
		$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();
		$SITE_ID = $_GET["SITE_ID"];
		$DELIVERY_ID = intval($_GET["DEVIVERY_TYPE"]);
		$DELIVERY_CODE = !empty($_GET["DEVIVERY_TYPE"]) ? \Bitrix\Sale\Delivery\Services\Table::getCodeById($_GET["DEVIVERY_TYPE"]) : null;
		$SAVE_FIELDS = TRUE;
		
		if(!empty($_GET["USER_NAME"])){
			$USER_NAME = explode(" ", $_GET["USER_NAME"]);
		}

		if(!empty($_GET["PERSONAL_MOBILE"])){
			$PERSONAL_MOBILE = $_GET["PERSONAL_MOBILE"];
		}

		if(!empty($_GET["PERSONAL_ADDRESS"])){
			$PERSONAL_ADDRESS = $_GET["PERSONAL_ADDRESS"];
		}

		$db_props = CSaleOrderProps::GetList(
	        array("SORT" => "ASC"),
	        array(
	                "PERSON_TYPE_ID" => intval($_GET["PERSON_TYPE"]),
	                "IS_EMAIL" => "Y",
	                "CODE" => "EMAIL"
	            ),
	        false,
	        false,
	        array()
	    );

		if ($props = $db_props->Fetch()){
			if($props["REQUIED"] == "Y"){
				$OPTION_REGISTER = "Y";
			}
		}

		if(!$USER->IsAuthorized()){
			if($OPTION_REGISTER == "Y"){
				$arResult = $USER->SimpleRegister($_GET["email"]);
				if($arResult["TYPE"] == "ERROR"){
					exit(
						jsonEn(
							array(
								"ERROR" => $arResult["MESSAGE"]
							)
						)
					);
				}
				//else{
					// CUser::SendUserInfo($USER->GetID(), $_GET["SITE_ID"], GetMessage("NEW_REGISTER"));
				// }
			}else{

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
				$SAVE_FIELDS = false;
			}
		}

		if(!empty($USER_NAME) && count($USER_NAME) > 0){

			if(!empty($USER_NAME[0])){
				$fields["NAME"] = BX_UTF == true ? $USER_NAME[0] : iconv("UTF-8","windows-1251//IGNORE", $USER_NAME[0]);
			}
			
			if(!empty($USER_NAME[1])){
				$fields["LAST_NAME"] = BX_UTF == true ? $USER_NAME[1] : iconv("UTF-8","windows-1251//IGNORE", $USER_NAME[1]);
			}

			if(!empty($USER_NAME[2])){
				$fields["SECOND_NAME"] = BX_UTF == true ? $USER_NAME[2] : iconv("UTF-8","windows-1251//IGNORE", $USER_NAME[2]);
			}

		}

		if(!empty($PERSONAL_MOBILE)){
			$fields["PERSONAL_MOBILE"] = BX_UTF == true ? $PERSONAL_MOBILE : iconv("UTF-8","windows-1251//IGNORE", $PERSONAL_MOBILE);
		}

		if(!empty($PERSONAL_ADDRESS)){
			$fields["PERSONAL_STREET"] = BX_UTF == true ? $PERSONAL_ADDRESS : iconv("UTF-8","windows-1251//IGNORE", $PERSONAL_ADDRESS);
		}
		
		$user = new CUser;
		$user->Update($USER->GetID(), $fields);

		$ORDER_PRICE    = 0;
		$ORDER_QUANTITY = 0;
		$ORDER_DISCOUNT = 0;
		$ORDER_WEIGHT   = 0;
		$ORDER_MESSAGE  = "<tr><td>".GetMessage("TOP_NAME")."</td><td>".GetMessage("TOP_QTY")."</td><td>".GetMessage("PRICE")."</td></tr>";



		CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), $SITE_ID);

		$arID = array();
		$arBasketItems = array();
		$arBasketOrder = array("NAME" => "ASC", "ID" => "ASC");
		$arBasketUser = array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => $SITE_ID, "ORDER_ID" => "NULL");
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

			if($dbBasketItems->SelectedRowsCount() <= 0){
				exit(
					jsonEn(
						array(
							"ERROR" => GetMessage("ORDER_EMPTY")
						)
					)
				);
			}

			while ($arItems = $dbBasketItems->Fetch()){
			    $arResult["SUM"]    += ($arItems["PRICE"]  * $arItems["QUANTITY"]);
			    $arResult["ORDER_WEIGHT"] += ($arItems["WEIGHT"] * $arItems["QUANTITY"]);
			    $arResult["ITEMS"][$arItems["PRODUCT_ID"]] = $arItems;
			    $arResult["ID"][] = $arItems["PRODUCT_ID"];
			}
		 
			$arOrder = array(
				"SITE_ID" => $SITE_ID,
				"USER_ID" => $USER_ID,
				"ORDER_PRICE" => $arResult["SUM"],
				"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
				"BASKET_ITEMS" => $arResult["ITEMS"],
				"PERSON_TYPE_ID" => intval($_GET["PERSON_TYPE"]),
				"PAY_SYSTEM_ID" => intval($_GET["PAY_TYPE"]),
				"DELIVERY_ID" => $DELIVERY_ID
			);

			$arOptions = array(
				"COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
			);

			$arErrors = array();

			CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

			foreach ($arOrder["BASKET_ITEMS"] as $arItem){

				$ORDER_DISCOUNT  += ($arItem["QUANTITY"] * $arItem["DISCOUNT_PRICE"]);
				$ORDER_WEIGHT    += ($arItem["WEIGHT"] * $arItem["QUANTITY"]);
				$ORDER_PRICE     += ($arItem["PRICE"] * $arItem["QUANTITY"]);
				$ORDER_QUANTITY  += $arItem["QUANTITY"];
				$ORDER_MESSAGE   .= "<tr><td>".$arItem["NAME"]."</td><td>".$arItem["QUANTITY"]."</td><td>".SaleFormatCurrency($arItem["PRICE"], $arItem["CURRENCY"])." ".$arItem["CURRENCY"]."</td></tr>";

				if (!CSaleBasketHelper::isSetItem($arItem))
					$arResult["BASKET_ITEMS"][$arRes["ID"]] = $arItem;

				$arDim = $arItem["DIMENSIONS"] = $arItem["~DIMENSIONS"];

				if(is_array($arDim)){
					$arResult["MAX_DIMENSIONS"] = CSaleDeliveryHelper::getMaxDimensions(
					array(
						$arItem["WIDTH"],
						$arItem["HEIGHT"],
						$arItem["LENGTH"]
					),
					$arResult["MAX_DIMENSIONS"]);

					$arResult["ITEMS_DIMENSIONS"][] = $arDim;
				}

			}
		}

		if(!empty($_GET["location"])){
			
			$dbLoc = CSaleLocation::GetList(array(), array("ID" => $_GET["location"]), false, false, array("*"));
			if($arLoc = $dbLoc->Fetch()){
				$arResult["LOCATION"] = $arLoc;
				$arUserResult["DELIVERY_LOCATION_ID"] = $arLoc["ID"];
				$arUserResult["DELIVERY_LOCATION"] = $arLoc["CODE"];			}

			$arLocs = CSaleLocation::GetLocationZIP($arUserResult["DELIVERY_LOCATION_ID"]); 
			if(!empty($arLocs)){
				$arLocs = $arLocs->Fetch();
			}

			$locFrom = COption::GetOptionString("sale", "location", false, $SITE_ID);

			$arFilter = array(
				"COMPABILITY" => array(
					"WEIGHT" => $ORDER_WEIGHT,
					"PRICE" => $ORDER_PRICE,
					"LOCATION_FROM" => $locFrom,
					"LOCATION_TO" => $arUserResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => !empty($arLocs["ZIP"]) ? $arLocs["ZIP"] : false,
					"MAX_DIMENSIONS" => $arResult["MAX_DIMENSIONS"],
					"ITEMS" => $arResult["BASKET_ITEMS"]
				)
			);

			$bFirst = true;
			$bFound = false;

			if(!$bFound && !empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") !== false){
				$arUserResult["DELIVERY_ID"] = "";
				$arResult["DELIVERY_PRICE"] = 0;
				$arResult["DELIVERY_PRICE_FORMATED"] = "";
			}

			//select delivery to paysystem
			$arUserResult["PERSON_TYPE_ID"] = $_GET["PERSON_TYPE"];
			$arUserResult["PAY_SYSTEM_ID"] = IntVal($_GET["PAY_TYPE"]);
			$arUserResult["DELIVERY_ID"] = $DELIVERY_ID;
			$bShowDefaultSelected = True;
			$arD2P = array();
			$arP2D = array();
			$delivery = "";
			$bSelected = false;

			$dbRes = CSaleDelivery::GetDelivery2PaySystem(array());
			while ($arRes = $dbRes->Fetch()){
				
				if(!empty($arRes["DELIVERY_PROFILE_ID"])){
					$arRes["DELIVERY_ID"] = $arRes["DELIVERY_ID"].":".$arRes["DELIVERY_PROFILE_ID"];
				}

				$arD2P[$arRes["DELIVERY_ID"]][$arRes["PAYSYSTEM_ID"]] = $arRes["PAYSYSTEM_ID"];
				$arP2D[$arRes["PAYSYSTEM_ID"]][$arRes["DELIVERY_ID"]] = $arRes["DELIVERY_ID"];
				$bShowDefaultSelected = False;
			}

			$bFirst = True;
			$bFound = false;
			$bSelected = false;

			$shipment = CSaleDelivery::convertOrderOldToNew(array(
				"SITE_ID" => $SITE_ID,
				"WEIGHT" => $ORDER_WEIGHT,
				"PRICE" =>  $ORDER_PRICE,
				"LOCATION_TO" => $arUserResult["DELIVERY_LOCATION"],
				"LOCATION_ZIP" => $arLocs["ZIP"],
				"ITEMS" =>  $arResult["BASKET_ITEMS"],
				"CURRENCY" => $OPTION_CURRENCY
			));

			$arResult["TMP_DELIVERY"] = CSaleDelivery::DoLoadDelivery(isset($arUserResult["DELIVERY_LOCATION_BCODE"]) ? $arUserResult["DELIVERY_LOCATION_BCODE"] : $arUserResult["DELIVERY_LOCATION"], $arLocs["ZIP"], $arResult["ORDER_WEIGHT"], $arResult["SUM"], $arResult["BASE_LANG_CURRENCY"], $_GET["SITE_ID"]);
			if(!empty($arResult["TMP_DELIVERY"])){
				foreach ($arResult["TMP_DELIVERY"] as $did => $arNextDelivery) {
					$arResult["DELIVERY_PERSON_ARRAY_ID"][$arPersonType["ID"]][$arNextDelivery["ID"]] = true;
					$arNextDelivery["PRICE_FORMATED"] = str_replace("-", ".", CCurrencyLang::CurrencyFormat($arNextDelivery["PRICE"], $OPTION_CURRENCY));
					$arResult["DELIVERY"][$arNextDelivery["ID"]] = $arNextDelivery;
				}
			}
			
			//uasort($arResult["DELIVERY"], array('CSaleBasketHelper', 'cmpBySort')); // resort delivery arrays according to SORT value

			//If none delivery service was selected let's select the first one.
			if(!$bSelected && !empty($arResult["DELIVERY"]))
			{
				reset($arResult["DELIVERY"]);
				$key = key($arResult["DELIVERY"]);
				$arResult["DELIVERY"][$key]["CHECKED"] = "Y";

				if(!isset($arResult["DELIVERY"][$key]["PRICE"]))
				{
					$deliveryObj = $arDeliveryServiceAll[$arResult["DELIVERY"][$key]["ID"]];
					$calcResult = $deliveryObj->calculate($shipment);
					$arResult["DELIVERY"][$key]["PRICE_FORMATED"] = SaleFormatCurrency($calcResult->getPrice(), $OPTION_CURRENCY);
					$arResult["DELIVERY"][$key]["CURRENCY"] = $OPTION_CURRENCY;
					$arResult["DELIVERY"][$key]["PRICE"] = $calcResult->getPrice();

					if(strlen($calcResult->getPeriodDescription()) > 0)
						$arResult["DELIVERY"][$key]["PERIOD_TEXT"] = $calcResult->getPeriodDescription();

					$arResult["DELIVERY_PRICE"] = roundEx($calcResult->getPrice(), SALE_VALUE_PRECISION);
				}
			}

			if ($arUserResult["PAY_SYSTEM_ID"] > 0 || strlen($arUserResult["DELIVERY_ID"]) > 0)
				if (strlen($arUserResult["DELIVERY_ID"]) > 0 && $arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
					$delivery = intval($arUserResult["DELIVERY_ID"]);

			if(DoubleVal($arResult["DELIVERY_PRICE"]) > 0)
				$arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $OPTION_CURRENCY);

		}else{
			exit(
				jsonEn(
					array(
						"ERROR" => "Delivery error (3); Check field IS_LOCATION!!!."
					)
				)
			);
		}

		$DELIVERY_INFO = $arResult["DELIVERY"][$DELIVERY_ID];

		if(!empty($DELIVERY_INFO)){

			foreach ($_GET as $i => $prop_value) {
				if(strstr($i, "ORDER_PROP")){

					$nextProp = CSaleOrderProps::GetByID(
						preg_replace('/[^0-9]/', '', $i)
					);

					if($nextProp["IS_LOCATION"] == "Y"){
						$prop_value = $_GET["location"];
					}

					$arResult["ORDER_PROP"][$nextProp["ID"]] = (BX_UTF == 1) ? $prop_value : iconv("UTF-8", "windows-1251//IGNORE", $prop_value);

				}
			}

			$arOrderDat = CSaleOrder::DoCalculateOrder(
				$SITE_ID,
				!empty($USER_ID) ? $USER_ID : IntVal($USER->GetID()),
				$arResult["BASKET_ITEMS"],
				$_GET["PERSON_TYPE"],
				$arResult["ORDER_PROP"],
				$DELIVERY_CODE,
				$_GET["PAY_TYPE"],
				array(),
				$arErrors,
				$arWarnings
			);
			
			$arOrderFields = array(
			   "LID" => $SITE_ID,
			   "PERSON_TYPE_ID" => $_GET["PERSON_TYPE"],
			   "PAYED" => "N",
			   "CANCELED" => "N",
			   "STATUS_ID" => "N",
			   "PRICE" => ($DELIVERY_INFO["PRICE"] + $ORDER_PRICE),
			   "CURRENCY" => $OPTION_CURRENCY,
			   "USER_ID" => !empty($USER_ID) ? $USER_ID : IntVal($USER->GetID()),
			   "PAY_SYSTEM_ID" => $_GET["PAY_TYPE"],
			   "PRICE_DELIVERY" => $DELIVERY_INFO["PRICE"],
			   "DELIVERY_ID" => $DELIVERY_CODE,
			   "DISCOUNT_VALUE" => $ORDER_DISCOUNT,
			   "TAX_VALUE" => 0.0,
			   "USER_DESCRIPTION" => (BX_UTF == 1) ? $_GET["COMMENT"] : iconv("UTF-8", "windows-1251//IGNORE", $_GET["COMMENT"])
			);
			
			$ORDER_ID = (int)CSaleOrder::DoSaveOrder($arOrderDat, $arOrderFields, 0, $arResult["ERROR"]);
			
			if(!empty($arResult["ERROR"])){
				exit(
					jsonEn(
						array(
							"ERROR" => $arResult["ERROR"]
						)
					)
				);	
			}

			if(empty($ORDER_ID)){
				exit(
					jsonEn(
						array(
							"ERROR" => GetMessage("ORDER_ERROR")
						)
					)
				);
			}

			$orderInfo = CSaleOrder::GetByID($ORDER_ID);
	
			CSaleBasket::OrderBasket(
				intval($ORDER_ID), intval($_SESSION["SALE_USER_ID"]), $SITE_ID, false
			);


			$PAYSYSTEM = CSalePaySystem::GetByID(
				$_GET["PAY_TYPE"],
				$_GET["PERSON_TYPE"]
			);
			
			$res = CSalePaySystemAction::GetList(
				array(),
				array(
						"PAY_SYSTEM_ID" => $PAYSYSTEM["ID"],
						"PERSON_TYPE_ID" => $_GET["PERSON_TYPE"]
					),
				false,
				false,
				array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "NAME", "ACTION_FILE", "RESULT_FILE", "NEW_WINDOW", "PARAMS", "ENCODING", "LOGOTIP")
			);

			if ($PAYSYSTEM_ACTION = $res->Fetch()){
				$dbOrder = CSaleOrder::GetList(
					array("DATE_UPDATE" => "DESC"),
					array(
						"LID" => $SITE_ID,
						"ID" => $ORDER_ID
					)
				);
				if($arOrder = $dbOrder->GetNext()){
					CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], $PAYSYSTEM_ACTION["PARAMS"]);
					$PAY_DATA = returnBuff($_SERVER["DOCUMENT_ROOT"].$PAYSYSTEM_ACTION["ACTION_FILE"]."/payment.php");
					echo jsonEn(
						array(
							"ORDER_ID" => $orderInfo["ACCOUNT_NUMBER"],
							"NEW_WINDOW" => $PAYSYSTEM_ACTION["NEW_WINDOW"],
							"PAYSYSTEM" => trim(
								str_replace(
									array("\n", "\r", "\t"), "", $PAY_DATA)
							)
						)
					);
				}
			}
		
			$arFields = Array(
				"ORDER_ID" => $orderInfo["ACCOUNT_NUMBER"],
				"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", $SITE_ID))),
				"ORDER_USER" => $USER->GetFormattedName(false),
				"PRICE" => SaleFormatCurrency($ORDER_PRICE, $OPTION_CURRENCY),
				"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
				"EMAIL" => !empty($_GET["email"]) ? $_GET["email"] : $USER->GetEmail(),
				"ORDER_LIST" => "<table width=100%>".$ORDER_MESSAGE."</table>",
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
				"DELIVERY_PRICE" => $DELIVERY_INFO["PRICE"],
			);

			$eventName = "SALE_NEW_ORDER";

			$bSend = true;
			foreach(GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
				if (ExecuteModuleEventEx($arEvent, Array($ORDER_ID, &$eventName, &$arFields))===false)
					$bSend = false;

			if($bSend){
				$event = new CEvent;
				$event->Send($eventName, $SITE_ID, $arFields, Y);
			}

			// CSaleMobileOrderPush::send("ORDER_CREATED", array("ORDER_ID" => $arFields["ORDER_ID"]));


		}else{
			exit(
				jsonEn(
					array(
						"ERROR" => "Delivery error (4); Check logo delivery system please."
					)
				)
			);
		}

	}
}else{
	die(false);
}

function jsonEn($data){
	foreach ($data as $index => $arValue) {
		$arJsn[] = '"'.$index.'" : "'.addslashes($arValue).'"';
	}
	return  "{".implode($arJsn, ",")."}";
}

function jsonMultiEn($data){
	if(is_array($data)){
		if(count($data) > 0){
			$arJsn = "[".implode(getJnLevel($data, 0), ",")."]";
		}else{
			$arJsn = implode(getJnLevel($data), ",");
		}
	}
	return str_replace(array("\t", "\r", "\n"), "", $arJsn);
}

function getJnLevel($data = array(), $level = 1, $arJsn = array()){
	if(!empty($data)){
		foreach ($data as $i => $arNext) {
			if(!is_array($arNext)){
				$arJsn[] = '"'.$i.'":"'.addslashes($arNext).'"';
			}else{
				if($level === 0){
					$arJsn[] = "{".implode(getJnLevel($arNext), ",")."}";
				}else{
					$arJsn[] = '"'.$i.'":{'.implode(getJnLevel($arNext),",").'}';
				}
				
			}
		}
	}
	return $arJsn;
}

function returnBuff($file){
	ob_start();
	include($file);
	$fData = ob_get_contents();
	ob_end_clean();
	return $fData;
}

?>