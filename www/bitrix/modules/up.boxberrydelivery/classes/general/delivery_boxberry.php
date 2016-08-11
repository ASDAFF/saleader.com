<?
CModule::IncludeModule("sale");
IncludeModuleLangFile(__FILE__);

class CDeliveryBoxberry
{
    public static $api;
    public static $widget = array('key', 'settings');
    public static $possible_delivery = array();
    protected static $settings;

    public static function Init()
    {
        ?>
        <script type="text/javascript"
                src="https://saleader.com/bitrix/templates/dresscode/js/boxberry/boxberry.js"></script>
        <script>
            function setBXBCookie(key, value) {
                var expires = new Date();
                expires.setTime(expires.getTime() + (30 * 60 * 1000));
                document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
            }
            function pvz_delivery(result) {
                setBXBCookie('bxb_price', result.price);
                setBXBCookie('bxb_period', result.period);
                setBXBCookie('bxb_city', result.name);
                setBXBCookie('bxb_address', result.address);
                document.getElementById('ORDER_PROP_7').value = '<?=GetMessage('BOXBERRY_CITY_RESULT');?>' + result.name + "\n" + '<?=GetMessage('BOXBERRY_PVZ_RESULT');?>' + result.address + "\n";
                submitForm();
                return false;
            }
            function pvz_delivery_cod(result) {
                setBXBCookie('bxb_price_cod', result.price);
                setBXBCookie('bxb_period', result.period);
                setBXBCookie('bxb_city', result.name);
                setBXBCookie('bxb_address', result.address);
                document.getElementById('ORDER_PROP_7').value = '<?=GetMessage('BOXBERRY_CITY_RESULT');?>' + result.name + "\n" + '<?=GetMessage('BOXBERRY_PVZ_RESULT');?>' + result.address + "\n";
                submitForm();
                return false;
            }
            setBXBCookie('settings_activate', 1);
        </script>

        <? ;

        /*	CModule::IncludeModule("boxberry");*/
        return array(
            "SID" => "boxberry",
            "NAME" => GetMessage('DELIVERY_NAME'),
            "DESCRIPTION" => "",
            "DESCRIPTION_INNER" => GetMessage('DESCRIPTION_INNER'),
            "BASE_CURRENCY" => COption::GetOptionString("sale", "default_currency", "RUB"),
            "HANDLER" => __FILE__,
            "DBGETSETTINGS" => array("CDeliveryBoxberry", "GetSettings"),
            "DBSETSETTINGS" => array("CDeliveryBoxberry", "SetSettings"),
            "GETCONFIG" => array("CDeliveryBoxberry", "GetConfig"),

            "COMPABILITY" => array("CDeliveryBoxberry", "Compability"),
            "CALCULATOR" => array("CDeliveryBoxberry", "Calculate"),

            'PROFILES' => array(
                'PVZ' => array(
                    'TITLE' => GetMessage('BOXBERRY_PVZ'),
                    'DESCRIPTION' => "",
                ),
                'KD' => array(
                    'TITLE' => GetMessage('BOXBERRY_KD'),
                    'DESCRIPTION' => "",
                ),
                'PVZ_COD' => array(
                    'TITLE' => GetMessage('BOXBERRY_PVZ_COD'),
                    'DESCRIPTION' => "",
                ),
                'KD_COD' => array(
                    'TITLE' => GetMessage('BOXBERRY_KD_COD'),
                    'DESCRIPTION' => "",
                )
            )
        );
    }

    public static function GetConfig()
    {

        $arConfig = array(
            "CONFIG_GROUPS" => array(
                "params" => GetMessage('PARAMS'),

            ),
            "CONFIG" => array(
                "api_token" => array(
                    "TYPE" => "TEXT",
                    "DEFAULT" => "",
                    "TITLE" => GetMessage('API_TOKEN'),
                    "GROUP" => "params"
                ),
                "api_url" => array(
                    "TYPE" => "TEXT",
                    "DEFAULT" => "http://api.boxberry.de/json.php",
                    "TITLE" => GetMessage('API_URL'),
                    "GROUP" => "params"
                ),
                "widget_url" => array(
                    "TYPE" => "TEXT",
                    "DEFAULT" => "http://points.boxberry.ru/js/boxberry.js",
                    "TITLE" => GetMessage('WIDGET_URL'),
                    "GROUP" => "params"
                ),
                "weight_default" => array(
                    "TYPE" => "TEXT",
                    "DEFAULT" => "5",
                    "TITLE" => GetMessage('WEIGHT_DEFAULT'),
                    "GROUP" => "params"
                ),
                "address_field" => array(
                    "TYPE" => "TEXT",
                    "DEFAULT" => "ORDER_PROP_7",
                    "TITLE" => GetMessage('ADDRESS_FIELD'),
                    "GROUP" => "params"
                ),
            )

        );
        return $arConfig;
    }

    public static function SetSettings($arSettings)
    {

        foreach ($arSettings as $key => $value) {
            if (strlen($value) > 0)
                $arSettings[$key] = ($value);
            else
                unset($arSettings[$key]);
        }

        return serialize($arSettings);
    }


    public static function GetSettings($strSettings)
    {
        $settings = unserialize($strSettings);
        if (empty($settings)) return;

        self::$api = new Boxberry_api($settings["api_token"], $settings["api_url"]);
        self::$widget = self::$api->method_exec('GetKeyIntegration');
        AddMessage2Log(self::$widget );
        self::$widget['settings'] = self::$api->method_exec('WidgetSettings');

        return $settings;

    }

    private function plural_form($number, $after)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        return $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]] . ' ';
    }

    private function ceilPrice($p, $d = 1)
    {
        return ceil($p / $d) * $d;
    }

    public static function GetPointCode($city_code)
    {
        if ($possible_boxberry_points = self::$api->method_exec('ListPoints', array('CityCode=' . $city_code))) {
            return $possible_boxberry_points[0]['Code'];
        } else {
            return false;
        }
    }

    public static function GetCityCode($location)
    {
        $arLocation = CSaleLocation::GetByID($location, 'ru');
        $possible_boxberry_city = self::$api->method_exec('ListCities');
        if (LANG_CHARSET == 'windows-1251') {
            $arLocation["CITY_NAME_LANG"] = iconv('CP1251', 'UTF-8', $arLocation["CITY_NAME_LANG"]);

        }
        $city_bitrix_name = mb_strtoupper($arLocation["CITY_NAME_LANG"]);


        if (LANG_CHARSET == 'windows-1251') {
            foreach ($possible_boxberry_city as $city) {
                $boxberry_cities[$city['Code']] = mb_strtoupper(iconv('CP1251', 'UTF-8', $city['Name']));
            }
        } else {
            foreach ($possible_boxberry_city as $city) {
                $boxberry_cities[$city['Code']] = mb_strtoupper($city['Name']);
            }
        }

        if ($code_city = array_search($city_bitrix_name, $boxberry_cities)) {
            return $code_city;
        }
        return false;
    }

    public static function GetZipKD($city_code)
    {
        $location = CSaleLocation::GetByID($city_code, 'ru');

        if (LANG_CHARSET == 'windows-1251') {
            $location["CITY_NAME_LANG"] = iconv('CP1251', 'UTF-8', $location["CITY_NAME_LANG"]);
        }
        $possible_zip = self::$api->method_exec('ListZips', array('CityName=' . urlencode(mb_strtoupper($location["CITY_NAME_LANG"]))));
        $bxb_yes = GetMessage('1C_BXB_YES');

        foreach ($possible_zip as $city) {
            if ($city["ExpressDelivery"] == $bxb_yes) {
                return $city["Zip"];
            }
        }
        return false;
    }

    public static function Compability($arOrder, $arConfig)
    {
        $arReturn = array();
        if (empty(self::$api->err)) {
            // KD IS POSSIBLE ?        

            if ($location_to = self::GetCityCode($arOrder['LOCATION_TO'])) {
                if (!in_array($location_to, self::$widget['settings']["result"][1]["CityCode"])) {
                    if (self::GetZipKD($arOrder['LOCATION_TO'])) {
                        $arReturn[] = 'KD';
                        $arReturn[] = 'KD_COD';
                    }
                }
                // PVZ ID POSSIBLE ?
                if (!in_array($location_to, self::$widget['settings']["result"][1]["CityCode"])) {
                    $arReturn[] = 'PVZ';
                    $arReturn[] = 'PVZ_COD';
                }
            }
        }

        return $arReturn;
    }

    public static function calclulate_w_surchages($price)
    {
        $price_settings = self::$widget['settings'];
        $result['price'] = $price;

        if (isset($price_settings["result"][2]['power']) && $price_settings["result"][2]['power'] == 1) {
            switch ($price_settings["result"][2]["surcharges"]) {
                case "1":
                    $result['price'] = $result['price'] + $price_settings["result"][2]["surcharges_value"];
                    break;

                case "2":
                    $result['price'] = $result['price'] + ($result['price'] * $price_settings["result"][2]["surcharges_percent"] / 100);
                    break;
            }

            if ($price_settings["result"][2]["length"] != 0) {

                switch ($price_settings["result"][2]["round"]) {

                    case "1":
                        $result['price'] = round($result['price']);
                        break;

                    case "2":
                        $result['price'] = ceil($result['price']);
                        break;
                }
            }

            switch ($price_settings["result"][2]["length"]) {
                case "1":
                    $result['price'] = ceil($result['price']);
                    break;

                case "2":
                    $result['price'] = self::ceilPrice($result['price'], 10);
                    break;

                case "3":
                    $result['price'] = self::ceilPrice($result['price'], 100);
                    break;

            }


            if ($price > $price_settings["result"][2]["sum_min"] && $price < $price_settings["result"][2]["sum_max"]) {
                switch ($price_settings["result"][2]["tariff"]) {
                    case "1":
                        break;

                    case "2":
                        $result['price'] = $price_settings["result"][2]["tariff_value"];
                        break;
                }
            }

            if ($price > $price_settings["result"][2]["sum_max"]) {
                switch ($price_settings["result"][2]["tariff_max"]) {
                    case "2":
                        $result['price'] = $price_settings["result"][2]["tariff_max_value"];
                        break;
                }
            }

            if ($price_settings["result"][2]["limit_min"] == 1 && $result["price"] <= $price_settings["result"][2]["limit_min_value"]) {
                $result["price"] = $price_settings["result"][2]["limit_min_value"];
            }
            if ($price_settings["result"][2]["limit_max"] == 1 && $result["price"] >= $price_settings["result"][2]["limit_max_value"]) {
                $result["price"] = $price_settings["result"][2]["limit_max_value"];
            }
        }

        return $result["price"];

    }

    public static function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
    {
        $location_from = self::GetCityCode($arOrder['LOCATION_FROM']); // 2BXB_CityCode from
        $location_to = self::GetCityCode($arOrder['LOCATION_TO']); // 2BXB_CityCode to

        $pvz_from = self::GetPointCode($location_from); // 2BXB_PointCode from
        $pvz_to = self::GetPointCode($location_to); // 2BXB_PointCode to

        $weight = (!empty($arOrder['WEIGHT']) ? $arOrder['WEIGHT'] : $arConfig['weight_default']['VALUE']);

        if ($profile == 'PVZ') {
            $arLocation = CSaleLocation::GetByID($arOrder['LOCATION_TO'], 'ru');
            $arrParams = array(
                'targetstart=' . $pvz_from,
                'target=' . $pvz_to,
                'weight=' . $weight,
                'height=5',
                'width=5',
                'depth=5',
                'ordersum=' . $arOrder['PRICE'],
                'paysum=' . $arOrder['PRICE'],


            );

            $price_delivery = self::$api->method_exec('DeliveryCosts', $arrParams);
            $price_delivery['price'] = self::calclulate_w_surchages($price_delivery['price']);
            $period = (isset($_COOKIE['bxb_period']) && !empty($_COOKIE['bxb_period']) ? self::plural_form($_COOKIE['bxb_period'], array(GetMessage("DAY"), GetMessage("DAYS"), GetMessage("DAYSS"))) : $price_delivery['delivery_period']);
            $price = (isset($_COOKIE['bxb_price']) && !empty($_COOKIE['bxb_price']) ? $_COOKIE['bxb_price'] : $price_delivery['price']);

            return array(
                "RESULT" => "OK",
                "VALUE" => $price,
                "TRANSIT" => $period . "<br/><a href=\"#\" onclick=\"boxberry.open(pvz_delivery, '" . self::$widget['key'] . "' , '" . $arLocation["CITY_NAME_LANG"] . "' , '" . $pvz_from . "', '" . $arOrder["PRICE"] . "' , '" . $weight . "','" . $arOrder["PRICE"] . "',5,5,5 ); return false\">" . GetMessage("SELECT_LINK_TEXT") . "</a>");

        } elseif ($profile == 'PVZ_COD') {
            $arrParams = array(
                'targetstart=' . $pvz_from,
                'target=' . $pvz_to,
                'weight=' . $weight,
                'height=5',
                'width=5',
                'depth=5',
                'ordersum=' . $arOrder['PRICE'],
                'paysum=0',

            );
            $price_delivery = self::$api->method_exec('DeliveryCosts', $arrParams);
            $price_delivery['price'] = self::calclulate_w_surchages($price_delivery['price']);

            $period = (isset($_COOKIE['bxb_period']) && !empty($_COOKIE['bxb_period']) ? self::plural_form($_COOKIE['bxb_period'], array(GetMessage("DAY"), GetMessage("DAYS"), GetMessage("DAYSS"))) : $price_delivery['delivery_period']);
            $price = (isset($_COOKIE['bxb_price_cod']) && !empty($_COOKIE['bxb_price_cod']) ? $_COOKIE['bxb_price_cod'] : $price_delivery['price']);

            $arLocation = CSaleLocation::GetByID($arOrder['LOCATION_TO'], 'ru');
            return array(
                "RESULT" => "OK",
                "VALUE" => $price,
                "TRANSIT" => $period . "<br/><a href=\"#\" onclick=\"boxberry.open(pvz_delivery_cod, '" . self::$widget['key'] . "' , '" . $arLocation["CITY_NAME_LANG"] . "' , '" . $pvz_from . "', '" . $arOrder["PRICE"] . "' , '" . $weight . "' ,0,5,5,5 ); return false\">" . GetMessage("SELECT_LINK_TEXT") . "</a>");

        } elseif ($profile == 'KD') {

            $arrParams = array(
                'targetstart=' . $pvz_from,
                'target=' . $pvz_to,
                'weight=' . $weight,
                'height=5',
                'width=5',
                'depth=5',
                'ordersum=' . $arOrder['PRICE'],
                'paysum=' . $arOrder['PRICE'],
                'zip=' . self::GetZipKD($arOrder['LOCATION_TO']),
            );

            $price_delivery = self::$api->method_exec('DeliveryCosts', $arrParams);

            return array(
                "RESULT" => "OK",
                "VALUE" => $price_delivery['price'],
                "TRANSIT" => self::plural_form($price_delivery['delivery_period'], array(GetMessage("DAY"), GetMessage("DAYS"), GetMessage("DAYSS")))
            );
        } elseif ($profile == 'KD_COD') {

            $arrParams = array(
                'targetstart=' . $pvz_from,
                'target=' . $pvz_to,
                'height=5',
                'width=5',
                'depth=5',
                'weight=' . $weight,
                'ordersum=' . $arOrder['PRICE'],
                'paysum=0',
                'zip=' . self::GetZipKD($arOrder['LOCATION_TO']),
            );

            $price_delivery = self::$api->method_exec('DeliveryCosts', $arrParams);

            return array(
                "RESULT" => "OK",
                "VALUE" => $price_delivery['price'],
                "TRANSIT" => self::plural_form($price_delivery['delivery_period'], array(GetMessage("DAY"), GetMessage("DAYS"), GetMessage("DAYSS")))
            );
        }
    }
}

class Boxberry_api
{

    var $err = NULL;
    var $api_token = NULL;
    var $url = NULL;
    var $DS = DIRECTORY_SEPARATOR;

    function __construct($token, $url)
    {

        if (isset ($token) && !empty ($token)) {
            $this->api_token = $token;
            $this->url = $url;
        } else {
            $this->err = GetMessage('WRONG_TOKEN');
            return false;
        }
    }

    function get_cache($key, $cache_time = 5)
    {
        $file = $key . '.cache';
        $file_cache = $_SERVER['DOCUMENT_ROOT'] . $this->DS . 'bitrix' . $this->DS . 'cache' . $this->DS . $file;
        if (is_file($file_cache) && (filemtime($file_cache) >= (time() - (3600 * $cache_time)))) {
            return @file_get_contents($file);
        }
        return false;
    }

    function set_cache($key, $cnt)
    {
        $file = $key . '.cache';
        $file_cache = $_SERVER['DOCUMENT_ROOT'] . $this->DS . 'bitrix' . $this->DS . 'cache' . $this->DS . $file;
        return @file_put_contents($file_cache, $cnt);
    }

    function api_get($url, $cache_time = 5)
    {
        $cache_key = md5($url);

        if ($cnt = $this->get_cache($cache_key)) {
            return $cnt;
        } else {
            if (false !== ($cnt = @file_get_contents($url))) {
                $this->set_cache($cache_key, $cnt);
                return $cnt;
            }
        }
        return false;
    }

    function method_exec($method, $params = NULL, $nocache = FALSE)
    {
        $start_time = microtime();
        $url_token = '?token=' . $this->api_token;
        $url_params = '';
        if (isset ($method) && !empty($method)) {
            if (is_array($params)) {
                $params = implode('&', $params);
                $url_params = '&' . $params;
            } else {
                $url_params = $params;
            }
            $exec_string = $this->url . $url_token . '&method=' . $method . $url_params;
            if ($method == 'GetKeyIntegration') {
                $data = $this->api_get($exec_string, 3650);
            } else {
                $data = $this->api_get($exec_string);
            }

            $data = json_decode($data, true);
            if (LANG_CHARSET == 'windows-1251') {
                array_walk_recursive($data, function (&$value, $key) {
                    $value = iconv("UTF-8", "CP1251", $value);
                });
            }

            return $data;

        } else {
            return false;
        }
    }
}

AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array('CDeliveryBoxberry', 'Init'));

?>