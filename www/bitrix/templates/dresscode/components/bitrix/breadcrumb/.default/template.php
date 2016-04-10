<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(empty($arResult))
	return "";
	
$strReturn = '<div id="breadcrumbs"><ul vocab="http://schema.org/" typeof="BreadcrumbList">';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
		$strReturn .= '<li  property="itemListElement" typeof="ListItem"><a  property="item" typeof="WebPage" href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a><meta property="position" content="'.($index+1).'"></li><li><span class="arrow"> &bull; </span> </li>';
	else
		$strReturn .= '<li  property="itemListElement" typeof="ListItem"><span property="name" class="changeName">'.$title.'</span> <meta property="position" content="'.($index+1).'"></li>';
}

$strReturn .= '</ul></div>';

return $strReturn;
?>