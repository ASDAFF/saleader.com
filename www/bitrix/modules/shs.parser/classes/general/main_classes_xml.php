<?
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;
use Bitrix\Seo\Engine;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;

\Bitrix\Main\Loader::includeModule('seo');
\Bitrix\Main\Loader::includeModule('socialservices');

class SotbitXmlParser{
    
    public $id = false;
    public $rss;
    public $typeN;
    public $active;
    public $iblock_id;
    public $section_id;
    public $detail_dom;
    public $encoding;
    public $preview_delete_tag="";
    public $bool_preview_delete_tag="";
    public $detail_delete_tag="";
    public $bool_detail_delete_tag="";
    public $preview_first_img="";
    public $detail_first_img="";
    public $preview_save_img="";
    public $detail_save_img="";
    public $text = "";
    public $site = "";
    public $link = "";
    public $preview_delete_element="";
    public $detail_delete_element="";
    public $preview_delete_attribute="";
    public $detail_delete_attribute="";
    public $index_element="";
    public $resize_image="";
    public $meta_description="";
    public $meta_keywords="";
    public $meta_description_text="";
    public $meta_keywords_text="";
    public $agent = false;
    public $active_element = "Y";
    public $header_url;
    public $settings;
    public $countPage = 0;
    public $countItem = 0;
    public $stepStart = false;
    public $countSection = 0;

    public $page;
    const TEST = 0;
    const DEFAULT_DEBUG_ITEM = 30;
    public function __construct()
    {
        global $zis, $shs_ID, $shs_TYPE, $shs_ACTIVE, $shs_IBLOCK_ID, $shs_RSS, $shs_SECTION_ID, $shs_SELECTOR, $shs_ENCODING, $shs_PREVIEW_DELETE_TAG, $shs_PREVIEW_TEXT_TYPE, $shs_DETAIL_TEXT_TYPE, $shs_BOOL_PREVIEW_DELETE_TAG,$shs_PREVIEW_FIRST_IMG, $shs_PREVIEW_SAVE_IMG, $shs_DETAIL_DELETE_TAG, $shs_BOOL_DETAIL_DELETE_TAG,$shs_DETAIL_FIRST_IMG, $shs_DETAIL_SAVE_IMG, $shs_PREVIEW_DELETE_ELEMENT, $shs_DETAIL_DELETE_ELEMENT, $shs_PREVIEW_DELETE_ATTRIBUTE, $shs_DETAIL_DELETE_ATTRIBUTE, $shs_INDEX_ELEMENT, $shs_CODE_ELEMENT, $shs_RESIZE_IMAGE, $shs_META_DESCRIPTION, $shs_META_KEYWORDS, $shs_ACTIVE_ELEMENT, $shs_FIRST_TITLE, $shs_DATE_PUBLIC, $shs_FIRST_URL, $shs_DATE_ACTIVE, $shs_META_TITLE, $shs_SETTINGS, $shs_TMP;
        $this->id = $shs_ID;
        $this->typeN = $shs_TYPE;
        $this->rss = $shs_RSS;
        $this->active = $shs_ACTIVE;
        $this->iblock_id = $shs_IBLOCK_ID;
        $this->section_id = $shs_SECTION_ID;
        $this->detail_dom = $shs_SELECTOR;
        $this->first_url = trim($shs_FIRST_URL);
        $this->encoding = $shs_ENCODING;
        $this->preview_text_type = $shs_PREVIEW_TEXT_TYPE;
        $this->detail_text_type = $shs_DETAIL_TEXT_TYPE;
        $this->preview_delete_tag = $shs_PREVIEW_DELETE_TAG;
        $this->detail_delete_tag = $shs_DETAIL_DELETE_TAG;
        $this->bool_preview_delete_tag = $shs_BOOL_PREVIEW_DELETE_TAG;
        $this->bool_detail_delete_tag = $shs_BOOL_DETAIL_DELETE_TAG;
        $this->preview_first_img = $shs_PREVIEW_FIRST_IMG;
        $this->detail_first_img = $shs_DETAIL_FIRST_IMG;
        $this->preview_save_img = $shs_PREVIEW_SAVE_IMG;
        $this->detail_save_img = $shs_DETAIL_SAVE_IMG;
        $this->preview_delete_element = $shs_PREVIEW_DELETE_ELEMENT;
        $this->detail_delete_element = $shs_DETAIL_DELETE_ELEMENT;
        $this->preview_delete_attribute = $shs_PREVIEW_DELETE_ATTRIBUTE;
        $this->detail_delete_attribute = $shs_DETAIL_DELETE_ATTRIBUTE;
        $this->index_element = ($shs_INDEX_ELEMENT=="Y")?true:false;
        $this->code_element = $shs_CODE_ELEMENT;
        $this->resize_image = ($shs_RESIZE_IMAGE=="Y")?true:false;
        $this->meta_title = $shs_META_TITLE;
        $this->meta_description = $shs_META_DESCRIPTION;
        $this->meta_keywords = $shs_META_KEYWORDS;
        $this->active_element = $shs_ACTIVE_ELEMENT;
        $this->first_title = $shs_FIRST_TITLE;
        $this->date_public = $shs_DATE_PUBLIC;
        $this->date_active = $shs_DATE_ACTIVE;
        $this->tmp = $shs_TMP;
        $this->settings = unserialize(base64_decode($shs_SETTINGS));
        $this->header_url = "";
        $this->sleep = (int)$this->settings[$this->typeN]["sleep"];
        $this->proxy = (int)$this->settings[$this->typeN]["proxy"];
        $this->errors = array();
        $this->auth = $this->settings[$this->typeN]["auth"]["active"]?true:false;
        $this->currentPage = 0;
        $this->activeCurrentPage = 0;
        $this->debugErrors = array();
        $this->stepStart = false;
        $this->pagePrevElement = array();
        $this->pagenavigationPrev = array();
        $this->pagenavigation = array();
            
    }

    protected function parseXmlCatalog()
    {   
        set_time_limit(0);
        $this->ClearAjaxFiles(); 
        $this->DeleteLog(); 
        $this->checkActionBegin(); 
        $this->arUrl = array(); 
        if(isset($this->settings["catalog"]["url_dop"]) && !empty($this->settings["catalog"]["url_dop"]))$this->arUrl = explode("\r\n", $this->settings["catalog"]["url_dop"]);
        
        $this->arUrl = array_merge(array($this->rss), $this->arUrl);
        $this->arUrlSave = $this->arUrl;
        
        if(!$this->PageFromFile()) return false; 
        $this->CalculateStep();
        if($this->settings["catalog"]["mode"]!="debug" && !$this->agent) $this->arUrlSave = array($this->rss);
        else $this->arUrlSave = $this->arUrl; 
        //if(!$this->connectCatalogPage($this->rss));
        //return;
        foreach($this->arUrlSave as $rss):
            $rss = trim($rss);
            if(empty($rss)) continue;
            $this->rss = $rss;
            $this->convetCyrillic($this->rss);
            $this->connectCatalogPage($this->rss);
            
            if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && isset($this->errors) && count($this->errors)>0)
            {
                $this->SaveLog();
                unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
                unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_copy_page".$this->id.".txt");
                return false;    
            }
            if ($this->parseCatalogSectionXml($this->rss) === false)
            {
                if ($this->settings["catalog"]["add_parser_section"] == "Y")
                {
                    $this->SaveLog();
                    return false; 
                }   
            }
            $n = $this->currentPage;
            $this->parseCatalogProducts();
            if($this->settings["catalog"]["mode"]!="debug" && !$this->agent)
            {
                $this->stepStart = true;
                $this->SavePrevPage($this->rss);    
            }
         
            $this->SaveCurrentPage($this->pagenavigation);
            if($this->stepStart)
            {
                if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt"))
                    unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
                $this->DeleteCopyPage();
            } 
            if((!$this->CheckOnePageNavigation() && $this->agent) || (!$this->CheckOnePageNavigation() && !$this->agent && $this->settings["catalog"]["mode"]=="debug"))$this->parseCatalogPages();
            if($this->CheckOnePageNavigation() && $this->stepStart)
            {
                if($this->IsEndSectionUrl())$this->ClearBufferStop();
                else $this->ClearBufferStep();
                return false;    
            }
        endforeach;
        
        $this->checkActionAgent();
    }
 
    protected function parseCatalogProductElementXml(&$el)
    {
        $this->countItem++;
        $this->parseCatalogSection();
        if(!$this->parserCatalogPreviewXml($el))
        {
            $this->SaveCatalogError();
            $this->clearFields();
            return false;    
        }

        /*$this->parserCatalogDetail();
        $this->parseCatalogSection();
        $this->parseCatalogMeta();
        $this->parseCatalogFirstUrl();*/
        $this->parseCatalogDate();
        $this->parseCatalogAllFields();


        $db_events = GetModuleEvents("shs.parser", "parserBeforeAddElementXml", true);
        $error = false;
        foreach($db_events as $arEvent)
        {
            $bEventRes = ExecuteModuleEventEx($arEvent, array(&$this, &$el));
            if($bEventRes===false)
            {
                $error = true;
                break 1;
            }
        }

        if(!$error && !$error_isad)
        {
            $this->AddElementCatalog();
            foreach(GetModuleEvents("shs.parser", "parserAfterAddElementXml", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array(&$this, &$el));
        }

        if($this->isCatalog && $this->elementID)
        {   
            /*if($this->isOfferCatalog && !$this->boolOffer)
            {                                  
                $this->AddElementOfferCatalog();
                $this->elementID = $this->elementOfferID;
                $this->elementUpdate = $this->elementOfferUpdate;
            }*/
            if($this->boolOffer)
            {
                $this->addProductPriceOffers();
            }else{
                
                $this->AddProductCatalog();
                $this->AddMeasureCatalog();
                $this->AddPriceCatalog();    
            }
            
        }/*else{
            $this->AddElementOfferCatalog();
            $this->AddProductCatalog();
            $this->AddMeasureCatalog();
            $this->AddPriceCatalog();    
        }*/

        $this->SetCatalogElementsResult();
        $this->clearFields();
        
    }

    protected function parserCatalogPreviewXml(&$el)
    {
        if(!$this->parseCatalogIdElement($el)) return false;
        $this->parseCatalogNamePreview($el);
        //$this->parseCatalogPropertiesPreview($el);
        if($this->isCatalog)$this->parseCatalogPricePreview($el);
        if ($this->settings["catalog"]["add_parser_section"] == "Y") $this->parseCatalogParrentSection($el);
        $this->parseCatalogPreviewPicturePreview($el);
        $this->parseCatalogDetailPicture($el);
        $this->parseCatalogDescriptionXml($el);
        $this->parseCatalogDetailMorePhoto($el);
        $this->parseCatalogPropertiesXml($el);
        $this->parserOffersXml($el);
        
        return true;
    }
    
    protected function parserOffersXml($el)
    {
        $this->boolOffer = false;
        if($this->settings["offer"]["load"]=="table" && $this->isOfferParsing && isset($this->settings["offer"]["selector_item"]) && $this->settings["offer"]["selector_item"])
        {
            $this->parserOffersXmlTable($el);
        }elseif($this->settings["offer"]["load"]=="one" && $this->isOfferParsing && isset($this->settings["offer"]["one"]["selector"]) && $this->settings["offer"]["one"]["selector"])
        {
            $this->parserOffersOne($el);
        }
    
    }
    
    protected function parserOffersXmlTable(&$el)
    {
       $offerItem = $this->settings["offer"]["selector_item"];

       foreach(pq($el)->find($offerItem) as $offer)
       {
            $this->boolOffer = true;
            if($this->parseOfferName($offer))
            {
                $this->parseOfferPrice($offer);
                $this->parseOfferPropsXml($offer);
                if(!$this->parseOfferGetUniq())
                {
                    $this->deleteOfferFields();;
                    continue 1;
                }

            }else
                continue 1;

            $this->arOfferAll["FIELDS"][] = $this->arOffer;
            $this->arOfferAll["PRICE"][] = $this->arPriceOffer;

            $this->deleteOfferFields();
                
       }
    }
    
    protected function parserOffersOne(&$el)
    {
        $offerItem = trim($this->settings["offer"]["one"]["selector"]);
        foreach(pq($el)->find($offerItem) as $offer)
        {
            if (empty($this->settings["offer"]["one"]["separator"])){   
                $this->boolOffer = true;
                if($this->parseOfferNameOneXml($offer))
                {
                    $this->parseOfferPrice($offer);
                    $this->parseOfferProps($offer);
                    if(!$this->parseOfferGetUniq())
                    {
                        $this->deleteOfferFields();;
                        continue 1;
                    }

                }else
                    continue 1;

                $this->arOfferAll["FIELDS"][] = $this->arOffer;
                $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
                $this->deleteOfferFields(); 
            }
            elseif(!empty($this->settings["offer"]["one"]["separator"])){
                if(is_array($arrOffers = $this->GetArrayNameOffes($offer)))
                {
                    foreach($arrOffers as $nameOffer)
                    {
                        if(empty($nameOffer)) continue 1;
                        $this->boolOffer = true;
                        if($this->parseOfferNameOneXml($offer, $nameOffer))
                        {
                            $this->parseOfferPrice($offer);
                            $this->parseOfferProps($offer, $nameOffer);
                            if(!$this->parseOfferGetUniq())
                            {
                                $this->deleteOfferFields();;
                                continue 1;
                            }file_put_contents(dirname(__FILE__)."/log1111.log", print_r($this->arOffer, true),FILE_APPEND);
                        }else
                            continue 1;
                        $this->arOfferAll["FIELDS"][] = $this->arOffer;
                        $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
                        $this->deleteOfferFields();
                    }
                }   
            } 
        }
    }

    protected function parseOfferNameOneXml($offer, $nameOffer=false)
    {
        if(!empty($this->settings["offer"]["one"]["selector"]) && !empty($this->settings["offer"]["add_name"]))
        {
            if ($nameOffer === false)
            {
                $arr = $this->GetArraySrcAttr($this->settings["offer"]["selector_name"]);
                if (empty($arr["path"]) && !empty($arr["attr"]))
                {
                    $name = trim(pq($offer)->attr($arr["attr"]));
                }
                else{
                    if(empty($arr["attr"])){
                        $name = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                    }
                    elseif(!empty($arr["attr"]))
                    {
                        $name = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                    }
                } 
            } 
            elseif ($nameOffer !== false)
            {
                $name =  $nameOffer;  
            } 
            $deleteSymb = $this->getOfferDeleteSelector();
            $name = str_replace($deleteSymb, "", $name);
            $this->arOffer["NAME"] = htmlspecialchars_decode($name);
            if(isset($this->settings["loc"]["f_name"]) && $this->settings["loc"]["f_name"]=="Y")
            {
                $this->arOffer["NAME"] = $this->locText($this->arOffer["NAME"]);    
            }
        }
        if(!isset($this->arOffer["NAME"]) && (!isset($this->settings["offer"]["add_name"]) || empty($this->settings["offer"]["add_name"])))
        {
            $this->errors[] = GetMessage("parser_error_name_notfound_offer");
            return false;
        }elseif(!isset($this->arOffer["NAME"]))
            $this->arOffer["NAME"] = $this->arFields["NAME"];     
        return true;
        
    }
    
    protected function GetArrayNameOffes($offer)
    {
        $arr = $this->GetArraySrcAttr($this->settings["offer"]["selector_name"]);
        if(empty($arr["path"]) && !empty($arr["attr"]))
        {
            $name = trim(pq($offer)->attr($arr["attr"]));
        }
        else{
            if(empty($arr["attr"])){
                $name = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
            }
            elseif(!empty($arr["attr"]))
            {
                $name = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
            }
        } 
        
        $arrName = explode($this->settings["offer"]["one"]["separator"], $name);
        for($i = 0; $i < count($arrName); $i++)
        {
            $arrName[$i] = trim($arrName[$i]);
        }
        return $arrName;
    }
    
    protected function parseOfferPropsXml($offer)
    {
        if($this->checkUniq() && !$this->isUpdate) return false;
        if(isset($this->settings["offer"]["selector_prop"]) && !empty($this->settings["offer"]["selector_item"]))
        {
            $deleteSymb = $this->getOfferDeleteSelector();
            $deleteSymbRegular = $this->getOfferDeleteSelectorRegular();
            
            $arProperties = $this->arSelectorPropertiesOffer;
            
            foreach($arProperties as $code=>$val)
            {
                $arProp = $this->arPropertiesOffer[$code];
                if($arProp["PROPERTY_TYPE"]=="F")
                {
                    $this->parseCatalogPropFile($code, $offer);
                }else{
                    
                    $arr = $this->GetArraySrcAttr($this->settings["offer"]["selector_prop"][$code]);
                    if (empty($arr["path"]) && !empty($arr["attr"]))
                    {
                        $text = trim(pq($offer)->attr($arr["attr"]));
                    }
                    else{
                        if(empty($arr["attr"])){
                            $text = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                        }
                        elseif (!empty($arr["attr"]))
                        {
                            $text = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                        }
                    }
                    if($arProp["USER_TYPE"]!="HTML")
                        $text = strip_tags($text);
                    $text = str_replace($deleteSymb, "", $text);
                    $text = preg_replace($deleteSymbRegular, "", $text);
                    $this->parseCatalogPropOffer($code, $val, $text);
                }

            }
                
        }
    }
    
    protected function parseCatalogPropertiesXml(&$el)
    {
        if($this->checkUniq() && !$this->isUpdate) return false;
        $this->parseCatalogDefaultProperties($el);
        $this->parseCatalogSelectorProperties($el);
        $this->parseCatalogAutoProps($el);
        $this->parseCatalogAutoPropsAdd();
        $this->AllDoProps();
        if($this->isCatalog)$this->parseCatalogFindProduct($el);
        if($this->isCatalog)$this->parseCatalogSelectorProduct($el);
    }

    protected function parseCatalogAutoProps(&$el)
    {
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        elseif (($this->settings["catalog"]["add_auto_props"] != "Y") && empty($this->settings["catalog"]["selector_find_props"]) && empty($this->settings["catalog"]["attr_auto_props"])) return false;
        $props = $this->settings["catalog"]["selector_find_props"];
        $props_name = $this->GetArraySrcAttr($this->settings["catalog"]["attr_auto_props"]);
        $arr_val = $this->GetArraySrcAttr($this->settings["catalog"]["selector_attr_value_auto_props"]);
        
        foreach(pq($el)->find($props) as $property)
        {
            $isset_props = false;
            
            if(empty($props_name["path"])){
                $name = trim(pq($property)->attr($props_name["attr"])); //name props
            }
            else{
                if(empty($props_name["attr"]))
                {
                    $name = trim(strip_tags(pq($property)->find($props_name["path"])->html()));
                }
                elseif(!empty($props_name["attr"]))
                {
                    $name = trim(pq($property)->find($props_name["path"])->attr($props_name["attr"]));
                }
            }
               
            if (empty($name)) continue 1;
            
            if (empty($arr_val["path"]) && empty($arr_val["attr"]))
            {
                $value = trim(pq($property)->text());
            }
            elseif(empty($arr_val["path"]) && !empty($arr_val["attr"]))
            {
                $value = trim(pq($property)->attr($arr_val["attr"]));
            }
            else
            {
                if(empty($arr_val["attr"]))
                {
                    $value = strip_tags(trim(pq($property)->find($arr_val["path"])->html()));
                }
                elseif(!empty($arr_val["attr"]))
                {
                    $value = trim(pq($property)->find($arr_val["path"])->attr($arr_val["attr"]));
                }
            }
            
            $isset_props = $this->issetPropsForName($name);
            if (!$isset_props)
            {
                $error = $this->addAutoProps($name);
                if ($error !== false)
                {
                    $db_props = CIBlockProperty::GetByID($error, $this->iblock_id, false);
                    $code_props = $db_props->GetNext();
                    if(!isset($this->arProperties[$code_props["CODE"]]) || empty($this->arProperties[$code_props["CODE"]]))
                    {
                        $this->arProperties[$code_props["CODE"]] = $code_props;
                    }
                    if (!isset($this->arPropertiesParseAuto[$code_props["CODE"]]) || empty($this->arPropertiesParseAuto[$code_props["CODE"]]))
                    {
                        $this->arPropertiesParseAuto[$code_props["CODE"]] = $value;
                    }
                }
            }
            if($isset_props)
            {
                foreach($isset_props as $code => $props)
                {
                    if(!isset($this->arProperties[$code]) || empty($this->arProperties[$code]))
                    {
                        $this->arProperties[$code] = $props;
                    }
                    if (!isset($this->arPropertiesParseAuto[$code]) || empty($this->arPropertiesParseAuto[$code]))
                    {
                        $this->arPropertiesParseAuto[$code] = $value;
                    }
                }
            }
           
        }
        
    }
    
    protected function parseCatalogAutoPropsAdd()
    {
        $arProperties = $this->arPropertiesParseAuto;
        if(!$arProperties) return false;
        if($this->settings["catalog"]["catalog_delete_selector_find_props_symb"])
        {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_find_props_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if($symb=="\\\\")
                {
                    $deleteSymb[$i] = ",";
                }

            }
        }
        foreach($arProperties as $code => $value)
        {
            $arProp = $this->arProperties[$code];
            if(($arProp["PROPERTY_TYPE"] == "S") || ($arProp["PROPERTY_TYPE"] == "L") || ($arProp["PROPERTY_TYPE"] == "N")) 
            {   
                $text = $value;
                if($arProp["USER_TYPE"]!="HTML")
                    $text = strip_tags($value);
                $text = str_replace($deleteSymb, "", $text);
                $this->parseCatalogPropAuto($code, $text);
            }
        }
    }
    
    public function parseCatalogPropAuto($code, $val)
    {
        if(empty($code)) return false;
        $val = html_entity_decode($val);
        $arProp = $this->arProperties[$code];
        //$default = $this->settings["catalog"]["default_prop"][$code];
        
        if($arProp["PROPERTY_TYPE"]!="N" && isset($this->settings["loc"]["f_props"]) && $this->settings["loc"]["f_props"])
            $val = $this->locText($val, $arProp["USER_TYPE"]=="HTML"?"html":"plain");
        
        if($arProp["USER_TYPE"]=="HTML" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = Array("VALUE" => Array ("TEXT" => $val, "TYPE" => "html"));
        }elseif($arProp["USER_TYPE"]=="HTML" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = Array("VALUE" => Array ("TEXT" => $val, "TYPE" => "html"));
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsDirectory($arProp, $code, $val);;
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsDirectory($arProp, $code, $val);;    
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y")
        {
            $val = $this->actionFieldProps($code, $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        }elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y")
        {
            $val = $this->actionFieldProps($code, $val);
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $val;
        }
        elseif($arProp["PROPERTY_TYPE"]=="N")
        {
            $val =  str_replace(",", ".", $val);
            $val = preg_replace("/\.{1}$/", "", $val);
            $val = preg_replace('/[^0-9.]/', "", $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;    

        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsL($arProp["ID"], $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsL($arProp["ID"], $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]!="Y")
        {   
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsE($arProp, $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]=="Y")
        {   
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsE($arProp, $code, $val);
        }
    }
    
    protected function addAutoProps($setting) //add auto props
    {
        if (empty($setting)) return false;
        $CODE = strtoupper(CUtil::translit($setting, "ru", array(
                        "max_len" => 100,
                        "change_case" => 'S', // 'L' - toLower, 'U' - toUpper, false - do not change
                        "replace_space" => '_',
                        "replace_other" => '_',
                        "delete_repeat_replace" => true,
          )));
        $arFields = Array(
          "NAME" => $setting,
          "ACTIVE" => "Y",
          "SORT" => "100",
          "CODE" => $CODE,
          "PROPERTY_TYPE" => $this->settings["catalog"]["type_auto_props"],
          "IBLOCK_ID" => $this->iblock_id
          );
          $ibp = new CIBlockProperty;
          $PropID = $ibp->Add($arFields);
          if ($PropID) return $PropID;
          else 
          {
              $this->errors[] = "[".$setting."]".$ibp->LAST_ERROR;
              return false;
          }
    }
    
    protected function issetPropsForName($name) //search props for name
    {
        if (empty($name)) return true;
        $property = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "?PROPERTY_TYPE" => "S || N || L", "NAME" => $name));
        $prors = array();
        while($prop = $property->Fetch())
        {
             $prors[$prop["CODE"]] = $prop;
        }
        if($property)
        {
            return $prors;
        }
        if(!$property) return false;
    }
  
    protected function parseCatalogIdElement($el)
    {   
        $id_element = $this->settings["catalog"]["id_selector"];
        
        $ar = $this->GetArraySrcAttr($id_element);
        $selector = $ar["path"];
        $attr = $ar["attr"];
        if (empty($id_element)) return false;
        if ($selector == "")
        {
            $p = trim(pq($el)->attr($attr));
        }
        else{
            if(!empty($attr)) 
            {
                $p = trim(pq($el)->find($selector)->attr($attr)); 
            }
            elseif(empty($attr))
            {
                $p = strip_tags((pq($el)->find($selector)->html())); 
            }
        }
        if(!$p)
        { 
            $this->errors[] = GetMessage("parser_error_id_element_notfound");
            return false;
        }
        $this->arFields["LINK"] = $p;
        return true;
    }
    
    protected function parseCatalogParrentSection(&$el)
    {
        if (($this->settings["catalog"]["add_parser_section"] == "N") || empty($this->settings["catalog"]["id_section"])) return false;
        $ar = $this->GetArraySrcAttr($this->settings["catalog"]["id_section"]);
        $selector = $ar["path"];
        $attr = $ar["attr"];
        
        if($selector == ""){
            $parrent_id = trim(pq($el)->attr($attr));
        }
        else
        {
            if(empty($attr)) $parrent_id = trim(strip_tags(pq($el)->find($selector)->html()));
            elseif(!empty($attr)) $parrent_id = trim(pq($el)->find($selector)->attr());
        }
        /*$parrent_id = trim($this->settings["catalog"]["id_section"]);
        $parrent_id = trim(pq($el)->find($parrent_id)->text());*/
        if(empty($parrent_id)) return false;
        elseif (!empty($parrent_id)) $parrent_id = $this->issetSectionCatalog($parrent_id);
        if($parrent_id !== false)
        {
            $this->arFields["IBLOCK_SECTION_ID"] = $parrent_id;
        }
    }

    protected function parseCatalogSectionXml($pageHref)
    {   
        $this->html = phpQuery::newDocument($this->page);
        $this->base = $this->GetMetaBase($this->html);
        if ($this->settings["catalog"]["add_parser_section"] !== "Y") return false;
        if (empty($this->settings['catalog']['selector_category']))
        {   
            $this->errors[] = GetMessage("parser_no_selector_category");
            return false;
        }
        $arr = $this->GetArrSectionXml();
        if ($arr !== false)
        {
            $new_section_arr = $this->GetTreeArrSectionXml($arr);
        }
        if(is_array($new_section_arr))
        {
            if($this->settings["catalog"]["field_id_category"] == "EXT_FIELD")
            {
                $this->AddExtFieldSection();
            }
            $this->addAllSectionXml($new_section_arr);
        }
    }
    
    protected function GetArrSectionXml()
    {
        if(empty($this->settings['catalog']['selector_category'])) return false;
        
        $arr_section = $this->html->find($this->settings['catalog']['selector_category']);
        $arr = array();
        $arr_id = $this->GetArraySrcAttr($this->settings['catalog']["attr_id_category"]);
        $arr_name = $this->GetArraySrcAttr($this->settings['catalog']["attr_category"]);
        $arr_parent = $this->GetArraySrcAttr($this->settings['catalog']["attr_id_parrent_category"]);
        
        
        foreach ($arr_section as $el_section)
        {
            if ($arr_id['path'] == "")
            {
                $section_id = trim(pq($el_section)->attr($arr_id['attr']));
            } 
            else{
                if(!empty($arr_id['attr'])) $section_id = trim(pq($el_section)->find($arr_id['path'])->attr($arr_id['attr']));
                elseif(empty($arr_id['attr']))
                {
                    $section_id = trim(pq($el_section)->find($arr_id['path'])->html());
                    $section_id = trim(strip_tags($section_id));
                }
            }  
            if (empty($section_id))
            {
                continue 1; 
            }
            if ($arr_parent["path"] == "") 
            {
                $parentId = trim(pq($el_section)->attr($arr_parent["attr"]));
            }
            else{
                if (!empty($arr_parent["attr"])) $parentId = trim(pq($el_section)->find($arr_parent["path"])->attr($arr_parent["attr"]));
                elseif(empty($arr_parent["attr"])) {
                    $parentId = trim(pq($el_section)->find($arr_parent["path"])->html());
                    $parentId = trim(strip_tags($parentId));
                }
                 
            }
            if (!isset($parentId) || empty($parentId)) $parentId = 0;
            
            if(empty($this->settings['catalog']["attr_category"]))
            {
                $arr[$section_id]['text'] = strip_tags(trim(pq($el_section)->html()));
            }
            elseif(!empty($this->settings['catalog']["attr_category"]))
            {
                if (!empty($arr_name['attr'])) 
                {
                    $arr[$section_id]['text'] = trim(pq($el_section)->find($arr_name["path"])->attr($arr_name["attr"]));
                }
                elseif(empty($arr_name['attr']))
                {
                    $arr[$section_id]['text'] = strip_tags(trim(pq($el_section)->find($arr_name["path"])->html()));
                }
            }
            $arr[$section_id]['parentId'] = $parentId;
        }
        
        ksort($arr);
        return $arr;
    }
    
    protected function GetTreeArrSectionXml($arr)
    {
        if (!is_array($arr)) return false;
        
        $new_section_arr = array();
        foreach ($arr as $key => $value) 
        {
            if (!isset($new_section_arr[$value['parentId']]) || empty($new_section_arr[$value['parentId']]))
                $new_section_arr[$value['parentId']]['text'] = $arr[$value['parentId']]['text']; 
        }
        
        foreach ($arr as $k => $v)
        {
            $new_section_arr[$v['parentId']][$k]['text'] = $v['text'];
        }
        ksort($new_section_arr);
        return $new_section_arr;
    }
    
    protected function addAllSectionXml($arr)
    {                    
        if (empty($arr)) return false;
        foreach ($arr as $id => $val)
        {
            $section_title =  $arr[$id]['text'];
            if (empty($section_title) && ($id != 0))
            {
                continue;
            } 
            elseif ($id == 0) $parrent_section = $this->section_id;
            elseif ($id != 0) $parrent_section = $this->issetSectionCatalog($id); 
            
            if (($this->issetSectionCatalog($id) === false) && ($id != 0))
            {
                $this->addSectionXlm(array("id_section" => $id, "text_section" => $section_title, "id_parrent" => $parrent_section));
            }
            foreach ($val as $key => $text)
            {  
                if($key !== "text")
                {
                   if (($this->issetSectionCatalog($key) === false) && $parrent_section !== false)
                   {
                       $this->addSectionXlm(array("id_section" => $key, "text_section" => $text["text"], "id_parrent" => $parrent_section));
                   } 
                }
            }           
        }
    }
    
    protected function addSectionXlm($settings)
    {
        if (empty($settings) || !is_array($settings)) return false;
        $new_section = new CIBlockSection;
        $arFields = $this->GetArrFieldsSection($settings);
        $ID = $new_section->Add($arFields);
        if ($ID !== false)
        {   
            $this->countSection ++; 
        } 
        elseif ($ID === false)
        {
            $this->errors[] = $new_section->LAST_ERROR;
        } 
    }
    
    protected function GetArrFieldsSection($settings)
    {
        $code = $this->GetCodeSection($settings["text_section"]);
         $arFields = Array(
              "ACTIVE" => "Y",
              "CODE" => $code,
              "IBLOCK_SECTION_ID" => $settings["id_parrent"],
              "IBLOCK_ID" => $this->iblock_id,
              "NAME" => $settings["text_section"],
              "DESCRIPTION" => GetMessage("parser_add_category_description"),
              "DESCRIPTION_TYPE" => "text"
        );
        if ($this->settings["catalog"]["field_id_category"] == "XML_ID")
        {
            $arFields["XML_ID"] = "shs_".md5($this->rss)."_".$settings["id_section"];
        }
        elseif ($this->settings["catalog"]["field_id_category"] == "EXT_FIELD")
        {
            $arFields["UF_SHS_PARSER"] = "shs_".md5($this->rss)."_".$settings["id_section"];
        }
        return $arFields;
    }
    
    protected function GetCodeSection($settings)
    {
        $code = CUtil::translit($settings, "ru", array("max_len" => 100, "change_case" => 'L', "replace_space" => '_', "replace_other" => '_', "delete_repeat_replace" => true));
        $db_section = CIBlockSection::GetList(Array("SORT"=>"ASC"), array("%CODE" => $code, "IBLOCK_ID" => $this->iblock_id) , false, Array("ID"), false);
        $i = 0;
        while($res = $db_section->Fetch())
        {
            $i++;
        }
        if($i == 0) return $code;
        else return $code."_".$i++;
    }
    
    protected function AddExtFieldSection()
    {
        $arFields = Array(
            "ENTITY_ID" => "IBLOCK_".$this->iblock_id."_SECTION",
            "FIELD_NAME" => "UF_SHS_PARSER",
            "USER_TYPE_ID" => "string",
            "EDIT_FORM_LABEL" => Array("ru"=>GetMessage("EDIT_FORM_LABEL_RU"), "en"=>GetMessage("EDIT_FORM_LABEL_EN"))
            );
        $obUserField  = new CUserTypeEntity;
        $obUserField->Add($arFields);
    }
    
    protected function issetSectionCatalog($settings)
    {                       
        if (empty($settings)) return false;
        $arFilter = $this->GetArrFilterSection($settings);
        if (!is_array($arFilter)) return false;
        $db_section = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, Array("ID", "XML_ID", "UF_SHS_PARSER"), false);
        $id_section = $db_section->Fetch();
        if ($id_section) return $id_section["ID"];
        else return false;
    }
    
    protected function GetArrFilterSection($settings)
    {
        if ($this->settings["catalog"]["field_id_category"] == "XML_ID")
        {
            $arFilter = Array('IBLOCK_ID'=>$this->iblock_id, '=XML_ID'=>"shs_".md5($this->rss)."_".$settings);  
        }
        elseif ($this->settings["catalog"]["field_id_category"] == "EXT_FIELD") 
        {
            $arFilter = Array('IBLOCK_ID'=>$this->iblock_id, '=UF_SHS_PARSER'=>"shs_".md5($this->rss)."_".$settings);  
        }
        return $arFilter;
    }
    
    protected function parseCatalogDescriptionXml(&$el)
    {
        if($this->checkUniq() && (!$this->isUpdate || (!$this->isUpdate["detail_descr"] && (!$this->isUpdate["preview_descr"] && !$this->settings["catalog"]["text_preview_from_detail"]!="Y")))) return false;
        if($this->settings["catalog"]["detail_text_selector"])
        {
            $detail = $this->settings["catalog"]["detail_text_selector"];
            
            $arDetail = explode(",", $detail);
            $detail_text = "";
            if($arDetail && !empty($arDetail))
            {
                foreach($arDetail as $detail)
                {
                    $detail = trim($detail);
                    if(!$detail) continue 1;

                    foreach(pq($el)->find($detail." img") as $img)
                    {
                        $src = pq($img)->attr("src");
                        $src = $this->parseCaralogFilterSrc($src);
                        $src = $this->getCatalogLink($src);
                        $this->parseCatalogSaveImgServer($img, $src);
                    }
                    
                    $arr = $this->GetArraySrcAttr($detail);
                    $path = $arr["path"];
                    $attr = $arr["attr"];
                    if(empty($attr))
                        $detail_text .= pq($el)->find($path)->html();
                    elseif(!empty($attr))
                        $detail_text .= pq($el)->find($path)->attr($attr);

                }
            }

            $detail_text = trim($detail_text);
            if(isset($this->settings["loc"]["f_detail_text"]) && $this->settings["loc"]["f_detail_text"]=="Y")
            {
                $detail_text = $this->locText($detail_text, $this->detail_text_type=="html"?"html":"plain");    
            }
            $this->arFields["DETAIL_TEXT"] = $detail_text;
            $this->arFields["DETAIL_TEXT_TYPE"] = $this->detail_text_type;
            if($this->settings["catalog"]["text_preview_from_detail"]=="Y")
            {
                $this->arFields["PREVIEW_TEXT"] = $this->arFields["DETAIL_TEXT"];
                $this->arFields["PREVIEW_TEXT_TYPE"] = $this->arFields["DETAIL_TEXT_TYPE"];
            }
        }
    }
    
    protected function ValidateUrl($url)
    {
        if (preg_match("/^(http|https)?(:\/\/)?([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/Diu", $url))
        {
           return true; 
        }
        else 
        {
            return false;
        }
    }

}
?>