<?
set_time_limit(0);
IncludeModuleLangFile(__FILE__);
Class CSotbitYandexApiContent
{
    public $API_KEY = "";
    public $host = "";
    public $type = "apisystem";
    public $https = "https";
    public $count = 30;
    public $taskID = 0;
    public $page = 1;
    protected $geoID = 213;
    protected $debug = true;
    private $nPageSize = 10;
    private $category;
    private $activeCategory;
    private $arFields = array();
    private $arProduct = array();
    private $arPrice = array();
    protected $settings = array();
    private $elementID = 0;
    private $arResultElement = array();
    protected $countCat = 0;
    private $countCatReal = 0;
    private $task = array();
    private $start = 0;
    private $current = 0;
    private $allCount = 0;
    private $countProduct = 0;
    private $countProductProcent = 0;
    private $countElements = 0;
    private $countElementsItem = 0;
    private $countError = 0;
    private $xmlID = 0;
    private $isCatalog = 0;
    private $isCatalogOffer = 0;
    protected $arErrors = array();
    private $isModel = true;
    private $searchCount = 1;
    private $searchPage = 1;
    
    const DEFAULT_DEBUG_LIST = 3;
    const DEFAULT_DEBUG_ITEM = 30;
    const DEFAULT_TIME = 0.15;
    
    public function __construct($key)
    {
        global $APPLICATION;
        $this->API_KEY = $key;
        $this->host = "api.content.market.yandex.ru";
        $this->https = "https";
        $this->keyapisystemV = "";
        $this->keyapisystemA = "";
        $this->type = COption::GetOptionString("sotbit.yandex", "TYPE_SERVICE", "ym");
        if($this->type=="apisystem")
        {
            $this->host = "market.apisystem.ru";
            $this->https = "http";
            if($key)
            {
                $this->keyapisystemV = "?api_key=".$key;
                $this->keyapisystemA = "&api_key=".$key;    
            }
            
        }elseif($this->type=="icsystem")
        {
            $this->host = "market.icsystem.ru";
            $this->https = "http";    
        }
    }
    
    public function getRegions()
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/georegion.json".$this->keyapisystemV." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        
        $url = $this->https."://".$this->host."/v1/georegion.json".$this->keyapisystemV;
        $data = $this->getCurl($url, $headers, $this->taskID);
        
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;        
                }    
            }
            return array();
        }

        return $data;    
    }
    
    public function getChildRegions($geo_id, $page=1)
    {
        if(!$geo_id) return false;
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/georegion/".$geo_id."/children.json HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        
        $url = $this->https."://".$this->host."/v1/georegion/".$geo_id."/children.json?count=".$this->count."&page=".$page.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;        
                }    
            }
            return array();
        }
        return $data;
    }
    
    public function getCity($geo_id=false)
    {
        if(!$geo_id)$arData = $this->getRegions();
        else $arData = $this->getChildRegions($geo_id);
        $nTotal = (int)$arData->georegions->total;;
        $nPage = (int)$arData->georegions->page;
        if(!empty($arData->georegions->items))
        {
            foreach($arData->georegions->items as $arVal)
            {
                if($arVal->childrenCount)$this->getCity($arVal->id);
                else{
                    $this->arCity[] = $arVal;
                    //printr($arVal);
                } 
            }
            
            if($nPage*$this->count<$nTotal)
            {
                $this->getCity($geo_id, $nPage+1);
            }    
        }
    }
    
    public function searchRegion($text)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/georegion/suggest.json?count=".$this->count."&part_name=".$text.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        
        $url = $this->https."://".$this->host."/v1/georegion/suggest.json?count=".$this->count."&part_name=".$text.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;        
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getListCategory($geo_id, $page=1)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/category.json?count=".$this->count."&page=".$page."&geo_id=".$geo_id.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        
        $url = $this->https."://".$this->host."/v1/category.json?count=".$this->count."&page=".$page."&geo_id=".$geo_id.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getListChildCategory($geo_id, $cat_id, $page=1)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/category/".$cat_id."/children.json?geo_id=".$geo_id."&count=".$this->count."&page=".$page.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        
        $url = $this->https."://".$this->host."/v1/category/".$cat_id."/children.json?geo_id=".$geo_id."&count=".$this->count."&page=".$page.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getCategoryModels($geo_id, $cat_id, $page=1)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/category/".$cat_id."/models.json?geo_id=".$geo_id."&count=".$this->count."&page=".$page.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/category/".$cat_id."/models.json?geo_id=".$geo_id."&count=".$this->count."&page=".$page.$this->keyapisystemA;
        
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getCategoryFilterModels($geo_id, $cat_id, $page=1)
    {
        sleep(self::DEFAULT_TIME);
        $strUri = "";
        $strUri = $this->getFilterUri($cat_id);
        
        $headers = array(
            "GET /v1/filter/".$cat_id.".json?geo_id=".$geo_id."&count=".$this->count."&sort=price&page=".$page.$strUri.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        
        $url = $this->https."://".$this->host."/v1/filter/".$cat_id.".json?geo_id=".$geo_id."&sort=price&count=".$this->count."&page=".$page.$strUri.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;    
    }
    
    protected function getFilterUri($cat_id=0)
    {
        if($cat_id && isset($this->settings["FILT"][$cat_id]))
        {
            $strUri = "";
            foreach($this->settings["FILT"][$cat_id] as $id=>&$filter)
            {
                if(is_array($filter))
                {
                    $arF = array();
                    foreach($filter as $f)
                    {
                        if(empty($f)) continue 1;
                        $arF[] = $f;
                    }
                    if(empty($arF)) continue 1;
                    $strUri .= "&".$id."=";
                    //foreach($filter as $f)
                    {
                        //if(empty($f)) continue 1;
                        $strUri .= implode(",", $arF);
                    }        
                }elseif($filter=="Y")
                {
                    $strUri .= "&".$id."=y";    
                }
            }    
        }
        
        return $strUri;    
    }
    
    public function getModelDetails($model_id, $details_set)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/model/".$model_id."/details.json?details_set=".$details_set.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/model/".$model_id."/details.json?details_set=".$details_set.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID); 
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;
    }
    
    public function getOffer($geo_id, $offer_id)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/offer/".$offer_id.".json?geo_id=".$geo_id.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/offer/".$offer_id.".json?geo_id=".$geo_id.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;
    }
    
    public function getModelOffers($geo_id, $model_id, $page)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/model/".$model_id."/offers.json?geo_id=".$geo_id."&count=".$this->count."&page=".$page.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/model/".$model_id."/offers.json?geo_id=".$geo_id."&count=".$this->count."&page=".$page.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;
    }
    
    public function getSearchModels($geo_id, $text, $page)
    {
        sleep(self::DEFAULT_TIME);
        $text = urlencode($text);
        $headers = array(
            "GET /v1/search.json?geo_id=".$geo_id."&text=".$text."&count=".$this->count."&page=".$page.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/search.json?geo_id=".$geo_id."&text=".$text."&count=".$this->count."&page=".$page.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getModel($geo_id, $model_id, $page)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/model/".$model_id.".json?geo_id=".$geo_id.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/model/".$model_id.".json?geo_id=".$geo_id.$this->keyapisystemA;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getVendor($vendor_id)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/vendor/".$vendor_id.".json".$this->keyapisystemV." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/vendor/".$vendor_id.".json".$this->keyapisystemV;
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;   
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getFilter($geo_id, $cat_id)
    {
        sleep(self::DEFAULT_TIME);
        $headers = array(
            "GET /v1/category/".$cat_id."/filters.json?vendor_max_values=10000&geo_id=".$geo_id.$this->keyapisystemA." HTTP/1.1",
            "Host: ".$this->host,
            "Accept: */*",
            "Authorization: ".$this->API_KEY,
        );
        $url = $this->https."://".$this->host."/v1/category/".$cat_id."/filters.json?vendor_max_values=10000&geo_id=".$geo_id.$this->keyapisystemA;
        
        $data = $this->getCurl($url, $headers, $this->taskID);
        if(isset($data->errors))
        {
            if(!$this->agent && $this->debug)
            {
                foreach($data->errors as $error)
                {
                    $this->arErrors[] = $error;
                }    
            }
            return array();
        }
        return $data;    
    }
    
    public function getMenuCategory($geo_id, $cat_id=false, $page=1, $depth=0, $timeOut=true)
    {
        $depth++;
        if(!isset($this->menu))$this->menu = array();
        if($cat_id)
        {
            $arData = $this->getListChildCategory($geo_id, $cat_id);
            $nTotal = (int)$arData->categories->total;;
            $nPage = (int)$arData->categories->page;
            if($arData->categories->items)
            {
                foreach($arData->categories->items as $arItem)
                {
                    $arItem->depth = $depth;
                    $this->menu[$arItem->id] = $arItem;
                    if($arItem->childrenCount)
                    {
                        $this->getMenuCategory($geo_id, $arItem->id, $page, $depth);    
                    }    
                }
                
                if($nPage*$this->count<$nTotal)
                {
                    $this->getMenuCategory($geo_id, $cat_id, $page+1, $depth--);    
                }    
            }    
        }else{
            $arData = $this->getListCategory($geo_id);
            $nTotal = (int)$arData->categories->total;;
            $nPage = (int)$arData->categories->page;
            if($arData->categories->items)
            {
                foreach($arData->categories->items as $arItem)
                {
                    $arItem->depth = $depth;
                    $this->menu[$arItem->id] = $arItem;
                    if($arItem->childrenCount)
                    {
                        $this->getMenuCategory($geo_id, $arItem->id, $page, $depth);
                        
                        
                        return $this->menu;    
                    }    
                }
                
                if($nPage*$this->count<$nTotal)
                {
                    $this->getMenuCategory($geo_id, false, $page+1, 0);    
                }
            }
        }
        
        return $this->menu;    
    }
    
    public function startTask($ID=0, $agent=false)
    {
        if(!$ID) return false;
        $this->taskID = $ID;
        $this->agent = $agent;
        $this->startInit();
        if($this->active!="Y") 
        {
            return false;    
        }
           
        $this->loadIblock();
        $this->isCatalog();
        $this->checkUniqProp(); 
        if($this->task=="sect")$this->loadCategory();
        if($this->task=="ym")$this->getModelsFromCategory();
        if($this->task=="search")$this->getModelsFromSearch();
    }
    
    public function startInit()
    {
        $arDataTable = SotbitYandexTable::GetByID($this->taskID)->Fetch();
        if($arDataTable["SETTINGS"])
        {
            $arDataTable["SETTINGS"] = (string)$arDataTable["SETTINGS"];
            $arDataTable["SETTINGS"] = unserialize(base64_decode($arDataTable["SETTINGS"]));
            
            $geoName = $arDataTable["SETTINGS"]["GEO"]["REGION_NAME"];
            preg_match("/\[(\\d+)\]/", $geoName, $match);
            $this->geoID = $match[1];    
        }
        
        $this->iblock_id = $arDataTable["IBLOCK_ID"];
        //$this->iblock_id = 0;
        $this->mode = $arDataTable["MODE"];
        $this->active = $arDataTable["ACTIVE"];
        $this->settings = $arDataTable["SETTINGS"]; 
        //$this->agent = false;
        $this->debug = ($this->mode=="debug")?true:false;
        if(!isset($this->isDemo) && !$this->isDemo) $this->debug = true;
        $this->task = $arDataTable["TASK"];
        $this->start = ($_REQUEST["TYPE"]=="start")?1:0;
        $this->current = ($_REQUEST["TYPE"]=="current")?1:0;
        $this->countCat = isset($_REQUEST["countCat"])?$_REQUEST["countCat"]:0;
        $this->countCatReal = isset($_REQUEST["countCatReal"])?$_REQUEST["countCatReal"]:0;
        $this->countProduct = isset($_REQUEST["countProduct"])?$_REQUEST["countProduct"]:0;
        $this->countError = isset($_REQUEST["countError"])?$_REQUEST["countError"]:0;
        $this->searchCount = isset($_REQUEST["searchCount"])?$_REQUEST["searchCount"]:1;
        $this->searchPage = isset($_REQUEST["searchPage"])?$_REQUEST["searchPage"]:1;
        $this->countProductProcent = isset($_REQUEST["countProductProcent"])?$_REQUEST["countProductProcent"]:1;
        $this->nPageSize = $arDataTable["SETTINGS"]["DOP"]["STEP"]?$arDataTable["SETTINGS"]["DOP"]["STEP"]:10;
        
        if(!$this->agent)
            $this->requestTask = $_REQUEST["task"];
        //file_put_contents(dirname(__FILE__)."/ar.txt", print_r($this, true), FILE_APPEND);
        /*if(!$this->getDemo()) 
            $this->debug = true;*/   
    }
    
    protected function loadIblock()
    {
        $arIBlock = CIBlock::GetArrayByID($this->iblock_id);
        $this->arrayIblock = $arIBlock;
    }
    
    protected function checkUniqProp()
    {
        $code = "SOTBIT_YM_ID";
        
        $prop = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "CODE"=>$code))->Fetch();
        
        if(!$prop){
            $obProperty = new CIBlockProperty;
            $arProperty = array(
                "IBLOCK_ID" => $this->iblock_id,
                "NAME" => GetMessage("sotbit_yandex_uniq_id"),
                "CODE" => $code,
                "XML_ID" => $code,
                "MULTIPLE" => "N",
                "PROPERTY_TYPE" => "S",
                "ACTIVE" => "Y",
            );
            
            $ID = $obProperty->Add($arProperty);
            if(!$ID)
            {
                $error = $this->arFields["NAME"]." [".$this->xmlID."] ".$obProperty->LAST_ERROR;
                $this->arErrors[] = $error;
                $this->setError($error, true, $this->taskID);
            }
                
            unset($obProperty);
        }
        
        
    }
    
    protected function loadCategory()
    {
        if(isset($this->settings["CAT"]["VALUES"]) && !empty($this->settings["CAT"]["VALUES"]))
        {   
            //if(isset($depthCurrent))
            {   
                $this->returnStartBuffer("sect");
            }
            
            $this->checkCategoryUF();
            $this->allCount = $this->getCountCategory();
            
            $ID = 0;
            $depthPrev = 1;
         
            $n = 0;
       
            
            if(!$this->agent)$this->countCat++;
            foreach($this->settings["CAT"]["VALUES"] as $cat)
            {
                
                $arCat = explode("|", $cat);
                
                if(isset($arCat[3]) && !empty($arCat[3]))
                {
                    if($this->agent) $this->countCat++;
                    $n++;
                    if($n<$this->countCat && !$this->agent) continue 1;
                    
                    $depthCurrent = (int)$arCat[1];
                    if(!$this->agent)
                    {
                        $arDepthID = isset($_SESSION["CAT"]["arDepthID"])?$_SESSION["CAT"]["arDepthID"]:array();
                        $depthPrev = isset($_SESSION["CAT"]["depthPrev"])?$_SESSION["CAT"]["depthPrev"]:1; 
                        $ID = $_SESSION["CAT"]["ID"];   
                    }
                    
                    if(!$ID)
                        $depthBegin = $depthCurrent;
                    
                    if($depthCurrent>$depthPrev/* && !isset($arDepthID[$depthCurrent])*/)
                    {
                        $arDepthID[$depthCurrent] = $ID;
                        $_SESSION["CAT"]["arDepthID"][$depthCurrent] = $ID;    
                    }
                        
                    if($depthCurrent==$depthBegin)
                    {
                        if(isset($arDepthID) || isset($_SESSION["CAT"]["arDepthID"]))
                        {
                            if($this->agent) unset($arDepthID);
                            else unset($_SESSION["CAT"]["arDepthID"]);    
                        }
                            
                        if($this->agent)$ID = 0;
                        else $_SESSION["CAT"]["ID"] = 0;
                    }else{
                        if($this->agent)$ID = $arDepthID[$depthCurrent];
                        else $ID = $_SESSION["CAT"]["arDepthID"][$depthCurrent];    
                    } 
                    
                    $ID = $this->addCategory($arCat[0], $arCat[1], $arCat[2], $ID);
                    
                    $depthPrev = (int)$arCat[1];
                    if(!$this->agent)
                    {
                        $_SESSION["CAT"]["ID"] = $ID;
                        $_SESSION["CAT"]["depthPrev"] = $depthPrev;    
                    }
                    if($ID)
                        $this->countCatReal++;
                    $bool = $this->returnCurrentBuffer("sect", array("NAME"=>$arCat[2], "ID"=>$ID, "DEPTH"=>$depthPrev));
                    if($bool) break;
                }
            }
            
            //if(isset($depthCurrent))
            {   
                $this->returnEndBuffer("sect");
            }
        }    
    }
    
    protected function getCountCategory()
    {
        $count = 0;
        if(isset($this->settings["CAT"]["VALUES"]) && !empty($this->settings["CAT"]["VALUES"]))
        {
            
            foreach($this->settings["CAT"]["VALUES"] as $cat)
            {
                $arCat = explode("|", $cat);
                if(isset($arCat[3]) && !empty($arCat[3]))
                {
                    $count++;
                }    
            }    
        }
        return $count;    
    }
    
    protected function returnCurrentBuffer($task, $arData)
    {
        global $APPLICATION;
        $error = "";
        $arData["countError"] = 0;
        if($this->debug && !empty($this->arErrors))
        {
            $error = implode("<br/>", $this->arErrors);
            $arData["countError"] = count($this->arErrors)+$this->countError;
        } 
        
        $bool = false;
        if($task=="sect")
        {
            $percent = ceil($this->countCat*100/$this->allCount);
            
            if($this->debug && isset($this->countCat) && $this->countCat>=self::DEFAULT_DEBUG_LIST)
            {
                $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_section_end'), "SUCCESS"=>GetMessage('sotbit_yandex_section_count_0')." ".$this->countCatReal." ".GetMessage('sotbit_yandex_section_count_1'), "PERCENT"=>100, "TASK"=>"SECT", "STEP"=>"END", "ERROR"=>$error, "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"");
                $bool = true;
                $this->setEndTask($this->taskID);
            }
            else{
                $addResult = array('STATUS' => 'OK', 'MESSAGE' => $arData["NAME"], "SUCCESS"=>GetMessage('sotbit_yandex_section_count_0')." ".($this->countCatReal)." ".GetMessage('sotbit_yandex_section_count_1'), "PERCENT"=>$percent, "TASK"=>"SECT", "STEP"=>"CURRENT", "NAME"=>$arData["NAME"], "SECT_ID"=>$arData["ID"], "DEPTH"=>$arData["DEPTH"],"countCat"=>$this->countCat,"countCatReal"=>$this->countCatReal, "ERROR"=>$error, "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"");
                
            } 
            $this->setLog(GetMessage('sotbit_yandex_section_count_0')." ".$this->countCatReal." ".GetMessage('sotbit_yandex_section_count_1')." (".$arData["NAME"].")", $this->taskID);
        }elseif($task=="ym")
        {
            $percent = ceil($this->countProductProcent*100/$arData["nTotal"]);
            if($this->debug && isset($this->countProduct) && $this->countProduct>=self::DEFAULT_DEBUG_ITEM){
                $bool = true;
                $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_section_product_end'), "SUCCESS"=>GetMessage('sotbit_yandex_itogo_find')." ".$arData["nTotal"]." ".GetMessage('sotbit_yandex_itogo_find_0').GetMessage('sotbit_yandex_section_product_count_0')." ".$this->countProduct." ".GetMessage('sotbit_yandex_section_product_count_1')." ".((isset($arData["IS_SECT"]))?$arData["countCat"]:($arData["countCat"]-1))." ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>100, "TASK"=>"YM", "STEP"=>"END", "ERROR"=>$error, "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"");
                $this->setEndTask($this->taskID);
            }
            else{
                $addResult = array('STATUS' => 'OK', 'MESSAGE' => $arData["NAME"], "SUCCESS"=>GetMessage('sotbit_yandex_itogo_find')." ".$arData["nTotal"]." ".GetMessage('sotbit_yandex_itogo_find_0').GetMessage('sotbit_yandex_section_product_count_0')." ".($this->countProduct)." ".GetMessage('sotbit_yandex_section_product_count_1')." ".((isset($arData["IS_SECT"]))?$arData["countCat"]:($arData["countCat"]-1))." ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>$percent, "TASK"=>"YM", "STEP"=>"CURRENT", "NAME"=>$arData["NAME"], "countProduct"=>$this->countProduct, "countProductProcent"=>$this->countProductProcent, "countCat"=>$arData["countCat"], "page"=>$arData["page"], "ERROR"=>$error, "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"");    
            }
        
            $this->setLog(GetMessage('sotbit_yandex_itogo_find')." ".$arData["nTotal"]." ".GetMessage('sotbit_yandex_itogo_find_0').GetMessage('sotbit_yandex_section_product_count_0')." ".($this->countProduct)." ".GetMessage('sotbit_yandex_section_product_count_1')." ".((isset($arData["IS_SECT"]))?$arData["countCat"]:($arData["countCat"]-1))." ".GetMessage('sotbit_yandex_section_product_count_2')." (".$arData["NAME"].")", $this->taskID);
        }elseif($task=="search")
        {
            $percent = ceil($this->countProductProcent*100/$arData["nTotal"]);
            if($this->debug && isset($this->countProduct) && $this->countProduct>=self::DEFAULT_DEBUG_ITEM){
                $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_search_end'), "SUCCESS"=>GetMessage('sotbit_yandex_itogo_find')." ".$arData["nTotal"]." ".GetMessage('sotbit_yandex_itogo_find_0').GetMessage('sotbit_yandex_search_count_0')." ".$this->countProduct." ".GetMessage('sotbit_yandex_search_count_1')." ".(isset($arData["PAGE"])?$arData["countCat"]:($arData["countCat"]-1))." ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>100, "TASK"=>"SEARCH", "STEP"=>"END", "ERROR"=>$error, "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"");
                $bool = true;
                $this->setEndTask($this->taskID);
            }
            else{
                $addResult = array('STATUS' => 'OK', 'MESSAGE' => $arData["NAME"], "SUCCESS"=>GetMessage('sotbit_yandex_itogo_find')." ".$arData["nTotal"]." ".GetMessage('sotbit_yandex_itogo_find_0').GetMessage('sotbit_yandex_search_count_0')." ".($this->countProduct)." ".GetMessage('sotbit_yandex_search_count_1')." ".(isset($arData["PAGE"])?$arData["countCat"]:($arData["countCat"]-1))." ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>$percent, "TASK"=>"SEARCH", "STEP"=>"CURRENT", "NAME"=>$arData["NAME"], "countProduct"=>$this->countProduct, "countProductProcent"=>$this->countProductProcent, "countCat"=>$arData["countCat"], "page"=>$arData["page"], "countItem"=>$arData["countElementsItem"], "ERROR"=>$error, "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"", "searchCount"=>$arData["searchCount"], "searchPage"=>$arData["searchPage"]);    
            }
            $this->setLog(GetMessage('sotbit_yandex_itogo_find')." ".$arData["nTotal"]." ".GetMessage('sotbit_yandex_itogo_find_0').GetMessage('sotbit_yandex_search_count_0')." ".($this->countProduct)." ".GetMessage('sotbit_yandex_search_count_1')." ".(isset($arData["PAGE"])?$arData["countCat"]:($arData["countCat"]-1))." ".GetMessage('sotbit_yandex_section_product_count_2')." (".$arData["NAME"].")", $this->taskID);
        }
        if($bool && $this->agent) return true;
        if($this->agent || !$this->current) return false;
        if(!isset($addResult)) return false;
        $APPLICATION->RestartBuffer();
        echo CUtil::PhpToJSObject($addResult);
        die();
        
            
    }
    
    protected function returnStartBuffer($task)
    {
        global $APPLICATION;
        if($this->agent || (!$this->agent && $this->start))
            $this->setStartTask($this->taskID);
           
        if(!$this->agent && !$this->start) return false; 
        if($task=="sect")
        {   
            if(isset($_SESSION["CAT"]))
                unset($_SESSION["CAT"]);
            
            $this->deleteLog($this->taskID);
            $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_section_start'), "SUCCESS"=>GetMessage('sotbit_yandex_section_count_0')." ".$this->countCat." ".GetMessage('sotbit_yandex_section_count_1'), "PERCENT"=>0, "TASK"=>"SECT", "STEP"=>"START"); 
            $this->setLog(GetMessage('sotbit_yandex_section_start'), $this->taskID);
        }elseif($task=="ym")
        {
            if(isset($_SESSION["YM"]))
                unset($_SESSION["YM"]);
            $this->deleteLog($this->taskID);
            $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_section_product_start'), "SUCCESS"=>GetMessage('sotbit_yandex_section_product_count_0')." ".$this->countProduct." ".GetMessage('sotbit_yandex_section_product_count_1')." 0 ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>0, "TASK"=>"YM", "STEP"=>"START");
            $this->setLog(GetMessage('sotbit_yandex_section_product_start'), $this->taskID);
        }elseif($task=="search")
        {
            if(isset($_SESSION["SEARCH"]))
                unset($_SESSION["SEARCH"]);
            $this->deleteLog($this->taskID);
            $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_search_start'), "SUCCESS"=>GetMessage('sotbit_yandex_search_count_0')." ".$this->countProduct." ".GetMessage('sotbit_yandex_search_count_1')." 0 ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>0, "TASK"=>"SEARCH", "STEP"=>"START");    
            $this->setLog(GetMessage('sotbit_yandex_search_start'), $this->taskID);
        }
            
        
        if($this->agent || !$this->start) return false;
        
        if(!isset($addResult)) return false;
        $APPLICATION->RestartBuffer();
        echo CUtil::PhpToJSObject($addResult);
        die();
    }
    
    protected function returnEndBuffer($task)
    {
        global $APPLICATION;
        $this->setEndTask($this->taskID);
        $error = "";
        $arData["countError"] = 0;
        if($this->debug && !empty($this->arErrors))
        {
            $error = implode("<br/>", $this->arErrors);
            $arData["countError"] = count($this->arErrors)+$this->countError;
        }
        
        if($task=="sect")
        {   
            if($this->countCat && !$this->agent)$this->countCat--;
            $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_section_end'), "SUCCESS"=>GetMessage('sotbit_yandex_section_count_0')." ".$this->countCatReal." ".GetMessage('sotbit_yandex_section_count_1'), "PERCENT"=>100, "TASK"=>"SECT", "STEP"=>"END", "ERROR"=>$error, "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"");    
            $this->setLog(GetMessage('sotbit_yandex_section_end'), $this->taskID);
        }elseif($task=="ym")
        {
            $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_section_product_end'), "SUCCESS"=>GetMessage('sotbit_yandex_section_product_count_0')." ".$this->countProduct." ".GetMessage('sotbit_yandex_section_product_count_1')." ".($this->countCat-1)." ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>100, "TASK"=>"YM", "STEP"=>"END", "countError"=>$arData["countError"], "ITOGO_ERROR"=>$arData["countError"]?(GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]):"");    
            $this->setLog(GetMessage('sotbit_yandex_section_product_end'), $this->taskID);
        }elseif($task=="search")
        {
            $addResult = array('STATUS' => 'OK', 'MESSAGE' => GetMessage('sotbit_yandex_search_end'), "SUCCESS"=>GetMessage('sotbit_yandex_search_count_0')." ".$this->countProduct." ".GetMessage('sotbit_yandex_search_count_1')." ".($this->countCat-1)." ".GetMessage('sotbit_yandex_section_product_count_2'), "PERCENT"=>100, "TASK"=>"SEARCH", "STEP"=>"END", "countError"=>$arData["countError"], "ITOGO_ERROR"=>GetMessage("sotbit_yandex_itogo_errors").": ".$arData["countError"]);
            $this->setLog(GetMessage('sotbit_yandex_section_product_end'), $this->taskID);
        }
        if($this->agent || !$this->current) return false;
        if(!isset($addResult)) return false;
        $APPLICATION->RestartBuffer();
        echo CUtil::PhpToJSObject($addResult);
        die();
    }
    
    protected function setStartTask($ID)
    {
        $arFields["START_LAST_TIME_X"] = new Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s',time()),'Y-m-d H:i:s');
        $result = SotbitYandexTable::Update($ID, $arFields);    
    }
    
    protected function setEndTask($ID)
    {
        $arFields["END_LAST_TIME_X"] = new Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s',time()),'Y-m-d H:i:s');
        $result = SotbitYandexTable::Update($ID, $arFields);    
    }
    
    protected function checkCategoryUF()
    {
        global $APPLICATION;
        if($this->settings["CAT"]["UNIQ_RECORD"]=="uf")
        {
            $filter = array("ENTITY_ID"=>"IBLOCK_".$this->iblock_id."_SECTION", "FIELD_NAME"=>"UF_SOTBIT_YA_XML_ID");
            $arData = CUserTypeEntity::GetList( array("ID"=>"asc"), $filter)->Fetch();
            if(!$arData)
            {
                $arUserType = new CUserTypeEntity();
                $arFields = array(
                    "ENTITY_ID" => "IBLOCK_".$this->iblock_id."_SECTION",
                    "FIELD_NAME"=>"UF_SOTBIT_YA_XML_ID",
                    "USER_TYPE_ID" => "string",
                );
                $arUserType->Add($arFields);
                if($ex = $APPLICATION->GetException())
                {
                    $strError = $ex->GetString();
                    $this->arErrors[] = $strError;
                    $this->setError($strError, true, $this->taskID);
                }
                unset($arUserType);
            }    
        }
    }
    
    protected function checkCategoryUniq($xml_id, $name)
    {
        $filter = array();
        if($this->settings["CAT"]["UNIQ"]=="name")
            $filter = array("IBLOCK_ID"=>$this->iblock_id, "NAME"=>$name);
        elseif(/*$this->settings["CAT"]["UNIQ"]=="xml_id" && */$this->settings["CAT"]["UNIQ_RECORD"]=="xml_id")
            $filter = array("IBLOCK_ID"=>$this->iblock_id, "XML_ID"=>$xml_id);
        elseif(/*$this->settings["CAT"]["UNIQ"]=="xml_id" && */$this->settings["CAT"]["UNIQ_RECORD"]=="uf")
            $filter = array("IBLOCK_ID"=>$this->iblock_id, "UF_SOTBIT_YA_XML_ID"=>$xml_id);
        if(!empty($filter))
        {
            $arSect = CIBlockSection::GetList(array(), $filter, false, array("ID"))->Fetch();
        }
       
        if($arSect) return $arSect["ID"];
        
        return false;    
    }
    
    protected function getModelsFromSearch()
    {
        $this->returnStartBuffer("search");
        $i = 0;
        
        
        if(!$this->agent && !isset($_SESSION["SEARCH"]["countElements"]))
        {
            $this->countElements = $this->getCountElements();
            $_SESSION["SEARCH"]["countElements"] = $this->countElements;        
        }elseif(!$this->agent && isset($_SESSION["SEARCH"]["countElements"]))
        {
            $this->countElements = $_SESSION["SEARCH"]["countElements"];    
        }
        if(isset($this->settings["CATEGORY"]["VALUES"]) && !empty($this->settings["CATEGORY"]["VALUES"]))
        {
            foreach($this->settings["CATEGORY"]["VALUES"] as $val)
            {
                $i++;
                if($i<$this->countCat && !$this->agent) continue 1;
                $bool = $this->searchElements($val);
                if($bool) break 1;   
            }
        }
        
        $this->returnEndBuffer("search");
                
    }
    
    protected function searchElements($sectID)
    {
        $arFilter = array("IBLOCK_ID"=>$this->iblock_id, "SECTION_ID"=>$sectID, "INCLUDE_SUBSECTIONS"=>"Y");
        
        $arSect =  $this->getSection($sectID);
        
        $page = isset($_REQUEST["page"])?$_REQUEST["page"]:1;
        
                
        if(!$this->agent && (!isset($_SESSION["SEARCH"]["countElementsItem"]) || $page==1)) $this->countElementsItem = $this->getCountElements($sectID);
        elseif($this->agent) $this->countElementsItem = $this->getCountElements($sectID);
        
        
        if(!$this->agent && (!isset($_SESSION["SEARCH"]["countElementsItem"]) || $this->countElementsItem))
        {
            $_SESSION["SEARCH"]["countElementsItem"] = $this->countElementsItem;   
        }elseif(!$this->agent && isset($_SESSION["SEARCH"]["countElementsItem"]))
        {
            $this->countElementsItem = $_SESSION["SEARCH"]["countElementsItem"];    
        }
        
        $count = ceil($this->countElementsItem/$this->nPageSize);
        
        $bool = false;
        if($this->countElementsItem)
        {
            for($i=1;$i<=$count;$i++)
            {
                
                if(!$this->agent && $i<$page) continue 1;
                $arPage = Array("nPageSize"=>$this->nPageSize, "iNumPage"=>$i);
                $this->arFieldEmptyUpdate = $this->createEmptyFieldsUpdate();
                $rsElement = CIBlockElement::GetList(Array(), $arFilter, false, $arPage, array_merge(array("ID", "NAME", "IBLOCK_ID", "XML_ID", "PROPERTY_SOTBIT_YM_ID"), $this->arFieldEmptyUpdate)); 
                $j = 0;
                while($arElement = $rsElement->Fetch())
                {   
                    $j++;
                    if(!$this->agent && $j<$this->searchCount) continue 1;
                    if(!$this->agent && $this->searchPage==1)$this->countProductProcent++;
                    
                    if($this->settings["PRODUCT"]["UNIQ_PRODUCT"]=="name" && isset($this->settings["PRODUCT"]["LOAD"]) && $this->settings["PRODUCT"]["LOAD"]=="Y")
                    {
                        $text = $arElement["NAME"];
                        if($text)
                            $this->getSearchText($text, $this->searchPage, $arElement["ID"], $i);
                    }elseif($this->settings["OFFER"]["UNIQ_PRODUCT"]=="name" && isset($this->settings["OFFER"]["LOAD"]) && $this->settings["OFFER"]["LOAD"]=="Y")
                    {
                        $text = $arElement["NAME"];
                        if($text)
                            $this->getSearchText($text, $this->searchPage, $arElement["ID"], $i);
                    }
                    elseif(isset($this->settings["PRODUCT"]["LOAD"]) && $this->settings["PRODUCT"]["LOAD"]=="Y" && $this->settings["PRODUCT"]["UNIQ_PRODUCT"]=="xml_id")
                    {
                        if($this->settings["PRODUCT"]["UNIQ_PRODUCT_RECORD"]=="xml_id")
                            $xml_id = $arElement["XML_ID"];
                        else
                            $xml_id = $arElement["PROPERTY_SOTBIT_YM_ID_VALUE"]; 
                            
                        if($xml_id)
                            $this->getModelElement($xml_id, $arElement["ID"]);
                    }elseif(isset($this->settings["OFFER"]["LOAD"]) && $this->settings["OFFER"]["LOAD"]=="Y" && $this->settings["OFFER"]["UNIQ_PRODUCT"]=="xml_id")
                    {
                        if($this->settings["OFFER"]["UNIQ_PRODUCT_RECORD"]=="xml_id")
                            $xml_id = $arElement["XML_ID"];
                        else
                            $xml_id = $arElement["PROPERTY_SOTBIT_YM_ID_VALUE"]; 
                            
                        if($xml_id)
                            $this->getOfferElement($xml_id, $arElement["ID"]);       
                    }
                    
                    if(!$this->agent && (($this->settings["PRODUCT"]["UNIQ_PRODUCT"]=="name" && isset($this->settings["PRODUCT"]["LOAD"]) && $this->settings["PRODUCT"]["LOAD"]=="Y") || $this->settings["OFFER"]["UNIQ_PRODUCT"]=="name" && isset($this->settings["OFFER"]["LOAD"]) && $this->settings["OFFER"]["LOAD"]=="Y"))
                    {
                        $bool = $this->returnCurrentBuffer("search", array("PAGE"=>1, "NAME"=>$arElement["NAME"], "countCat"=>$this->countCat, "countProduct"=>"", "page"=>$i, "countElements"=>$this->countElements, "nTotal"=>$this->countElements, "countElementsItem"=>$this->countElementsItem, "searchCount"=>++$this->searchCount, "searchPage"=>1));    
                        if($bool) return true;    
                    }
                    
                }  
                if($i!=$count)
                {
                    $bool = $this->returnCurrentBuffer("search", array("PAGE"=>1, "NAME"=>$arSect["NAME"], "countCat"=>$this->countCat, "countProduct"=>"", "page"=>$i+1, "countElements"=>$this->countElements, "nTotal"=>$this->countElements, "countElementsItem"=>$this->countElementsItem, "searchCount"=>1, "searchPage"=>1));    
                    if($bool) return true;
                }
            }
            $bool = $this->returnCurrentBuffer("search", array("NAME"=>$arSect["NAME"], "countCat"=>$this->agent?++$this->countCat:$this->countCat+1, "countProduct"=>"", "page"=>1, "countElements"=>$this->countElements, "nTotal"=>$this->countElements, "countElementsItem"=>$this->countElementsItem, "searchCount"=>1, "searchPage"=>1));
        }
        return $bool;
    }
    
    protected function getModelElement($model_id, $ID)
    {
        $arData = $this->getModel($this->geoID, $model_id);
        if($arData->model)
        {   
            $this->createProductData($arData->model, 0, $ID);    
        }    
    }
    
    protected function getOfferElement($offer_id, $ID)
    {
        $arData = $this->getOffer($this->geoID, $offer_id);
        if($arData->offer)
        {   
            $this->createOfferData($arData->offer, 0, $ID);    
        }    
    }
    
    protected function getSection($sectID)
    {
        $arSect = CIBlockSection::GetList(array("left_margin"=>"asc"), array('ID'=>$sectID), false, array("ID", "NAME"))->Fetch();
        
        return $arSect;    
    }
    
    protected function getCountElements($sectID = 0)
    {
        $arSect = false;
        $cnt = 0;
        if(!$sectID && isset($this->settings["CATEGORY"]["VALUES"]) && !empty($this->settings["CATEGORY"]["VALUES"]))
        {
            foreach($this->settings["CATEGORY"]["VALUES"] as $val)
            {
                $arSect[] = $val;    
            }
        }elseif($sectID && isset($this->settings["CATEGORY"]["VALUES"]) && !empty($this->settings["CATEGORY"]["VALUES"]))
        {
            $arSect = $sectID;    
        }
        
        $arFilter = array("IBLOCK_ID"=>$this->iblock_id, "SECTION_ID"=>$arSect, "INCLUDE_SUBSECTIONS"=>"Y");
        if($arSect)
        {
            $cnt = CIBlockElement::GetList(
                array(),
                $arFilter,
                array(),
                false,
                array('ID')
            );    
        }
        
        
        return $cnt; 
    }
    
    protected function getSearchText($text = "", $page=1, $ID, $i)
    {
        $arData = $this->getSearchModels($this->geoID, $text, $page);
        
        $bool = $this->updateProductData($arData, $text, $ID);
        
        $nTotal = (int)($arData->searchResult->total);
        $nPage = (int)($arData->searchResult->page);
        
               
        if(!$bool && $nPage*$this->count<$nTotal && !$this->debug)
        {   
            
            $bool = $this->returnCurrentBuffer("search", array("PAGE"=>1, "NAME"=>$text, "countCat"=>$this->countCat, "countProduct"=>"", "page"=>$i, "countElements"=>$this->countElements, "nTotal"=>$this->countElements, "countElementsItem"=>$this->countElementsItem, "searchCount"=>$this->searchCount, "searchPage"=>$page+1));    
            if($bool) return true;
            $this->getSearchText($text, $page+1, $ID, $i);    
        }
    }
    
    protected function updateProductData($arData, $text, $ID)
    {
        if(isset($arData->searchResult->results) && !empty($arData->searchResult->results))
        {   
            foreach($arData->searchResult->results as $res)
            {   
                if(isset($res->model) && ($text==$res->model->name || strripos($text, $res->model->name)!==false || strripos($res->model->name, $text)!==false)  && isset($this->settings["PRODUCT"]["LOAD"]) && $this->settings["PRODUCT"]["LOAD"]=="Y")
                {
                    $this->createProductData($res->model, 0, $ID);
                    return true;
                }elseif(isset($res->offer) && ($text==$res->offer->name || strripos($text, $res->offer->name)!==false || strripos($res->offer->name, $text)!==false) && isset($this->settings["OFFER"]["LOAD"]) && $this->settings["OFFER"]["LOAD"]=="Y")
                {
                    $this->createOfferData($res->offer, 0, $ID);
                    return true;    
                }
            }
        }
        
        return false;    
    }
    
    protected function addCategory($id, $depth, $name, $parentID)
    {
        $sectID = $this->checkCategoryUniq($id, $name);
        if($sectID) return $sectID;
        if($this->settings["DOP"]["SECT_CODE"]=="Y")
            $code = $this->getCodeSection($name);
        
        $bs = new CIBlockSection;
        $arFields = Array(
            "ACTIVE" => "Y",
            "IBLOCK_SECTION_ID" => $parentID,
            "IBLOCK_ID" => $this->iblock_id,
            "NAME" => $name,
            "CODE" => $code,
            "DEPTH_"
        );
        if($this->settings["CAT"]["UNIQ_RECORD"]=="xml_id")
            $arFields = array_merge($arFields, array("XML_ID"=>$id));
        elseif($this->settings["CAT"]["UNIQ_RECORD"]=="uf")
            $arFields = array_merge($arFields, array("UF_SOTBIT_YA_XML_ID"=>$id));
        
        $ID = $bs->Add($arFields, true, $this->settings["DOP"]["SECT_CODE"], false);
        $res = ($ID>0);
        
        if(!$res)
        {
            $error = $name." [".$id."] ".$bs->LAST_ERROR;
            $this->arErrors[] = $error;
            $this->setError($error, true, $this->taskID);
        }
            
        unset($bs);
        
        return $ID;        
    }
    
    protected function getCategory()
    {
        if(isset($this->settings["CAT"]["VALUES"]) && !empty($this->settings["CAT"]["VALUES"]) && (!isset($this->settings["CATEGORY"]["VALUES"]) || empty($this->settings["CATEGORY"]["VALUES"])))
        {
            foreach($this->settings["CAT"]["VALUES"] as $val)
            {
                $arV = explode("|", $val);
                $selected = false;
                if(isset($arV[3]) && !empty($arV[3]))
                {
                    $id = $arV[0];
                    $level = $arV[1];
                    $name = $arV[2];
                    $arSect = array("NAME"=>$name, "ID"=>$id, "LEVEL"=>$level);
                    $this->category[] = $arSect;
                }
                
            }
        }elseif(isset($this->settings["CATEGORY"]["VALUES"]) && !empty($this->settings["CATEGORY"]["VALUES"]))
        {
            foreach($this->settings["CATEGORY"]["VALUES"] as $val)
            {
                $arSel = array();
                if($this->settings["CAT"]["UNIQ_RECORD"]=="xml_id")
                {
                    $code = "XML_ID";
                    $arSel =  array("XML_ID");
                }
                elseif($this->settings["CAT"]["UNIQ_RECORD"]=="uf")
                {
                    $arSel = array("UF_SOTBIT_YA_XML_ID");
                    $code = "UF_SOTBIT_YA_XML_ID";    
                }
                    
                $isSect = CIBlockSection::GetList(array("left_margin"=>"asc"), array('ID'=>$val, "IBLOCK_ID"=>$this->iblock_id), false, array_merge(array('ID', 'NAME', "IBLOCK_ID", "DEPTH_LEVEL"), $arSel))->Fetch();
                if($isSect && $isSect[$code])
                {
                    $arSect = array("NAME"=>$isSect["NAME"], "ID"=>$isSect[$code], "LEVEL"=>$isSect["DEPTH_LEVEL"]);
                    $this->category[] = $arSect;    
                }
            }    
        }
        else return false;    
    }
    
    protected function getActiveCategory()
    {
        if(!empty($this->category))
        {   
            foreach($this->category as $i=>$cat)
            {
                $current = $cat["LEVEL"];
                $prev = isset($this->category[$i+1]["LEVEL"])?$this->category[$i+1]["LEVEL"]:0;
                if($current>=$prev)
                {
                    $this->activeCategory[] = $cat;
                }
            }
            
        }
    }
    
    protected function getModelsFromCategory()
    {
        $this->getCategory();
        $this->getActiveCategory();
        $this->returnStartBuffer("ym");
        

        if(!empty($this->activeCategory) && 1)
        {
            $i = 0;
            foreach($this->activeCategory as $cat)
            {
                $i++;
                if(!$this->agent && $i<$this->countCat) continue 1;
                $page = isset($_REQUEST["page"])?$_REQUEST["page"]:1;
                if($page==1)
                    $this->countProductProcent = 0;
                $this->countCat = $i;
                $id = $cat["ID"];
                $name = $cat["NAME"];   
                $sectID = $this->checkCategoryUniq($id, $name);
                $this->searchModel = array();
                $bool = $this->getModelsFromItemCategory($id, $page, $name, $sectID);
                
                if($bool) break 1; 
            }
            
        }
        $this->returnEndBuffer("ym");    
    }
    
    protected function getModelsFromItemCategory($catID, $page=1, $name, $sectID)
    {
        $arData = $this->getCategoryFilterModels($this->geoID, $catID, $page);
        $this->searchModel[] = $arData;
        
        $this->loadModelsFromCategory($sectID);
        
        $nTotal = (int)($arData->searchResult->total);
        $nPage = (int)($arData->searchResult->page);
        
        $bool = false;
        unset($this->searchModel);
        if($nPage*$this->count<$nTotal && !$this->debug)
        {
            
            $this->returnCurrentBuffer("ym", array("IS_SECT"=>1, "NAME"=>$name, "nTotal"=>$nTotal, "page"=>$page+1, "countProduct"=>$this->countProduct, "countCat"=>$this->countCat));
            
            $this->getModelsFromItemCategory($catID, $page+1, $name, $sectID);
        }else{
            
            $bool = $this->returnCurrentBuffer("ym", array("NAME"=>$name, "nTotal"=>$nTotal, "page"=>1, "countProduct"=>$this->countProduct, "countCat"=>$this->agent?++$this->countCat:$this->countCat+1));    
        }
        return $bool;
    } 
    
    protected function loadModelsFromCategory($sectID)
    {
        if(isset($this->searchModel) && !empty($this->searchModel))
        {
            foreach($this->searchModel as $search)
            {
                foreach($search->searchResult->results as $arItem)
                {   
                    $this->loadPosition($arItem, $sectID);
                }
            }    
        }
            
    }
    
    protected function loadPosition($arItem, $sectID)
    {
        $this->countProductProcent++;    
        if(isset($arItem->model) && isset($this->settings["PRODUCT"]["LOAD"]) && $this->settings["PRODUCT"]["LOAD"]=="Y")            
            $this->createProductData($arItem->model, $sectID);
        elseif(isset($arItem->offer) && isset($this->settings["OFFER"]["LOAD"]) && $this->settings["OFFER"]["LOAD"]=="Y")
            $this->createOfferData($arItem->offer, $sectID);
    }
    
    protected function createProductData($model, $sectID=0, $ID=0)
    {
        $this->isModel = true;
        $this->createProductIblock($model);
        $this->createProductName($model);
        
        $this->updateElementID = $ID?$ID:$this->checkUniqElement($model);
        
        $this->createProductCode($model);
        $this->createProductActive($model);
        $this->createProductXmlID($model);
        $this->createProductSection($model, $sectID);
        $this->createProductDescription($model);
        $this->createProductPreviewPicture($model); 
        $this->createProductDetailPicture($model);
        $this->createProductMorePicture($model);
        
        $this->CheckImage();
        $this->createProductUrl($model);
        $this->createProductVendor($model);
        $this->createProductProps($model);
        $this->AllDoProps();
        
        $this->createProductPrice($model);

        $this->addElementCatalog();
        
        if($this->isCatalog && $this->elementID)
        {
            $this->AddProductCatalog();
            $this->AddMeasureCatalog();
            $this->AddPriceCatalog();
        }
  
        
        $this->deleteFields();
          
    }
    
    protected function createOfferData($offer, $sectID=0, $ID=0)
    {
        $this->isModel = false;
        $this->createProductIblock($offer);
        $this->createProductName($offer);
        
        $this->updateElementID = $ID?$ID:$this->checkUniqElementOffer($offer);
        
        $this->createProductCodeOffer($offer);
        $this->createProductActiveOffer($offer);
        $this->createProductXmlIDOffer($offer);
        $this->createProductSectionOffer($offer, $sectID);
        $this->createProductDescriptionOffer($offer);
        $this->createProductPreviewPictureOffer($offer); 
        $this->createProductDetailPictureOffer($offer);
        $this->createProductMorePictureOffer($offer);
        $this->CheckImageOffer();
        $this->createProductUrlOffer($offer);
        $this->createOfferVendor($offer);
        $this->createProductShopOffer($offer);
        $this->createProductPropsOffer($offer);
        $this->AllDoProps(true);
        $this->createProductPriceOffer($offer);
        
        $this->addElementCatalog(true);
        if($this->isCatalogOffer && $this->elementID)
        {
            $this->AddProductCatalog(true);
            $this->AddMeasureCatalog(true);
            $this->AddPriceCatalog(true);
        }

        $this->deleteFields();            
    }
    
    protected function isCatalog()
    {
        if(CModule::IncludeModule('catalog') && ($this->iblock_id && CCatalog::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y", "ID"=>$this->iblock_id))->Fetch()))
        {
            if($this->settings["PRODUCT"]["PRICE"]!="0")
            {
                $this->isCatalog = true;
            }else
                $this->isCatalog = false;
            if($this->settings["OFFER"]["PRICE"]!="0")
                $this->isCatalogOffer = true;
            else{
                $this->isCatalogOffer = false;    
            } 
        }else{
            $this->isCatalog = false;
            $this->isCatalogOffer = false;    
        } 
        /*if(CModule::IncludeModule('catalog') && isset($this->settings["catalog"]["cat_vat_price_offer"]) && $this->settings["catalog"]["cat_vat_price_offer"]=="Y")
        {
            $arIblock = CCatalogSKU::GetInfoByIBlock($this->iblock_id);
            if(is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0)
            {
                $this->isOfferCatalog = true;
                $this->offerArray = $arIblock;
                $this->isCatalog = true;
            }else $this->isOfferCatalog = false;
        }*/
    }
    
    protected function checkUniqElement($model)
    {
        if($this->updateElementID) return $this->updateElementID;
        $arFilter = array("IBLOCK_ID"=>$this->iblock_id);
        
        if($this->settings["PRODUCT"]["UNIQ_PRODUCT"]=="name")
        {
            $arFilter["NAME"] = $this->arFields["NAME"];    
        }elseif($this->settings["PRODUCT"]["UNIQ_PRODUCT"]=="xml_id")
        {
            if($this->settings["PRODUCT"]["UNIQ_PRODUCT_RECORD"]=="xml_id")
                $arFilter["XML_ID"] = $model->id;
            else
                $arFilter["PROPERTY_SOTBIT_YM_ID"] = $model->id;
        }
        
        $this->arFieldEmptyUpdate = $this->createEmptyFieldsUpdate();
        
        $isElement = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount"=>1), array_merge(array("ID"), $this->arFieldEmptyUpdate))->Fetch();
        $this->arResultElement = $isElement;
        
        if($isElement) return $isElement["ID"];
        else return false;
    }
    
    protected function checkUniqElementOffer($offer)
    {
        if($this->updateElementID) return $this->updateElementID;
        $arFilter = array("IBLOCK_ID"=>$this->iblock_id);
        
        if($this->settings["OFFER"]["UNIQ_PRODUCT"]=="name")
        {
            $arFilter["NAME"] = $this->arFields["NAME"];    
        }elseif($this->settings["OFFER"]["UNIQ_PRODUCT"]=="xml_id")
        {
            if($this->settings["OFFER"]["UNIQ_PRODUCT_RECORD"]=="xml_id")
                $arFilter["XML_ID"] = $offer->id;
            else
                $arFilter["PROPERTY_SOTBIT_YM_ID"] = $offer->id;
        }
        
        $this->arFieldEmptyUpdateOffer = $this->createEmptyFieldsUpdateOffer();
        
        
        $isElement = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount"=>1), array_merge(array("ID"), $this->arFieldEmptyUpdateOffer))->Fetch();
        $this->arResultElement = $isElement;
        
        if($isElement) return $isElement["ID"];
        else return false;
    }
    
    protected function checkUpdateInsert($str = "")
    {
        if(!$this->settings["PRODUCT"]["UPDATE"] && $this->updateElementID)
            return false;    
        elseif(!$this->settings["PRODUCT"]["UPDATE"] && !$str && $this->updateElementID)
            return false;
        elseif($this->settings["PRODUCT"]["UPDATE"] && !$str && $this->updateElementID)
            return true;
        elseif($str && $this->updateElementID && isset($this->settings["PRODUCT"]["UPDATE_".$str]) && $this->settings["PRODUCT"]["UPDATE_".$str] && $this->settings["PRODUCT"]["UPDATE_".$str]!="empty")
            return true;
        elseif($str && $this->updateElementID && isset($this->settings["PRODUCT"]["UPDATE_".$str]) && $this->settings["PRODUCT"]["UPDATE_".$str] && $this->settings["PRODUCT"]["UPDATE_".$str]=="empty" && isset($this->arResultElement[$str]) && !$this->arResultElement[$str])
            return true;
        elseif(!$this->updateElementID)
            return true;
        else 
            return false;
    }
    
    protected function checkUpdateInsertOffer($str = "")
    {
        if(!$this->settings["OFFER"]["UPDATE"] && $this->updateElementID)
            return false;    
        elseif(!$this->settings["OFFER"]["UPDATE"] && !$str && $this->updateElementID)
            return false;
        elseif($this->settings["OFFER"]["UPDATE"] && !$str && $this->updateElementID)
            return true;
        elseif($str && $this->updateElementID && isset($this->settings["OFFER"]["UPDATE_".$str]) && $this->settings["OFFER"]["UPDATE_".$str] && $this->settings["OFFER"]["UPDATE_".$str]!="empty")
            return true;
        elseif($str && $this->updateElementID && isset($this->settings["OFFER"]["UPDATE_".$str]) && $this->settings["OFFER"]["UPDATE_".$str] && $this->settings["OFFER"]["UPDATE_".$str]=="empty" && isset($this->arResultElement[$str]) && !$this->arResultElement[$str])
            return true;
        elseif(!$this->updateElementID)
            return true;
        else 
            return false;
    }
    
    protected function isUpdate()
    {
        if($this->settings["PRODUCT"]["UPDATE"] && $this->updateElementID)
            return true;
        elseif(!$this->settings["PRODUCT"]["UPDATE"] && $this->updateElementID)
            return false;
        else
            return true;
    }
    
    protected function isUpdateOffer()
    {
        if($this->settings["OFFER"]["UPDATE"] && $this->updateElementID)
            return true;
        elseif(!$this->settings["OFFER"]["UPDATE"] && $this->updateElementID)
            return false;
        else
            return true;
    }
    
    protected function createEmptyFieldsUpdate()
    {
        if(isset($this->arFieldEmptyUpdate)) return $this->arFieldEmptyUpdate;
        $ar = array();
        if($this->settings["PRODUCT"]["UPDATE_PREVIEW_TEXT"]=="empty")
            $ar[] = "PREVIEW_TEXT";
        if($this->settings["PRODUCT"]["UPDATE_DETAIL_TEXT"]=="empty")
            $ar[] = "DETAIL_TEXT";
        if($this->settings["PRODUCT"]["UPDATE_PREVIEW_PICTURE"]=="empty")
            $ar[] = "PREVIEW_PICTURE";
        if($this->settings["PRODUCT"]["UPDATE_DETAIL_PICTURE"]=="empty")
            $ar[] = "DETAIL_PICTURE";
            
        return $ar;
        
    }
    
    protected function createEmptyFieldsUpdateOffer()
    {
        if(isset($this->arFieldEmptyUpdateOffer)) return $this->arFieldEmptyUpdateOffer;
        $ar = array();
        if($this->settings["OFFER"]["UPDATE_PREVIEW_TEXT"]=="empty")
            $ar[] = "PREVIEW_TEXT";
        if($this->settings["OFFER"]["UPDATE_DETAIL_TEXT"]=="empty")
            $ar[] = "DETAIL_TEXT";
        if($this->settings["OFFER"]["UPDATE_PREVIEW_PICTURE"]=="empty")
            $ar[] = "PREVIEW_PICTURE";
        if($this->settings["OFFER"]["UPDATE_DETAIL_PICTURE"]=="empty")
            $ar[] = "DETAIL_PICTURE";
            
        return $ar;
        
    }
    
    
    protected function getCodeElement($name)
    {
        $arFieldCode = $this->arrayIblock["FIELDS"]["CODE"]["DEFAULT_VALUE"];
        $CODE = CUtil::translit($name, "ru", array(
            "max_len" => $arFieldCode["TRANS_LEN"],
            "change_case" => $arFieldCode["TRANS_CASE"],
            "replace_space" => $arFieldCode["TRANS_SPACE"],
            "replace_other" => $arFieldCode["TRANS_OTHER"],
            "delete_repeat_replace" => $arFieldCode["TRANS_EAT"]=="Y"?true:false,
        ));
        
        $IBLOCK_ID = $this->arrayIblock['ID'];

        $arCodes = array();
        $rsCodeLike = CIBlockElement::GetList(array(), array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "CODE" => $CODE."%",
        ), false, false, array("ID", "CODE"));
        while($ar = $rsCodeLike->Fetch())
            $arCodes[$ar["CODE"]] = $ar["ID"];

        if (array_key_exists($CODE, $arCodes))
        {
            $i = 1;
            while(array_key_exists($CODE."_".$i, $arCodes))
                $i++;

            return $CODE."_".$i;
        }
        else
        {
            return $CODE;
        }
    }
    
    protected function getCodeSection($name)
    {
        $arFieldCode = $this->arrayIblock["FIELDS"]["SECTION_CODE"]["DEFAULT_VALUE"];
        
        $CODE = CUtil::translit($name, "ru", array(
            "max_len" => $arFieldCode["TRANS_LEN"],
            "change_case" => $arFieldCode["TRANS_CASE"],
            "replace_space" => $arFieldCode["TRANS_SPACE"],
            "replace_other" => $arFieldCode["TRANS_OTHER"],
            "delete_repeat_replace" => $arFieldCode["TRANS_EAT"]=="Y"?true:false,
        ));
        
        $IBLOCK_ID = $this->arrayIblock['ID'];

        $arCodes = array();
        $rsCodeLike = CIBlockSection::GetList(array(), array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "CODE" => $CODE."%",
        ), false, array("ID", "CODE"));
        while($ar = $rsCodeLike->Fetch())
            $arCodes[$ar["CODE"]] = $ar["ID"];

        if (array_key_exists($CODE, $arCodes))
        {
            $i = 1;
            while(array_key_exists($CODE."_".$i, $arCodes))
                $i++;

            return $CODE."_".$i;
        }
        else
        {
            return $CODE;
        }
    }
    
    protected function createProductIblock($model)
    {
        $this->arFields["IBLOCK_ID"] = $this->iblock_id;
    }
    
    protected function createProductName($model)
    {
        if(isset($model->vendor) && $model->vendor && is_string($model->vendor))
            $model->name = $model->vendor." ".$model->name;
        //else
        $this->arFields["NAME"] = $model->name;
    }
    
    protected function createProductCode($model)
    {
        if(!$this->updateElementID && isset($this->settings["DOP"]["ELEMENT_CODE"]) && $this->settings["DOP"]["ELEMENT_CODE"]=="Y")
        {
            $code = $this->getCodeElement($model->name);
            $this->arFields["CODE"] = $code;
        }
    }
    
    protected function createProductCodeOffer($offer)
    {
        if(!$this->updateElementID && isset($this->settings["DOP"]["ELEMENT_CODE"]) && $this->settings["DOP"]["ELEMENT_CODE"]=="Y")
        {
            $code = $this->getCodeElement($offer->name);
            $this->arFields["CODE"] = $code;
        }
    }
    
    /*protected function createProductActive($model)
    {
        if(isset($this->settings["DOP"]["ELEMENT_ACTIVE"]) && $this->settings["DOP"]["ELEMENT_ACTIVE"]=="Y")
        {
            $this->arFields["ACTIVE"] = "Y";
        }else
            $this->arFields["ACTIVE"] = "N";
    }*/
    
    protected function createProductActive($model)
    {
        if(isset($this->settings["DOP"]["ELEMENT_ACTIVE"]) && $this->settings["DOP"]["ELEMENT_ACTIVE"]=="Y" && $this->checkUpdateInsert())
        {
            $this->arFields["ACTIVE"] = "Y";
        }elseif($this->checkUpdateInsert())
            $this->arFields["ACTIVE"] = "N";
    }
    
    protected function createProductActiveOffer($model)
    {
        if(isset($this->settings["DOP"]["ELEMENT_ACTIVE"]) && $this->settings["DOP"]["ELEMENT_ACTIVE"]=="Y" && $this->checkUpdateInsertOffer())
        {
            $this->arFields["ACTIVE"] = "Y";
        }elseif($this->checkUpdateInsertOffer())
            $this->arFields["ACTIVE"] = "N";
    }
    
    protected function createProductXmlID($model)
    {
        if($this->settings["PRODUCT"]["UNIQ_PRODUCT_RECORD"]=="xml_id")
            $this->arFields["XML_ID"] = $model->id;
        elseif($this->settings["PRODUCT"]["UNIQ_PRODUCT_RECORD"]=="prop" && $this->checkUpdateInsert("PROPS"))
            $this->arFields["PROPERTY_VALUES"]["SOTBIT_YM_ID"] = $model->id;
            
        $this->xmlID = $model->id;
    }
    
    protected function createProductXmlIDOffer($offer)
    {
        if($this->settings["OFFER"]["UNIQ_PRODUCT_RECORD"]=="xml_id")
            $this->arFields["XML_ID"] = $offer->id;
        elseif($this->settings["OFFER"]["UNIQ_PRODUCT_RECORD"]=="prop" && $this->checkUpdateInsertOffer("PROPS"))
            $this->arFields["PROPERTY_VALUES"]["SOTBIT_YM_ID"] = $offer->id;
            
        $this->xmlID = $offer->id;
    }
    
    protected function createProductSection($model, $sectID)
    {
        if($sectID && !$this->updateElementID)
        {
            $this->arFields["IBLOCK_SECTION_ID"] = $sectID;
        }    
    }
    
    protected function createProductSectionOffer($offer, $sectID)
    {
        if($sectID && !$this->updateElementID)
        {
            $this->arFields["IBLOCK_SECTION_ID"] = $sectID;
        }    
    }
    
    protected function createProductDescription($model)
    {
        if(isset($model->description) && !empty($model->description))
        {
            if($this->settings["PRODUCT"]["DESCRIPTION"]=="preview" && $this->checkUpdateInsert("PREVIEW_TEXT"))
            {
                $this->arFields["PREVIEW_TEXT"] = $model->description;
                $this->arFields["PREVIEW_TEXT_TYPE"] = "text";
            }elseif($this->settings["PRODUCT"]["DESCRIPTION"]=="detail" && $this->checkUpdateInsert("DETAIL_TEXT"))
            {
                $this->arFields["DETAIL_TEXT"] = $model->description;
                $this->arFields["DETAIL_TEXT_TYPE"] = "text";    
            }
            if($this->settings["PRODUCT"]["DESCRIPTION"]=="pd" && $this->checkUpdateInsert("PREVIEW_TEXT"))
            {
                $this->arFields["PREVIEW_TEXT"] = $model->description;
                $this->arFields["PREVIEW_TEXT_TYPE"] = "text";    
            }
            if($this->settings["PRODUCT"]["DESCRIPTION"]=="pd" && $this->checkUpdateInsert("DETAIL_TEXT"))
            {
                $this->arFields["DETAIL_TEXT"] = $model->description;
                $this->arFields["DETAIL_TEXT_TYPE"] = "text";    
            }
        }    
    }
    
    protected function createProductDescriptionOffer($offer)
    {
        if(isset($offer->description) && !empty($offer->description))
        {
            if($this->settings["OFFER"]["DESCRIPTION"]=="preview" && $this->checkUpdateInsertOffer("PREVIEW_TEXT"))
            {
                $this->arFields["PREVIEW_TEXT"] = $offer->description;
                $this->arFields["PREVIEW_TEXT_TYPE"] = "text";
            }elseif($this->settings["OFFER"]["DESCRIPTION"]=="detail" && $this->checkUpdateInsertOffer("DETAIL_TEXT"))
            {
                $this->arFields["DETAIL_TEXT"] = $offer->description;
                $this->arFields["DETAIL_TEXT_TYPE"] = "text";    
            }
            if($this->settings["OFFER"]["DESCRIPTION"]=="pd" && $this->checkUpdateInsertOffer("PREVIEW_TEXT"))
            {
                $this->arFields["PREVIEW_TEXT"] = $offer->description;
                $this->arFields["PREVIEW_TEXT_TYPE"] = "text";    
            }
            if($this->settings["OFFER"]["DESCRIPTION"]=="pd" && $this->checkUpdateInsertOffer("DETAIL_TEXT"))
            {
                $this->arFields["DETAIL_TEXT"] = $offer->description;
                $this->arFields["DETAIL_TEXT_TYPE"] = "text";    
            }
        }    
    }
    
    protected function createProductPreviewPicture($model)
    {
        if($this->settings["PRODUCT"]["PREVIEW_PICTURE"]=="1" && isset($model->previewPhoto) && !empty($model->previewPhoto) && $this->checkUpdateInsert("PREVIEW_PICTURE"))
        {
            $src = $model->previewPhoto->url;
            $src = $this->createImage($src);
            $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);    
        }elseif($this->settings["PRODUCT"]["PREVIEW_PICTURE"]=="1" && isset($model->previewPhotos) && !empty($model->previewPhotos) && $this->checkUpdateInsert("PREVIEW_PICTURE"))
        {
            foreach($model->previewPhotos as $photo)
            {
                $src = $photo->url;
                $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);
                break 1;
            }
        }
    }
    
    protected function createProductPreviewPictureOffer($offer)
    {
        if($this->settings["OFFER"]["PREVIEW_PICTURE"]=="1" && isset($offer->previewPhoto) && !empty($offer->previewPhoto) && $this->checkUpdateInsertOffer("PREVIEW_PICTURE"))
        {
            $src = $offer->previewPhoto->url;
            $src = $this->createImage($src);
            $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);    
        }elseif($this->settings["PRODUCT"]["PREVIEW_PICTURE"]=="1" && isset($offer->previewPhotos) && !empty($offer->previewPhotos) && $this->checkUpdateInsertOffer("PREVIEW_PICTURE"))
        {
            foreach($offer->previewPhotos as $photo)
            {
                $src = $photo->url;
                $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);
                break 1;
            }
        }
    }
    
    protected function createProductDetailPicture($model)
    {
        if($this->settings["PRODUCT"]["DETAIL_PICTURE"]=="1" && isset($model->mainPhoto) && !empty($model->mainPhoto) && $this->checkUpdateInsert("DETAIL_PICTURE"))
        {
            $src = $model->mainPhoto->url;
            $src = $this->createImage($src);
            $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);    
        }elseif($this->settings["PRODUCT"]["DETAIL_PICTURE"]=="1" && isset($model->bigPhoto) && !empty($model->bigPhoto) && $this->checkUpdateInsert("DETAIL_PICTURE"))
        {
            $src = $model->bigPhoto->url;
            $src = $this->createImage($src);
            $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);    
        }
        
        if($this->settings["PRODUCT"]["PREVIEW_PICTURE"]=="detail" && $this->settings["PRODUCT"]["DETAIL_PICTURE"]=="1" && $this->checkUpdateInsert("PREVIEW_PICTURE") && $this->checkUpdateInsert("DETAIL_PICTURE"))
        {
            $this->arFields["PREVIEW_PICTURE"] = $this->arFields["DETAIL_PICTURE"];    
        }
    }
    
    protected function createProductDetailPictureOffer($offer)
    {
        if($this->settings["OFFER"]["DETAIL_PICTURE"]=="1" && isset($offer->mainPhoto) && !empty($offer->mainPhoto) && $this->checkUpdateInsertOffer("DETAIL_PICTURE"))
        {
            $src = $offer->mainPhoto->url;
            $src = $this->createImage($src);
            $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);    
        }elseif($this->settings["OFFER"]["DETAIL_PICTURE"]=="1" && isset($offer->bigPhoto) && !empty($offer->bigPhoto) && $this->checkUpdateInsertOffer("DETAIL_PICTURE"))
        {
            $src = $offer->bigPhoto->url;
            $src = $this->createImage($src);
            $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);    
        }
        
        if($this->settings["OFFER"]["PREVIEW_PICTURE"]=="detail" && $this->settings["OFFER"]["DETAIL_PICTURE"]=="1" && $this->checkUpdateInsertOffer("PREVIEW_PICTURE") && $this->checkUpdateInsertOffer("DETAIL_PICTURE"))
        {
            $this->arFields["PREVIEW_PICTURE"] = $this->arFields["DETAIL_PICTURE"];    
        }
    }
    
    protected function createProductMorePicture($model)
    {
        if($this->settings["PRODUCT"]["MORE_PICTURE"]!="0" && isset($model->photos) && !empty($model->photos) && $this->checkUpdateInsert("PROPS"))
        {
            $code = $this->settings["PRODUCT"]["MORE_PICTURE"];
            $n = 0;
            foreach($model->photos as $photo)
            {
                $src = $photo->url;
                $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
                $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = ""; 
                $n++;   
            }
            
            if($this->updateElementID)
            {
                $arImages = $this->arFields["PROPERTY_VALUES"][$code];
                $obElement = new CIBlockElement;
                $rsProperties = $obElement->GetProperty($this->iblock_id, $this->updateElementID, "sort", "asc",  Array("CODE"=>$code));
                while($arProperty = $rsProperties->Fetch())
                {
                    $this->arFields["PROPERTY_VALUES"][$code][$arProperty["PROPERTY_VALUE_ID"]] = array(
                        "tmp_name" => "",
                        "del" => "Y",
                    );
                }
                unset($obElement);
            }    
        }    
    }
    
    protected function createProductMorePictureOffer($offer)
    {
        if($this->settings["OFFER"]["MORE_PICTURE"]!="0" && isset($offer->photos) && !empty($offer->photos) && $this->checkUpdateInsertOffer("PROPS"))
        {
            $code = $this->settings["OFFER"]["MORE_PICTURE"];
            $n = 0;
            foreach($offer->photos as $photo)
            {
                $src = $photo->url;
                $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
                $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = ""; 
                $n++;   
            }
            
            if($this->updateElementID)
            {
                $arImages = $this->arFields["PROPERTY_VALUES"][$code];
                $obElement = new CIBlockElement;
                $rsProperties = $obElement->GetProperty($this->iblock_id, $this->updateElementID, "sort", "asc",  Array("CODE"=>$code));
                while($arProperty = $rsProperties->Fetch())
                {
                    $this->arFields["PROPERTY_VALUES"][$code][$arProperty["PROPERTY_VALUE_ID"]] = array(
                        "tmp_name" => "",
                        "del" => "Y",
                    );
                }
                unset($obElement);
            }    
        }    
    }
    
    protected function checkImage()
    {
        $code = $this->settings["PRODUCT"]["MORE_PICTURE"];
        if($this->settings["PRODUCT"]["MORE_PICTURE"]!="0" && $this->settings["PRODUCT"]["PREVIEW_PICTURE"]=="1" && !isset($this->arFields["PREVIEW_PICTURE"]) && isset($this->arFields["PROPERTY_VALUES"][$code]))
        {
            foreach($this->arFields["PROPERTY_VALUES"][$code] as $arPic)
            {
                $this->arFields["PREVIEW_PICTURE"] = $arPic["VALUE"];
                break;    
            }    
        }
        if($this->settings["PRODUCT"]["MORE_PICTURE"]!="0" && $this->settings["PRODUCT"]["DETAIL_PICTURE"]=="1" && !isset($this->arFields["DETAIL_PICTURE"]) && isset($this->arFields["PROPERTY_VALUES"][$code]))
        {
            foreach($this->arFields["PROPERTY_VALUES"][$code] as $arPic)
            {
                $this->arFields["DETAIL_PICTURE"] = $arPic["VALUE"];
                break;    
            }    
        }    
    }
    
    protected function checkImageOffer()
    {
        $code = $this->settings["OFFER"]["MORE_PICTURE"];
        if($this->settings["OFFER"]["MORE_PICTURE"]!="0" && $this->settings["OFFER"]["PREVIEW_PICTURE"]=="1" && !isset($this->arFields["PREVIEW_PICTURE"]) && isset($this->arFields["PROPERTY_VALUES"][$code]))
        {
            foreach($this->arFields["PROPERTY_VALUES"][$code] as $arPic)
            {
                $this->arFields["PREVIEW_PICTURE"] = $arPic["VALUE"];
                break;    
            }    
        }
        if($this->settings["OFFER"]["MORE_PICTURE"]!="0" && $this->settings["OFFER"]["DETAIL_PICTURE"]=="1" && !isset($this->arFields["DETAIL_PICTURE"]) && isset($this->arFields["PROPERTY_VALUES"][$code]))
        {
            foreach($this->arFields["PROPERTY_VALUES"][$code] as $arPic)
            {
                $this->arFields["DETAIL_PICTURE"] = $arPic["VALUE"];
                break;    
            }    
        }    
    }
    
    protected function createProductUrl($model)
    {
        if($this->settings["PRODUCT"]["URL"]!="0" && $this->checkUpdateInsert("PROPS"))
        {
            $code = $this->settings["PRODUCT"]["URL"];
            $this->arFields["PROPERTY_VALUES"][$code] = $model->link;
        }        
    }
    
    protected function createProductVendor($model)
    {
        if($this->settings["PRODUCT"]["VENDOR"]!="0" && $this->checkUpdateInsert("PROPS"))
        {
            $code = $this->settings["PRODUCT"]["VENDOR"];
            
            if(isset($model->vendor))
                $value = $model->vendor;
            else{
                $vendorID = $model->vendorId;
                $vendor = $this->getVendor($vendorID);
                $value = $vendor->vendor->name;    
            }
            
            $this->createProp($value, $code, $code);
        }        
    }
    
    protected function createOfferVendor($offer)
    {
        if($this->settings["OFFER"]["VENDOR"]!="0" && $this->checkUpdateInsert("PROPS"))
        {
            $code = $this->settings["OFFER"]["VENDOR"];
            
            if(isset($offer->vendor) && is_array($offer->vendor) && $offer->vendor->name)
                $value = $offer->vendor->name;
            else{
                $vendorID = $offer->vendorId;
                $vendor = $this->getVendor($vendorID);
                $value = $vendor->vendor->name;    
            }
            
            $this->createProp($value, $code, $code);
        }        
    }
    
    protected function createProductUrlOffer($offer)
    {
        if($this->settings["OFFER"]["URL"]!="0" && $this->checkUpdateInsertOffer("PROPS"))
        {
            $code = $this->settings["OFFER"]["URL"];
            $this->arFields["PROPERTY_VALUES"][$code] = $offer->url;
        }        
    }
    
    protected function createProductShopOffer($offer)
    {
        if($this->settings["OFFER"]["SHOP"]!="0" && $this->checkUpdateInsertOffer("PROPS") && isset($offer->shopInfo->name))
        {
            $code = $this->settings["OFFER"]["SHOP"];
            $value = $offer->shopInfo->name; 
            $this->createProp($value, $code, $code);   
        }
    }
    
    protected function createProductProps($model)
    {
        if($this->settings["PRODUCT"]["DETAILS"]!="" && $this->checkUpdateInsert("PROPS"))
        {
            $id = $model->id;
            $details_set = $this->settings["PRODUCT"]["DETAILS"];
            $arData = $this->getModelDetails($id, $details_set);
            $this->calculateProps($arData);    
        }    
    }
    
    protected function createProductPropsOffer($offer)
    {
        if($this->settings["OFFER"]["DETAILS"]!="" && $this->checkUpdateInsertOffer("PROPS") && isset($offer->modelId))
        {
            $id = $offer->modelId;
            $details_set = $this->settings["OFFER"]["DETAILS"];
            $arData = $this->getModelDetails($id, $details_set);
            $this->calculateProps($arData);    
        }    
    }
    
    protected function calculateProps($arData)
    {
        if(isset($arData->modelDetails) && !empty($arData->modelDetails))
        {
            foreach($arData->modelDetails as $arParams)
            {
                $this->calculateItemProps($arParams->params);
            }
        }
    }
    
    protected function calculateItemProps($arParams)
    {
        if($arParams)
        {
            foreach($arParams as $param)
            {
                $name = $param->name;
                $value = $param->value;
                if(strpos($value, ","))
                {
                    $arVal = explode(",", $value);
                    foreach($arVal as &$v)
                    {
                        $v = str_replace(array($name.":", $name), "", $v);
                        $v = trim($v);
                    }
                    $this->createProp($arVal, $name);
                        
                }else{
                    $value = str_replace(array($name.":", $name), "", $value); 
                    $value = trim($value);
                    $this->createProp($value, $name);   
                }
            }    
        }    
    }
    
    protected function createProp($value, $name, $code=false)
    {
        $multi = is_array($value)?"Y":"N";
        if($code) $arProp = $this->checkPropCode($code, $multi);
        else 
            $arProp = $this->checkProp($name, $multi);
        if(!empty($arProp))
        {
            $code = $arProp["CODE"];
            if($arProp["PROPERTY_TYPE"]=="S")
            {
                $this->arFields["PROPERTY_VALUES"][$code] = $value;
            }elseif($arProp["PROPERTY_TYPE"]=="N" && $arProp["MULTIPLE"]!="Y")
            {
                $value =  str_replace(",", ".", $value);
                $value = preg_replace("/\.{1}$/", "", $value);
                $value = preg_replace('/[^0-9.]/', "", $value);
                $this->arFields["PROPERTY_VALUES"][$code] = $val;
            }elseif($arProp["PROPERTY_TYPE"]=="N" && $arProp["MULTIPLE"]=="Y")
            {
                $n = 0;
                if(is_array($value))
                {
                    foreach($value as $v)
                    {
                        $value =  str_replace(",", ".", $v);
                        $value = preg_replace("/\.{1}$/", "", $v);
                        $value = preg_replace('/[^0-9.]/', "", $v);
                        $this->arFields["PROPERTY_VALUES"][$code]["n".$n] = $v; 
                        $n++;   
                    }    
                }else{
                    $this->arFields["PROPERTY_VALUES"][$code]["n".$n] = $value;    
                }
                    
            }
            elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]!="Y")
            {
                $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsL($arProp["ID"], $code, $value);
            }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]=="Y")
            {
                $n = 0;
                if(is_array($value))
                {
                    foreach($value as $v)
                    {
                        $this->arFields["PROPERTY_VALUES"][$code]["n".$n] = $this->CheckPropsL($arProp["ID"], $code, $v); 
                        $n++;   
                    }    
                }else{
                    $this->arFields["PROPERTY_VALUES"][$code]["n".$n] = $this->CheckPropsL($arProp["ID"], $code, $value);    
                }
                
            }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]!="Y")
            {
                $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsE($arProp, $code, $value);
            }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]=="Y")
            {
                $n = 0;
                if(is_array($value))
                {
                    foreach($value as $v)
                    {
                        $this->arFields["PROPERTY_VALUES"][$code]["n".$n] = $this->CheckPropsE($arProp, $code, $v); 
                        $n++;   
                    }    
                }else{
                    $this->arFields["PROPERTY_VALUES"][$code]["n".$n] = $this->CheckPropsE($arProp, $code, $value);    
                }
            }    
        }
    }
    
    protected function checkProp($name, $multi)
    {
        if($name)
        {
            $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "NAME"=>$name))->Fetch();
            if($properties["ID"])
            {
                $arProp["ID"] = $properties["ID"]; 
                $arProp["CODE"]  = $properties["CODE"];
                $arProp["NAME"]  = $properties["NAME"]; 
                $arProp["LINK_IBLOCK_ID"]  = $properties["LINK_IBLOCK_ID"]; 
                $arProp["MULTIPLE"]  = $properties["MULTIPLE"];
                $arProp["PROPERTY_TYPE"] = $properties["PROPERTY_TYPE"];
                if($multi == "Y" && $arProp["MULTIPLE"]!=$multi)
                {
                    $ibp = new CIBlockProperty;
                    if(!$ibp->Update($arProp["ID"], array("MULTIPLE"=>"Y")))
                    {
                        $error = $this->arFields["NAME"]." [".$this->xmlID."] ".$ibp->LAST_ERROR;
                        $this->arErrors[] = $error;
                        $this->setError($error, true, $this->taskID);
                    }else
                        $arProp["MULTIPLE"] = "Y";    
                    unset($ibp);
                }
                
            }else{
                $code = $this->getCodeProp($name);
                $arFields = Array(
                    "NAME" => $name,
                    "ACTIVE" => "Y",
                    "CODE" => $code,
                    "PROPERTY_TYPE" => $this->isModel?$this->settings["PRODUCT"]["PROP_TYPE"]:$this->settings["OFFER"]["PROP_TYPE"],
                    "IBLOCK_ID" => $this->iblock_id,
                    "MULTIPLE" => $multi
                );
                $arProp = $arFields;
                $ibp = new CIBlockProperty;
                $PropID = $ibp->Add($arFields);
                if(!$PropID)
                {
                    $error = $this->arFields["NAME"]." [".$this->xmlID."] ".$ibp->LAST_ERROR;
                    $this->arErrors[] = $error;
                    $this->setError($error, true, $this->taskID);
                    $arProp = array();    
                }
                unset($ibp);
            }
        }
        return $arProp;    
    }
    
    protected function checkPropCode($code, $multi)
    {
        if($code)
        {
            $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "CODE"=>$code))->Fetch();
            if($properties["ID"])
            {
                $arProp["ID"] = $properties["ID"]; 
                $arProp["CODE"]  = $properties["CODE"];
                $arProp["NAME"]  = $properties["NAME"]; 
                $arProp["LINK_IBLOCK_ID"]  = $properties["LINK_IBLOCK_ID"]; 
                $arProp["MULTIPLE"]  = $properties["MULTIPLE"];
                $arProp["PROPERTY_TYPE"] = $properties["PROPERTY_TYPE"];
                if($multi == "Y" && $arProp["MULTIPLE"]!=$multi)
                {
                    $ibp = new CIBlockProperty;
                    if(!$ibp->Update($arProp["ID"], array("MULTIPLE"=>"Y")))
                    {
                        $error = $this->arFields["NAME"]." [".$this->xmlID."] ".$ibp->LAST_ERROR;
                        $this->arErrors[] = $error;
                        $this->setError($error, true, $this->taskID);
                    }else
                        $arProp["MULTIPLE"] = "Y";    
                    unset($ibp);
                }
                
            }else{
                $code = $this->getCodeProp($code);
                $arFields = Array(
                    "NAME" => $code,
                    "ACTIVE" => "Y",
                    "CODE" => $code,
                    "PROPERTY_TYPE" => $this->isModel?$this->settings["PRODUCT"]["PROP_TYPE"]:$this->settings["OFFER"]["PROP_TYPE"],
                    "IBLOCK_ID" => $this->iblock_id,
                    "MULTIPLE" => $multi
                );
                $arProp = $arFields;
                $ibp = new CIBlockProperty;
                $PropID = $ibp->Add($arFields);
                if(!$PropID)
                {
                    $error = $this->arFields["NAME"]." [".$this->xmlID."] ".$ibp->LAST_ERROR;
                    $this->arErrors[] = $error;
                    $this->setError($error, true, $this->taskID);
                    $arProp = array();    
                }
                unset($ibp);
            }
        }
        
        return $arProp;    
    }
    
    protected function getCodeProp($name)
    {
        $arProperty["CODE"] = CUtil::translit($name, LANGUAGE_ID, array(
                        "max_len" => 50,
                        "change_case" => 'U', // 'L' - toLower, 'U' - toUpper, false - do not change
                        "replace_space" => '_',
                        "replace_other" => '_',
                        "delete_repeat_replace" => true,
        ));
        if(preg_match('/^[0-9]/', $arProperty["CODE"]))
            $arProperty["CODE"] = '_'.$arProperty["CODE"];
        $IBLOCK_ID = $this->iblock_id;
        $obProperty = new CIBlockProperty;
        $rsProperty = $obProperty->GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$arProperty["CODE"]));
        if($arDBProperty = $rsProperty->Fetch())
        {
            $suffix = 0;
            do {
                $suffix++;
                $rsProperty = $obProperty->GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$arProperty["CODE"]."_".$suffix));
            } while ($rsProperty->Fetch());
            $arProperty["CODE"] .= '_'.$suffix;
        }
        unset($obProperty); 
        
        return $arProperty["CODE"];   
    }
    
    protected function CheckPropsL($id, $code, $val)
    {
        $res2 = CIBlockProperty::GetPropertyEnum(
            $id,
            array(),
            array("IBLOCK_ID" => $this->iblock_id, "VALUE" => $val)
        );

        if ($arRes2 = $res2->Fetch())
        {
            $kz = $arRes2["ID"];
        }
        else
        {
            $tmpid = md5(uniqid(""));
            $kz = CIBlockPropertyEnum::Add(
                array(
                "PROPERTY_ID" => $id,
                "VALUE" => $val,
                "TMP_ID" => $tmpid
            )
            );
        }

        return $kz;
    }
    
    protected function CheckPropsE($arProp, $code, $val)
    {
        $IBLOCK_ID = $arProp["LINK_IBLOCK_ID"];
        
        $rsProp = CIBlockElement::GetList(Array(), array("IBLOCK_ID"=>$IBLOCK_ID, "%NAME"=>$val), false, false, array("ID", "NAME"));
        while($arIsProp = $rsProp->Fetch())
        {
            $arIsProp["NAME"] = mb_strtolower($arIsProp["NAME"], LANG_CHARSET); 
            $val0 = mb_strtolower($val, LANG_CHARSET);
            if($val0==$arIsProp["NAME"])
            {
                $isProp = $arIsProp["ID"];
            }   
        }
        
        if($isProp) return $isProp;
        else{
            $arFields = array(
                "NAME"=>$val,
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $IBLOCK_ID
            );
            $el = new CIBlockElement;
            $id = $el->Add($arFields);
            unset($el);
            return $id;
        }
    }
    
    protected function createImage($src)
    {
        if($src)
        {
            $dir = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/sotbit.yandex/include/";
            $name = preg_replace("/.*path=/", "", $src);
            $img = file_get_contents($src);
            $newImg = $dir.$name;
            if($img)
                file_put_contents($newImg, $img);
                
            return $newImg;    
        }
        
    }
    
    protected function createProductPrice($model)
    {
        if($this->settings["PRODUCT"]["PRICE"]!="0" && $this->checkUpdateInsert("PRICE") && isset($model->prices))
        {
            $code = $this->settings["PRODUCT"]["PRICE"];
            $this->arPrice["PRICE"] = isset($model->prices->$code)?$model->prices->$code:$model->prices->min;
            $this->arPrice["CATALOG_GROUP_ID"] = $this->settings["CATALOG"]["PRICE_TYPE"];
            $this->arPrice["CURRENCY"] = $this->settings["CATALOG"]["CURRENCY"];    
        }
    }
    
    protected function createProductPriceOffer($offer)
    {   
        if($this->settings["OFFER"]["PRICE"]!="0" && $this->checkUpdateInsertOffer("PRICE") && isset($offer->price))
        {   
            $this->arPrice["PRICE"] = $offer->price->value;
            $this->arPrice["CATALOG_GROUP_ID"] = $this->settings["CATALOG"]["PRICE_TYPE"];
            $this->arPrice["CURRENCY"] = $this->settings["CATALOG"]["CURRENCY"];    
        }
    }
    
    protected function addElementCatalog($offer=false)
    {
        if(($offer && !$this->checkUpdateInsertOffer()) || (!$offer && !$this->checkUpdateInsert())) return false;
        
        $el = new CIBlockElement;
        $isElement = $this->updateElementID;
        if(!$isElement)
        {
            $id = $el->Add($this->arFields, "N", $this->settings["DOP"]["ELEMENT_INDEX"], $this->settings["DOP"]["RESIZE_IMAGE"]);
            if(!$id)
            {
                $error = $this->arFields["NAME"]."[".$this->xmlID."] - ".$el->LAST_ERROR;
                $this->arErrors[] = $error;
                $this->setError($error, true, $this->taskID);
            }else{
                $this->elementID = $id;
                $this->countProduct++;    
            } 
        }else{
             $this->elementID = $isElement;
             $name = $this->arFields["NAME"];
             unset($this->arFields["NAME"]);
             if(!$el->Update($isElement, $this->arFields))
             {
                $error = $name."[".$this->xmlID."] - ".$el->LAST_ERROR;
                $this->arErrors[] = $error;
                $this->setError($error, true, $this->taskID);
             }else
                $this->countProduct++;

             $this->arFields["NAME"] = $name;
        }
        unset($el);
           
    }
    
    protected function AddProductCatalog($offer=false)
    {
        if(($offer && !$this->checkUpdateInsertOffer("PARAM")) || (!$offer && !$this->checkUpdateInsert("PARAM"))) return false;
        
        $this->arProduct["MEASURE"] = $this->settings["CATALOG"]["MEASURE"];
        $this->arProduct["VAT_ID"] = $this->settings["CATALOG"]["CAT_VAT_ID"];
        $this->arProduct["VAT_INCLUDED"] = $this->settings["CATALOG"]["CAT_VAT_INCLUDED"];
        $this->arProduct["ID"] = $this->elementID;

        $isElement = $this->updateElementID;
        if(!$isElement)
        {
            if(!CCatalogProduct::Add($this->arProduct))
            {
                $error = $this->arFields["NAME"]."[".$this->xmlID."] ".GetMessage("sotbit_yandex_error_add_product");
                $this->arErrors[] = $error;
                $this->setError($error, true, $this->taskID);
            }
        }else{
            $this->UpdateProductCatalog($isElement);
        }

    }
    
    protected function UpdateProductCatalog($productID)
    {
        if(!$productID){
            $error = $this->arFields["NAME"]."[".$this->xmlID."] ".GetMessage("sotbit_yandex_error_update_product");
            $this->arErrors[] = $error;
            $this->setError($error, true, $this->taskID);
            return false;
        }
        CCatalogProduct::Update($productID, $this->arProduct);
    }
    
    protected function AddMeasureCatalog($offer=false)
    {
        if(($offer && !$this->checkUpdateInsertOffer("PARAM")) || (!$offer && !$this->checkUpdateInsert("PARAM"))) return false;
        $info = CModule::CreateModuleObject('catalog');
        if(!CheckVersion("14.0.0", $info->MODULE_VERSION))
        {
            if($this->settings["CATALOG"]["KOEF"]>0)
            {
                $arMes = array("RATIO"=>$this->settings["CATALOG"]["KOEF"], "PRODUCT_ID"=>$this->elementID);
                $str_CAT_MEASURE_RATIO = 1;
                $CAT_MEASURE_RATIO_ID = 0;
                $db_CAT_MEASURE_RATIO = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $this->elementID));
                if($ar_CAT_MEASURE_RATIO = $db_CAT_MEASURE_RATIO->Fetch())
                {
                    $str_CAT_MEASURE_RATIO = $ar_CAT_MEASURE_RATIO["RATIO"];
                    $CAT_MEASURE_RATIO_ID =  $ar_CAT_MEASURE_RATIO["ID"];
                }
                if($CAT_MEASURE_RATIO_ID>0)
                {
                    if(!CCatalogMeasureRatioAll::Update($CAT_MEASURE_RATIO_ID, $arMes))
                    {
                        $error = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("sotbit_yandex_error_ratio");
                        $this->arErrors[] = $error;
                        $this->setError($error, true, $this->taskID);
                    }    
                }
                else{
                    if(!CCatalogMeasureRatio::add($arMes))
                    {
                        $error = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("sotbit_yandex_error_ratio");
                        $this->arErrors[] = $error;
                        $this->setError($error, true, $this->taskID);
                    }
                }

            }
        }
    }
    
    protected function AddPriceCatalog($offer=false)
    {
        if(($offer && !$this->checkUpdateInsertOffer("PRICE")) || (!$offer && !$this->checkUpdateInsert("PRICE"))) return false;

        if(!$this->arPrice || !$this->arPrice["PRICE"]) return false;
        $isElement = $this->elementID;
        $this->arPrice["PRODUCT_ID"] = $this->elementID;
        $this->ChangePrice();
        $this->ConvertCurrency();
        /*$obPrice = new CPrice();
        if(!$isElement)
        {
            $price = $obPrice->Add($this->arPrice);
            if(!$price)
            {
                $error = $this->arFields["NAME"]."[".$this->xmlID."] ".GetMessage("sotbit_yandex_error_add_price").$obPrice->LAST_ERROR;
                $this->arErrors[] = $error;
                $this->setError($error, true, $this->taskID);
            }
        }else */$this->UpdatePriceCatalog($isElement);

    }
    
    protected function UpdatePriceCatalog($elementID)
    {
        if(!$elementID){
            $error = $this->arFields["NAME"]."[".$this->xmlID."] ".GetMessage("sotbit_yandex_error_update_price");
            $this->arErrors[] = $error;
            $this->setError($error, true, $this->taskID);
            return false;
        }
        $res = CPrice::GetList(
            array(),
            array(
                "PRODUCT_ID" => $elementID,
                "CATALOG_GROUP_ID" => $this->arPrice["CATALOG_GROUP_ID"]
                )
        );
        if ($arr = $res->Fetch())
        {   
            CPrice::Update($arr["ID"], $this->arPrice);
        }else{ 
            $obPrice = new CPrice();
            $price = $obPrice->Add($this->arPrice);
            if(!$price)
            {
                $error = $this->arFields["NAME"]."[".$this->xmlID."] ".GetMessage("sotbit_yandex_error_add_price").$obPrice->LAST_ERROR;
                $this->arErrors[] = $error;
                $this->setError($error, true, $this->taskID);
            } 
            unset($obPrice);   
        }
    }
    
    protected function ChangePrice()
    {
        if(is_array($this->settings["CATALOG"]["PRICE_UPDOWN"]) && count($this->settings["CATALOG"]["PRICE_UPDOWN"])>0)
        {
            foreach($this->settings["CATALOG"]["PRICE_UPDOWN"] as $i=>$val)
            {
                if($this->settings["CATALOG"]["PRICE_UPDOWN"][$i] && $this->settings["CATALOG"]["PRICE_VALUE"][$i])
                {
                    if($this->settings["CATALOG"]["PRICE_TERMS"][$i]=="delta")
                    {
                        if(empty($this->settings["CATALOG"]["PRICE_TERMS_VALUE"][$i]) && !empty($this->settings["CATALOG"]["PRICE_TERMS_VALUE_TO"][$i]))
                        {
                            if($this->arPrice["PRICE"]>$this->settings["CATALOG"]["PRICE_TERMS_VALUE_TO"][$i]) continue;
                        }
                        
                        if(!empty($this->settings["CATALOG"]["PRICE_TERMS_VALUE"][$i]) && empty($this->settings["CATALOG"]["PRICE_TERMS_VALUE_TO"][$i]))
                        {
                            if($this->arPrice["PRICE"]<$this->settings["catalog"]["PRICE_TERMS_VALUE"][$i]) continue;
                        }

                        if(!empty($this->settings["CATALOG"]["PRICE_TERMS_VALUE"][$i]) && !empty($this->settings["CATALOG"]["PRICE_TERMS_VALUE_TO"][$i]))
                        {
                            if($this->arPrice["PRICE"]<$this->settings["CATALOG"]["PRICE_TERMS_VALUE"][$i] || $this->arPrice["PRICE"]>$this->settings["CATALOG"]["PRICE_TERMS_VALUE_TO"][$i]) continue;
                        }
                    }
                    if($this->settings["CATALOG"]["PRICE_TYPE_VALUE"][$i]=="percent")
                    {
                        $delta = $this->arPrice["PRICE"]*$this->settings["CATALOG"]["PRICE_VALUE"][$i]/100; 
                    }else{
                        $delta = $this->settings["CATALOG"]["PRICE_VALUE"][$i];
                    }
                    if($this->settings["CATALOG"]["PRICE_UPDOWN"][$i]=="up")
                    {   
                        $this->arPrice["PRICE"] += $delta;
                    }
                    elseif($this->settings["CATALOG"]["PRICE_UPDOWN"][$i]=="down")
                    {    
                        $this->arPrice["PRICE"] -= $delta;
                    }
                    break;
                }
            }
        }
    }
    
    protected function ConvertCurrency()
    {
        if($this->settings["CATALOG"]["CONVERT_CURRENCY"])
        {
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["CONVERT_CURRENCY"];
            $this->arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($this->arPrice["PRICE"], $this->settings["CATALOG"]["CURRENCY"], $this->settings["CATALOG"]["CONVERT_CURRENCY"]);
        }
    }
    
    protected function deleteFields()
    {
        $this->deleteImage();
        if(isset($this->arFields) && !empty($this->arFields))
            unset($this->arFields);
            
        if(isset($this->arProduct) && !empty($this->arProduct))
            unset($this->arProduct);
            
        if(isset($this->arPrice) && !empty($this->arPrice))
            unset($this->arPrice);
            
        //if(isset($this->arErrors) && !empty($this->arErrors))
        //    unset($this->arErrors);
            
        $this->elementID = false;
        $this->updateElementID = false;
        
        unset($this->xmlID);
        
        if(isset($this->arResultElement))
            unset($this->arResultElement);   
    }
    
    protected function deleteImage()
    {
        if(isset($this->arFields["PREVIEW_PICTURE"]) && !empty($this->arFields["PREVIEW_PICTURE"]))
        {
            if(file_exists($this->arFields["PREVIEW_PICTURE"]["tmp_name"]))
                unlink($this->arFields["PREVIEW_PICTURE"]["tmp_name"]);    
        }
        if(isset($this->arFields["DETAIL_PICTURE"]) && !empty($this->arFields["DETAIL_PICTURE"]))
        {
            if(file_exists($this->arFields["DETAIL_PICTURE"]["tmp_name"]))
                unlink($this->arFields["DETAIL_PICTURE"]["tmp_name"]);    
        }
    }
    
    protected function AllDoProps($offer=false)
    {
        if(($offer && !$this->checkUpdateInsertOffer("PROPS")) && (!$offer && $this->checkUpdateInsert("PROPS"))) return false;
        if(isset($this->arFields["PROPERTY_VALUES"]) && !empty($this->arFields["PROPERTY_VALUES"]))
        {
            $isElement = $this->updateElementID;
            if($isElement)
            {
                $obElement = new CIBlockElement;
                $rsProperties = $obElement->GetProperty($this->iblock_id, $isElement, "sort", "asc");
                while($arProperty = $rsProperties->Fetch())
                {

                    if(isset($this->arFields["PROPERTY_VALUES"][$arProperty["CODE"]]) || $arProperty["PROPERTY_TYPE"]=="F") continue;
                    if($arProperty['VALUE'])$this->arFields["PROPERTY_VALUES"][$arProperty["ID"]][$arProperty['PROPERTY_VALUE_ID']] = array(
                        "VALUE"=>$arProperty['VALUE'],
                        "DESCRIPTION"=>$arProperty["DESCRIPTION"]
                    );
                }
            }    
        }
    }
}