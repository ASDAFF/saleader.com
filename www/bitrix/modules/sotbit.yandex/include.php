<?
IncludeModuleLangFile(__FILE__);
$AddAutoloadClasses = CModule::AddAutoloadClasses(
    'sotbit.yandex',
    array(
        "SotbitYandexTable" => "classes/mysql/yandex_list.php",
        "CSotbitYandexApiContent" => "classes/general/yandex_content_api.php",
        "CSotbitYandexTask" => "classes/general/yandex_sotbit_api.php",
    )
);
//include($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sotbit.yandex/classes/general/yandex_content_api.php'); 
Class CSotbitYandex extends CSotbitYandexApiContent
{
    protected static $demo = true;
    protected $isDemo = true;
    
    const DEFAULT_DEBUG_LIST = 3;
    const DEFAULT_DEBUG_ITEM = 30;
    const DEFAULT_TIME = 0.15; 
    
    public function __construct($key)
    {
        self::$demo = self::getDemo();
        $this->isDemo = self::$demo;
        $this->createFolder();
        parent::__construct($key);
    }
    
    public function getDemo()
    {
        $module_id = "sotbit.yandex";
        $demo = CModule::IncludeModuleEx($module_id);
        if($demo==3) die("SOTBIT.YANDEX DEMO END");
        elseif($demo==2) return false;
        return $demo;    
    }
    
    public function isDemoEnd()
    {
        
    }
    
    public function MessageError($text, $hide=false)
    {
        $str = "";
        if($hide) $str = 'style="display:none"';
        echo '<div class="adm-info-message-wrap adm-info-message-red" '.$str.'>
            <div class="adm-info-message">
                <div class="adm-info-message-title">'.$text.'</div>
        
                <div class="adm-info-message-icon"></div>
        </div>
        </div>';
        
    }

    protected function getCurl($url, $headers, $ID)
    {  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        self::getDemo();
        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            //$ar["ERROR"] = "Error: " . curl_error($ch);
            //$ar["TIME"] = ConvertTimeStamp(time(), "FULL");

            return false;
        } else {
           
            $data = json_decode($data);
            if(isset($data->errors))
            {
                foreach($data->errors as $error)
                {
                    $this->setError($error, true, $ID);
                }

            }
            //file_put_contents(dirname(__FILE__)."/ttt.txt", print_r(array($headers, $data), true), FILE_APPEND);
            curl_close($ch);
            if(isset($arData->searchResult->page) && !self::$demo && $arData->searchResult->page>1)
            {
                return array();    
            }
            
        }

        $charset = strtolower(SITE_CHARSET);
        
        if($charset!="utf-8")
        {
            self::convertCharset($data, $charset);
        }
        return $data;
    }
    
    private function convertCharset(&$data, $charset)
    {
        global $APPLICATION;
        if(is_string($data))
        {
            $data = $APPLICATION->ConvertCharset($data, "utf-8", $charset);
        }else{
            foreach($data as &$v)
            {
                self::convertCharset($v, $charset);    
            }
                    
        }
    }
    
    public function setSettings(&$SETTINGS)
    {
        foreach($SETTINGS as &$v)
        {
            if(is_array($v)) self::setSettings($v);
            else $v = htmlspecialcharsBack($v);
        }
    }
    
    public function setError($error, $log=false, $ID=0)
    {
        if($log && $ID)
        {
            $error = GetMessage("sotbit_yandex_error").": ".$error;
            $this->setLog($error, $ID);
        }
    }
    
    static function startAgent($ID){
        ignore_user_abort(true);    
        if(CModule::IncludeModule('iblock') && CModule::IncludeModule('main') && CModule::IncludeModule('sotbit.yandex') && self::$demo)
        {   
            $KEY_API = COption::GetOptionString("sotbit.yandex", "KEY", "");
            $sotbitYandex = new CSotbitYandex($KEY_API);
            $sotbitYandex->startTask($ID, true); 

        }
        
        return "CSotbitYandex::startAgent(".$ID.");";
    }
    
    protected function setLog($log, $ID)
    {   
        if(isset($this->settings["LOG"]["FILE"]) && $this->settings["LOG"]["FILE"]=="Y" && $ID && self::$demo)
        {   
            if(is_string($log))
            {   
                $time = ConvertTimeStamp(time(), "FULL");
                $log = "[".$time."] ".$log."\r\n";
                $file = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/sotbit.yandex/include/log".$ID.".txt";
                file_put_contents($file, $log, FILE_APPEND);
            }    
        }elseif(isset($this->settings["LOG"]["SMART"]) && $this->settings["LOG"]["SMART"]=="Y")
        {
            
        }
    }
    
    public function createFolder()
    {
        $dir = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/sotbit.yandex/include";
        if(!file_exists($dir))
            mkdir($dir, BX_DIR_PERMISSIONS);
    }
    
    protected function deleteLog($ID)
    {
        $file = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/sotbit.yandex/include/log".$ID.".txt";
        if($ID && file_exists($file))
            unlink($file);
    }	
}
?>
