<?
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_NAME"] = "Экспорт в систему авито (Для бизнеса)";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_ID"] = "Уникальный идентификатор объявления<br/>(строка не более 100 символов)<br/><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_DATEEND"] = "Дата конца экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_ADSTATUS"] = "Платная услуга, которую нужно применить к объявлению — одно из значений списка:<br/><br/>\"Free\" — обычное объявление;<br/>\"Premium\" — премиум-объявление;<br/>\"VIP\" — VIP-объявление;<br/>\"PushUp\" — поднятие объявления в поиске;<br/>\"Highlight\" — выделение объявления;<br/>\"TurboSale\"— применение пакета \"Турбо-продажа\";<br/>\"QuickSale\" — применение пакета \"Быстрая продажа\".";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_ALLOWEMAIL"] = "Возможность написать сообщение по объявлению через сайт — одно из значений списка: Да, Нет. Примечание: значение по умолчанию — Да.";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_MANAGERNAME"] = "Имя менеджера, контактного лица компании по данному объявлению — строка не более 40 символов.";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_CONTACTPHONE"] = "Контактный телефон — строка, содержащая только один российский номер телефона; должен быть обязательно указан код города или мобильного оператора. Корректные примеры:<br/>+7 (495) 777-10-66,<br/>(81374) 4-55-75,<br/>8 905 207 04 90,<br/>+7 905 2070490,<br/>88123855085,<br/>9052070490.";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_REGION"] = "Регион,<br/>в котором находится объект объявления<br/>в соответствии со значениями из справочника.<br/><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_CITY"] = "Город или населенный пункт, в котором находится объект объявления — в соответствии со значениями из справочника.<br/>
Элемент обязателен для всех регионов, кроме Москвы и Санкт-Петербурга.<br/>
Справочник является неполным. Если требуемое значение в нем отсутствует, то укажите ближайший к вашему объекту пункт из справочника, а точное название населенного пункта — в элементе Street.";

$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_DISTRICT"] = "Район города — в соответствии со значениями из справочника.";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_SUBWAY"] = "Ближайшая станция метро<br/>(в соответствии со значениями из справочника)";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_CATEGORY"] = "Категория товара — одно из значений списка:<br/>\"Готовый бизнес\",<br/>\"Оборудование для бизнеса\".";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_GOODSTYPE"] = "Вид товара — одно из значений списка (отдельно для каждой категории):<br/>
Готовый бизнес:<br/>
Интернет-магазин,<br/>
Общественное питание,<br/>
Производство,<br/>
Развлечения,<br/>
Сельское хозяйство,<br/>
Строительство,<br/>
Сфера услуг,<br/>
Торговля,<br/>
Другое;<br/>
Оборудование для бизнеса:<br/>
Для магазина,<br/>
Для офиса,<br/>
Для ресторана,<br/>
Для салона красоты,<br/>
Промышленное,<br/>
Другое.";

$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_TITLE"] = "Название объявления — строка до 50 символов.<br/>Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля — и не используйте слово \"продам\".";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_DESCRIPTION"] = "Текстовое описание объявления в соответствии с правилами Avito — строка не более 3000 символов.";
$MESS["ACRIT_EXPORTPRO_AVITO_ELECTRONICS_FIELD_PRICE"] = "Цена в рублях - целое число.";
$MESS["ACRIT_EXPORTPRO_AVITO_BUSINESS_FIELD_IMAGE"] = "Изображения";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_BUSINESS_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/dlya_biznesa/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_BUSINESS_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_BUSINESS_EXAMPLE"] = "
<Ads formatVersion=\"3\" target=\"Avito.ru\">
    <Ad>
        <Id>723681273</Id>
        <DateBegin>2015-11-27</DateBegin>
        <DateEnd>2079-08-28</DateEnd>
        <AdStatus>TurboSale</AdStatus>
        <AllowEmail>Да</AllowEmail>
        <ManagerName>Иван Петров-Водкин</ManagerName>
        <ContactPhone>+7 916 683-78-22</ContactPhone>
        <Region>Владимирская область</Region>
        <City>Владимир</City>
        <District>Ленинский</District>
        <Category>Оборудование для бизнеса</Category>        
        <GoodsType>Для салона красоты</GoodsType>      
        <Title>Электрический бойлерный пароконвектомат Ratio C1</Title>
        <Description><![CDATA[
<p><strong>Электрический бойлерный пароконвектомат RATIO C1</strong></p>
<ul>
<li>Cool Down – быстрое охлаждение рабочей камеры.
<li>Режим понижения мощности для электрических моделей (1/2 энергии).
</ul>
]]></Description>
        <Price>150000</Price>
        <Images>
            <Image url=\"http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg\" />
            <Image url=\"http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg\" />
        </Images>
    </Ad>
    <Ad>
        <Id>odb3727321</Id>
        <Region>Санкт-Петербург</Region>
        <Subway>Автово</Subway>
        <Category>Готовый бизнес</Category>        
        <GoodsType>Торговля</GoodsType>        
        <Title>Магазин разливного пива в прикассовой зоне</Title>
        <Description>Продаем успешный магазин разливного пива, находящийся в прикассовой зоне супермаркета Дикси на первой линии домов крупной магистрали. Отдел расположен сразу при входе в супермаркет, не заметить невозможно. Посещаемость супермаркета в среднем 1500 человек в день</Description>
        <Price>450000</Price>
    </Ad>        
</Ads>
";
?>