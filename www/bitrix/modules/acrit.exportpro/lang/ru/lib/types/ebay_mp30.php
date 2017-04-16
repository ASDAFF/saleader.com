<?
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_NAME"] = "Экспорт на Ebay через сервис MP30";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_AVAILABLE"] = "Cтатус доступности товара";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_CATEGORY"] = "Идентификатор категории товара.<br/>Товарное предложение может принадлежать только одной категории.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_NAME"] = "Наименование товарного предложения.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_DESCRIPTION"] = "Описание товара.<br/>Длина текста не более 175 символов (не включая знаки препинания),<br/> запрещено использовать HTML-теги <br/>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_STOCK"] = "Остаток";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_CONDITION"] = "Состояние";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM"] = "Характеристики товара";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_1"] = "Бренд";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_2"] = "Коллекция";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_3"] = "Тип механизма";
$MESS["ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_4"] = "Серийный номер";
$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_1_NAME"] = "Бренд";
$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_2_NAME"] = "Коллекция";
$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_3_NAME"] = "Тип механизма";
$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_4_NAME"] = "Серийный номер";
$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PORTAL_REQUIREMENTS"] = "http://pages.ebay.com/ru/ru-ru/kak-prodavat-na-ebay-spravka/mip-neobhodimie-dannie.html";
$MESS["ACRIT_EXPORTPRO_TYPE_EBAY_MP30_EXAMPLE"] = "
<offer available=\"true\" id=\"32865619\">
    <currencyId>RUR</currencyId>
    <categoryId>3166042</categoryId>
    <price>7160</price>
    <picture>http://site.ru/images/products/32865619_1.jpg</picture>
    <picture>http://site.ru/images/products/32865619_2.jpg</picture>
    <picture>http://site.ru/images/products/32865619_3.jpg</picture>
    <picture>http://site.ru/images/products/32865619_4.jpg</picture>
    <name>Название товара</name>
    <description><![CDATA[Подробное описание модели]]></description>
    <param name=\"Бренд\">Diesel</param>
    <param name=\"Коллекция\">Digital</param>
    <param name=\"Тип механизма\">кварцевые</param>
    <param name=\"Источник энергии\">от батарейки</param>
    <param name=\"Водозащита\">WR 50</param>
    <param name=\"Дисплей\">цифры</param>
    <param name=\"Серийный номер\">есть</param>
</offer>
";
?>