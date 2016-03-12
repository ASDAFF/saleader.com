<?
define("ADMIN_MODULE_NAME", "sender");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!\Bitrix\Main\Loader::includeModule("sender"))
	ShowError(\Bitrix\Main\Localization\Loc::getMessage("MAIN_MODULE_NOT_INSTALLED"));

IncludeModuleLangFile(__FILE__);

$MAILING_ID = intval($_REQUEST['MAILING_ID']);
$ID = intval($_REQUEST['ID']);

$find_mailing_id = intval($_REQUEST['find_mailing_id']);
if($find_mailing_id>0)
	$MAILING_ID= $find_mailing_id;
$find_mailing_chain_id = intval($_REQUEST['find_mailing_chain_id']);
if($find_mailing_chain_id>0)
	$ID = $find_mailing_chain_id;

CJSCore::RegisterExt('sender_stat', array(
	'js' => array(
		'/bitrix/js/main/amcharts/3.3/amcharts.js',
		'/bitrix/js/main/amcharts/3.3/funnel.js',
		'/bitrix/js/main/amcharts/3.3/serial.js',
		'/bitrix/js/main/amcharts/3.3/themes/light.js',
	),
	'rel' => array('ajax', "date")
));
CJSCore::Init(array("sender_stat"));

$POST_RIGHT = $APPLICATION->GetGroupRight("sender");
if($POST_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "tbl_sender_statistics";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;

	return count($lAdmin->arFilterErrors)==0;
}

if($lAdmin->IsDefaultFilter())
{

}

$FilterArr = Array(
	"find_mailing_id",
	"find_mailing_chain_id",
);

$lAdmin->InitFilter($FilterArr);

if (CheckFilter())
{
	$arFilter = Array(
		"=POSTING.MAILING_CHAIN.ID" => $find_mailing_chain_id,
	);
	if($find_mailing_id>0)
		$arFilter["=POSTING.MAILING_ID"] = $find_mailing_id;

	foreach($arFilter as $k => $v) if($v!==0 && empty($v)) unset($arFilter[$k]);
}

if($ID <= 0)
{
	$postingDb = \Bitrix\Sender\PostingTable::getList(array(
		'select' => array('MAILING_CHAIN_ID'),
		'filter' => array('MAILING_ID' => $MAILING_ID, '!DATE_SENT' => null),
		'order' => array('DATE_SENT' => 'DESC', 'DATE_CREATE' => 'DESC'),
	));
	$arPosting = $postingDb->fetch();
	if($arPosting)
		$ID = intval($arPosting['MAILING_CHAIN_ID']);
}

$statClickList = array();
$statResult = array(
	'all' => 0,
	'all_print' => 0,
	'delivered' => 0,
	'error' => 0,
	'not_send' => 0,
	'read' => 0,
	'click' => 0,
	'unsub' => 0,
);


if($ID > 0)
{
	$postingDb = \Bitrix\Sender\PostingTable::getList(array(
		'select' => array(
			'ID', 'DATE_CREATE', 'DATE_SENT',
			'MAILING_CHAIN_REITERATE' => 'MAILING_CHAIN.REITERATE',
			'SUBJECT' => 'MAILING_CHAIN.SUBJECT'
		),
		'filter' => array('MAILING_CHAIN_ID' => $ID, '!DATE_SENT' => null),
		'order' => array('DATE_SENT' => 'DESC', 'DATE_CREATE' => 'DESC'),
		'limit' => 1
	));
	$arPosting = $postingDb->fetch();

	$arPostingReiterateList = array();
	if (!empty($arPosting) && $arPosting['MAILING_CHAIN_REITERATE'] == 'Y')
	{
		$defaultDate = new \Bitrix\Main\Type\DateTime();

		$postingReiterateList = array();
		$postingReiterateDb = \Bitrix\Sender\PostingTable::getList(array(
			'select' => array(
				'ID', 'DATE_SENT'
			),
			'filter' => array(
				'MAILING_CHAIN_ID' => $ID,
				'!STATUS' => \Bitrix\Sender\PostingTable::STATUS_NEW,
			),
			'order' => array('DATE_SENT' => 'DESC', 'ID' => 'DESC'),
			'limit' => 50,
		));
		while($postingReiterate = $postingReiterateDb->fetch())
		{
			$postingReiterate['CNT'] = 0;
			$postingReiterate['READ_CNT'] = 0;
			$postingReiterate['CLICK_CNT'] = 0;
			$postingReiterate['UNSUB_CNT'] = 0;

			$postingReiterateList[$postingReiterate['ID']] = $postingReiterate;
		}
		$postingReiterateListId = array_keys($postingReiterateList);

		$paramList = array('Recipient', 'Read', 'Click', 'Unsub');
		foreach($paramList as $paramName)
		{
			if($paramName == 'Recipient')
			{
				$paramNameKey = 'CNT';
				$paramGetListArgs = array(
					'select' => array('POSTING_ID', 'CNT'),
					'filter' => array('POSTING_ID' => $postingReiterateListId),
					'runtime' => array(new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(%s)', 'ID'))
				);
			}
			else
			{
				$paramNameKey = strtoupper($paramName).'_CNT';
				$paramGetListArgs = array(
					'select' => array('POSTING_ID', 'CNT'),
					'filter' => array('POSTING_ID' => $postingReiterateListId),
					'runtime' => array(new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(DISTINCT %s)', 'RECIPIENT_ID'))
				);
			}

			$statDb = call_user_func_array(
				array('Bitrix\Sender\Posting' . $paramName . 'Table', 'getList'),
				array($paramGetListArgs));
			while($statParam = $statDb->fetch())
			{
				$postingReiterateList[$statParam['POSTING_ID']][$paramNameKey] = $statParam['CNT'];
			}
		}

		foreach($postingReiterateList as $arPostingReiterate)
		{
			if(empty($arPostingReiterate['DATE_SENT']))
				$arPostingReiterate['DATE_SENT'] = $defaultDate;

			$cntDivider = $arPostingReiterate['CNT'] > 0 ? $arPostingReiterate['CNT'] : 1;
			$cntDivider = $cntDivider/100;

			$defaultDateTimeStamp = $arPostingReiterate['DATE_SENT']->getTimestamp();
			$arPostingReiterateList[$defaultDateTimeStamp] = array(
				'date' => $arPostingReiterate['DATE_SENT']->format("d/m"),

				'sent' => $arPostingReiterate['CNT'],
				'read' => $arPostingReiterate['READ_CNT'],
				'click' => $arPostingReiterate['CLICK_CNT'],
				'unsub' => $arPostingReiterate['UNSUB_CNT'],

				'sent_prsnt' => '100',
				'read_prsnt' => round($arPostingReiterate['READ_CNT']/$cntDivider, 2),
				'click_prsnt' => round($arPostingReiterate['CLICK_CNT']/$cntDivider, 2),
				'unsub_prsnt' => round($arPostingReiterate['UNSUB_CNT']/$cntDivider, 2)
			);
		}

		if(!empty($arPostingReiterateList))
		{
			if (count($arPostingReiterateList) < 2)
			{
				$arPostingReiterateList = array();
			}
			else
			{
				ksort($arPostingReiterateList);
				$arPostingReiterateList = array_values($arPostingReiterateList);
			}
		}
	}

	if(!empty($arPosting))
	{
		$statListDb = \Bitrix\Sender\PostingRecipientTable::getList(array(
			'select' => array('STATUS', 'CNT'),
			'filter' => array('POSTING_ID' => $arPosting['ID']),
			'runtime' => array(
				new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(%s)', 'ID'),
			),
		));
		while($stat = $statListDb->fetch())
		{
			$statResult['all'] += $stat['CNT'];
			switch($stat['STATUS'])
			{
				case \Bitrix\Sender\PostingRecipientTable::SEND_RESULT_SUCCESS:
					$statResult['delivered'] = $stat['CNT'];
					break;
				case \Bitrix\Sender\PostingRecipientTable::SEND_RESULT_ERROR:
					$statResult['error'] = $stat['CNT'];
					break;
				case \Bitrix\Sender\PostingRecipientTable::SEND_RESULT_NONE:
					$statResult['not_send'] = $stat['CNT'];
					break;
			}
		}
		$statResult['all_print'] = $statResult['all'];

		$paramList = array('Read', 'Click', 'Unsub');
		foreach($paramList as $paramName)
		{
			$paramGetListArgs = array(
				'select' => array('CNT'),
				'filter' => array('POSTING_ID' => $arPosting['ID']),
				'runtime' => array(new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(DISTINCT %s)', 'RECIPIENT_ID'))
			);
			$statDb = call_user_func_array(
				array('Bitrix\Sender\Posting' . $paramName . 'Table', 'getList'),
				array($paramGetListArgs));
			$statParam = $statDb->fetch();
			$statResult[strtolower($paramName)] = $statParam['CNT'];
		}
	}

	if(!empty($arPosting))
	{
		$statClickDb = \Bitrix\Sender\PostingClickTable::getList(array(
			'select' => array(
				'URL', 'CNT'
			),
			'filter' => array('POSTING_ID' => $arPosting['ID']),
			'runtime' => array(
				new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(%s)', 'ID'),
			),
			'group' => array('URL'),
			'order' => array('CNT' => 'DESC'),
			'limit' => 15
		));

		while($statClick = $statClickDb->fetch())
		{
			$statClickList[] = $statClick;
		}
	}
}

$strError = "";
if(empty($arPosting))
{
	$strError = GetMessage("sender_stat_error_no_data");
}

$lAdmin->BeginCustomContent();
if(!empty($strError)):
	CAdminMessage::ShowMessage($strError);
else:

	if(intval($statResult['all'])<=0)
	{
		$statResult['all'] = 1;
	}
	$cntDivider = $statResult['all']/100;
?>
<div class="sender_statistics">
	<div class="sender-stat-cont">
		<div class="sender-stat-left">
			<?=GetMessage("sender_stat_report")?>
			<div class="sender-stat-info">
				<div class="sender-stat-info-list">
					<table>
						<tr><td class="sender-stat-info-list-head" colspan="2"><?=GetMessage("sender_stat_report_title")?> <?=htmlspecialcharsbx($arPosting['DATE_SENT'])?></td></tr>
						<tr>
							<td><?=GetMessage("sender_stat_report_subject")?></td>
							<td class="sender-stat-info-list-value"><?=htmlspecialcharsbx($arPosting['SUBJECT'])?></td>
						</tr>
						<tr>
							<td><?=GetMessage("sender_stat_report_date_create")?></td>
							<td class="sender-stat-info-list-value"><?=htmlspecialcharsbx($arPosting['DATE_CREATE'])?></td>
						</tr>
						<tr>
							<td><?=GetMessage("sender_stat_report_date_sent")?></td>
							<td class="sender-stat-info-list-value"><?=htmlspecialcharsbx($arPosting['DATE_SENT'])?></td>
						</tr>
					</table>
				</div>
				<div class="sender-stat-info-cnt">
					<?if(!empty($statResult)):?>
						<table>
							<tr>
								<td><?=GetMessage("sender_stat_report_cnt_all")?></td>
								<td>
									<span><?=intval($statResult['all_print'])?> </span>
									(<?=round(intval($statResult['all_print'])/$cntDivider, 2)?>%)
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td class="sender-stat-info-cnt-metric-add"><?=GetMessage("sender_stat_report_cnt_in")?></td>
								<td></td>
							</tr>
							<tr>
								<td class="sender-stat-info-cnt-metric-name"><?=GetMessage("sender_stat_report_cnt_read")?></td>
								<td>
									<span><?=intval($statResult['read'])?> </span>
									(<?=round(intval($statResult['read'])/$cntDivider, 2)?>%)
								</td>
							</tr>
							<tr>
								<td class="sender-stat-info-cnt-metric-name"><?=GetMessage("sender_stat_report_cnt_click")?></td>
								<td>
									<span><?=intval($statResult['click'])?> </span>
									(<?=round(intval($statResult['click'])/$cntDivider, 2)?>%)
								</td>
							</tr>
							<tr>
								<td class="sender-stat-info-cnt-metric-name"><?=GetMessage("sender_stat_report_cnt_error")?></td>
								<td>
									<span><?=intval($statResult['error'])?> </span>
									(<?=round(intval($statResult['error'])/$cntDivider, 2)?>%)
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td><?=GetMessage("sender_stat_report_cnt_unsub")?></td>
								<td>
									<span><?=intval($statResult['unsub'])?> </span>
									(<?=round(intval($statResult['unsub'])/$cntDivider, 2)?>%)
								</td>
							</tr>
						</table>
					<?endif;?>
				</div>
			</div>
		</div>

		<div class="sender-stat-right">
			<?=GetMessage("sender_stat_graph")?>
			<div id="chartdiv" class="sender-stat-graph"></div>
			<div class="container-fluid"></div>
		</div>

	</div>

	<script>
		<?
		$postingDataProvider = array();
		$postingDataProvider[] = array(
			"title" => GetMessage("sender_stat_graph_all"),
			"value" => intval($statResult['all']),
			"value_print" => intval($statResult['all_print']),
			"value_prsnt" => round(intval($statResult['all_print'])/$cntDivider, 2)
		);

		$paramList = array('read', 'click', 'unsub', 'error');
		foreach($paramList as $paramName)
		{
			if(intval($statResult[$paramName])>0)
			{
				$postingDataProvider[] = array(
					"title" => GetMessage("sender_stat_graph_" . $paramName),
					"value" => intval($statResult[$paramName]),
					"value_print" => intval($statResult[$paramName]),
					"value_prsnt" => round(intval($statResult[$paramName])/$cntDivider, 2)
				);
			}
		}

		?>
		BX.ready(function(){
			var chart = AmCharts.makeChart("chartdiv", {
				"type": "funnel",
				"theme": "light",
				"dataProvider": <?=CUtil::PhpToJSObject($postingDataProvider)?>,
				"balloon": {
					"fixedPosition": false
				},
				"valueField": "value",
				"titleField": "title",
				"marginRight": 250,
				"marginLeft": 0,
				"startX": 0,
				"depth3D":0,
				"angle":0,
				"outlineAlpha": (BX.browser.IsIE()) ? 0 : 20,
				"outlineColor":"#FFFFFF",
				"outlineThickness": 10,
				"labelPosition": "right",
				"labelText": String.fromCharCode("0x200B")+" [[title]]: [[value_print]]",
				"balloonText": "[[title]]: [[value_prsnt]]%[[description]]"
			});
		});
	</script>


	<?if(!empty($arPostingReiterateList)):?>
	<div  class="sender-stat-reiterate-cont">
		<div class="sender-stat-reiterate-head"><?=GetMessage("sender_stat_graphperiod")?></div>
		<div id="reiteratechartdiv" class="sender-stat-reiterate-graph"></div>
	</div>
	<script>
		BX.ready(function(){
			var reiterateChart = AmCharts.makeChart("reiteratechartdiv", {
				"type": "serial",
				"theme": "light",
				"pathToImages": "/bitrix/js/main/amcharts/3.3/images/",
				"legend": {
					"equalWidths": false,
					"periodValueText": "<?=GetMessage("sender_stat_graphperiod_all")?> [[value.sum]]",
					"position": "top",
					"valueAlign": "left",
					"valueWidth": 100
				},
				"dataProvider": <?=CUtil::PhpToJSObject($arPostingReiterateList);?>,
				"valueAxes": [{
					"stackType": "regular",
					"gridAlpha": 0.07,
					"position": "left",
					"title": "<?=GetMessage("sender_stat_graphperiod_action")?>"
				}],
				"graphs": [{
					"balloonText": "<?=GetMessage("sender_stat_graphperiod_cnt_all")?>: <span style='font-size:14px; color:#000000;'><b>[[value]]</b></span>",
					"fillAlphas": 0.6,
					"hidden": false,
					"lineAlpha": 0.4,
					"title": "<?=GetMessage("sender_stat_graphperiod_cnt_all")?>",
					"valueField": "sent"
				}, {
					"balloonText": "<?=GetMessage("sender_stat_graphperiod_cnt_read")?>: <span style='font-size:14px; color:#000000;'><b>[[read_prsnt]]%</b></span>",
					"fillAlphas": 0.6,
					"hidden": false,
					"lineAlpha": 0.4,
					"title": "<?=GetMessage("sender_stat_graphperiod_cnt_read")?>",
					"valueField": "read"
				}, {
					"balloonText": "<?=GetMessage("sender_stat_graphperiod_cnt_click")?>: <span style='font-size:14px; color:#000000;'><b>[[click_prsnt]]%</b></span>",
					"fillAlphas": 0.6,
					"hidden": false,
					"lineAlpha": 0.4,
					"title": "<?=GetMessage("sender_stat_graphperiod_cnt_click")?>",
					"valueField": "click"
				}, {
					"balloonText": "<?=GetMessage("sender_stat_graphperiod_cnt_unsub")?>: <span style='font-size:14px; color:#000000;'><b>[[unsub_prsnt]]%</b></span>",
					"fillAlphas": 0.6,
					"hidden": false,
					"lineAlpha": 0.4,
					"title": "<?=GetMessage("sender_stat_graphperiod_cnt_unsub")?>",
					"valueField": "unsub"
				}],
				"plotAreaBorderAlpha": 0,
				"marginTop": 10,
				"marginLeft": 0,
				"marginBottom": 0,
				"chartScrollbar": {},
				"chartCursor": {
					"cursorAlpha": 0
				},
				"categoryAxis": {
				},
				"categoryField": "date"
			});
		});
	</script>
	<?endif;?>

	<div  class="sender-stat-reiterate-cont" style="margin-top: 0px;">
		<div class="sender-stat-reiterate-head"><?=GetMessage("sender_stat_click_title")?></div>
		<div class="" style="width: 90%;">
			<br>
			<table width="100%" class="list-table">
				<tbody><tr class="heading">
					<td align="left" width="20%"><?=GetMessage("sender_stat_click_cnt")?></td>
					<td style="text-align: left !important;"><?=GetMessage("sender_stat_click_link")?></td>
				</tr>
				<?foreach($statClickList as $clickItem):?>
					<tr>
						<td align="left"><?=htmlspecialcharsbx($clickItem['CNT'])?></td>
						<td align="left"><a href="<?=htmlspecialcharsbx($clickItem['URL'])?>"><?=htmlspecialcharsbx($clickItem['URL'])?></a></td>
					</tr>
				<?endforeach?>
				<?if(count($statClickList) <= 0):?>
					<tr>
						<td colspan="2" align="left"><?=GetMessage("sender_stat_click_no_data")?></td>
					</tr>
				<?endif;?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?
endif;
$lAdmin->EndCustomContent();
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("sender_stat_title"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$arMailingFilter = array();
$arFilterNames = array(
	GetMessage("sender_stat_flt_mailing")
);
if($MAILING_ID > 0)
{
	$arFilterNames[] = GetMessage("sender_stat_flt_mailing_chain");
	$arMailingFilter['=ID'] = $MAILING_ID;
}

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	$arFilterNames
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
	<tr>
		<td><?=GetMessage("sender_stat_flt_mailing")?>:</td>
		<td>
			<?
			$arr = array();
			$mailingDb = \Bitrix\Sender\MailingTable::getList(array(
				'select'=>array('REFERENCE'=>'NAME','REFERENCE_ID'=>'ID'),
				'filter' => $arMailingFilter
			));
			while($arMailing = $mailingDb->fetch())
			{
				$arr['reference'][] = $arMailing['REFERENCE'];
				$arr['reference_id'][] = $arMailing['REFERENCE_ID'];
			}
			echo SelectBoxFromArray("find_mailing_id", $arr, $MAILING_ID, false, "");
			?>
		</td>
	</tr>

	<?if($MAILING_ID > 0):?>
	<tr>
		<td><?=GetMessage("sender_stat_flt_mailing_chain")?>:</td>
		<td valign="middle">
			<?
			$arr = array();
			$mailingChainDb = \Bitrix\Sender\MailingChainTable::getList(array(
				'select' => array('REFERENCE'=>'SUBJECT','REFERENCE_ID'=>'ID'),
				'filter' => array('MAILING_ID' => $MAILING_ID)
			));
			while($arMailingChain = $mailingChainDb->fetch())
			{
				$arr['reference'][] = $arMailingChain['REFERENCE'];
				$arr['reference_id'][] = $arMailingChain['REFERENCE_ID'];
			}
			echo SelectBoxFromArray("find_mailing_chain_id", $arr, $ID, false, "");
			?>
		</td>
	</tr>
	<?endif;?>
<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>