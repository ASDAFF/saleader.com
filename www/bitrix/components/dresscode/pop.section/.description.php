<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
   "NAME" => GetMessage("NAME"),
   "DESCRIPTION" => GetMessage("DESCRIPTION"),
   "ICON" => "/images/offers.gif",
   "PATH" => array(
      "ID" => "ELECTRO",
      "CHILD" => array(
         "ID" => "electroCarousel",
         "NAME" => GetMessage("NAME")
      )
   )
);
?>