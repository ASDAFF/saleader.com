<?
IncludeModuleLangFile(__FILE__);
Class sotbit_yandex extends CModule
{
	const MODULE_ID = 'sotbit.yandex';
	var $MODULE_ID = 'sotbit.yandex'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("sotbit.yandex_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("sotbit.yandex_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("sotbit.yandex_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("sotbit.yandex_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".$DBType."/install.sql");
        if($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }
		return true;
	}

	function UnInstallDB($arParams = array())
	{
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;
        if(!array_key_exists("save_tables", $arParams) || ($arParams["save_tables"] != "Y"))
        {
            $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".$DBType."/uninstall.sql");
            $strSql = "SELECT ID FROM b_file WHERE MODULE_ID='".self::MODULE_ID."'";
            $rsFile = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            while($arFile = $rsFile->Fetch())
                CFile::Delete($arFile["ID"]);
        }
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/sotbit.'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
        
        if($_ENV["COMPUTERNAME"]!='BX')
        {
            //CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sotbit.yandex/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/sotbit.yandex", false, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sotbit.yandex/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", false, true);
        }
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/sotbit.'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
        
        if($_ENV["COMPUTERNAME"]!='BX')
        {
            //css
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sotbit.yandex/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
            //icons
            DeleteDirFilesEx("/bitrix/themes/.default/icons/sotbit.yandex/");
            //images
            DeleteDirFilesEx("/bitrix/images/sotbit.yandex/");
        }
		return true;
	}
    
    function UnInstallAgent()
    {
        CModule::IncludeModule('main');
        $dbAgent = CAgent::GetList(array(), array("MODULE_ID"=>"sotbit.yandex"));
        while($arAgent = $dbAgent->Fetch()){
            CAgent::Delete($arAgent["ID"]);
        }
    }

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
        $this->UnInstallAgent();
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
