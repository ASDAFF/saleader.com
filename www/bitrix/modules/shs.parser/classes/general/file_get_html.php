<?php
class FileGetHtml{
    public $headerUrl = "";
    public $httpCode = "";
    function file_get_html($path, $proxy=false, $auth=false, $this_=false) {
        global $shs_ID; 
        $ch = curl_init($path); 
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);// �� ��������� SSL ����������
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);    // �� ��������� Host SSL �����������
        curl_setopt($ch, CURLOPT_TIMEOUT, 1200);        // ������� ������
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
         
        if($auth && $shs_ID)
        {
            if(isset($this_->settings[$this_->typeN]["auth"]["type"]) && $this_->settings[$this_->typeN]["auth"]["type"]=="http")
            {
                $user = $this_->settings[$this_->typeN]["auth"]["login"];
                $pass = $this_->settings[$this_->typeN]["auth"]["password"];
                curl_setopt($ch, CURLOPT_USERPWD, $user.":".$pass);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include/coo".$shs_ID.".txt");
                curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include/coo".$shs_ID.".txt");
            }else{
                curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include/coo".$shs_ID.".txt");
                curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include/coo".$shs_ID.".txt");
            }
            
        }elseif($shs_ID)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include/loc".$shs_ID.".txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include/loc".$shs_ID.".txt");    
        } 

        if($proxy) curl_setopt($ch, CURLOPT_PROXY, "$proxy");
        $data = curl_exec($ch); //print $data;
        
        $this->headerUrl  = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL);
        $this->httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        curl_close($ch); 
        return $data;
    }
    
    function file_get_local_html($path)
    {
        $path = $_SERVER["DOCUMENT_ROOT"]."/".$path;
        $data = file_put_contents($path);
    }

    function auth($path, $proxy=false, $postdata, $check=false)
    {   
        global $shs_ID;
        $coo = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include/coo".$shs_ID.".txt";
        if($check && file_exists($coo))
            unlink($coo);
        $ch = curl_init( $path );

        $strPost = "";
        $count = 0;
        foreach($postdata as $i=>$v)
        {
            $count++;
            if(empty($i)) continue;
            $strPost .= $i."=".$v;
            if($count!=count($postdata)) $strPost .="&";
        }
        
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);// �� ��������� SSL ����������
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);    // �� ��������� Host SSL �����������
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strPost);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $coo);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $coo);
        if($proxy) curl_setopt($ch, CURLOPT_PROXY, "$proxy");
        $this->headerUrl  = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL);
        $this->httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
?>