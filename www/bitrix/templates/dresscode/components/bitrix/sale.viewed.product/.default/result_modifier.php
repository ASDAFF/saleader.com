<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if(!empty($arResult)){
	foreach($arResult as $key => $val){
		
		$img = "";
		
		if ($val["DETAIL_PICTURE"] > 0)
			$img = $val["DETAIL_PICTURE"];
		elseif ($val["PREVIEW_PICTURE"] > 0)
			$img = $val["PREVIEW_PICTURE"];

		$file = CFile::ResizeImageGet($img, array('width' => $arParams["VIEWED_IMG_WIDTH"], 'height' => $arParams["VIEWED_IMG_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false);
		$file["src"] = !empty($file["src"]) ? $file["src"] : SITE_TEMPLATE_PATH."/images/empty.png";
		$val["PICTURE"] = $file;
		
		if(!empty($val["ID"])){
			$arResult["ITEMS"][$val["PRODUCT_ID"]] = $val;
			$arElementsID[$val["PRODUCT_ID"]] = $val["PRODUCT_ID"];
		}

	}
}

if(!empty($arElementsID)){
	$arSelect = Array("ID", "IBLOCK_ID", "*");
	$arFilter = Array("ID" => $arElementsID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while($ob = $res->GetNextElement()){ 
		$arFields = $ob->GetFields();  
		$arResult["ITEMS"][$arFields["ID"]]["PROPERTIES"] = $ob->GetProperties();
		$arResult["ITEMS"][$arFields["ID"]]["ARRAY_PRICE"] = CCatalogProduct::GetOptimalPrice($arFields["ID"], 1, $USER->GetUserGroupArray());
	}
}
?>