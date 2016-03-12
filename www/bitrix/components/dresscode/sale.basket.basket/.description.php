<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
   "NAME" => GetMessage("NAME"),
   "DESCRIPTION" => GetMessage("DESCRIPTION"),
   "ICON" => "/images/cart.gif",
   "PATH" => array(
      "ID" => "ELECTRO",
      "CHILD" => array(
         "ID" => "eCart",
         "NAME" => GetMessage("NAME")
      )
   )
);
?>