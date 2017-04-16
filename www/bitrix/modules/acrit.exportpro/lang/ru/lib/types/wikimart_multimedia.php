<?
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_NAME"] = "Музыкальная и видео продукция (artist.title)";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_BID"] = "Основная ставка клика";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_AVAILABLE"] = "Cтатус доступности товара";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_URL"] = "URL страницы товара.<br/>Максимальная длина URL — 512 символов.<br/>Необязательный элемент для магазинов-салонов";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_PRICE"] = "Цена, по которой данный товар можно приобрести.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_CATEGORY"] = "Идентификатор категории товара.<br/>Товарное предложение может принадлежать только одной категории.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_NAME"] = "Наименование товарного предложения.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_YEAR"] = "Год";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_DESCRIPTION"] = "Описание товара.<br/>Длина текста не более 175 символов (не включая знаки препинания),<br/> запрещено использовать HTML-теги <br/>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_AGE"] = "Возрастная категория товара";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_STORE"] = "Покупка соответствующего товара в розничном магазине<br/>Возможные значения: true, false";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_TITLE"] = "Название фильма.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_MEDIA"] = "Носитель";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_WIKIMART_MULTIMEDIA_FIELD_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";
$MESS["ACRIT_EXPORTPRO_TYPE_WIKIMART_MULTIMEDIA_EXAMPLE"] = "
<offer id=\"12345\" type=\"artist.title\" available=\"true\" bid=\"11\">
    <url>http://best.seller.ru/product_page.asp?pid=12946</url>
    <price>450</price>
    <currencyId>USD</currencyId>
    <categoryId>2</categoryId>
    <picture>http://best.seller.ru/product_page.asp?pid=14345.jpg</picture>
    <store>false</store>
    <artist>Pink Floyd</artist>
    <title>Dark Side Of The Moon, Platinum Disc</title>
    <year>1999</year>
    <media>CD</media>
    <description>Dark Side Of The Moon, поставивший мир на уши
    невиданным сочетанием звуков, — это всего-навсего девять 
    треков, и даже не все они писались специально для альбома. 
    Порывшись по сусекам, участники Pink Floyd мудро сделали 
    новое из хорошо забытого старого — песен, которые 
    почему-либо не пошли в дело или остались незаконченными. 
    Одним из источников вдохновения стали саундтреки 
    для кинофильмов, которые группа производила в больших количествах.</description>
    <age unit=\"year\">18</age>    
</offer>
";
?>