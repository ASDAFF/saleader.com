<?
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_NAME"] = "Лекарства (ym_medicine)";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_BID"] = "Размер ставки на остальных местах размещения (кроме карточки модели).";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CBID"] = "Размер ставки для карточки модели";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_FEE"] = "Размер комиссии на товарное предложение, участвующее в программе \"Заказ на Маркете\"";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_AVAILABLE"] = "Срок поставки товара в пункт выдачи. Возможные значения:<br/>true — товар поставляется в течение 0–2 дней;<br/>false — товар поставляется в течение 3–60 дней.";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_URL"] = "URL страницы товара.<br/>Максимальная длина URL — 512 символов.<br/>Необязательный элемент для магазинов-салонов";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_OLDPRICE"] = "Старая цена на товар, которая обязательно должна быть выше новой цены (<price>). Параметр <oldprice> необходим для автоматического расчета скидки на товар";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CATEGORY"] = "Идентификатор категории товара.<br/>Товарное предложение может принадлежать только одной категории.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_NAME"] = "Название товарного предложения: тип товара (БАД, лекарственный препарат, медицинское изделие и т. п.) + торговое название препарата.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_DESCRIPTION"] = "Описание товара.<br/>Длина текста не более 175 символов (не включая знаки препинания),<br/> запрещено использовать HTML-теги <br/>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_STORE"] = "Покупка соответствующего товара в розничном магазине<br/>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PICKUP"] = "Возможность забрать товар в пункте выдачи (самовывоз).<br/><b>Внимание!</b> Значение обязательно должно бытье true.";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_DELIVERY"] = "Возможность курьерской доставки товара.<br/><b>Внимание!</b> Значение обязательно должно быть false.";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_VENDOR"] = "Производитель. Не отображается в названии предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_VENDORCODE"] = "Код производителя для данного товара.";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_SALESNOTES"] = "Элемент используется для отражения информации<br/>о минимальной сумме заказа, минимальной партии<br/>товара или необходимости предоплаты, а так же для<br/>описания акций, скидок и распродаж.<br/>Допустимая длина текста в элементе — 50 символов";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_COUNTRYOFORIGIN"] = "Страны производства товара.<br/> Список стран доступен по адресу:<br/>http://partner.market.yandex.ru/pages/help/Countries.pdf";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_BARCODE"] = "Штрихкод товара, указанный производителем";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CPA"] = "<b>Участие товарных предложений в программе «Заказ на Маркете»:</b><br/>
0 — все товары из прайс-листа не участвуют в программе «Заказ на Маркете»;<br/> 
1 — все товары из прайс-листа участвуют в программе «Заказ на Маркете».";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PARAM"] = "Характеристики и параметры лекарства. Для описания каждого параметра используется отдельный элемент param. Элемент offer может содержать несколько элементов param.<br/>Один из параметров, который следует указать в <param>, — код лекарства в Едином городском классификаторе (ЕГК).";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_EXPIRY"] = "Срок годности.<br/>Значение элемента должно быть в формате ISO8601";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_WEIGHT"] = "Веса товара.<br/><b>Вес указывается в килограммах с учетом упаковки</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_DIMENSIONS"] = "Габариты товара (длина, ширина, высота) в упаковке.<br/><b>Размеры указываются в сантиметрах</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_MEDICINE_PORTAL_REQUIREMENTS"] = "https://yandex.ru/support/partnermarket/export/medicine.xml";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_MEDICINE_PORTAL_VALIDATOR"] = "https://webmaster.yandex.ru/xsdtest.xml";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_MEDICINE_EXAMPLE"] = "<offer id=\"12345\" available=\"true\" type=\"medicine\" bid=\"80\" cbid=\"90\">
    <currencyId>RUB</currencyId>
    <categoryId>4062</categoryId>
    <name>БАД Селен-актив n30 таблетки</name>
    <vendor>ОАО ДИОД Завод эко.тех.и питания</vendor>
    <vendorCode>123456</vendorCode>
    <url>http://www.example-apteka.ru/selen-aktiv.html</url>
    <picture>http://www.example-apteka.ru/selen-aktiv.jpg</picture>
    <price>1000</price>
    <delivery>false</delivery>
    <pickup>true</pickup>
    <store>true</store>
    <barcode>4981046350037</barcode>
    <sales_notes>Самовывоз возможен через 3 часа после заказа</sales_notes>
    <description>Биоусвояемый селен 50 мкг, витамин С 50 мг. Селен-актив обеспечивает оптимальную и постоянную антиоксидантную защиту.</description>
    <country_of_origin>Россия</country_of_origin>
    <expiry>P1Y2M10DT2H30M</expiry>
    <param name=\"Побочные действия\">нет</param>
    <param name=\"Код egk\">123456</param>
</offer>";
?>