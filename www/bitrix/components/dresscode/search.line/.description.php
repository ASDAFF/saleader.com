<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
   "NAME" => GetMessage("NAME"),
   "DESCRIPTION" => GetMessage("DESCRIPTION"),
   "ICON" => "/images/search.gif",
   "PATH" => array(
      "ID" => "ELECTRO",
      "CHILD" => array(
         "ID" => "searchLine",
         "NAME" => GetMessage("NAME"),
      )
   )
);
?>