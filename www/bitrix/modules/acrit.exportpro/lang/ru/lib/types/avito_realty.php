<?
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_NAME"] = "Экспорт в систему авито (Недвижимость)";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ID"] = "Уникальный идентификатор объявления<br/>(строка не более 100 символов)<br/><b class='required'>Обязательный элемент</b>";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CATEGORY"] = "Категория объекта недвижимости — одно из значений списка:<br/>
\"Квартиры\",<br/>\"Комнаты\",<br/>\"Дома, дачи, коттеджи\",<br/>\"Земельные участки\",<br/>\"Гаражи и машиноместа\",<br/>\"Коммерческая недвижимость\",<br/>\"Недвижимость за рубежом\".";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DATEBEGIN"] = "Дата начала экспозиции объявления";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DATEEND"] = "Дата конца экспозиции объявления";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_COUNTRY"] = "Страна, в которой находится объект объявления — в соответствии со значениями из справочника.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_REGION"] = "Регион, в котором находится объект объявления — в соответствии со значениями из справочника*";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CITY"] = "Город или населенный пункт, в котором находится объект объявления — в соответствии со значениями из справочника*.<br/>
Элемент обязателен для всех регионов, кроме Москвы и Санкт-Петербурга.<br/>
Справочник является неполным. Если требуемое значение в нем отсутствует, то укажите ближайший к вашему объекту пункт из справочника, а точное название населенного пункта — в элементе Street.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DISTRICT"] = "Район города — в соответствии со значениями из справочника.<br/><b>Обязательно, если в справочнике для города указаны районы.</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_STREET"] = "Адрес объекта объявления — строка до 65 символов, содержащая:
<br/>название улицы и номер дома — если задан точный населенный пункт из справочника;
<br/>если нужного населенного пункта нет в справочнике, то в этом элементе нужно указать:<br/>
район региона (если есть),<br/>населенный пункт (обязательно),<br/>улицу и номер дома,<br/>
например для Тамбовской обл.: \"Моршанский р-н, с. Устьи, ул. Лесная, д. 7\".<br/>Примечания:<br/>
для квартир-новостроек при указании NewDevelopmentId поле Street не обязательно, т. к. значение берется из внутреннего справочника Avito и не может быть переопределено;
для того, чтобы ваш объект мог полноценно отображаться в поиске на карте, необходимо:<br/>
указать его точный адрес, известный Яндекс.Картам,<br/>
или задать географические координаты (см. ниже).";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_SUBWAY"] = "Ближайшая станция метро — в соответствии со значениями из справочника. <b>Обязательно, если в справочнике для города указаны станции метро</b>.";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LATITUDE"] = "Географические координаты: широта.";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LONGTITUDE"] = "Географические координаты: долгота.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DISTANCETOCITY"] = "Расстояние до города, в км — целое число.<br/>
Примечание: если объект находится в черте города, то:<br/>
нужно указывать значение \"0\";<br/>
если в городе есть метро, то нужно обязательно указать ближайшую станцию метро (поле Subway);<br/>
если по справочнику локаций в городе есть районы, то нужно указать район в соответствии со значениями справочника (поле District).";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DIRECTIONROAD"] = "Обязательно для объектов не в черте города: направление от города — в соответствии со значениями справочника.<br/><b>Обязательно, если в справочнике для города указаны направления.</b>";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DESCRIPTION"] = "Текстовое описание объявления в соответствии с правилами Avito — строка не более 3000 символов.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_PRICE"] = "Цена в рублях в зависимости от типа объявления — целое число:<br/>
\"Продам\" — руб. за всё;<br/>\"Сдам\" — в зависимости от срока аренды:<br/>\"На длительный срок\" — руб. в месяц за весь объект;<br/>\"Посуточно\" — руб. за сутки.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_COMPANYNAME"] = "Название компании";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_MANAGERNAME"] = "Имя менеджера, контактного лица компании по данному объявлению — строка не более 40 символов.";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_EMAIL"] = "Email менеджера, контактного лица компании по данному объявлению";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ALLOWEMAIL"] = "Возможность написать сообщение по объявлению через сайт — одно из значений списка: Да, Нет. Примечание: значение по умолчанию — Да.";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CONTACTPHONE"] = "Контактный телефон — строка, содержащая только один российский номер телефона; должен быть обязательно указан код города или мобильного оператора. Корректные примеры:<br/>+7 (495) 777-10-66,<br/>(81374) 4-55-75,<br/>8 905 207 04 90,<br/>+7 905 2070490,<br/>88123855085,<br/>9052070490.";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ADSTATUS"] = "Платная услуга, которую нужно применить к объявлению. Статус объявления. Возможные значения:<br/><b>Free, Premium, VIP, Highlight, PushUp, Highlight, QuickSale, TurboSale</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_CATEGORY_VALUE"] = "Комнаты";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_OPERATIONTYPE"] = "Тип объявления<br/><b>Возможные значения: Продам, Сдам<br/><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_OBJECTTYPE"] = "Вид объекта — одно из значений списка (отдельно для каждой категории):<br/><br/>
Дома, дачи, коттеджи:<br/>
Дом,<br/>
Дача,<br/>
Коттедж,<br/>
Таунхаус;<br/><br/>
Земельные участки:<br/>
Поселений (ИЖС),<br/>
Сельхозназначения (СНТ, ДНП),<br/>
Промназначения;<br/><br/>
Гаражи и машиноместа:<br/>                                                                           
Гараж,<br/>
Машиноместо;<br/><br/>
Коммерческая недвижимость:<br/>
Гостиница,<br/>
Офисное помещение,<br/>
Помещение свободного назначения,<br/>
Производственное помещение,<br/>
Складское помещение<br/>
Торговое помещение;<br/><br/>
Недвижимость за рубежом:<br/>
Квартира, апартаменты,<br/>
Дом, вилла,<br/>
Земельный участок,<br/>
Гараж, машиноместо,<br/>
Коммерческая недвижимость.
";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DISTANCETOCITY"] = "Расстояние до города в км.<br/> Примечание: значение 0 означает,<br/> что объект находится в черте города.";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LANDAREA"] = "Площадь участка, в сотках — десятичное число.";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_WALLSTYPE"] = "Материал стен<br/><b>Возможные значения:<ul>
<li>Кирпич</li>
<li>Брус</li>
<li>Бревно</li>
<li>Металл</li>
<li>Пеноблоки</li>
<li>Сэндвич-панели</li>
<li>Ж/б панели</li>
<li>Экспериментальные материалы</li>
</ul></b>
<br/>";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_OBJECTSUBTYPE"] = "Подвид объекта — одно из значений списка (отдельно для каждого типа):<br/><ul>
<li>Гараж:<ul>
<li>\"Железобетонный\",</li>
<li>\"Кирпичный\",</li>
<li>\"Металлический\";</li>
</ul></li>
<li>Машиноместо:<ul>
<li>\"Многоуровневый паркинг\",</li>
<li>\"Подземный паркинг\",</li>
<li>\"Крытая стоянка\",</li>
<li>\"Открытая стоянка\".</li>
</ul></li></ul>";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_SECURED"] = "Охрана объекта — одно из значений списка: \"Да\",\"Нет\".";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_BUILDINGCLASS"] = "Класс здания для офисных и складских помещений<br/><b>Возможные значения:<ul>
<li>A</li>
<li>B</li>
<li>C</li>
<li>D</li>
</ul></b>";


$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_TITLE"] = "Названия объявлений формируются автоматически, исходя из выбранных параметров объекта.<br/><br/>
Только в категории Коммерческая недвижимость заголовок можно задать самостоятельно. В заголовке необходимо указывать только вид объекта и основные параметры. Указание цены, слов, привлекающих внимание, контактной информации, адреса сайта или только слова продам / куплю нарушает правила Avito.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_PRICETYPE"] = "Вариант задания цены — одно из значений списка:<br/><br/>
Продам — руб.;<br/>
за всё — значение по умолчанию,<br/>
за м2<br/>
Сдам — руб.:<br/>
в месяц — значение по умолчанию,<br/>
в месяц за м2<br/>
в год<br/>
в год за м2";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ROOMS"] = "Количество комнат в квартире — целое число или текст \"Студия\".<br/><b class='required'>Обязательный элемент</b>";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_SQUARE"] = "Общая площадь объекта недвижимости, выставленная на продажу, в кв. метрах — десятичное число.<br/>Примечание: для категории \"Дома, дачи, коттеджи\" здесь нужно указывать площадь дома, площадь участка указывается в поле LandArea.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_FLOOR"] = "Этаж, на котором находится объект";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_FLOORS"] = "Количество этажей в доме";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_HOUSETYPE"] = "Тип дома. <br/><b>Возможные значения:<ul>
<li>Кирпичный</li>
<li>Панельный</li>
<li>Блочный</li>
<li>Монолит</li>
<li>Деревянный</li></ul> </b>";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_MARKETTYPE"] = "Обязательно для типа (OperationType) \"Продам\": принадлежность квартиры к рынку новостроек<br/> или вторичному,
только для типа 'Продам'<br/><b>Возможные значения:<ul>
<li>Вторичка</li>
<li>Новостройка</li>
</ul></b>
<br/><b class='required'>Обязательный элемент</b>
";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASETYPE"] = "Обязательно для типа \"Сдам\": тип аренды — одно из значений списка:<br/><b>Возможные значения:<ul>
<li>На длительный срок</li>
<li>Посуточно</li>
</ul></b>
";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASECOMMISSION"] = "Обязательно для долгосрочной аренды: комиссия агента — одно из значений списка:<br/><br/>
Нет,<br/>
Есть";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASECOMMISSIONSIZE"] = "Обязательно для долгосрочной аренды: размер комиссии в % — целое число.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEDEPOSIT"] = "Обязательно для долгосрочной аренды: залог — одно из значений списка:<br/><br/>
Без залога,<br/>
0,5 месяца,<br/>
1 месяц,<br/>
1,5 месяца,<br/>
2 месяца,<br/>
2,5 месяца,<br/>
3 месяца.
";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEBEDS"] = "Только для аренды: количество кроватей — целое число.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASESLEEPINGPLACES"] = "Только для аренды: количество спальных мест — целое число.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEMULTIMEDIAOPTION"] = "Только для аренды: опции Мультимедиа — вложенные элементы Option с возможными значениями из списка:<br/><br/>
Wi-Fi,<br/>
Телевизор,<br/>
Кабельное / цифровое ТВ.
";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEAPPLIANCESOPTION"] = "Только для аренды: опции Бытовая техника — вложенные элементы Option с возможными значениями из списка:<br/><br/>
Плита,<br/>
Микроволновка,<br/>
Холодильник,<br/>
Стиральная машина,<br/>
Фен,<br/>
Утюг.
";
 
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASECOMFORTOPTION"] = "Только для аренды: опции Комфорт — вложенные элементы Option с возможными значениями из списка:<br/><br/>
Кондиционер,<br/>
Камин,<br/><br/>
только в категориях Квартиры и Комнаты:<br/>
Балкон / лоджия,<br/>
Парковочное место;<br/><br/>
только в категории Дома, дачи, коттеджи:<br/>
Бассейн,<br/>
Баня / сауна.
";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEADDITIONALLYOPTION"] = "Только для аренды: опции Дополнительно — вложенные элементы Option с возможными значениями из списка:<br/><br/>
Можно с питомцами,<br/>
Можно с детьми,<br/>
Можно для мероприятий,<br/>
Можно курить.
";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_IMAGE"] = "Изображения";
$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_VIDEOURL"] = "Видео c YouTube — ссылка.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_NEWDEVELOPMENTID"] = "Объект новостройки (будет обязательно для типа Новостройка) — ID объекта из XML-справочника:<br/><br/>
если в жилом комплексе новостроек есть корпуса, то обязательно ID корпуса;<br/>
если корпусов нет, то ID жилого комплекса.<br/>
Если задан элемент NewDevelopmentId, то значения полей Street и географических координат берутся из внутреннего справочника Avito.";

$MESS["ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CADASTRAL_NUMBER"] = "Кадастровый номер участка — строка.<br/>Примечание: не показывается в объявлении полностью.";

$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_REALTY_PORTAL_REQUIREMENTS"] = "http://autoload.avito.ru/format/realty/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_REALTY_PORTAL_VALIDATOR"] = "http://autoload.avito.ru/format/xmlcheck/";
$MESS["ACRIT_EXPORTPRO_TYPE_AVITO_REALTY_EXAMPLE"] = "
<Ads formatVersion=\"3\" target=\"Avito.ru\">
    <Ad>
        <Id>xjfdge4735202</Id>
        <Category>Квартиры</Category>
        <OperationType>Продам</OperationType>
        <DateBegin>2015-11-27</DateBegin>
        <DateEnd>2079-08-28</DateEnd>
        <Region>Алтайский край</Region>
        <City>Барнаул</City>
        <Description>
            Новая, просторная, светлая и уютная квартира с типовым косметическим ремонтом в новом доме серии \"П-44Т\". 
 
            Комнаты изолированные, 19 и 15 метров, кухня 13 метров с эркером, большая ванная, с/у раздельный, две застекленные лоджии. 
 
            А также:
                * стеклопакеты, 
                * паркетная доска, 
                * свободна, 
                * никто не прописан, 
                * прямая продажа.
        </Description>
        <Price>123000</Price>
        <CompanyName>ООО \"Рога и копыта\"</CompanyName>
        <ManagerName>Иван Петров-Водкин</ManagerName>
        <EMail>manager@company.com</EMail>
        <AllowEmail>Нет</AllowEmail>
        <ContactPhone>+7 916 683-78-22</ContactPhone>
        <Images>
            <Image url=\"http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg\" />
            <Image url=\"http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg\" />
        </Images>
        <VideoURL>http://www.youtube.com/watch?v=YKmDXNrDdBI</VideoURL>
        <AdStatus>PushUp</AdStatus>
        <Rooms>2</Rooms>
        <Square>61</Square>
        <Floor>13</Floor>
        <Floors>16</Floors>
        <HouseType>Деревянный</HouseType>
        <MarketType>Новостройка</MarketType>
        <NewDevelopmentId>28212</NewDevelopmentId>
        <CadastralNumber>77:04:0004011:3882</CadastralNumber>
    </Ad>
    <Ad>
        <Id>xjfdge4735204</Id>
        <Category>Комнаты</Category>
        <Region>Тамбовская область</Region>
        <City>Моршанск</City>
        <Street>Моршанский р-н, с. Устьи, ул. Лесная, 7</Street>
        <Latitude>53.485221</Latitude>
        <Longitude>41.840005</Longitude>
        <Description><![CDATA[
<p>Новая, просторная, <strong>светлая и уютная квартира<strong> с ремонтом и большим холодильником,<br />
комнаты изолированные, 11 и 10 метров, кухня 3 метра с балконом.</p>
<p><em>Важно:</em></p>
<ul>
<li>маленькая ванная, 
<li>с/у совмещенный, 
<li>3 застекленные лоджии, 
<li>стеклопакеты, 
<li>паркет, 
<li>свободна.
</ul>
]]>
        </Description>
        <Price>102000</Price>
        <Rooms>2</Rooms>
        <Square>61</Square>
        <Floor>14</Floor>
        <Floors>16</Floors>
        <HouseType>Деревянный</HouseType>
        <OperationType>Сдам</OperationType>
        <LeaseType>На длительный срок</LeaseType>
        <LeaseCommission>Есть</LeaseCommission>
        <LeaseCommissionSize>50</LeaseCommissionSize>
        <LeaseDeposit>2,5 месяца</LeaseDeposit>
        <LeaseBeds>3</LeaseBeds>
        <LeaseSleepingPlaces>5</LeaseSleepingPlaces>
        <LeaseMultimedia>
            <Option>Wi-Fi</Option>
            <Option>Кабельное / цифровое ТВ</Option>
        </LeaseMultimedia>
        <LeaseAppliances>
            <Option>Плита</Option>
            <Option>Стиральная машина</Option>
            <Option>Фен</Option>
        </LeaseAppliances>
        <LeaseComfort>
            <Option>Кондиционер</Option>
            <Option>Баня / сауна</Option>
        </LeaseComfort>
        <LeaseAdditionally>
            <Option>Можно с питомцами</Option>
            <Option>Можно для мероприятий</Option>
            <Option>Можно курить</Option>
        </LeaseAdditionally>
        <Images>
            <Image name=\"a1.jpg\"/>
            <Image name=\"a2.jpg\"/>
            <Image name=\"a3.jpg\"/>
        </Images>
        <AllowEmail>Нет</AllowEmail>        
    </Ad>
    <Ad>
        <Id>47352067890</Id>
        <Category>Дома, дачи, коттеджи</Category>
        <OperationType>Продам</OperationType>
        <ObjectType>Дом</ObjectType>
        <Region>Владимирская область</Region>
        <City>Владимир</City>
        <Street>Судогодское шоссе</Street>
        <DistanceToCity>5</DistanceToCity>
        <Latitude>56.046606</Latitude>
        <Longitude>40.445891</Longitude>
        <Square>85</Square>
        <LandArea>6.5</LandArea>
        <Floors>2</Floors>
        <WallsType>Брус</WallsType>
        <Description>Тестовое объявление — тест Автозагрузки - Дома, дачи, коттеджи.</Description>
        <Price>2000000</Price>
        <CadastralNumber>77:04:0004011:3885</CadastralNumber>
        <AdStatus>Free</AdStatus>
    </Ad>
    <Ad>
        <Id>зем47352067891</Id>
        <Category>Земельные участки</Category>
        <OperationType>Сдам</OperationType>
        <ObjectType>Сельхозназначения (СНТ, ДНП)</ObjectType>
        <Region>Владимирская область</Region>
        <City>Владимир</City>
        <District>Ленинский</District>
        <Street>микрорайон Коммунар, Набережная улица</Street>
        <District>Октябрьский</District>
        <DistanceToCity>0</DistanceToCity>
        <Latitude>56.108573</Latitude>
        <Longitude>40.42247</Longitude>
        <LandArea>11.7</LandArea>
        <Description>Тестовое объявление — тест Автозагрузки - Земельные участки.</Description>
        <Price>2000</Price>
        <CadastralNumber>77:04:0004011:3892</CadastralNumber>
    </Ad>    
    <Ad>
        <Id>гараж345353</Id>
        <Category>Гаражи и машиноместа</Category>
        <OperationType>Продам</OperationType>
        <ObjectType>Машиноместо</ObjectType>
        <ObjectSubtype>Многоуровневый паркинг</ObjectSubtype>
        <Secured>Нет</Secured>
        <Region>Санкт-Петербург</Region>
        <Street>Роменская улица, 2</Street>
        <Subway>Лиговский проспект</Subway>
        <Square>10</Square>
        <Description>Тестовое объявление — тест Автозагрузки - Гаражи и машиноместа.</Description>
        <Price>1500000</Price>
        <AdStatus>Free</AdStatus>
    </Ad>
    <Ad>
        <Id>xjfdge4735205</Id>
        <Category>Коммерческая недвижимость</Category>
        <OperationType>Сдам</OperationType>
        <ObjectType>Офисное помещение</ObjectType>
        <BuildingClass>A</BuildingClass>
        <LeaseType>На длительный срок</LeaseType>
        <Region>Москва</Region>
        <Subway>Белорусская</Subway>
        <Street>ул. Лесная, д. 7</Street>
        <Square>300</Square>
        <Title>Офис 300 кв. м, бизнес-центр \"Белые сады\"</Title>
        <Description>Тестовое объявление — тест Автозагрузки - Коммерческая недвижимость.</Description>
        <Price>12000</Price>
        <PriceType>в год за м2</PriceType>
    </Ad>
    <Ad>
        <Id>нзр4735207</Id>
        <Category>Недвижимость за рубежом</Category>
        <ObjectType>Квартира, апартаменты</ObjectType>
        <OperationType>Продам</OperationType>
        <Region>Москва</Region>
        <Subway>Белорусская</Subway>
        <Street>ул. Лесная, д. 5</Street>
        <Country>Уругвай</Country>        
        <Description>Тестовое объявление — тест Автозагрузки - Недвижимость за рубежом.</Description>
        <Price>2100000</Price>
        <AdStatus>TurboSale</AdStatus>
    </Ad>    
</Ads>
";
?>