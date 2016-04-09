<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Задайте вопрос");
?><h1>Контактная информация </h1>
<? $APPLICATION->IncludeComponent(
    "bitrix:menu",
    "personal",
    Array(
        "ALLOW_MULTI_SELECT" => "N",
        "CHILD_MENU_TYPE" => "",
        "COMPONENT_TEMPLATE" => "personal",
        "DELAY" => "N",
        "MAX_LEVEL" => "1",
        "MENU_CACHE_GET_VARS" => array(),
        "MENU_CACHE_TIME" => "3600000",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "ROOT_MENU_TYPE" => "about",
        "USE_EXT" => "N"
    )
); ?>
    <div class="bx_page" class="contactList"  itemscope itemtype="http://schema.org/Organization">
        <h2>Интернет-гипермаркет <span itemprop="name">SaLeader.com</span></h2>
        <ul>
            <li>
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <img alt="cont1.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont1.png" title="cont1.png">
                        </td>
                        <td>
                            <a href="tel:74951044845" title="Контактный телефон интернет-магазина SaLeader.com"
                               rel="nofollow" itemprop="telephone">+7 (495) 104-48-45</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
            <li>
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <img alt="cont2.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont2.png" title="cont2.png">
                        </td>
                        <td>
                            <a href="mailto:sale@saleader.com" title="Электронная почта интернет-магазина Лидер Продаж"
                               rel="nofollow" itemprop="email">sale@saleader.com</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
            <li>
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <img alt="cont3.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont3.png" title="cont3.png">
                        </td>
                        <td itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                            <span  itemprop="addressLocality">
                                г. Мытищи
                                </span>
                             <span itemprop="postalCode">
                                141014
                            </span>
                            <br>
                            <span  itemprop="streetAddress">
                                ул. Трудовая, д. 31, офис 218     
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
            <li>
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <img alt="cont4.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont4.png" title="cont4.png">
                        </td>
                        <td>
                            Пн-Пт : с 09:00 до 18:00<br>
                            Сб, Вс : выходной<br>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
        </ul>
        <? $APPLICATION->IncludeComponent(
            "bitrix:map.google.view",
            ".default",
            array(
                "COMPONENT_TEMPLATE" => ".default",
                "CONTROLS" => array(
                    0 => "SMALL_ZOOM_CONTROL",
                    1 => "TYPECONTROL",
                    2 => "SCALELINE",
                ),
                "INIT_MAP_TYPE" => "ROADMAP",
                "MAP_DATA" => "a:4:{s:10:\"google_lat\";d:55.89039584389596;s:10:\"google_lon\";d:37.71288532394237;s:12:\"google_scale\";i:17;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:12:\"SaLeader.com\";s:3:\"LON\";d:37.713510990143;s:3:\"LAT\";d:55.890649806489;}}}",
                "MAP_HEIGHT" => "500",
                "MAP_ID" => "",
                "MAP_WIDTH" => "100%",
                "OPTIONS" => array(
                    0 => "ENABLE_DBLCLICK_ZOOM",
                    1 => "ENABLE_DRAGGING",
                    2 => "ENABLE_KEYBOARD",
                )
            ),
            false
        ); ?><br>
        <noindex>
            <small><a
                    href="https://maps.google.ru/maps?f=q&source=embed&hl=ru&geocode=&q=%D0%B3.+%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0,+%D1%83%D0%BB.+2-%D1%8F+%D0%A5%D1%83%D1%82%D0%BE%D1%80%D1%81%D0%BA%D0%B0%D1%8F,+%D0%B4.+38%D0%90&aq=&sll=55,103&sspn=90.84699,270.527344&t=m&ie=UTF8&hq=&hnear=2-%D1%8F+%D0%A5%D1%83%D1%82%D0%BE%D1%80%D1%81%D0%BA%D0%B0%D1%8F+%D1%83%D0%BB.,+38,+%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0,+127287&ll=55.805478,37.569551&spn=0.023154,0.054932&z=14&iwloc=A"
                    class="blackLink" target="_blank" rel="nofollow">Просмотреть увеличенную карту</a></small>
        </noindex>
        <h2 class="bold">ЗАДАТЬ ВОПРОС</h2>
    </div>
<? $APPLICATION->IncludeComponent(
    "bitrix:main.feedback",
    ".default",
    Array(
        "AJAX_MODE" => "Y",
        "COMPONENT_TEMPLATE" => ".default",
        "EMAIL_TO" => "em00s8@mail.ru",
        "EVENT_MESSAGE_ID" => array(0 => "7",),
        "OK_TEXT" => "Спасибо, ваше сообщение принято.",
        "REQUIRED_FIELDS" => array(),
        "USE_CAPTCHA" => "Y"
    )
); ?>
    <h2 class="personalMenu bold">Реквизиты интернет-магазина</h2>
    <p>5СЕК.РУ</p>
    <p><b>Адрес</b>: 367000 РД, г Махачкала, пр-т Акушинского 62 к2 оф22</p>
    <p><b>ИНН</b>: 0573003548</p>
    <p><b>КПП</b>: 057301001</p>
    <p><b>ОГРН</b>: 1140573000270</p><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php") ?>