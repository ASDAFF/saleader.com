(function() {
var BX = window.BX;
if (BX.SocNetLogDestination)
{
	return;
}

BX.SocNetLogDestination =
{
	popupWindow: null,
	popupSearchWindow: null,
	bByFocusEvent: false,
	bLoadAllInitialized: false,
	createSocNetGroupWindow: null,
	sendEvent: true,
	extranetUser: false,

	obCurrentElement: {
		last: null,
		search: null,
		group: false
	},
	obSearchFirstElement: null,
	obResult: {
		last: null,
		search: null,
		group: false
	},
	obCursorPosition: {
		last: null,
		search: null,
		group: false
	},
	obTabs: {},
	focusOnTabs: false,

	searchTimeout: null,
	createSonetGroupTimeout: null,

	obAllowAddSocNetGroup: {},
	obNewSocNetGroupCnt: {},

	obDepartmentEnable: {},
	obSonetgroupsEnable: {},
	obLastEnable: {},

	arDialogGroups: {},

	obWindowClass: {},
	obWindowCloseIcon: {},
	obPathToAjax: {},
	obDepartmentLoad: {},
	obDepartmentSelectDisable: {},
	obUserSearchArea: {},
	obItems: {},
	obItemsLast: {},
	obItemsSelected: {},
	obCallback: {},
	obElementSearchInput: {},
	obElementBindMainPopup: {},
	obElementBindSearchPopup: {},

	obSiteDepartmentID: {},

	obCrmFeed: {},

	bFinderInited: false,
	obClientDb: null,
	obClientDbData: {},
	obClientDbDataSearchIndex: {},

	oDbUserSearchResult: {},
	oAjaxUserSearchResult: {},

	obDestSort: {},

	oSearchWaiterEnabled: {},
	oSearchWaiterContentHeight: 0,

	bResultMoved: {
		search: false,
		last: false,
		group: false
	}, // cursor move
	oXHR: null,

	obTabSelected: {},

	obTemplateClass: {
		1: 'bx-finder-box-item',
		2: 'bx-finder-box-item-t2',
		3: 'bx-finder-box-item-t3',
		4: 'bx-finder-box-item-t3',
		5: 'bx-finder-box-item-t5',
		6: 'bx-finder-box-item-t6',
		7: 'bx-finder-box-item-t7',
		'department-user': 'bx-finder-company-department-employee-selected',
		'department': 'bx-finder-company-department-check-checked'
	},

	obTemplateClassSelected: {
		1: 'bx-finder-box-item-selected',
		2: 'bx-finder-box-item-t2-selected',
		3: 'bx-finder-box-item-t3-selected',
		4: 'bx-finder-box-item-t3-selected',
		5: 'bx-finder-box-item-t5-selected',
		6: 'bx-finder-box-item-t6-selected',
		7: 'bx-finder-box-item-t7-selected',
		'department-user': 'bx-finder-company-department-employee-selected',
		'department': 'bx-finder-company-department-check-checked'
	}
};

BX.SocNetLogDestination.init = function(arParams)
{
	if(!arParams.name)
	{
		arParams.name = 'lm';
	}

	BX.SocNetLogDestination.obPathToAjax[arParams.name] = (!arParams.pathToAjax ? '/bitrix/components/bitrix/main.post.form/post.ajax.php' : arParams.pathToAjax);

	BX.SocNetLogDestination.obCallback[arParams.name] = arParams.callback;
	BX.SocNetLogDestination.obElementBindMainPopup[arParams.name] = arParams.bindMainPopup;
	BX.SocNetLogDestination.obElementBindSearchPopup[arParams.name] = arParams.bindSearchPopup;
	BX.SocNetLogDestination.obElementSearchInput[arParams.name] = arParams.searchInput;
	BX.SocNetLogDestination.obDepartmentSelectDisable[arParams.name] = (arParams.departmentSelectDisable == true ? true : false);
	BX.SocNetLogDestination.obUserSearchArea[arParams.name] = (BX.util.in_array(arParams.userSearchArea, ['I', 'E']) ? arParams.userSearchArea : false);
	BX.SocNetLogDestination.obDepartmentLoad[arParams.name] = {};
	BX.SocNetLogDestination.obWindowClass[arParams.name] = (!arParams.obWindowClass ? 'bx-lm-socnet-log-destination' : arParams.obWindowClass);
	BX.SocNetLogDestination.obWindowCloseIcon[arParams.name] = (typeof (arParams.obWindowCloseIcon) == 'undefined' ? true : arParams.obWindowCloseIcon);
	BX.SocNetLogDestination.extranetUser = arParams.extranetUser;

	BX.SocNetLogDestination.obCrmFeed[arParams.name] = arParams.isCrmFeed;
	BX.SocNetLogDestination.obAllowAddSocNetGroup[arParams.name] = (arParams.allowAddSocNetGroup === true ? true : false);
	BX.SocNetLogDestination.obSiteDepartmentID[arParams.name] = (typeof (arParams.siteDepartmentID) != 'undefined' && parseInt(arParams.siteDepartmentID) > 0 ? parseInt(arParams.siteDepartmentID) : false);

	BX.SocNetLogDestination.obNewSocNetGroupCnt[arParams.name] = 0;

	BX.SocNetLogDestination.obLastEnable[arParams.name] = (arParams.lastTabDisable == true ? false : true);
	BX.SocNetLogDestination.obDepartmentEnable[arParams.name] = false;

	BX.SocNetLogDestination.oDbUserSearchResult[arParams.name] = {};

	BX.SocNetLogDestination.obDestSort[arParams.name] = (typeof arParams.destSort != 'undefined' ? arParams.destSort : []);

	if (arParams.items.department)
	{
		for(var i in arParams.items.department)
		{
			BX.SocNetLogDestination.obDepartmentEnable[arParams.name] = true;
			break;
		}
	}

	BX.SocNetLogDestination.obSonetgroupsEnable[arParams.name] = false;
	if (arParams.items.sonetgroups)
	{
		for(var i in arParams.items.sonetgroups)
		{
			BX.SocNetLogDestination.obSonetgroupsEnable[arParams.name] = true;
			break;
		}
	}

	BX.SocNetLogDestination.obTabs[arParams.name] = [];
	if (BX.SocNetLogDestination.obLastEnable[arParams.name])
	{
		BX.SocNetLogDestination.obTabs[arParams.name].push('last');
	}
	if (BX.SocNetLogDestination.obSonetgroupsEnable[arParams.name])
	{
		BX.SocNetLogDestination.obTabs[arParams.name].push('group');
	}
	if (BX.SocNetLogDestination.obDepartmentEnable[arParams.name])
	{
		BX.SocNetLogDestination.obTabs[arParams.name].push('department');
	}

	BX.SocNetLogDestination.arDialogGroups[arParams.name] = [];

	BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
		bCrm: true,
		groupCode: 'contacts',
		className: 'bx-lm-element-contacts',
		title: BX.message('LM_POPUP_TAB_LAST_CONTACTS')
	});

	BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
		bCrm: true,
		groupCode: 'companies',
		className: 'bx-lm-element-companies',
		title: BX.message('LM_POPUP_TAB_LAST_COMPANIES')
	});

	BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
		bCrm: true,
		groupCode: 'leads',
		className: 'bx-lm-element-leads',
		title: BX.message('LM_POPUP_TAB_LAST_LEADS')
	});

	BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
		bCrm: true,
		groupCode: 'deals',
		className: 'bx-lm-element-deals',
		avatarLessMode: true,
		title: BX.message('LM_POPUP_TAB_LAST_DEALS')
	});

	BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
		bCrm: false,
		groupCode: 'groups',
		bHideGroup: true,
		className: 'bx-lm-element-groups',
		descLessMode: true
	});

	BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
		bCrm: false,
		groupCode: 'users',
		className: 'bx-lm-element-user',
		descLessMode: true,
		title: BX.message('LM_POPUP_TAB_LAST_USERS')
	});

	BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
		bCrm: false,
		groupCode: 'sonetgroups',
		className: 'bx-lm-element-sonetgroup',
		classNameExtranetGroup: 'bx-lm-element-extranet',
		groupboxClassName: 'bx-lm-groupbox-sonetgroup',
		descLessMode: true,
		title: BX.message('LM_POPUP_TAB_LAST_SG')
	});

	if (BX.SocNetLogDestination.obDepartmentEnable[arParams.name])
	{
		BX.SocNetLogDestination.arDialogGroups[arParams.name].push({
			bCrm: false,
			groupCode: 'department',
			className: 'bx-lm-element-department',
			groupboxClassName: 'bx-lm-groupbox-department',
			descLessMode: true,
			title: BX.message('LM_POPUP_TAB_LAST_STRUCTURE')
		});
	}

	BX.SocNetLogDestination.obItems[arParams.name] = BX.clone(arParams.items);
	BX.SocNetLogDestination.obItemsLast[arParams.name] = BX.clone(arParams.itemsLast);
	BX.SocNetLogDestination.obItemsSelected[arParams.name] = BX.clone(arParams.itemsSelected);

	for (var itemId in BX.SocNetLogDestination.obItemsSelected[arParams.name])
	{
		var type = BX.SocNetLogDestination.obItemsSelected[arParams.name][itemId];
		BX.SocNetLogDestination.runSelectCallback(itemId, type, arParams.name);
	}

	if (!BX.SocNetLogDestination.bFinderInited)
	{
		BX.Finder(false, 'destination', [], {});
		BX.onCustomEvent('initFinderDb', [ BX.SocNetLogDestination, arParams.name, 5 ]);
		BX.SocNetLogDestination.bFinderInited = true;
	}

	if (
		typeof (arParams.LHEObjName) != 'undefined'
		&& BX('div' + arParams.LHEObjName)
	)
	{
		BX.addCustomEvent(BX('div' + arParams.LHEObjName), 'OnShowLHE', function(show) {
			if (!show)
			{
				if (BX.SocNetLogDestination.isOpenDialog())
				{
					BX.SocNetLogDestination.closeDialog();
				}
				BX.SocNetLogDestination.closeSearch();
			}
		});
	}

	BX.SocNetLogDestination.obTabSelected[arParams.name] = (
		BX.SocNetLogDestination.obLastEnable[arParams.name]
			? 'last'
			: ''
	);

	if (!BX.SocNetLogDestination.bLoadAllInitialized)
	{
		BX.addCustomEvent('loadAllFinderDb', function(params) {
			BX.SocNetLogDestination.loadAll(params);
		});
		BX.SocNetLogDestination.bLoadAllInitialized = true;
	}
};

BX.SocNetLogDestination.reInit = function(name)
{
	for (var itemId in BX.SocNetLogDestination.obItemsSelected[name])
	{
		var type = BX.SocNetLogDestination.obItemsSelected[name][itemId];
		BX.SocNetLogDestination.runSelectCallback(itemId, type, name);
	}
};

BX.SocNetLogDestination.openDialog = function(name, params)
{
	if(!name)
	{
		name = 'lm';
	}

	if (!params)
	{
		params = {};
	}

	if (
		typeof params.bByFocusEvent != 'undefined'
		&& params.bByFocusEvent
	)
	{
		BX.SocNetLogDestination.bByFocusEvent = true;
	}

	if (BX.SocNetLogDestination.popupSearchWindow != null)
	{
		BX.SocNetLogDestination.popupSearchWindow.close();
	}

	if (BX.SocNetLogDestination.popupWindow != null)
	{
		if (!BX.SocNetLogDestination.bByFocusEvent)
		{
			BX.SocNetLogDestination.popupWindow.close();
		}
		return false;
	}

	if (
		typeof params.bByFocusEvent == 'undefined'
		|| !params.bByFocusEvent
	)
	{
		BX.SocNetLogDestination.bByFocusEvent = false;
	}

	BX.SocNetLogDestination.popupWindow = new BX.PopupWindow('BXSocNetLogDestination', params.bindNode || BX.SocNetLogDestination.obElementBindMainPopup[name].node, {
		autoHide: true,
		zIndex: 1200,
		offsetLeft: parseInt(BX.SocNetLogDestination.obElementBindMainPopup[name].offsetLeft),
		offsetTop: parseInt(BX.SocNetLogDestination.obElementBindMainPopup[name].offsetTop),
		bindOptions: {forceBindPosition: true},
		closeByEsc: true,
		closeIcon: BX.SocNetLogDestination.obWindowCloseIcon[name] ? {'top': '12px', 'right': '15px'} : false,
		lightShadow: true,
		events: {
			onPopupShow : function() {
				if (
					BX.SocNetLogDestination.sendEvent
					&& BX.SocNetLogDestination.obCallback[name]
					&& BX.SocNetLogDestination.obCallback[name].openDialog
				)
				{
					BX.SocNetLogDestination.obCallback[name].openDialog(name);
				}
			},
			onPopupClose : function() {
				this.destroy();
			},
			onPopupDestroy : BX.proxy(function() {
				BX.SocNetLogDestination.popupWindow = null;
				if (
					BX.SocNetLogDestination.sendEvent
					&& BX.SocNetLogDestination.obCallback[name]
					&& BX.SocNetLogDestination.obCallback[name].closeDialog
				)
				{
					BX.SocNetLogDestination.obCallback[name].closeDialog(name);
				}
			}, this)
		},
		content:
		'<div class="bx-finder-box bx-lm-box '+BX.SocNetLogDestination.obWindowClass[name] +'" style="min-width: 450px; padding-bottom: 8px;">'+
			(
				!BX.SocNetLogDestination.obLastEnable[name]
				&& !BX.SocNetLogDestination.obSonetgroupsEnable[name]
				&& !BX.SocNetLogDestination.obDepartmentEnable[name]
					? ''
					:
					'<div class="bx-finder-box-tabs">' +
						(
							BX.SocNetLogDestination.obLastEnable[name]
								? '<a hidefocus="true" id="destLastTab_'+name+'" onclick="return BX.SocNetLogDestination.SwitchTab(\''+name+'\', this, \'last\')" class="bx-finder-box-tab bx-lm-tab-last bx-finder-box-tab-selected" href="#switchTab">'+BX.message('LM_POPUP_TAB_LAST')+'</a>'
								: ''
						) +
						(
							BX.SocNetLogDestination.obSonetgroupsEnable[name]
								? '<a hidefocus="true" id="destGroupTab_'+name+'" onclick="return BX.SocNetLogDestination.SwitchTab(\''+name+'\', this, \'group\')" class="bx-finder-box-tab bx-lm-tab-sonetgroup" href="#switchTab">'+BX.message('LM_POPUP_TAB_SG')+'</a>'
								: ''
						) +
						(
							BX.SocNetLogDestination.obDepartmentEnable[name]
								? '<a hidefocus="true" id="destDepartmentTab_'+name+'" onclick="return BX.SocNetLogDestination.SwitchTab(\''+name+'\', this, \'department\')" class="bx-finder-box-tab bx-lm-tab-department" href="#switchTab">'+(BX.SocNetLogDestination.obUserSearchArea[name] == 'E' ? BX.message('LM_POPUP_TAB_STRUCTURE_EXTRANET') : BX.message('LM_POPUP_TAB_STRUCTURE'))+'</a>'
								: ''
						) +
					'</div>' +
					'<div class="popup-window-hr popup-window-buttons-hr"><i></i></div>'
			) +
			'<div id="bx-lm-box-last-content" class="bx-finder-box-tabs-content bx-finder-box-tabs-content-window">'+
				'<table class="bx-finder-box-tabs-content-table">'+
					'<tr>'+
						'<td class="bx-finder-box-tabs-content-cell">'+
							(
								BX.SocNetLogDestination.obLastEnable[name]
									? '<div class="bx-finder-box-tab-content bx-lm-box-tab-content-last' + (BX.SocNetLogDestination.obLastEnable[name] ? ' bx-finder-box-tab-content-selected' : '') + '">' + BX.SocNetLogDestination.getItemLastHtml(false, false, name) + '</div>'
									: ''
							) +

							(BX.SocNetLogDestination.obSonetgroupsEnable[name] ? '<div id="bx-lm-box-group-content" class="bx-finder-box-tab-content bx-lm-box-tab-content-sonetgroup' + (!BX.SocNetLogDestination.obLastEnable[name] && BX.SocNetLogDestination.obSonetgroupsEnable[name] ? ' bx-finder-box-tab-content-selected' : '') + '"></div>' : '') +
							(BX.SocNetLogDestination.obDepartmentEnable[name] ? '<div class="bx-finder-box-tab-content bx-lm-box-tab-content-department' + (!BX.SocNetLogDestination.obLastEnable[name] && !BX.SocNetLogDestination.obSonetgroupsEnable[name] && BX.SocNetLogDestination.obDepartmentEnable[name] ? ' bx-finder-box-tab-content-selected' : '') + '"></div>' : '') +
						'</td>'+
					'</tr>'+
				'</table>'+
			'</div>'+
		'</div>'
	});
	BX.SocNetLogDestination.popupWindow.setAngle({});
	BX.SocNetLogDestination.popupWindow.show();

	if (BX.SocNetLogDestination.obLastEnable[name])
	{
		BX.SocNetLogDestination.initResultNavigation(name, 'last', BX.SocNetLogDestination.obItemsLast[name]);
		BX.SocNetLogDestination.obTabSelected[name] = 'last';
	}

	if (
		!BX.SocNetLogDestination.obLastEnable[name]
		&& !BX.SocNetLogDestination.obSonetgroupsEnable[name]
		&& BX.SocNetLogDestination.obDepartmentEnable[name]
		&& BX('destDepartmentTab_'+name)
	)
	{
		BX.SocNetLogDestination.SwitchTab(name, BX('destDepartmentTab_'+name), 'department');
	}
};

BX.SocNetLogDestination.search = function(text, sendAjax, name, nameTemplate, params)
{
	if(!name)
		name = 'lm';

	if (!params)
		params = {};

	sendAjax = sendAjax == false? false: true;

	if (BX.SocNetLogDestination.extranetUser)
	{
		sendAjax = false;
	}

	BX.SocNetLogDestination.obSearchFirstElement = null;
	BX.SocNetLogDestination.obCurrentElement.search = null;
	BX.SocNetLogDestination.obResult.search = [];
	BX.SocNetLogDestination.obCursorPosition.search = {
		group: 0,
		row: 0,
		column: 0
	};

	if (text.length <= 0)
	{
		clearTimeout(BX.SocNetLogDestination.searchTimeout);
		if(BX.SocNetLogDestination.popupSearchWindow != null)
		{
			BX.SocNetLogDestination.popupSearchWindow.close();
		}
		return false;
	}
	else
	{
		var items = {
			'groups': {}, 'users': {}, 'sonetgroups': {}, 'department': {},
			'contacts': {}, 'companies': {}, 'leads': {}, 'deals': {}
		};
		var count = 0;

		var resultGroupIndex = 0;
		var resultRowIndex = 0;
		var resultColumnIndex = 0;
		var bNewGroup = null;
		var storedItem = false;
		var bSkip = false;

		var partsItem = [];
		var bFound = false;
		var bPartFound = false;
		var partsSearchText = null;
		var arSearchStringAlternatives = [text];
		var searchString = null;

		var arTmp = [];
		var tmpVal = false;

		if (sendAjax) // before AJAX request
		{
			var obSearch = { searchString: text };
			BX.onCustomEvent('findEntityByName', [
				BX.SocNetLogDestination,
				obSearch,
				{ },
				BX.SocNetLogDestination.oDbUserSearchResult[name]
			]); // get result from the clientDb
			if (obSearch.searchString != text) // if text was converted to another charset
			{
				arSearchStringAlternatives.push(obSearch.searchString);
			}
			BX.SocNetLogDestination.bResultMoved.search = false;
		}
		else // from AJAX results
		{
			if (
				typeof params != 'undefined'
				&& typeof params.textAjax != 'undefined'
				&& params.textAjax != text
			)
			{
				arSearchStringAlternatives.push(params.textAjax);
			}

			// syncronize local DB
			if (!BX.SocNetLogDestination.obUserSearchArea[name])
			{
				for (var key = 0; key < arSearchStringAlternatives.length; key++)
				{
					searchString = arSearchStringAlternatives[key];
					if (
						typeof BX.SocNetLogDestination.oDbUserSearchResult[name][searchString] != 'undefined'
						&& BX.SocNetLogDestination.oDbUserSearchResult[name][searchString].length > 0
					)
					{
						/* sync minus */
						BX.onCustomEvent('syncClientDb', [
							BX.SocNetLogDestination,
							name,
							BX.SocNetLogDestination.oDbUserSearchResult[name][searchString],
							(
								typeof BX.SocNetLogDestination.oAjaxUserSearchResult[name][searchString] != 'undefined'
									? BX.SocNetLogDestination.oAjaxUserSearchResult[name][searchString]
									: {}
							)
						]);
					}
				}
			}
		}

		for (var group in items)
		{
			bNewGroup = true;
			arTmp = [];

			if (
				BX.SocNetLogDestination.obDepartmentSelectDisable[name]
				&& group == 'department'
			)
			{
				continue;
			}

			for (var key = 0; key < arSearchStringAlternatives.length; key++)
			{
				searchString = arSearchStringAlternatives[key];
				if (
					group == 'users'
					&& sendAjax
					&& typeof BX.SocNetLogDestination.oDbUserSearchResult[name][searchString] != 'undefined'
					&& BX.SocNetLogDestination.oDbUserSearchResult[name][searchString].length > 0 // results from local DB
				)
				{
					for (var i in BX.SocNetLogDestination.oDbUserSearchResult[name][searchString])
					{
						if (
							!BX.SocNetLogDestination.obUserSearchArea[name]
							|| (
								BX.SocNetLogDestination.obUserSearchArea[name] == 'E'
								&& BX.SocNetLogDestination.obClientDbData.users[BX.SocNetLogDestination.oDbUserSearchResult[name][searchString][i]]['isExtranet'] == 'Y'
							)
							|| (
								BX.SocNetLogDestination.obUserSearchArea[name] == 'I'
								&& BX.SocNetLogDestination.obClientDbData.users[BX.SocNetLogDestination.oDbUserSearchResult[name][searchString][i]]['isExtranet'] != 'Y'
							)
						)
						{
							BX.SocNetLogDestination.obItems[name][group][BX.SocNetLogDestination.oDbUserSearchResult[name][searchString][i]] = BX.SocNetLogDestination.obClientDbData.users[BX.SocNetLogDestination.oDbUserSearchResult[name][searchString][i]];
						}
					}
				}
			}

			for (var i in BX.SocNetLogDestination.obItems[name][group])
			{
				if (BX.SocNetLogDestination.obItemsSelected[name][i]) // if already in selected
				{
					continue;
				}

				for (var key = 0; key < arSearchStringAlternatives.length; key++)
				{
					bFound = false;

					searchString = arSearchStringAlternatives[key];
					partsSearchText = searchString.toLowerCase().split(" ");

					partsItem = BX.SocNetLogDestination.obItems[name][group][i].name.toLowerCase().split(" ");

					if (partsSearchText.length <= 1)
					{
						for (var k in partsItem)
						{
							if (partsItem[k].indexOf(searchString.toLowerCase()) === 0)
							{
								bFound = true;
								break;
							}
							else
							{
								continue;
							}
						}
					}
					else
					{
						bFound = true;

						for (var j in partsSearchText)
						{
							bPartFound = false;
							for (var k in partsItem)
							{
								if (partsItem[k].indexOf(partsSearchText[j]) === 0)
								{
									bPartFound = true;
									break;
								}
							}

							if (!bPartFound)
							{
								bFound = false;
								break;
							}
						}

						if (!bFound)
						{
							continue;
						}
					}

					if (bFound)
					{
						break;
					}
				}

				if (!bFound)
				{
					continue;
				}

				if (bNewGroup)
				{
					if (typeof BX.SocNetLogDestination.obResult.search[resultGroupIndex] != 'undefined')
					{
						resultGroupIndex++;
					}
					bNewGroup = false;
				}

				tmpVal = {
					value: i
				};

				if (typeof BX.SocNetLogDestination.obDestSort[name][i] != 'undefined')
				{
					tmpVal.sort = BX.SocNetLogDestination.obDestSort[name][i];
				}

				arTmp.push(tmpVal);
			}

			arTmp.sort(BX.SocNetLogDestination.compareDestinations);

			for (var key = 0; key < arTmp.length; key++)
			{
				i = arTmp[key].value;
				items[group][i] = true;

				bSkip = false;
				if (BX.SocNetLogDestination.obItems[name][group][i]['id'] == 'UA')
				{
					bSkip = true;
				}
				else // calculate position
				{
					if (typeof BX.SocNetLogDestination.obResult.search[resultGroupIndex] == 'undefined')
					{
						BX.SocNetLogDestination.obResult.search[resultGroupIndex] = [];
						resultRowIndex = 0;
						resultColumnIndex = 0;
					}

					if (resultColumnIndex == 2)
					{
						resultRowIndex++;
						resultColumnIndex = 0;
					}

					if (typeof BX.SocNetLogDestination.obResult.search[resultGroupIndex][resultRowIndex] == 'undefined')
					{
						BX.SocNetLogDestination.obResult.search[resultGroupIndex][resultRowIndex] = [];
						resultColumnIndex = 0;
					}
				}

				var item = BX.clone(BX.SocNetLogDestination.obItems[name][group][i]);

				if (bSkip)
				{
					storedItem = item;
				}

				item.type = group;
				if (!bSkip)
				{
					if (storedItem) // add stored item / UA
					{
						BX.SocNetLogDestination.obResult.search[resultGroupIndex][resultRowIndex][resultColumnIndex] = storedItem;
						storedItem = false;
						resultColumnIndex++;
					}

					BX.SocNetLogDestination.obResult.search[resultGroupIndex][resultRowIndex][resultColumnIndex] = item;
				}

				if (count <= 0)
				{
					BX.SocNetLogDestination.obSearchFirstElement = item;
					BX.SocNetLogDestination.obCurrentElement.search = item;
				}
				count++;

				resultColumnIndex++;
			}
		}

		if (sendAjax)
		{
			if (BX.SocNetLogDestination.popupSearchWindow != null)
			{
				BX.SocNetLogDestination.popupSearchWindowContent.innerHTML = BX.SocNetLogDestination.getItemLastHtml(items, true, name);
			}
			else
			{
				if (count > 0)
				{
					BX.SocNetLogDestination.openSearch(items, name, params);
				}
			}
		}
		else
		{
			if (count <= 0)
			{
				if (BX.SocNetLogDestination.popupSearchWindow != null)
				{
					BX.SocNetLogDestination.popupSearchWindow.destroy();
				}

				if (BX.SocNetLogDestination.obAllowAddSocNetGroup[name])
				{
					BX.SocNetLogDestination.createSonetGroupTimeout = setTimeout(function(){

						if (BX.SocNetLogDestination.createSocNetGroupWindow === null)
						{
							BX.SocNetLogDestination.createSocNetGroupWindow = new BX.PopupWindow("invite-dialog-creategroup-popup", BX.SocNetLogDestination.obElementBindSearchPopup[name].node, {
								offsetTop : 1,
								autoHide : true,
								content : BX.SocNetLogDestination.createSocNetGroupContent(text),
								zIndex : 1200,
								buttons : BX.SocNetLogDestination.createSocNetGroupButtons(text, name)
							});
						}
						else
						{
							BX.SocNetLogDestination.createSocNetGroupWindow.setContent(BX.SocNetLogDestination.createSocNetGroupContent(text));
							BX.SocNetLogDestination.createSocNetGroupWindow.setButtons(BX.SocNetLogDestination.createSocNetGroupButtons(text, name));
						}

						if (BX.SocNetLogDestination.createSocNetGroupWindow.popupContainer.style.display != "block")
						{
							BX.SocNetLogDestination.createSocNetGroupWindow.show();
						}

					}, 1000);
				}
			}
			else
			{
				if (BX.SocNetLogDestination.popupSearchWindow != null)
				{
					BX.SocNetLogDestination.popupSearchWindowContent.innerHTML = BX.SocNetLogDestination.getItemLastHtml(items, true, name);
				}
				else
				{
					BX.SocNetLogDestination.openSearch(items, name, params);
				}
			}
		}

		clearTimeout(BX.SocNetLogDestination.searchTimeout);

		if (sendAjax && text.toLowerCase() != '')
		{
			BX.SocNetLogDestination.showSearchWaiter(name);

			BX.SocNetLogDestination.searchTimeout = setTimeout(function()
			{
				BX.SocNetLogDestination.oXHR = BX.ajax({
					url: BX.SocNetLogDestination.obPathToAjax[name],
					method: 'POST',
					dataType: 'json',
					data: {
						'LD_SEARCH' : 'Y',
						'CRM_SEARCH' : BX.SocNetLogDestination.obCrmFeed[name] ? 'Y' : 'N',
						'EXTRANET_SEARCH' : BX.util.in_array(BX.SocNetLogDestination.obUserSearchArea[name], ['I', 'E']) ? BX.SocNetLogDestination.obUserSearchArea[name] : 'N',
						'SEARCH' : text.toLowerCase(),
						'SEARCH_CONVERTED' : (
							BX.message('LANGUAGE_ID') == 'ru'
							&& BX.correctText
								? BX.correctText(text.toLowerCase())
								: ''
						),
						'sessid': BX.bitrix_sessid(),
						'nt': nameTemplate,
						'DEPARTMENT_ID': (parseInt(BX.SocNetLogDestination.obSiteDepartmentID[name]) > 0 ? parseInt(BX.SocNetLogDestination.obSiteDepartmentID[name]) : 0)
					},
					onsuccess: function(data)
					{
						BX.SocNetLogDestination.hideSearchWaiter(name);

						/* sync plus */
						var textAjax = (typeof data.SEARCH != 'undefined' ? data.SEARCH : text);

						BX.onCustomEvent('onFinderAjaxSuccess', [ data, BX.SocNetLogDestination ]);

						if (!BX.SocNetLogDestination.bResultMoved.search)
						{
							BX.SocNetLogDestination.oAjaxUserSearchResult[name] = {};
							BX.SocNetLogDestination.oAjaxUserSearchResult[name][textAjax.toLowerCase()] = [];

							for (var i in data.USERS)
							{
								bFound = true;
								BX.SocNetLogDestination.oAjaxUserSearchResult[name][textAjax.toLowerCase()].push(i);

								if (!BX.SocNetLogDestination.obItems[name].users[i])
								{
									BX.SocNetLogDestination.obItems[name].users[i] = data.USERS[i];
								}
							}

							if (BX.SocNetLogDestination.obCrmFeed[name])
							{
								var types = {'contacts': 'CONTACTS', 'companies': 'COMPANIES', 'leads': 'LEADS', 'deals': 'DEALS'};
								for (type in types)
								{
									for (var i in data[types[type]])
									{
										bFound = true;
										if (!BX.SocNetLogDestination.obItems[name][type][i])
										{
											BX.SocNetLogDestination.obItems[name][type][i] = data[types[type]][i];
										}
									}
								}
							}

							BX.SocNetLogDestination.search(
								text,
								false,
								name,
								nameTemplate,
								{
									textAjax: textAjax
								}
							);
						}
					},
					onfailure: function(data)
					{
						BX.SocNetLogDestination.hideSearchWaiter(name);
					}
				});
			}, 1000);
		}
	}
};

BX.SocNetLogDestination.openSearch = function(items, name, params)
{
	if (!name)
	{
		name = 'lm';
	}

	if (!params)
	{
		params = {};
	}

	if (BX.SocNetLogDestination.popupWindow != null)
	{
		BX.SocNetLogDestination.popupWindow.close();
	}

	if (BX.SocNetLogDestination.popupSearchWindow != null)
	{
		BX.SocNetLogDestination.popupSearchWindow.close();
		return false;
	}

	BX.SocNetLogDestination.popupSearchWindow = new BX.PopupWindow('BXSocNetLogDestinationSearch', params.bindNode || BX.SocNetLogDestination.obElementBindSearchPopup[name].node, {
		autoHide: true,
		zIndex: 1200,
		offsetLeft: parseInt(BX.SocNetLogDestination.obElementBindSearchPopup[name].offsetLeft),
		offsetTop: parseInt(BX.SocNetLogDestination.obElementBindSearchPopup[name].offsetTop),
		bindOptions: {forceBindPosition: true},
		closeByEsc: true,
		closeIcon: BX.SocNetLogDestination.obWindowCloseIcon[name] ? {'top': '12px', 'right': '15px'} : false,
		lightShadow: true,
		events: {
			onPopupShow : function() {
				if(BX.SocNetLogDestination.sendEvent && BX.SocNetLogDestination.obCallback[name] && BX.SocNetLogDestination.obCallback[name].openSearch)
					BX.SocNetLogDestination.obCallback[name].openSearch(name);
			},
			onPopupClose : function() {
				this.destroy();
				if (
					BX.SocNetLogDestination.sendEvent
					&& BX.SocNetLogDestination.obCallback[name]
					&& BX.SocNetLogDestination.obCallback[name].closeSearch
				)
				{
					BX.SocNetLogDestination.obCallback[name].closeSearch(name);
				}
			},
			onPopupDestroy : BX.proxy(function() {
				BX.SocNetLogDestination.popupSearchWindow = null;
				BX.SocNetLogDestination.popupSearchWindowContent = null;
			}, this)
		},
		content: BX.create('DIV', {
			props: {
				className: 'bx-finder-box bx-lm-box ' + BX.SocNetLogDestination.obWindowClass[name]
			},
			style: {
				minWidth: '450px',
				paddingBottom: '8px'
			},
			children: [
				BX.create('DIV', {
					attrs : {
						id : 'bx-lm-box-search-tabs-content'
					},
					props: {
						className: 'bx-finder-box-tabs-content'
					},
					children: [
						BX.create('TABLE', {
							props: {
								className: 'bx-finder-box-tabs-content-table'
							},
							children: [
								BX.create('TR', {
									children: [
										BX.create('TD', {
											props: {
												className: 'bx-finder-box-tabs-content-cell'
											},
											children: [
												BX.create('DIV', {
													attrs : {
														id : 'bx-lm-box-search-content'
													},
													props: {
														className: 'bx-finder-box-tab-content bx-finder-box-tab-content-selected'
													},
													html: BX.SocNetLogDestination.getItemLastHtml(items, true, name)
												})
											]
										})
									]
								})
							]
						})
					]
				}),
				BX.create('DIV', {
					attrs : {
						id : 'bx-lm-box-search-waiter'
					},
					props: {
						className: 'bx-finder-box-search-waiter'
					},
					style: {
						height: '0px'
					},
					children: [
						BX.create('IMG', {
							props: {
								className: 'bx-finder-box-search-waiter-background'
							},
							attrs: {
								src: '/bitrix/js/main/core/images/waiter-white.gif'
							}
						}),
						BX.create('DIV', {
							props: {
								className: 'bx-finder-box-search-waiter-text'
							},
							text: BX.message('LM_POPUP_WAITER_TEXT')
						})
					]
				})
			]
		})
	});
	BX.SocNetLogDestination.popupSearchWindow.setAngle({});
	BX.SocNetLogDestination.popupSearchWindow.show();
	BX.SocNetLogDestination.popupSearchWindowContent = BX('bx-lm-box-search-content');

	BX.SocNetLogDestination.oSearchWaiterContentHeight = BX.pos(BX('bx-lm-box-search-tabs-content')).height;
};

BX.SocNetLogDestination.drawItemsGroup = function(lastItems, groupCode, name, search, count, params)
{
	var itemsHtml = (
		typeof params.itemsHtml != 'undefined'
		&& params.itemsHtml
			? params.itemsHtml
			: ''
	);

	for (var i in lastItems[groupCode])
	{
		if (!BX.SocNetLogDestination.obItems[name][groupCode][i])
		{
			continue;
		}

		itemsHtml += BX.SocNetLogDestination.getHtmlByTemplate7(
			name,
			BX.SocNetLogDestination.obItems[name][groupCode][i],
			{
				className: params.className + (
					groupCode == 'sonetgroup'
					&& typeof params.classNameExtranetGroup != 'undefined'
					&& typeof window['arExtranetGroupID'] != 'undefined'
					&& BX.util.in_array(BX.SocNetLogDestination.obItems[name][groupCode][i].entityId, window['arExtranetGroupID'])
						? ' ' + params.classNameExtranetGroup
						: ''
				),
				descLessMode: (typeof params.descLessMode != 'undefined' && params.descLessMode ? true : false),
				itemType: groupCode,
				search: search,
				avatarLessMode: (typeof params.avatarLessMode != 'undefined' && params.avatarLessMode ? true : false),
				itemHover: (
//					search &&
					count <= 0
				)
			},
			(search ? 'search' : 'last')
		);

		count++;
	}

	if (
		itemsHtml != ''
		&& (
			typeof params.bHideGroup == 'undefined'
			|| !params.bHideGroup
		)
	)
	{
		itemsHtml = '<span class="bx-finder-groupbox ' + (typeof params.groupboxClassName != 'undefined' ? params.groupboxClassName : 'bx-lm-groupbox-last')+ '">' +
			'<span class="bx-finder-groupbox-name">' + params.title + ':</span>' +
			'<span class="bx-finder-groupbox-content">' + itemsHtml + '</span>' +
		'</span>';
	}

	return {
		html: itemsHtml,
		count: count
	};
}
/* vizualize lastItems - search result */

BX.SocNetLogDestination.getItemLastHtml = function(lastItems, search, name)
{
	if(!name)
		name = 'lm';

	if (!lastItems)
	{
		lastItems = BX.SocNetLogDestination.obItemsLast[name];
	}

	var html = '';
	var tmpHtml = null;
	var count = 0;
	var drawResult = null;
	var dialogGroup = null;

	for (var i = 0; i < BX.SocNetLogDestination.arDialogGroups[name].length; i++)
	{
		dialogGroup = BX.SocNetLogDestination.arDialogGroups[name][i];
		if (
			dialogGroup.bCrm
			&& BX.SocNetLogDestination.obCrmFeed[name]
			|| (
				!dialogGroup.bCrm
				&& (
					search
					|| !BX.SocNetLogDestination.obCrmFeed[name]
				)
			)
		)
		{
			drawResult = BX.SocNetLogDestination.drawItemsGroup(
				lastItems,
				dialogGroup.groupCode,
				name,
				search,
				count,
				{
					itemsHtml: (tmpHtml ? tmpHtml : false),
					bHideGroup: (
						typeof dialogGroup.bHideGroup != 'undefined'
							? dialogGroup.bHideGroup
							: false
					),
					className: (
						typeof dialogGroup.className != 'undefined'
							? dialogGroup.className
							: false
					),
					classNameExtranetGroup: (
						typeof dialogGroup.classNameExtranetGroup != 'undefined'
							? dialogGroup.classNameExtranetGroup
							: false
					),
					groupboxClassName: (
						typeof dialogGroup.groupboxClassName != 'undefined'
							? dialogGroup.groupboxClassName
							: false
					),
					avatarLessMode: (
						typeof dialogGroup.avatarLessMode != 'undefined'
							? dialogGroup.avatarLessMode
							: false
					),
					descLessMode: (
						typeof dialogGroup.descLessMode != 'undefined'
							? dialogGroup.descLessMode
							: false
					),
					title: (
						typeof dialogGroup.title != 'undefined'
							? dialogGroup.title
							: ''
					)
				}
			);

			if (drawResult.html.length > 0)
			{
				if (
					dialogGroup.bHideGroup != 'undefined'
					&& dialogGroup.bHideGroup
				)
				{
					tmpHtml = drawResult.html;
				}
				else
				{
					html += drawResult.html;
					tmpHtml = null;
				}
			}
			count = drawResult.count;
		}
	}

	if (html.length <= 0)
	{
		html = '<span class="bx-finder-groupbox bx-lm-groupbox-search">'+
			'<span class="bx-finder-groupbox-content">' + BX.message(search ? 'LM_SEARCH_PLEASE_WAIT' : 'LM_EMPTY_LIST') + '</span>'+
		'</span>';
	}

	return html;
};

BX.SocNetLogDestination.getItemGroupHtml = function(name)
{
	if(!name)
		name = 'lm';

	var html = '';
	var count = 0;
	for (var i in BX.SocNetLogDestination.obItems[name].sonetgroups)
	{
		html += BX.SocNetLogDestination.getHtmlByTemplate7(
			name,
			BX.SocNetLogDestination.obItems[name].sonetgroups[i],
			{
			className: "bx-lm-element-sonetgroup" + (typeof window['arExtranetGroupID'] != 'undefined' && BX.util.in_array(BX.SocNetLogDestination.obItems[name].sonetgroups[i].entityId, window['arExtranetGroupID']) ? ' bx-lm-element-extranet' : ''),
			descLessMode : true,
			itemType: 'sonetgroups',
			itemHover: (count <= 0)
			},
			'group'
		);
		count++;
	}

	return html;
};

BX.SocNetLogDestination.getItemDepartmentHtml = function(name, relation, categoryId, categoryOpened)
{
	if(!name)
		name = 'lm';

	categoryId = categoryId ? categoryId: false;
	categoryOpened = categoryOpened ? true: false;

	var bFirstRelation = false;
	if (
		typeof relation == 'undefined'
		|| !relation
	) // root
	{
		relation = BX.SocNetLogDestination.obItems[name].departmentRelation;
		bFirstRelation = true;
	}

	var html = '';
	for (var i in relation)
	{
		if (relation[i].type == 'category')
		{
			var category = BX.SocNetLogDestination.obItems[name].department[relation[i].id];
			var activeClass = (
				BX.SocNetLogDestination.obItemsSelected[name][relation[i].id]
					? BX.SocNetLogDestination.obTemplateClassSelected['department']
					: ''
			);
			bFirstRelation = (bFirstRelation && category.id != 'EX');

			html += '<div class="bx-finder-company-department' + (bFirstRelation ? ' bx-finder-company-department-opened' : '') + '">\
				<a href="#' + category.id + '" class="bx-finder-company-department-inner" onclick="return BX.SocNetLogDestination.OpenCompanyDepartment(\'' + name + '\', this.parentNode, \'' + category.entityId + '\')" hidefocus="true">\
					<div class="bx-finder-company-department-arrow"></div>\
					<div class="bx-finder-company-department-text">' + category.name + '</div>\
				</a>\
			</div>';

			html += '<div class="bx-finder-company-department-children'+(bFirstRelation? ' bx-finder-company-department-children-opened': '')+'">';
			if(
				!BX.SocNetLogDestination.obDepartmentSelectDisable[name]
				&& !bFirstRelation
				&& category.id != 'EX'
			)
			{
				html += '<a class="bx-finder-company-department-check '+activeClass+' bx-finder-element" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, \'department\', \''+relation[i].id+'\', \'department\')" rel="'+relation[i].id+'" href="#'+relation[i].id+'">';
				html += '<span class="bx-finder-company-department-check-inner">\
						<div class="bx-finder-company-department-check-arrow"></div>\
						<div class="bx-finder-company-department-check-text" rel="'+category.name+': '+BX.message("LM_POPUP_CHECK_STRUCTURE")+'">'+BX.message("LM_POPUP_CHECK_STRUCTURE")+'</div>\
					</span>\
				</a>';
			}
			html += BX.SocNetLogDestination.getItemDepartmentHtml(name, relation[i].items, category.entityId, bFirstRelation);
			html += '</div>';
		}
	}

	if (categoryId)
	{
		html += '<div class="bx-finder-company-department-employees" id="bx-lm-category-relation-'+categoryId+'">';
		userCount = 0;
		for (var i in relation)
		{
			if (relation[i].type == 'user')
			{
				var user = BX.SocNetLogDestination.obItems[name].users[relation[i].id];
				if (user == null)
				{
					continue;
				}

				var activeClass = (
					BX.SocNetLogDestination.obItemsSelected[name][relation[i].id]
						? BX.SocNetLogDestination.obTemplateClassSelected['department-user']
						: ''
				);
				html += '<a href="#'+user.id+'" class="bx-finder-company-department-employee '+activeClass+' bx-finder-element" rel="'+user.id+'" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, \'department-user\', \''+user.id+'\', \'users\')" hidefocus="true">\
					<div class="bx-finder-company-department-employee-info">\
						<div class="bx-finder-company-department-employee-name">'+user.name+'</div>\
						<div class="bx-finder-company-department-employee-position">'+user.desc+'</div>\
					</div>\
					<div style="'+(user.avatar? 'background:url(\''+user.avatar+'\') no-repeat center center': '')+'" class="bx-finder-company-department-employee-avatar"></div>\
				</a>';
				userCount++;
			}
		}
		if (userCount <= 0)
		{
			if (!BX.SocNetLogDestination.obDepartmentLoad[name][categoryId])
			{
				html += '<div class="bx-finder-company-department-employees-loading">' + BX.message('LM_PLEASE_WAIT') + '</div>';
			}

			if (categoryOpened)
			{
				BX.SocNetLogDestination.getDepartmentRelation(name, categoryId);
			}
		}
		html += '</div>';
	}

	return html;
};

BX.SocNetLogDestination.getDepartmentRelation = function(name, departmentId)
{
	if (BX.SocNetLogDestination.obDepartmentLoad[name][departmentId])
	{
		return false;
	}

	BX.ajax({
		url: BX.SocNetLogDestination.obPathToAjax[name],
		method: 'POST',
		dataType: 'json',
		data: {
			LD_DEPARTMENT_RELATION : 'Y',
			DEPARTMENT_ID : departmentId,
			sessid: BX.bitrix_sessid()
		},
		onsuccess: function(data){
			BX.SocNetLogDestination.obDepartmentLoad[name][departmentId] = true;
			var departmentItem = BX.util.object_search_key((departmentId == 'EX' ? departmentId : 'DR'+departmentId), BX.SocNetLogDestination.obItems[name].departmentRelation);

			html = '';
			for(var i in data.USERS)
			{
				if (!BX.SocNetLogDestination.obItems[name].users[i])
				{
					BX.SocNetLogDestination.obItems[name].users[i] = data.USERS[i];
				}

				if (!departmentItem.items[i])
				{
					departmentItem.items[i] = {'id': i,	'type': 'user'};
					var activeClass = (
						BX.SocNetLogDestination.obItemsSelected[name][data.USERS[i].id]
							? BX.SocNetLogDestination.obTemplateClassSelected['department-user']
							: ''
					);
					html += '<a href="#'+data.USERS[i].id+'" class="bx-finder-company-department-employee '+activeClass+' bx-finder-element" rel="'+data.USERS[i].id+'" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, \'department-user\', \''+data.USERS[i].id+'\', \'users\')" hidefocus="true">\
						<div class="bx-finder-company-department-employee-info">\
							<div class="bx-finder-company-department-employee-name">'+data.USERS[i].name+'</div>\
							<div class="bx-finder-company-department-employee-position">'+data.USERS[i].desc+'</div>\
						</div>\
						<div style="'+(data.USERS[i].avatar? 'background:url(\''+data.USERS[i].avatar+'\') no-repeat center center': '')+'" class="bx-finder-company-department-employee-avatar"></div>\
					</a>';
				}
			}
			BX('bx-lm-category-relation-'+departmentId).innerHTML = html;

		},
		onfailure: function(data)	{}
	});
};

BX.SocNetLogDestination.getHtmlByTemplate1 = function(name, item, params)
{
	if(!name)
		name = 'lm';
	if(!params)
		params = {};

	var activeClass = (
		BX.SocNetLogDestination.obItemsSelected[name][item.id]
			? ' ' + BX.SocNetLogDestination.obTemplateClassSelected[1]
			: ''
	);
	var hoverClass = params.itemHover? 'bx-finder-box-item-hover': '';
	var html = '<a id="' + name + '_' + item.id + '" class="' + BX.SocNetLogDestination.obTemplateClass[1] + ' '+activeClass+' '+hoverClass+' bx-finder-element'+(params.className? ' '+params.className: '')+'" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, 1, \''+item.id+'\', \''+(params.itemType? params.itemType: 'item')+'\', '+(params.search? true: false)+')" rel="'+item.id+'" href="#'+item.id+'">\
		<div class="bx-finder-box-item-text">'+item.name+'</div>\
	</a>';
	return html;
};

BX.SocNetLogDestination.getHtmlByTemplate2 = function(name, item, params)
{
	if(!name)
		name = 'lm';
	if(!params)
		params = {};

	var activeClass = (
		BX.SocNetLogDestination.obItemsSelected[name][item.id]
			? ' ' + BX.SocNetLogDestination.obTemplateClassSelected[2]
			: ''
	);
	var hoverClass = params.itemHover? 'bx-finder-box-item-t2-hover': '';
	var html = '<a id="' + name + '_' + item.id + '" class="' + BX.SocNetLogDestination.obTemplateClass[2] + ' '+activeClass+' '+hoverClass+' bx-finder-element'+(params.className? ' '+params.className: '')+'" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, 2, \''+item.id+'\', \''+(params.itemType? params.itemType: 'item')+'\', '+(params.search? true: false)+')" rel="'+item.id+'" href="#'+item.id+'">\
		<div class="bx-finder-box-item-t2-text">'+item.name+'</div>\
	</a>';
	return html;
};

BX.SocNetLogDestination.getHtmlByTemplate3 = function(name, item, params)
{
	if(!name)
		name = 'lm';
	if(!params)
		params = {};

	var activeClass = (
		BX.SocNetLogDestination.obItemsSelected[name][item.id]
			? ' ' + BX.SocNetLogDestination.obTemplateClassSelected[3]
			: ''
	);
	var hoverClass = params.itemHover? 'bx-finder-box-item-t3-hover': '';
	var html = '<a id="' + name + '_' + item.id + '" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, 3, \''+item.id+'\', \''+(params.itemType? params.itemType: 'item')+'\', '+(params.search? true: false)+')" rel="'+item.id+'" class="' + BX.SocNetLogDestination.obTemplateClass[3] + ' '+activeClass+' '+hoverClass+' bx-finder-element'+(params.className? ' '+params.className: '')+'" href="#'+item.id+'">'+
		'<div class="bx-finder-box-item-t3-avatar" '+(item.avatar? 'style="background:url(\''+item.avatar+'\') no-repeat center center"':'')+'></div>'+
		'<div class="bx-finder-box-item-t3-info">'+
			'<div class="bx-finder-box-item-t3-name">'+item.name+'</div>'+
			(item.desc? '<div class="bx-finder-box-item-t3-desc">'+item.desc+'</div>': '')+
		'</div>'+
		'<div class="bx-clear"></div>'+
	'</a>';
	return html;
};

BX.SocNetLogDestination.getHtmlByTemplate5 = function(name, item, params)
{
	if(!name)
		name = 'lm';
	if(!params)
		params = {};

	var activeClass = (
		BX.SocNetLogDestination.obItemsSelected[name][item.id]
			? ' ' + BX.SocNetLogDestination.obTemplateClassSelected[5]
			: ''
	);
	var hoverClass = params.itemHover? 'bx-finder-box-item-t5-hover': '';
	var html = '<a id="' + name + '_' + item.id + '" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, 5, \''+item.id+'\', \''+(params.itemType? params.itemType: 'item')+'\', '+(params.search? true: false)+')" rel="'+item.id+'" class="' + BX.SocNetLogDestination.obTemplateClass[5] + ' '+activeClass+' '+hoverClass+' bx-finder-element'+(params.className? ' '+params.className: '')+'" href="#'+item.id+'">'+
		'<div class="bx-finder-box-item-t5-avatar" '+(item.avatar? 'style="background:url(\''+item.avatar+'\') no-repeat center center"':'')+'></div>'+
		'<div class="bx-finder-box-item-t5-info">'+
			'<div class="bx-finder-box-item-t5-name">'+item.name+'</div>'+
			(item.desc? '<div class="bx-finder-box-item-t5-desc">'+item.desc+'</div>': '')+
		'</div>'+
		'<div class="bx-clear"></div>'+
	'</a>';
	return html;
};

BX.SocNetLogDestination.getHtmlByTemplate6 = function(name, item, params)
{
	if(!name)
		name = 'lm';
	if(!params)
		params = {};

	var activeClass = (
		BX.SocNetLogDestination.obItemsSelected[name][item.id]
			? ' ' + BX.SocNetLogDestination.obTemplateClassSelected[6]
			: ''
	);
	var hoverClass = params.itemHover? 'bx-finder-box-item-t6-hover': '';
	var html = '<a id="' + name + '_' + item.id + '" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, 6, \''+item.id+'\', \''+(params.itemType? params.itemType: 'item')+'\', '+(params.search? true: false)+')" rel="'+item.id+'" class="' + BX.SocNetLogDestination.obTemplateClass[6] + ' '+activeClass+' '+hoverClass+' bx-finder-element'+(params.className? ' '+params.className: '')+'" href="#'+item.id+'">'+
		'<div class="bx-finder-box-item-t6-avatar" '+(item.avatar? 'style="background:url(\''+item.avatar+'\') no-repeat center center"':'')+'></div>'+
		'<div class="bx-finder-box-item-t6-info">'+
			'<div class="bx-finder-box-item-t6-name">'+item.name+'</div>'+
			(item.desc? '<div class="bx-finder-box-item-t6-desc">'+item.desc+'</div>': '')+
		'</div>'+
		'<div class="bx-clear"></div>'+
	'</a>';
	return html;
};

BX.SocNetLogDestination.getHtmlByTemplate7 = function(name, item, params, type)
{
	if(!name)
	{
		name = 'lm';
	}

	if(!params)
	{
		params = {};
	}

	if(!type)
	{
		type = '';
	}

	var showDesc = BX.type.isNotEmptyString(item.desc);
	showDesc = params.descLessMode && params.descLessMode == true ? false : showDesc;

	var itemClass = BX.SocNetLogDestination.obTemplateClass[7] + " bx-finder-element";
	itemClass += BX.SocNetLogDestination.obItemsSelected[name][item.id]
		? ' ' + BX.SocNetLogDestination.obTemplateClassSelected[7]
		: '';
	itemClass += params.itemHover ? ' bx-finder-box-item-t7-hover': '';
	itemClass += showDesc ? ' bx-finder-box-item-t7-desc-mode': '';
	itemClass += params.className ? ' ' + params.className: '';
	itemClass += params.avatarLessMode && params.avatarLessMode == true ? ' bx-finder-box-item-t7-avatarless' : '';
	itemClass += typeof (item.isExtranet != 'undefined') && item.isExtranet == 'Y' ? ' bx-lm-element-extranet' : '';

	var html = '<a id="' + name + '_' + type + '_' + item.id + '" hidefocus="true" onclick="return BX.SocNetLogDestination.selectItem(\''+name+'\', this, 7, \''+item.id+'\', \''+(params.itemType? params.itemType: 'item')+'\', '+(params.search? true: false)+')" rel="'+item.id+'" class="' + itemClass + '" href="#'+item.id+'">'+
		(
			item.avatar
				? '<div class="bx-finder-box-item-t7-avatar"><img bx-lm-item-id="' + item.id + '" bx-lm-item-type="' + params.itemType + '" class="bx-finder-box-item-t7-avatar-img" src="' + item.avatar + '" onerror="BX.onCustomEvent(\'removeClientDbObject\', [BX.SocNetLogDestination, this.getAttribute(\'bx-lm-item-id\'), this.getAttribute(\'bx-lm-item-type\')]); BX.cleanNode(this, true);"></div>'
				: '<div class="bx-finder-box-item-t7-avatar"></div>'
		) +
		'<div class="bx-finder-box-item-t7-space"></div>' +
		'<div class="bx-finder-box-item-t7-info">'+
		'<div class="bx-finder-box-item-t7-name">'+item.name+'</div>'+
		(showDesc? '<div class="bx-finder-box-item-t7-desc">'+item.desc+'</div>': '')+
		'</div>'+
		'</a>';
	return html;
};


BX.SocNetLogDestination.SwitchTab = function(name, currentTab, type)
{
	var tabsContent = BX.findChildren(
		BX.findChild(
			currentTab.parentNode.parentNode,
			{ tagName : "td", className : "bx-finder-box-tabs-content-cell"},
			true
		),
		{ tagName : "div" }
	);

	if (!tabsContent)
	{
		return false;
	}

	var tabIndex = 0;
	var tabs = BX.findChildren(currentTab.parentNode, { tagName : "a" });
	for (var i = 0; i < tabs.length; i++)
	{
		if (tabs[i] === currentTab)
		{
			BX.addClass(tabs[i], "bx-finder-box-tab-selected");
			tabIndex = i;
		}
		else
		{
			BX.removeClass(tabs[i], "bx-finder-box-tab-selected");
		}
	}

	for (i = 0; i < tabsContent.length; i++)
	{
		if (tabIndex === i)
		{
			if (type == 'last')
			{
				tabsContent[i].innerHTML = BX.SocNetLogDestination.getItemLastHtml(false, false, name);
			}
			else if (type == 'group')
			{
				tabsContent[i].innerHTML = BX.SocNetLogDestination.getItemGroupHtml(name);
			}
			else if (type == 'department')
			{
				tabsContent[i].innerHTML = BX.SocNetLogDestination.getItemDepartmentHtml(name);
			}

			BX.addClass(tabsContent[i], "bx-finder-box-tab-content-selected");
		}
		else
		{
			BX.removeClass(tabsContent[i], "bx-finder-box-tab-content-selected");
		}
	}

	BX.focus(BX.SocNetLogDestination.obElementSearchInput[name]);

	if (type == 'last')
	{
		BX.SocNetLogDestination.initResultNavigation(name, 'last', BX.SocNetLogDestination.obItemsLast[name]);
	}
	else if (type == 'group')
	{
		BX.SocNetLogDestination.initResultNavigation(name, 'group', {
			sonetgroups: BX.SocNetLogDestination.obItems[name].sonetgroups
		});
	}

	BX.SocNetLogDestination.obTabSelected[name] = type;

	return false;
}

BX.SocNetLogDestination.OpenCompanyDepartment = function(name, department, categoryId)
{
	if(!name)
		name = 'lm';

	BX.toggleClass(department, "bx-finder-company-department-opened");

	var nextDiv = BX.findNextSibling(department, { tagName : "div"} );
	if (BX.hasClass(nextDiv, "bx-finder-company-department-children"))
		BX.toggleClass(nextDiv, "bx-finder-company-department-children-opened");

	BX.SocNetLogDestination.getDepartmentRelation(name, categoryId);

	return false;
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

BX.SocNetLogDestination.selectItem = function(name, element, template, itemId, type, search)
{
	if(!name)
	{
		name = 'lm';
	}
	BX.focus(BX.SocNetLogDestination.obElementSearchInput[name]);

	if (BX.SocNetLogDestination.obItemsSelected[name][itemId])
	{
		return BX.SocNetLogDestination.unSelectItem(name, element, template, itemId, type, search);
	}

	BX.SocNetLogDestination.obItemsSelected[name][itemId] = type;

	if (BX.SocNetLogDestination.obItemsLast[name][type] === null)
	{
		BX.SocNetLogDestination.obItemsLast[name][type] = [];
	}
	BX.SocNetLogDestination.obItemsLast[name][type][itemId] = itemId;

	if (!(element == null || template == null))
	{
		BX.SocNetLogDestination.changeItemClass(element, template, true);
	}

	BX.SocNetLogDestination.runSelectCallback(itemId, type, name, search);

	if (search === true)
	{
		if (BX.SocNetLogDestination.popupWindow != null)
		{
			BX.SocNetLogDestination.popupWindow.close();
		}

		if (BX.SocNetLogDestination.popupSearchWindow != null)
		{
			BX.SocNetLogDestination.popupSearchWindow.close();
		}
	}
	else
	{
		if (BX.SocNetLogDestination.popupWindow != null)
			BX.SocNetLogDestination.popupWindow.adjustPosition();
		if (BX.SocNetLogDestination.popupSearchWindow != null)
			BX.SocNetLogDestination.popupSearchWindow.adjustPosition();
	}


	var objSize = Object.size(BX.SocNetLogDestination.obItemsLast[name][type]);

	if(objSize > 5)
	{
		var destLast = {};
		var ii = 0;
		var jj = objSize-5;

		for(var i in BX.SocNetLogDestination.obItemsLast[name][type])
		{
			if(ii >= jj)
				destLast[BX.SocNetLogDestination.obItemsLast[name][type][i]] = BX.SocNetLogDestination.obItemsLast[name][type][i];
			ii++;
		}
	}
	else
	{
		var destLast = BX.SocNetLogDestination.obItemsLast[name][type];
	}

 	BX.userOptions.save('socialnetwork', 'log_destination', type, JSON.stringify(destLast));

	if (BX.util.in_array(type, ['contacts', 'companies', 'leads', 'deals']) && BX.SocNetLogDestination.obCrmFeed[name])
	{
		var lastCrmItems = [itemId];
		for (var i = 0; i < BX.SocNetLogDestination.obItemsLast[name].crm.length && lastCrmItems.length < 20; i++)
		{
			if (BX.SocNetLogDestination.obItemsLast[name].crm[i] != itemId)
				lastCrmItems.push(BX.SocNetLogDestination.obItemsLast[name].crm[i]);
		}

		BX.SocNetLogDestination.obItemsLast[name].crm = lastCrmItems;

		BX.userOptions.save('crm', 'log_destination', 'items', lastCrmItems);
	}

	return false;
};

BX.SocNetLogDestination.unSelectItem = function(name, element, template, itemId, type, search)
{
	if(!name)
	{
		name = 'lm';
	}

	if (!BX.SocNetLogDestination.obItemsSelected[name][itemId])
	{
		return false;
	}

	BX.SocNetLogDestination.changeItemClass(element, template, false);
	BX.SocNetLogDestination.runUnSelectCallback(itemId, type, name, search);

	if (search === true)
	{
		if (BX.SocNetLogDestination.popupWindow != null)
		{
			BX.SocNetLogDestination.popupWindow.close();
		}

		if (BX.SocNetLogDestination.popupSearchWindow != null)
		{
			BX.SocNetLogDestination.popupSearchWindow.close();
		}
	}
	else
	{
		if (BX.SocNetLogDestination.popupWindow != null)
			BX.SocNetLogDestination.popupWindow.adjustPosition();
		if (BX.SocNetLogDestination.popupSearchWindow != null)
			BX.SocNetLogDestination.popupSearchWindow.adjustPosition();
	}

	return false;
};

BX.SocNetLogDestination.runSelectCallback = function(itemId, type, name, search)
{
	if(!name)
	{
		name = 'lm';
	}

	if(!search)
	{
		search = false;
	}

	if(
		BX.SocNetLogDestination.obCallback[name]
		&& BX.SocNetLogDestination.obCallback[name].select
		&& BX.SocNetLogDestination.obItems[name][type]
		&& BX.SocNetLogDestination.obItems[name][type][itemId]
	)
	{
		BX.SocNetLogDestination.obCallback[name].select(BX.SocNetLogDestination.obItems[name][type][itemId], type, search, false, name);
	}
};

BX.SocNetLogDestination.runUnSelectCallback = function(itemId, type, name, search)
{
	if(!name)
		name = 'lm';

	if(!search)
		search = false;

	delete BX.SocNetLogDestination.obItemsSelected[name][itemId];
	if(BX.SocNetLogDestination.obCallback[name] && BX.SocNetLogDestination.obCallback[name].unSelect && BX.SocNetLogDestination.obItems[name][type] && BX.SocNetLogDestination.obItems[name][type][itemId])
		BX.SocNetLogDestination.obCallback[name].unSelect(BX.SocNetLogDestination.obItems[name][type][itemId], type, search, name);
};

/* public function */
BX.SocNetLogDestination.deleteItem = function(itemId, type, name)
{
	if(!name)
		name = 'lm';

	BX.SocNetLogDestination.runUnSelectCallback(itemId, type, name);
};

BX.SocNetLogDestination.deleteLastItem = function(name)
{
	if(!name)
		name = 'lm';

	var lastId = false;
	for (var itemId in BX.SocNetLogDestination.obItemsSelected[name])
		lastId = itemId;

	if (lastId)
	{
		var type = BX.SocNetLogDestination.obItemsSelected[name][lastId];
		BX.SocNetLogDestination.runUnSelectCallback(lastId, type, name);
	}
};

BX.SocNetLogDestination.initResultNavigation = function(name, type, obSource)
{
	BX.SocNetLogDestination.obCurrentElement[type] = null;
	BX.SocNetLogDestination.obResult[type] = [];
	BX.SocNetLogDestination.obCursorPosition[type] = {
		group: 0,
		row: 0,
		column: 0
	};

	var itemCount = 0;
	var cntInGroup = null;
	var groupCode = null;
	var itemCode = null;
	var resultGroupIndex = -1;
	var resultRowIndex = 0;
	var resultColumnIndex = 0;
	var bSkipNewGroup = false;
	var item = null;

	for (i=0;i<BX.SocNetLogDestination.arDialogGroups[name].length;i++)
	{
		groupCode = BX.SocNetLogDestination.arDialogGroups[name][i].groupCode;

		if (typeof obSource[groupCode] == 'undefined')
		{
			continue;
		}

		if (bSkipNewGroup)
		{
			bSkipNewGroup = false;
		}
		else
		{
			cntInGroup = 0;
		}

		for (var itemCode in obSource[groupCode])
		{
			if (!BX.SocNetLogDestination.obItems[name][groupCode][itemCode])
			{
				continue;
			}

			if (cntInGroup == 0)
			{
				if (groupCode == 'groups')
				{
					bSkipNewGroup = true;
				}
				resultGroupIndex++;
				BX.SocNetLogDestination.obResult[type][resultGroupIndex] = [];
				resultRowIndex = 0;
				resultColumnIndex = 0;
			}

			if (resultColumnIndex == 2)
			{
				resultRowIndex++;
				resultColumnIndex = 0;
			}

			if (typeof BX.SocNetLogDestination.obResult[type][resultGroupIndex][resultRowIndex] == 'undefined')
			{
				BX.SocNetLogDestination.obResult[type][resultGroupIndex][resultRowIndex] = [];
			}

			item = {
				id: itemCode,
				type: groupCode
			}

			BX.SocNetLogDestination.obResult[type][resultGroupIndex][resultRowIndex][resultColumnIndex] = item;

			if (itemCount <= 0)
			{
				BX.SocNetLogDestination.obCurrentElement[type] = item;
			}

			resultColumnIndex++;
			cntInGroup++;
			itemCount++;
		}
	}
}


BX.SocNetLogDestination.selectFirstSearchItem = function(name)
{
	if(!name)
		name = 'lm';
	var item = BX.SocNetLogDestination.obSearchFirstElement;
	if (item != null)
	{
		BX.SocNetLogDestination.selectItem(name, null, null, item.id, item.type, true);
		BX.SocNetLogDestination.obSearchFirstElement = null;
	}
};

BX.SocNetLogDestination.selectCurrentSearchItem = function(name)
{
	BX.SocNetLogDestination.selectCurrentItem('search', name);
};

BX.SocNetLogDestination.selectCurrentItem = function(type, name, params)
{
	if(!name)
	{
		name = 'lm';
	}

	if (type == 'search')
	{
		clearTimeout(BX.SocNetLogDestination.searchTimeout);
		if (BX.SocNetLogDestination.oXHR)
		{
			BX.SocNetLogDestination.oXHR.abort();
		}
	}

	var item = BX.SocNetLogDestination.obCurrentElement[type];
	if (item != null)
	{
		var element = BX(name + '_' + type + '_' + item.id);
		var template = BX.SocNetLogDestination.getTemplateByItemClass(element);
		BX.SocNetLogDestination.selectItem(name, (element ? element : null), (template ? template : null), item.id, item.type, (item.type === 'search'));
		if (
			typeof params == 'undefined'
			|| typeof params.closeDialog == 'undefined'
			|| params.closeDialog
		)
		{
			BX.SocNetLogDestination.obCurrentElement[type] = null;
			if (BX.SocNetLogDestination.isOpenDialog())
			{
				BX.SocNetLogDestination.closeDialog();
			}
			BX.SocNetLogDestination.closeSearch();
		}
	}
};

BX.SocNetLogDestination.moveCurrentSearchItem = function(name, direction)
{
	BX.SocNetLogDestination.moveCurrentItem('search', name, direction)
};

BX.SocNetLogDestination.moveCurrentItem = function(type, name, direction)
{
	BX.SocNetLogDestination.bResultMoved[type] = true;

	if (
		type == 'search'
		&& BX.SocNetLogDestination.oXHR
	)
	{
		BX.SocNetLogDestination.oXHR.abort();
		BX.SocNetLogDestination.hideSearchWaiter(name);
	}

	if (!BX.SocNetLogDestination.obCursorPosition[type])
	{
		BX.SocNetLogDestination.obCursorPosition[type] = {
			group: 0,
			row: 0,
			column: 0
		};
	}

	var bMoved = false;

	switch (direction)
	{
		case 'left':
			if (BX.SocNetLogDestination.focusOnTabs)
			{
				BX.SocNetLogDestination.moveCurrentTab(type, name, direction);
			}
			else if (BX.SocNetLogDestination.obCursorPosition[type].column == 1)
			{
				if (typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row][BX.SocNetLogDestination.obCursorPosition[type].column - 1] != 'undefined')
				{
					BX.SocNetLogDestination.obCursorPosition[type].column--;
					bMoved = true;
				}
			}
			break;
		case 'right':
			if (BX.SocNetLogDestination.focusOnTabs)
			{
				BX.SocNetLogDestination.moveCurrentTab(type, name, direction);
			}
			else if (BX.SocNetLogDestination.obCursorPosition[type].column == 0)
			{
				if (typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row][BX.SocNetLogDestination.obCursorPosition[type].column + 1] != 'undefined')
				{
					BX.SocNetLogDestination.obCursorPosition[type].column++;
					bMoved = true;
				}
			}
			break;
		case 'up':
			if (
				BX.SocNetLogDestination.obCursorPosition[type].row > 0
				&& typeof BX.SocNetLogDestination.obResult[type] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row - 1] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row - 1][BX.SocNetLogDestination.obCursorPosition[type].column] != 'undefined'
			)
			{
				BX.SocNetLogDestination.obCursorPosition[type].row--;
				bMoved = true;
			}
			else if (
				BX.SocNetLogDestination.obCursorPosition[type].row == 0
				&& typeof BX.SocNetLogDestination.obResult[type] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group - 1] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group - 1][BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group - 1].length - 1] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group - 1][BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group - 1].length - 1][0] != 'undefined'
			)
			{
				BX.SocNetLogDestination.obCursorPosition[type].row = BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group - 1].length - 1;
				BX.SocNetLogDestination.obCursorPosition[type].column = 0;
				BX.SocNetLogDestination.obCursorPosition[type].group--;
				bMoved = true;
			}
			else if (
				BX.SocNetLogDestination.obCursorPosition[type].group == 0
				&& BX.SocNetLogDestination.obCursorPosition[type].row == 0
				&& BX.util.in_array(type, BX.SocNetLogDestination.obTabs[name])
			)
			{
				BX.SocNetLogDestination.focusOnTabs = true;
			}
			break;
		case 'down':
			if (BX.SocNetLogDestination.focusOnTabs)
			{
				BX.SocNetLogDestination.focusOnTabs = false;
			}
			else if (
				typeof BX.SocNetLogDestination.obResult[type] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row + 1] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row + 1][BX.SocNetLogDestination.obCursorPosition[type].column] != 'undefined'
			)
			{
				BX.SocNetLogDestination.obCursorPosition[type].row++;
				bMoved = true;
			}
			else if (
				typeof BX.SocNetLogDestination.obResult[type] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row + 1] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row + 1][0] != 'undefined'
			)
			{
				BX.SocNetLogDestination.obCursorPosition[type].column = 0;
				BX.SocNetLogDestination.obCursorPosition[type].row++;
				bMoved = true;
			}
			else if (
				typeof BX.SocNetLogDestination.obResult[type] != 'undefined'
				&& BX.SocNetLogDestination.obCursorPosition[type].row == (BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group].length - 1)
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group + 1] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group + 1][0] != 'undefined'
				&& typeof BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group + 1][0][0] != 'undefined'
			)
			{
				BX.SocNetLogDestination.obCursorPosition[type].group++;
				BX.SocNetLogDestination.obCursorPosition[type].row = 0;
				BX.SocNetLogDestination.obCursorPosition[type].column = 0;
				bMoved = true;
			}
			break;
		default:
	}

	if (bMoved)
	{
		var oldId = BX.SocNetLogDestination.obCurrentElement[type].id;
		BX.SocNetLogDestination.obCurrentElement[type] = BX.SocNetLogDestination.obResult[type][BX.SocNetLogDestination.obCursorPosition[type].group][BX.SocNetLogDestination.obCursorPosition[type].row][BX.SocNetLogDestination.obCursorPosition[type].column];

		if (BX(name + '_' + type + '_' + oldId))
		{
			BX.SocNetLogDestination.unhoverItem(BX(name + '_' + type + '_' + oldId));
		}

		var hoveredNode = BX(name + '_' + type + '_' + BX.SocNetLogDestination.obCurrentElement[type].id);
		var containerNode = null;

		if (type == 'search')
		{
			containerNode = BX('bx-lm-box-search-tabs-content');
		}
		else if (type == 'last')
		{
			containerNode = BX('bx-lm-box-last-content');
		}
		else if (type == 'group')
		{
			containerNode = BX('bx-lm-box-group-content');
		}

		if (
			hoveredNode
			&& containerNode
		)
		{
			var arPosContainer = BX.pos(containerNode);
			var arPosNode = BX.pos(hoveredNode);

			if (
				arPosNode.bottom > arPosContainer.bottom
				|| arPosNode.top < arPosContainer.top
			)
			{
				containerNode.scrollTop += (
					arPosNode.bottom > arPosContainer.bottom
						? (arPosNode.bottom - arPosContainer.bottom)
						: (arPosNode.top - arPosContainer.top)
				);
			}

			BX.SocNetLogDestination.hoverItem(hoveredNode);
		}
	}
};

BX.SocNetLogDestination.moveCurrentTab = function(type, name, direction)
{
	var obTypeToTab = {
		'last': 'destLastTab',
		'group': 'destGroupTab',
		'department': 'destDepartmentTab'
	};

	var curTabPos = BX.util.array_search(type, BX.SocNetLogDestination.obTabs[name]);

	if (curTabPos >= 0)
	{
		if (direction == 'right')
		{
			curTabPos++;
		}
		else if (direction == 'left')
		{
			curTabPos--;
		}

		if (
			curTabPos <= (BX.SocNetLogDestination.obTabs[name].length - 1)
			&& curTabPos >= 0
			&& typeof BX.SocNetLogDestination.obTabs[name][curTabPos] != 'undefined'
		)
		{
			BX.SocNetLogDestination.SwitchTab(
				name,
				BX(obTypeToTab[BX.SocNetLogDestination.obTabs[name][curTabPos]] + '_' + name),
				BX.SocNetLogDestination.obTabs[name][curTabPos]
			);
		}
	}
};

BX.SocNetLogDestination.getItemHoverClassName = function(node)
{
	if (!node)
	{
		return false;
	}

	if (node.classList.contains('bx-finder-box-item-t1'))
	{
		return 'bx-finder-box-item-t1-hover';
	}
	else if (node.classList.contains('bx-finder-box-item-t2'))
	{
		return 'bx-finder-box-item-t2-hover';
	}
	else if (node.classList.contains('bx-finder-box-item-t3'))
	{
		return 'bx-finder-box-item-t3-hover';
	}
	else if (node.classList.contains('bx-finder-box-item-t4'))
	{
		return 'bx-finder-box-item-t4-hover';
	}
	else if (node.classList.contains('bx-finder-box-item-t5'))
	{
		return 'bx-finder-box-item-t5-hover';
	}
	else if (node.classList.contains('bx-finder-box-item-t6'))
	{
		return 'bx-finder-box-item-t6-hover';
	}
	else if (node.classList.contains('bx-finder-box-item-t7'))
	{
		return 'bx-finder-box-item-t7-hover';
	}

	return  false;
}

BX.SocNetLogDestination.hoverItem = function(node)
{
	var hoverClassName = BX.SocNetLogDestination.getItemHoverClassName(node);

	if (hoverClassName)
	{
		BX.addClass(
			node,
			hoverClassName
		);
	}
}

BX.SocNetLogDestination.unhoverItem = function(node)
{
	var hoverClassName = BX.SocNetLogDestination.getItemHoverClassName(node);

	if (hoverClassName)
	{
		BX.removeClass(
			node,
			hoverClassName
		);
	}
}

BX.SocNetLogDestination.getSelectedCount = function(name)
{
	if(!name)
		name = 'lm';

	var count = 0;
	for (var i in BX.SocNetLogDestination.obItemsSelected[name])
		count++;

	return count;
};

BX.SocNetLogDestination.getSelected = function(name)
{
	if(!name)
		name = 'lm';
	return BX.SocNetLogDestination.obItemsSelected[name];
};

BX.SocNetLogDestination.isOpenDialog = function()
{
	return BX.SocNetLogDestination.popupWindow != null? true: false;
};

BX.SocNetLogDestination.isOpenSearch = function()
{
	return BX.SocNetLogDestination.popupSearchWindow != null? true: false;
};

BX.SocNetLogDestination.closeDialog = function(silent)
{
	silent = silent === true? true: false;
	if (BX.SocNetLogDestination.popupWindow != null)
	{
		if (silent)
		{
			BX.SocNetLogDestination.popupWindow.destroy();
		}
		else
		{
			BX.SocNetLogDestination.popupWindow.close();
		}
	}
	return true;
};

BX.SocNetLogDestination.closeSearch = function()
{
	if (BX.SocNetLogDestination.popupSearchWindow != null)
	{
		BX.SocNetLogDestination.popupSearchWindow.close();
	}
	return true;
};

BX.SocNetLogDestination.createSocNetGroupContent = function(text)
{
	return BX.create('div', {
		children: [
			BX.create('div', {
				text: BX.message('LM_CREATE_SONETGROUP_TITLE').replace("#TITLE#", text)
			})
		]
	});
};

BX.SocNetLogDestination.createSocNetGroupButtons = function(text, name)
{
	var strReturn = [
		new BX.PopupWindowButton({
			text : BX.message("LM_CREATE_SONETGROUP_BUTTON_CREATE"),
			events : {
				click : function() {
					var groupCode = 'SGN'+ BX.SocNetLogDestination.obNewSocNetGroupCnt[name] + '';
					BX.SocNetLogDestination.obItems[name]['sonetgroups'][groupCode] = {
						id: groupCode,
						entityId: BX.SocNetLogDestination.obNewSocNetGroupCnt[name],
						name: text,
						desc: ''
					};

					var itemsNew = {
						'sonetgroups': {
						}
					};
					itemsNew['sonetgroups'][groupCode] = true;

					if (BX.SocNetLogDestination.popupSearchWindow != null)
					{
						BX.SocNetLogDestination.popupSearchWindowContent.innerHTML = BX.SocNetLogDestination.getItemLastHtml(itemsNew, true, name);
					}
					else
					{
						BX.SocNetLogDestination.openSearch(itemsNew, name);
					}

					BX.SocNetLogDestination.obNewSocNetGroupCnt[name]++;
					BX.SocNetLogDestination.createSocNetGroupWindow.close();
				}
			}
		}),
		new BX.PopupWindowButtonLink({
			text : BX.message("LM_CREATE_SONETGROUP_BUTTON_CANCEL"),
			className : "popup-window-button-link-cancel",
			events : {
				click : function() {
					BX.SocNetLogDestination.createSocNetGroupWindow.close();
				}
			}
		})
	];

	return strReturn;
};

BX.SocNetLogDestination.showSearchWaiter = function(name)
{
	if (
		typeof BX.SocNetLogDestination.oSearchWaiterEnabled[name] == 'undefined'
		|| !BX.SocNetLogDestination.oSearchWaiterEnabled[name]
	)
	{
		if (BX.SocNetLogDestination.oSearchWaiterContentHeight > 0)
		{
			BX.SocNetLogDestination.oSearchWaiterEnabled[name] = true;
			var startHeight = 0;
			var finishHeight = 40;

			BX.SocNetLogDestination.animateSearchWaiter(startHeight, finishHeight);
		}
	}
}

BX.SocNetLogDestination.hideSearchWaiter = function(name)
{
	if (
		typeof BX.SocNetLogDestination.oSearchWaiterEnabled[name] != 'undefined'
		&& BX.SocNetLogDestination.oSearchWaiterEnabled[name]
	)
	{
		BX.SocNetLogDestination.oSearchWaiterEnabled[name] = false;

		var startHeight = 40;
		var finishHeight = 0;
		BX.SocNetLogDestination.animateSearchWaiter(startHeight, finishHeight);
	}
}

BX.SocNetLogDestination.animateSearchWaiter = function(startHeight, finishHeight)
{
	if (
		BX('bx-lm-box-search-waiter')
		&& BX('bx-lm-box-search-tabs-content')
	)
	{
		(new BX.fx({
			time: 0.5,
			step: 0.05,
			type: 'linear',
			start: startHeight,
			finish: finishHeight,
			callback: BX.delegate(function(height)
			{
				if (this)
				{
					this.waiterBlock.style.height = height + 'px';
					this.contentBlock.style.height = (BX.SocNetLogDestination.oSearchWaiterContentHeight) - height + 'px';
				}
			},
			{
				waiterBlock: BX('bx-lm-box-search-waiter'),
				contentBlock: BX('bx-lm-box-search-tabs-content')
			}),
			callback_complete: function()
			{
			}
		})).start();
	}
}

BX.SocNetLogDestination.changeItemClass = function(element, template, bSelect)
{
	if (
		element
		&& typeof BX.SocNetLogDestination.obTemplateClassSelected[template] != 'undefined'
	)
	{
		if (bSelect)
		{
			BX.addClass(element, BX.SocNetLogDestination.obTemplateClassSelected[template]);
		}
		else
		{
			BX.removeClass(element, BX.SocNetLogDestination.obTemplateClassSelected[template]);
		}
	}
}

BX.SocNetLogDestination.getTemplateByItemClass = function(element)
{
	if (element)
	{
		for (var key in BX.SocNetLogDestination.obTemplateClass)
		{
			if (BX.hasClass(element, BX.SocNetLogDestination.obTemplateClass[key]))
			{
				return key;
				break;
			}
		}
	}
}

BX.SocNetLogDestination.BXfpSetLinkName = function(ob)
{
	BX(ob.tagInputName).innerHTML = (
		BX.SocNetLogDestination.getSelectedCount(ob.formName) <= 0
			? ob.tagLink1
			: ob.tagLink2
	);
};

BX.SocNetLogDestination.BXfpUnSelectCallback = function(item)
{
	var elements = BX.findChildren(BX(this.inputContainerName), {attribute: {'data-id': '' + item.id + ''}}, true);
	if (elements !== null)
	{
		for (var j = 0; j < elements.length; j++)
		{
			if (
				typeof (this.undeleteClassName) == 'undefined'
				|| !BX.hasClass(elements[j], this.undeleteClassName)
			)
			{
				BX.remove(elements[j]);
			}
		}
	}
	BX(this.inputName).value = '';
	BX.SocNetLogDestination.BXfpSetLinkName(this);
};

BX.SocNetLogDestination.BXfpSearch = function(event)
{
	return BX.SocNetLogDestination.searchHandler(event, {
		formName: this.formName,
		inputId: this.inputName,
		linkId: this.tagInputName,
		sendAjax: true,
		multiSelect: true
	});
}

BX.SocNetLogDestination.BXfpSearchBefore = function(event)
{
	return BX.SocNetLogDestination.searchBeforeHandler(event, {
		formName: this.formName,
		inputId: this.inputName
	});
}

BX.SocNetLogDestination.BXfpOpenDialogCallback = function()
{
	BX.style(BX(this.inputBoxName), 'display', 'inline-block');
	BX.style(BX(this.tagInputName), 'display', 'none');
	BX.defer(BX.focus)(BX(this.inputName));
};

BX.SocNetLogDestination.BXfpCloseDialogCallback = function()
{
	if (
		!BX.SocNetLogDestination.isOpenSearch()
		&& BX(this.inputName).value.length <= 0
	)
	{
		BX.style(BX(this.inputBoxName), 'display', 'none');
		BX.style(BX(this.tagInputName), 'display', 'inline-block');
		BX.SocNetLogDestination.BXfpDisableBackspace();
	}
};

BX.SocNetLogDestination.BXfpCloseSearchCallback = function()
{
	if (
		!BX.SocNetLogDestination.isOpenSearch()
		&& BX(this.inputName).value.length > 0
	)
	{
		BX.style(BX(this.inputBoxName), 'display', 'none');
		BX.style(BX(this.tagInputName), 'display', 'inline-block');
		BX(this.inputName).value = '';
		BX.SocNetLogDestination.BXfpDisableBackspace();
	}
}

BX.SocNetLogDestination.BXfpDisableBackspace = function(event)
{
	if (
		BX.SocNetLogDestination.backspaceDisable
		|| BX.SocNetLogDestination.backspaceDisable !== null
	)
	{
		BX.unbind(window, 'keydown', BX.SocNetLogDestination.backspaceDisable);
	}

	BX.bind(window, 'keydown', BX.SocNetLogDestination.backspaceDisable = function(event)
	{
		if (event.keyCode == 8)
		{
			BX.PreventDefault(event);
			return false;
		}
	});

	setTimeout(function()
	{
		BX.unbind(window, 'keydown', BX.SocNetLogDestination.backspaceDisable);
		BX.SocNetLogDestination.backspaceDisable = null;
	}, 5000);
}

BX.SocNetLogDestination.searchHandler = function(event, params)
{
	if (
		event.keyCode == 16
		|| event.keyCode == 17
		|| event.keyCode == 18
		|| event.keyCode == 20
		|| event.keyCode == 244
		|| event.keyCode == 224
		|| event.keyCode == 91
		|| event.keyCode == 9 // tab
	)
	{
		return false;
	}

	var type = null;
	if (BX.SocNetLogDestination.popupSearchWindow != null)
	{
		type = 'search';
	}
	else if (
		BX.SocNetLogDestination.obTabSelected[params.formName] == 'last'
		|| BX.SocNetLogDestination.obTabSelected[params.formName] == 'group'
		|| BX.SocNetLogDestination.obTabSelected[params.formName] == 'department'
	)
	{
		type = BX.SocNetLogDestination.obTabSelected[params.formName];
	}

	if (type)
	{
		if (event.keyCode == 37)
		{
			BX.SocNetLogDestination.moveCurrentItem(type, params.formName, 'left');
			BX.PreventDefault(event);
			return false;
		}
		else if (event.keyCode == 38)
		{
			BX.SocNetLogDestination.moveCurrentItem(type, params.formName, 'up');
			BX.PreventDefault(event);
			return false;
		}
		else if (event.keyCode == 39)
		{
			BX.SocNetLogDestination.moveCurrentItem(type, params.formName, 'right');
			BX.PreventDefault(event);
			return false;
		}
		else if (event.keyCode == 40)
		{
			BX.SocNetLogDestination.moveCurrentItem(type, params.formName, 'down');
			BX.PreventDefault(event);
			return false;
		}
		else if (event.keyCode == 13)
		{
			BX.SocNetLogDestination.selectCurrentItem(type, params.formName);
			return BX.PreventDefault(event);
		}
		else if (
			typeof params.multiSelect != 'undefined'
			&& params.multiSelect
			&& event.keyCode == 32 // space
			&& type != 'search'
		)
		{
			BX.SocNetLogDestination.selectCurrentItem(type, params.formName, {
				closeDialog: false
			});
			return true;
		}
	}

	if (event.keyCode == 27)
	{
		BX(params.inputId).value = '';
		BX.style(BX(params.linkId), 'display', 'inline');
	}
	else
	{
		BX.SocNetLogDestination.search(
			BX(params.inputId).value,
			params.sendAjax,
			params.formName
		);
	}

	if (
		!BX.SocNetLogDestination.isOpenDialog()
		&& BX(params.inputId).value.length <= 0
	)
	{
		BX.SocNetLogDestination.openDialog(params.formName);
	}
	else
	{
		if (
			BX.SocNetLogDestination.sendEvent
			&& BX.SocNetLogDestination.isOpenDialog()
		)
		{
			BX.SocNetLogDestination.closeDialog();
		}
	}

	if (event.keyCode == 8)
	{
		BX.SocNetLogDestination.sendEvent = true;
	}
	return true;
}

BX.SocNetLogDestination.searchBeforeHandler = function(event, params)
{
	if (
		event.keyCode == 8
		&& BX(params.inputId).value.length <= 0
	)
	{
		BX.SocNetLogDestination.sendEvent = false;
		BX.SocNetLogDestination.deleteLastItem(params.formName);
	}
	else if (event.keyCode == 13)
	{
		return BX.PreventDefault(event);
	}

	return true;
}

BX.SocNetLogDestination.loadAll = function(params)
{
	if (
		typeof params != 'undefined'
		&& typeof params.name != 'undefined'
		&& typeof params.callback == 'function'
	)
	{
		BX.ajax({
			url: '/bitrix/components/bitrix/main.post.form/post.ajax.php',
			method: 'POST',
			dataType: 'json',
			data: {
				'LD_ALL' : 'Y',
				'sessid': BX.bitrix_sessid()
			},
			onsuccess: function(data)
			{
				BX.onCustomEvent('onFinderAjaxLoadAll', [ data, BX.SocNetLogDestination ]);
				params.callback();
			},
			onfailure: function(data)
			{
			}
		});
	}
};

BX.SocNetLogDestination.compareDestinations = function(a, b)
{
	if (
		typeof a.sort == 'undefined'
		&& typeof b.sort == 'undefined'
	)
	{
		return 0;
	}
	else if (
		typeof a.sort != 'undefined'
		&& typeof b.sort == 'undefined'
	)
	{
		return -1;
	}
	else if (
		typeof a.sort == 'undefined'
		&& typeof b.sort != 'undefined'
	)
	{
		return 1;
	}
	else
	{
		if (
			typeof a.sort.Y != 'undefined'
			&& typeof b.sort.Y == 'undefined'
		)
		{
			return -1;
		}
		else if (
			typeof a.sort.Y == 'undefined'
			&& typeof b.sort.Y != 'undefined'
		)
		{
			return 1;
		}
		else if (
			typeof a.sort.Y != 'undefined'
			&& typeof b.sort.Y != 'undefined'
		)
		{
			if (parseInt(a.sort.Y) > parseInt(b.sort.Y))
			{
				return -1;
			}
			else if (parseInt(a.sort.Y) < parseInt(b.sort.Y))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			if (parseInt(a.sort.N) > parseInt(b.sort.N))
			{
				return -1;
			}
			else if (parseInt(a.sort.N) < parseInt(b.sort.N))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}

		return 0;
	}

	return 0;

}

})();