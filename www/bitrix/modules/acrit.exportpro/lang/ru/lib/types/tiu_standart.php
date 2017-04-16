<?
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_NAME"] = "Произвольный товар с поддержкой категорий";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_ID"] = "Идентификатор торгового предложения";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_AVAILABLE"] = "Cтатус доступности товара";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_PRICE"] = "Цена или цена с учетом скидки. Параметр обязательный только при указании тега <oldprice>.";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_OLDPRICE"] = "Если у товара есть скидка, в данном поле указывается цена без учета скидки.<br/> При наличии данного тега тег <price> является обязательным.<br/> Тег <oldprice> нельзя использовать совместно с тегом <discount>.";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_CURRENCY"] = "Идентификатор валюты товара (RUR, USD, UAH, KZT).";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_CATEGORY"] = "Идентификатор категории товара.<br/>Товарное предложение может принадлежать только одной категории.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_PICTURE"] = "Ссылка на картинку соответствующего товарного предложения";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_NAME"] = "Наименование товарного предложения.<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_DESCRIPTION"] = "Описание товара.<br/>Длина текста не более 175 символов (не включая знаки препинания),<br/> запрещено использовать HTML-теги <br/>(информация внутри тегов публиковаться не будет)";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_VENDOR"] = "Производитель. Не отображается в названии предложения";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_VENDORCODE"] = "Код товара (указывается код производителя)";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_COUNTRYOFORIGIN"] = "Страны производства товара.<br/> Список стран доступен по адресу:<br/>http://partner.market.yandex.ru/pages/help/Countries.pdf";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_BARCODE"] = "Штрихкод товара, указанный производителем";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_PARAM"] = "Характеристики товара";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_TYPEPREFIX"] = "Группа товаров/категория";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_MODEL"] = "Модель";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_GROUP_ID"] = "Код разновидности товара<br/><b>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_SELLINGTYPE"] = "Тип товара на Tiu.ru. Возможные значения: r, w, u, s:<br/>
r — «Товар продается только в розницу» для потребительских<br/>
и промышленных товаров с розничными ценами.<br/>
w — «Товар продается только оптом» для потребительских и<br/>
промышленных товаров, которые продаются только оптом.<br/>
u — «Товар продается оптом и в розницу» для товаров,<br/>
которые продаются и оптом и в розницу.<br/>
s — услуга.";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_MINIMUM_ORDER_QUANTITY"] = "Используется для указания минимального количества<br/>(поле «При заказе от») для основной цены товаров<br/> с типом «Товар продается только оптом»";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_DISCOUNT"] = "Если у товара есть скидка, в данном поле<br/> указывается величина скидки или процент.<br/> Пример: 12.5, 30%. При наличии данного тега<br/> тег <price> является обязательным.";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_KEYWORDS"] = "Ключевые слова (поисковые запросы, теги) товарной позиции или услуги.";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_OPTPRICE1"] = "Указание оптовых цен для типов товаров «Товар продается только оптом».<br/>
При наличии данного тега, тег <price> является обязательным,<br/>
иначе при использовании тега <prices> и отсутствии цены в теге<br/>
<price> все товары будут загружены в статусе «Под заказ»";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SIMPLE_FIELD_OPTQUANTITY1"] = "Количество товара для опта";

$MESS["ACRIT_EXPORTPRO_TYPE_TIU_SIMPLE_PORTAL_REQUIREMENTS"] = "http://support.tiu.ru/documents/443";
$MESS["ACRIT_EXPORTPRO_TYPE_TIU_PORTAL_CATEGORY_SIMPLE_EXAMPLE"] = "<offer id=\"12341\" available=\"true\" selling_type=\"w\">
    <name>#NAME#</name>
    <portal_category_id>1234</portal_category_id>
    <portal_category_url>http://tiu.ru/Printers</portal_category_url>
    <available>true</available>
    <price>16800</price>
    <oldprice>19800</oldprice>
    <currencyId>USD</currencyId>
    <categoryId>6</categoryId>
    <picture>http://best.seller.ru/img/device12345.jpg</picture>
    <picture>http://best.seller.ru/img/device12346.jpg</picture>
    <picture>http://best.seller.ru/img/device12347.jpg</picture>
    <typePrefix>Принтер</typePrefix>
    <vendor>НP</vendor>
    <vendorCode>CH366C</vendorCode>
    <model>Deskjet D2663</model>
    <description>Серия принтеров для людей, которым нужен надежный, простой в использовании
    цветной принтер для повседневной печати. Формат А4. Технология печати: 4-цветная термальная струйная.
    Разрешение при печати: 4800х1200 т/д.
    </description>
    <country>Япония</country>
    <barcode>1234567890120</barcode>
    <param name=\"Максимальный формат\" unit=\"мм\">210</param>
</offer>";
$MESS["ACRIT_EXPORTPRO_TIU_PORTAL_CATEGORY_SCHEME_OFFER_DESCRIPTION"] = "
    <b>#PORTAL_ID#</b> - Категория соответствующия спецификации торгового портала на вкладке \"Категории выгрузки\", служебный шаблон<br/>
    <b>#PORTAL_URL#</b> - Категория соответствующия спецификации торгового портала на вкладке \"Категории выгрузки\", служебный шаблон<br/>
";
?>