<?
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_NAME"] = "Билеты на мероприятие (event-ticket)";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_BID"] = "Основная ставка клика";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_AVAILABLE"] = "Cтатус доступности товара";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_URL"] = "URL страницы товара.<br>Максимальная длина URL — 512 символов.<br>Необязательный элемент для магазинов-салонов";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_CATEGORY"] = "Идентификатор категории товара.<br>Товарное предложение может принадлежать только одной категории.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_NAME"] = "Название мероприятия.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_DESCRIPTION"] = "Описание тура.<br>Длина текста не более 175 символов (не включая знаки препинания),<br> запрещено использовать HTML-теги <br>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_AGE"] = "Возрастная категория товара";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_STORE"] = "Покупка соответствующего товара в розничном магазине<br>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PICKUP"] = "Возможность зарезервировать выбранный товар и забрать его самостоятельно<br>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_DELIVERY"] = "Возможность доставки<br>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PLACE"] = "Место проведения.<br><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_HALL"] = "Зал";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_HALLPART"] = "Ряд и место в зале";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_DATE"] = "Дата и время сеанса";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_ISPREMIERE"] = "Признак премьерности мероприятия";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_ISKIDS"] = "Признак детского мероприятия";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_EVENTTICKET_PORTAL_REQUIREMENTS"] = "https://yandex.ru/support/partnermarket/offers.xml#event";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_EVENTTICKET_PORTAL_VALIDATOR"] = "https://webmaster.yandex.ru/xsdtest.xml";
$MESS["ACRIT_EXPORTPRO_TYPE_MARKET_EVENTTICKET_EXAMPLE"] = "
<offer id=\"1234\" type=\"event-ticket\"  available=\"true\" bid=\"13\"> 
    <url>http://best.seller.ru/product_page.asp?pid=57384</url>
    <price>1000</price>
    <currencyId>RUR</currencyId>
    <categoryId>3</categoryId> 
    <picture>http://best.seller.ru/product_page.asp?pid=72945.jpg</picture>
    <store>false</store>
    <pickup>false</pickup>
    <delivery>true</delivery>
    <delivery-options> 
        <option cost=\"300\" days=\"0\"/> 
    </delivery-options>
    <name>Дмитрий Хворостовский и Национальный филармонический
 оркестр России. Дирижер — Владимир Спиваков.</name>
    <place>Московский  международный Дом музыки</place>
    <hall>Большой зал</hall>
    <hall_part>Партер р. 1-5<hall_part>
    <date>2012-02-25 12:03:14</date> 
    <is_premiere>0<is_premiere>
    <is_kids>0</is_kids>
    <description>Концерт Дмитрия Хворостовского и Национального филармонического
    оркестра России</description>
    <age>6</age>
</offer>
";
?>