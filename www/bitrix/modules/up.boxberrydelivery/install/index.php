<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

Class up_boxberrydelivery extends CModule {

	
	var $MODULE_ID = "up.boxberrydelivery";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';
	var $NEED_MAIN_VERSION = '15.0.0';
	var $NEED_MODULES = array('main', 'sale');

	function up_boxberrydelivery() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_URI  = "http://www.boxberry.ru";
		$this->PARTNER_NAME = GetMessage('BOXBERRY_DELIVERY_PARTNER_NAME');
		$this->MODULE_NAME = GetMessage('BOXBERRY_DELIVERY_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('BOXBERRY_DELIVERY_INSTALL_DESCRIPTION');

	}

	function DoInstall() {
		global $DOCUMENT_ROOT, $APPLICATION;

		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module)
				if (!IsModuleInstalled($module))
					$this->ShowForm('ERROR', GetMessage('BOXBERRY_DELIVERY_NEED_MODULES', array('#MODULE#' => $module)));

		if (strlen($this->NEED_MAIN_VERSION) <= 0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION) >= 0) {
			$this->InstallFiles();

			RegisterModule($this->MODULE_ID);

			$this->ShowForm('OK', GetMessage('BOXBERRY_DELIVERY_INSTALL_OK'));
		}
		else
			$this->ShowForm('ERROR', GetMessage('BOXBERRY_DELIVERY_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));

	}

	function DoUninstall() {
		global $DOCUMENT_ROOT, $APPLICATION;


		UnRegisterModule($this->MODULE_ID);

		$this->UnInstallFiles();
		$this->ShowForm('OK', GetMessage('BOXBERRY_DELIVERY_INSTALL_DEL'));
	}

	function InstallFiles()	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_boxberry/delivery_boxberry.php', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_delivery/delivery_boxberry.php', True, True);
		return true;
	}

	function UnInstallFiles() {
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_boxberry/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_delivery/');
		return true;
	}

	private function ShowForm($type, $message, $buttonName = '')
	{
		$keys = array_keys($GLOBALS);

		for ($i = 0; $i < count($keys); $i++)
			if ($keys[$i] != 'i' && $keys[$i] != 'GLOBALS' && $keys[$i] != 'strTitle' && $keys[$i] != 'filepath')
				global ${$keys[$i]};

		$APPLICATION->SetTitle(GetMessage('BOXBERRY_DELIVERY_INSTALL_NAME'));

		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action="<?= $APPLICATION->GetCurPage()?>" method="get">
		<p>
			<input type="hidden" name="lang" value="<?= LANG?>" />
			<input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
		</p>
		</form>
		<?

		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

		die();
	}

}
?>