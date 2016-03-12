<?
   if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
      die();

   if (!defined("WIZARD_TEMPLATE_ID"))
      return;

   $bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_THEME_ID;

   CopyDirFiles(
      $_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID,
      $bitrixTemplateDir,
      $rewrite = true,
      $recursive = true, 
      $delete_after_copy = false,
      $exclude = "themes"
   );

   $obSite = new CSite();
   $obSite->Update(WIZARD_SITE_ID, array(
      'ACTIVE' => "Y",
      'TEMPLATE'=>array(
         array(
            "CONDITION" => "",
            "SORT" => 1,
            "TEMPLATE" => "dresscode"
         )
      )
   ));

   $wizrdTemplateId = "dresscode";
   COption::SetOptionString("main", "wizard_template_id", $wizrdTemplateId, false, WIZARD_SITE_ID);

?>
