<?
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_NAME"] = "Туры (ym_tour)";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_BID"] = "Размер ставки на остальных местах размещения (кроме карточки модели).";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_CBID"] = "Размер ставки для карточки модели";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_FEE"] = "Размер комиссии на товарное предложение, участвующее в программе \"Заказ на Маркете\"";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_AVAILABLE"] = "Cтатус доступности товара";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_URL"] = "URL страницы товара.<br/>Максимальная длина URL — 512 символов.<br/>Необязательный элемент для магазинов-салонов";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_CATEGORY"] = "Идентификатор категории товара.<br/>Товарное предложение может принадлежать только одной категории.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_NAME"] = "Наименование товарного предложения.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DESCRIPTION"] = "Описание товара.<br/>Длина текста не более 175 символов (не включая знаки препинания),<br/> запрещено использовать HTML-теги <br/>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_AGE"] = "Возрастная категория товара";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_STORE"] = "Покупка соответствующего товара в розничном магазине<br/>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PICKUP"] = "Возможность зарезервировать выбранный товар и забрать его самостоятельно<br/>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DELIVERY"] = "Возможность доставки<br/>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_WORLDREGION"] = "Часть света";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_COUNTRY"] = "Страна";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_REGION"] = "Курорт или город";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DAYS"] = "Количество дней тура.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DATATOUR"] = "Даты заездов";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_HOTELSTARS"] = "Звезды отеля";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_ROOM"] = "Тип комнаты (SNG, DBL, ...)";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_MEAL"] = "Тип питания (All, HB, ...).";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_INCLUDED"] = "Что включено в стоимость тура.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_TRANSPORT"] = "Транспорт.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PRICEMIN"] = "Минимальная цена";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PRICEMAX"] = "Максимальная цена";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_OPTIONS"] = "Опции";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";

$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_TOUR_PORTAL_REQUIREMENTS"] = "https://yandex.ru/support/partnermarket/export/tours.xml";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_TOUR_PORTAL_VALIDATOR"] = "https://webmaster.yandex.ru/xsdtest.xml";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_TOUR_EXAMPLE"] = "
<offer id=\"12341\" type=\"tour\" available=\"true\" bid=\"71\">
    <url>http://best.seller.ru/product_page.asp?pid=12344</url>
    <price>24129</price>
    <currencyId>USD</currencyId>
    <categoryId>6</categoryId>
    <picture>http://best.seller.ru/img/device12345.jpg</picture>
    <store>false</store>
    <pickup>true</pickup>
    <delivery>false</delivery>
    <delivery-options> 
        <option cost=\"300\" days=\"0\"/> 
    </delivery-options>
    <worldRegion>Африка</worldRegion>
    <country>Египет</country>
    <region>Хургада</region>
    <days>7</days>
    <dataTour>2012-01-01 12:00:00</dataTour>
    <dataTour>2012-01-08 12:00:00</dataTour>
    <name>Hilton</name>
    <hotel_stars>5*****</hotel_stars>
    <room>SNG</room>
    <meal>ALL</meal>
    <included>авиаперелет, трансфер, проживание, питание, страховка</included>
    <transport>Авиа</transport>
    <description>Отдых в Египте.</description>
    <price_min>24000</price_min>
    <price_max>25000</price_max>
    <options>?</options>
</offer>
";
?>