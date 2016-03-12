<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?error_reporting(0);?>
<?if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")){
	if($_GET["act"] == "selectSku"){
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

			if($OPTION_ADD_CART == N){
				$arTmpFilter[">CATALOG_QUANTITY"] = 0;
			}

			$arProps = array();
			$arParams =  array();
			$arTmpParams = array();
			$arCastFilter = array();
			$arProperties = array();
			$arPropActive = array();
			$arAllProperties = array();

			$PROPS = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["props"]) : $_GET["props"];
			$PARAMS = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["params"]) : $_GET["params"];

			//normalize property
			$exProps = explode(";", trim($PROPS, ";"));
			$exParams = explode(";", trim($PARAMS, ";"));

			if(empty($exProps) || empty($exParams))
				die("error #1 | Empty params or propList _no valid data");

			foreach ($exProps as $ip => $sProp) {
				$msp = explode(":", $sProp);
				$arProps[$msp[0]][$msp[1]] = D;
			}

			foreach ($exParams as $ip => $pProp) {
				$msr = explode(":", $pProp);
				$arParams[$msr[0]] = $msr[1];
				$arTmpParams["PROPERTY_".$msr[0]."_VALUE"] = $msr[1];
			}

			$arFilter = array_merge($arTmpFilter, array_slice($arTmpParams, 0, $_GET["level"]));

			$rsOffer = CIBlockElement::GetList(
				array(),
				$arFilter, false, false,
				array(
					"ID",
					"NAME",
					"IBLOCK_ID"
				)
			);

			while($obOffer = $rsOffer->GetNextElement()){
				$arFilterProp = $obOffer->GetProperties();
				foreach ($arFilterProp as $ifp => $arNextProp) {
					if($arNextProp["PROPERTY_TYPE"] == "L" && !empty($arNextProp["VALUE"])){
						$arProps[$arNextProp["CODE"]][$arNextProp["VALUE"]] = "N";
						$arProperties[$arNextProp["CODE"]] = $arNextProp["VALUE"];
					}
				}
			}

			if(!empty($arParams)){
				foreach ($arParams as $propCode => $arField) {
					if($arProps[$propCode][$arField] == "N"){
					 	$arProps[$propCode][$arField] = "Y";
					}else{
						if(!empty($arProps[$propCode])){
							foreach ($arProps[$propCode] as $iCode => $upProp) {
								if($upProp == "N"){
									$arProps[$propCode][$iCode] = "Y";
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
						if($arNextPropValue == "Y"){
							$arPropActive[$ip] = $inv;
						}
					}
				}
			}

			$arLastFilter = array(
				"ACTIVE" => Y,
				"IBLOCK_ID" => intval($_GET["iblock_id"]),
				"PROPERTY_".intval($_GET["prop_id"]) => intval($_GET["product_id"])
			);

			if($OPTION_ADD_CART == "N" ){
				$arTmpFilter[">CATALOG_QUANTITY"] = 0;
			}

			foreach ($arPropActive as $icp => $arNextProp) {
				$arLastFilter["PROPERTY_".$icp."_VALUE"] = $arNextProp;
			}

			$arLastOffer = getLastOffer($arLastFilter, $arProps, $_GET["product_id"], $OPTION_CURRENCY);


			if(!empty($arLastOffer["PRODUCT"]["DETAIL_PICTURE"]) || !empty($arLastOffer["PRODUCT"]["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
				$arLastOffer["PRODUCT"]["IMAGES"] = array();
			}

			if(!empty($arLastOffer["PRODUCT"]["DETAIL_PICTURE"])){			
				array_push($arLastOffer["PRODUCT"]["IMAGES"], picture_separate_array_push($arLastOffer["PRODUCT"]["DETAIL_PICTURE"]));
			}

			if(!empty($arLastOffer["PRODUCT"]["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
				foreach ($arLastOffer["PRODUCT"]["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
					array_push($arLastOffer["PRODUCT"]["IMAGES"], picture_separate_array_push($nextPictureID));
				}
			}

			$arLastOffer["PRODUCT"]["CAN_BUY"] = $OPTION_ADD_CART == "Y" ? true : false;

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
}

function picture_separate_array_push($pictureID, $arPushImage = array()){
	$arPushImage["SMALL_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);  
	$arPushImage["MEDIUM_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => 500, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);  
	$arPushImage["LARGE_IMAGE"] = CFile::GetByID($pictureID)->Fetch();
	$arPushImage["LARGE_IMAGE"]["SRC"] = CFile::GetPath($arPushImage["LARGE_IMAGE"]["ID"]);
	return $arPushImage;
}

function getLastOffer($arLastFilter, $arProps, $productID, $priceCurrency){
	$rsLastOffer = CIBlockElement::GetList(
		array(),
		$arLastFilter, false, false,
		array(
			"ID",
			"NAME",
			"IBLOCK_ID",
			"DETAIL_PICTURE",
			"DETAIL_PAGE_URL",
			"PREVIEW_TEXT",
			"DETAIL_TEXT",
			"CATALOG_QUANTITY"
		)
	);
	if(!$rsLastOffer->SelectedRowsCount()){
		$st = array_pop($arLastFilter);
		$mt = array_pop($arProps);
		return getLastOffer($arLastFilter, $arProps, $productID, $priceCurrency);
	}else{
		if($obReturnOffer = $rsLastOffer->GetNextElement()){
			$productFilelds = $obReturnOffer->GetFields();
			$productFilelds["IMAGES"] = array();
			if(!empty($productFilelds["DETAIL_PICTURE"])){
				array_push($productFilelds["IMAGES"], picture_separate_array_push($productFilelds["DETAIL_PICTURE"]));
			}else{
				$rsProduct = CIBlockElement::GetList(
					array(),
					array("ID" => $productID), false, false,
					array("DETAIL_PICTURE")
				)->GetNext();
				if(!empty($rsProduct["DETAIL_PICTURE"])){
					array_push($productFilelds["IMAGES"], picture_separate_array_push($rsProduct["DETAIL_PICTURE"]));
				}else{
					$productFilelds["IMAGES"]["SMALL_IMAGE"] = array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png");  
					$productFilelds["IMAGES"]["MEDIUM_IMAGE"] = array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png");   
					$productFilelds["IMAGES"]["LARGE_IMAGE"] = array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png"); 
				}
			}

			global $USER;
			$productFilelds["PRICE"] = CCatalogProduct::GetOptimalPrice($productFilelds["ID"], 1, $USER->GetUserGroupArray());
			$productFilelds["PRICE"]["DISCOUNT_PRICE"] = FormatCurrency($productFilelds["PRICE"]["DISCOUNT_PRICE"], $priceCurrency);
			$productFilelds["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = FormatCurrency($productFilelds["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $priceCurrency);
			
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
	return str_replace(array("\t", "\r", "\n"), "", $arJsn);
}

function getJnLevel($data, $level = 1, $arJsn = array()){
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
	return $arJsn;
}

?>