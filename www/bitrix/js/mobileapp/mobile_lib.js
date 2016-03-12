;
(function ()
{

	if (window.BXMobileApp) return;

	window.BXMobileApp =
	{
		apiVersion: appVersion,
		//platform: platform,
		cordovaVersion: "3.6.3",
		UI: {
			IOS: {
				flip: function ()
				{
					app.flipScreen()
				}
			},
			types: {
				COMMON: 0,
				BUTTON: 1,
				PANEL: 2,
				TABLE: 3,
				MENU: 4,
				ACTION_SHEET: 5,
				NOTIFY_BAR: 6
			},
			parentTypes: {
				TOP_BAR: 0,
				BOTTOM_BAR: 1,
				SLIDING_PANEL: 2,
				UNKNOWN: 3
			},
			Slider: {
				state: {
					CENTER: 0,
					LEFT: 1,
					RIGHT: 2
				},
				setState: function (state)
				{
					switch (state)
					{
						case this.state.CENTER:
							app.openContent();
							break;
						case this.state.LEFT:
							app.openLeft();
							break;
						case this.state.RIGHT:
							app.exec("openRight");
							break;
						default ://to do nothing
					}
				},
				setStateEnabled: function (state, enabled)
				{
					switch (state)
					{
						case this.state.LEFT:
							app.enableSliderMenu(enabled);
							break;
						case this.state.RIGHT:
							app.exec("enableRight", enabled);
							break;
						default ://to do nothing
					}
				}
			},
			Photo: {
				show: function (params)
				{
					app.openPhotos(params);
				}
			},
			Document: {
				showCacheList: function (params)
				{
					app.showDocumentsCache(params);
				},
				open: function (params)
				{
					app.openDocument(params);
				}
			},
			DatePicker: {
				setParams: function (params)
				{
					if (typeof params == "object")
						this.params = params;
				},
				show: function (params)
				{
					this.setParams(params);
					app.showDatePicker(this.params);

				},
				hide: function ()
				{
					app.hideDatePicker();
				}
			},
			SelectPicker:{
				show: function(params){
					app.showSelectPicker(params);
				},
				hide: function(){
					app.hideSelectPicker();
				}
			},
			BarCodeScanner: {
				open: function (params)
				{
					app.openBarCodeScanner(params);
				}
			},
			NotifyPanel: {
				setNotificationNumber:function(number){
					app.setCounters({notifications:number});
				},
				setMessagesNumber:function(number){
					app.setCounters({messages:number});
				},
				setCounters: function (params)
				{
					app.setCounters(params);
				},
				refreshPage: function (pagename)
				{
					app.refreshPanelPage(pagename);
				},
				setPages: function (pages)
				{
					app.setPanelPages(pages);
				}
			}
		},
		PushManager:
		{
			prepareParams : function (push)
			{
				if (typeof (push) != 'object' || typeof (push.params) == 'undefined')
				{
					return {'ACTION': 'NONE'};
				}

				var result = {};
				try
				{
					result = JSON.parse(push.params);
				}
				catch(e)
				{
					result = {'ACTION': push.params};
				}

				return result;
			}
		},
		PageManager:
		{
			loadPageBlank: function (params)
			{
				/**
				 * Notice:
				 * use "bx24ModernStyle:true" to get new look of navigation bar
				 */
				app.loadPageBlank(params);
			},
			loadPageUnique: function (params)
			{
				if (typeof(params) != 'object')
					return false;

				/**
				 * Notice:
				 * use "bx24ModernStyle:true" to get new look of navigation bar
				 */

				params.unique = true;

				app.loadPageBlank(params);

				if (typeof(params.data) == 'object')
				{
					app.onCustomEvent("onPageParamsChangedLegacy", {url: params.url, data: params.data});
					BX.onCustomEvent("onPageParamsChangedLegacy", [{url: params.url, data: params.data}]);
				}

				return true;
			},
			loadPageStart: function (params)
			{
				app.loadPageStart(params);
			},
			loadPageModal: function (params)
			{
				app.showModalDialog(params)
			}
		},
		PushManager:
		{
			prepareParams : function (push)
			{
				if (typeof (push) != 'object' || typeof (push.params) == 'undefined')
				{
					return {'ACTION': 'NONE'};
				}

				var result = {};
				try
				{
					result = JSON.parse(push.params);
				}
				catch(e)
				{
					result = {'ACTION': push.params};
				}

				return result;
			}
		},
		TOOLS: {
			extend: function (child, parent)
			{
				var f = function ()
				{
				};
				f.prototype = parent.prototype;

				child.prototype = new f();
				child.prototype.constructor = child;

				child.superclass = parent.prototype;
				if (parent.prototype.constructor == Object.prototype.constructor)
				{
					parent.prototype.constructor = parent;
				}
			},
			merge: function (obj1, obj2)
			{

				for (var key in obj1)
				{
					if (typeof obj2[key] != "undefined")
					{
						obj1[key] = obj2[key];
					}
				}

				return obj1;
			}

		},
		onCustomEvent: function (eventName, params)
		{
			app.onCustomEvent(eventName, params, false, false)
		}
	};


//--->Base UI element
	BXMobileApp.UI.Element = function (id, params)
	{
		this.id = (typeof id == "undefined")
			? this.type + "_" + Math.random()
			: id;
		this.parentId = ((params.parentId) ? params.parentId : BXMobileApp.UI.UNKNOWN);
		this.isCreated = false;
		this.isShown = false;
	};


	BXMobileApp.UI.Element.prototype.onCreate = function ()
	{
		this.isCreated = true;
		if (this.isShown)
		{
			app.exec("show", {type: this.type, id: this.id});
		}
	};


	BXMobileApp.UI.Element.prototype.getIdentifiers = function ()
	{
		return {
			id: this.id,
			type: this.type,
			parentId: this.parentId
		};
	};


	BXMobileApp.UI.Element.prototype.show = function ()
	{
		this.isShown = true;
		if (this.isCreated)
		{
			app.exec("show", {type: this.type, id: this.id});
		}
	};


	BXMobileApp.UI.Element.prototype.hide = function ()
	{
		this.isShown = false;
		app.exec("hide", {type: this.type, id: this.id});
	};

	BXMobileApp.UI.Element.prototype.destroy = function ()
	{
		//TODO destroy object
	};


	/**
	 * Button class
	 * @param id
	 * @param params
	 * @constructor
	 */
	BXMobileApp.UI.Button = function (id, params)
	{
		this.callback = params.callback;
		this.icon = params.icon;
		this.name = params.title;
		this.type = BXMobileApp.UI.types.BUTTON;
		BXMobileApp.UI.Button.superclass.constructor.apply(this, [id, params]);
	};

	BXMobileApp.TOOLS.extend(BXMobileApp.UI.Button, BXMobileApp.UI.Element);


	BXMobileApp.UI.Button.prototype.setBadge = function (number)
	{
		var params = this.getIdentifiers();
		params["badgeText"] = number;

		app.updateButtonBadge(params);
	};

	BXMobileApp.UI.Button.prototype.remove = function ()
	{
		var params = this.getIdentifiers();

		app.removeButtons(params);
	};

	/**
	 * Menu class
	 * @param id
	 * @param params
	 * @constructor
	 */
	BXMobileApp.UI.Menu = function (params, id)
	{
		this.items = params.items;
		this.type = BXMobileApp.UI.types.MENU;
		BXMobileApp.UI.Menu.superclass.constructor.apply(this, [id, params]);
		app.menuCreate({items: this.items});
	};
	BXMobileApp.TOOLS.extend(BXMobileApp.UI.Menu, BXMobileApp.UI.Element);

	BXMobileApp.UI.Menu.prototype.show = function ()
	{
		app.menuShow();
	};

	BXMobileApp.UI.Menu.prototype.hide = function ()
	{
		app.menuHide();
	};


	/**
	 *
	 * @param params - params object
	 *    "message" - notification text
	 *    "groupId" - identifier of group ("common" by default)
	 *	  "color" - background color (alpha is supported)
	 *    "textColor" - color of text (alpha is supported)
	 *	  "loaderColor" - loader color (alpha is supported)
	 *	  "bottomBorderColor" - color of bottom border (alpha is supported)
	 *	  "indicatorHeight" - max height of indicator container (image or loader)
	 *	  "maxLines" - max number of lines
	 *	  "useLoader" - (false/true) loading indicator will be used
	 *	  "imageURL" - link to the image file which will be used as indicator
	 *	  "iconName" - name of image in application resources which will be used as indicator
	 *	  "imageBorderRadius" - border radius of the indicator in %
	 *	  "align" - alignment of content (indicator+text), "left"|"center"
	 *	  "useCloseButton" - close button will be displayed at the right side of the notification
	 *	  "autoHideTimeout" - auto close timeout (for example 2000 ms)
	 *	  "hideOnTap" - the notification will be close if user tapped on it.
	 *	  "onHideAfter" - the function which will be called after the notification has closed
	 *	  "onTap" - the function which will when user has tapped on the notification
	 *	  "extra" - custom data, it will be passed to the onTap and onHideAfter
	 *	  "isGlobal" - global notification flag
	 *
	 * @param id - identifier of the notification
	 * @constructor
	 *
	 */
	BXMobileApp.UI.NotificationBar = function (params, id)
	{
		this.params = BXMobileApp.TOOLS.merge(params, {});
		this.type = BXMobileApp.UI.types.NOTIFY_BAR;

		BXMobileApp.UI.NotificationBar.superclass.constructor.apply(this, [id, params]);
		var addParams = this.params;
		addParams["id"] = this.id;
		addParams["onCreate"] =  BX.proxy(function (params)
		{
			this.onCreate(params)
		}, this);
		app.exec("notificationBar",
			{
				action: "add",
				params: addParams

			});
	};
	BXMobileApp.TOOLS.extend(BXMobileApp.UI.NotificationBar, BXMobileApp.UI.Element);

	BXMobileApp.UI.NotificationBar.prototype.onCreate = function (json)
	{
		this.isCreated = true;
		if(this.isShown)
		{
			app.exec("notificationBar", {action:"show", params: this.params});
		}
	};

	BXMobileApp.UI.NotificationBar.prototype.show = function ()
	{
		if (this.isCreated)
		{
			app.exec("notificationBar", {action: "show", params: this.params});
		}

		this.isShown = true;
	};

	BXMobileApp.UI.NotificationBar.prototype.hide = function ()
	{
		if (this.isShown)
		{
			app.exec("notificationBar", {action: "hide", params: this.params});
		}

		this.isShown = false;
	};



	/**
	 * ActionSheet class
	 * @param id
	 * @param params
	 * @constructor
	 */
	BXMobileApp.UI.ActionSheet = function (params, id)
	{

		this.items = params.buttons;
		this.title = (params.title ? params.title : "");
		this.type = BXMobileApp.UI.types.ACTION_SHEET;
		BXMobileApp.UI.ActionSheet.superclass.constructor.apply(this, [id, params]);
		app.exec("createActionSheet", {
			"onCreate": BX.proxy(function (sheet)
			{
				this.onCreate(sheet);
			}, this),
			id: this.id,
			title: this.title,
			buttons: this.items
		});
	};

	BXMobileApp.TOOLS.extend(BXMobileApp.UI.ActionSheet, BXMobileApp.UI.Element);

	BXMobileApp.UI.ActionSheet.prototype.show = function ()
	{
		if (this.isCreated)
		{
			app.exec("showActionSheet", {"id": this.id});
		}
		this.isShown = true;
	};

	BXMobileApp.UI.ActionSheet.prototype.onCreate = function (json)
	{
		this.isCreated = true;
		if (this.isShown)
		{
			app.exec("showActionSheet", {"id": this.id});
		}
	};

	/**
	 * ActionSheet class
	 * @param id
	 * @param params
	 * @constructor
	 */
	BXMobileApp.UI.Table = function (params, id)
	{
		this.params = {
			table_id: id,
			url: params.url||"",
			isroot: false,

			TABLE_SETTINGS: {
				callback: function ()
				{
				},
				markmode: false,
				modal: false,
				multiple: false,
				okname: "OK",
				cancelname: "Cancel",
				showtitle: false,
				alphabet_index: false,
				selected: {},
				button: {}
			}
		};

		this.params.table_settings = this.params.TABLE_SETTINGS;

		this.params = BXMobileApp.TOOLS.merge(this.params, params);
		this.params.type = BXMobileApp.UI.types.TABLE;
		BXMobileApp.UI.Table.superclass.constructor.apply(this, [id, params]);
	};

	BXMobileApp.TOOLS.extend(BXMobileApp.UI.Table, BXMobileApp.UI.Element);

	BXMobileApp.UI.Table.prototype.show = function ()
	{
		app.openBXTable(this.params);
	};

	BXMobileApp.UI.Table.prototype.useCache = function (cacheEnable)
	{
		this.params.TABLE_SETTINGS.cache = cacheEnable || false;
	};

	BXMobileApp.UI.Table.prototype.useAlphabet = function (useAlphabet)
	{
		this.params.TABLE_SETTINGS.alphabet_index = useAlphabet || false;
	};

	BXMobileApp.UI.Table.prototype.setModal = function (modal)
	{
		this.params.TABLE_SETTINGS.modal = modal || false;
	};

	BXMobileApp.UI.Table.prototype.clearCache = function ()
	{
		return this.exec("removeTableCache", {"table_id": this.id});
	};


	/**
	 * Page object
	 * @type {{topBar: {show: Function, hide: Function, buttons: {}, addRightButton: Function, addLeftButton: Function, title: {show: Function, hide: Function, setImage: Function, setText: Function, setDetailText: Function}}, slidingPanel: {buttons: {}, hide: {}, show: {}, addButton: Function, removeButton: Function}, refresh: {params: {enable: boolean, callback: boolean, pulltext: string, downtext: string, loadtext: string}, setParams: Function, setEnabled: Function, start: Function, stop: Function}, bottomBar: {show: Function, hide: Function, buttons: {}, addButton: Function}, menus: {items: {}, create: Function, get: Function, update: Function}}}
	 */

	BXMobileApp.UI.Page =
	{
		isVisible: function (params)
		{
			app.exec("checkOpenStatus", params);
		},
		reload: function ()
		{
			app.reload();
		},
		reloadUnique: function()
		{
			BXMobileApp.UI.Page.params.get({callback:function(data){

				BX.localStorage.set('mobileReloadPageData', {url: location.pathname+location.search, data: data});
				app.reload();
			}});
		},
		close: function (params)
		{
			app.closeController(params)
		},
		captureKeyboardEvents: function (enable)
		{
			app.enableCaptureKeyboard(!((typeof enable == "boolean" && enable === false)))
		},
		setId:function(id)
		{
			app.setPageID(id);
		},
		getTitle:function(){
			return this.TopBar.title;
		},
		params: {
			set: function (params)
			{
				app.changeCurPageParams(params);
			},
			get: function (params)
			{
				var data = BX.localStorage.get('mobileReloadPageData');
				if (data && data.url == location.pathname+location.search && params.callback)
				{
					BX.localStorage.remove('mobileReloadPageData');
					params.callback(data.data);
				}
				else
				{
					app.getPageParams(params);
				}
			}
		},
		TopBar: {
			show: function ()
			{
				app.visibleNavigationBar(true);
			},
			hide: function ()
			{
				app.visibleNavigationBar(false);
			},
			buttons: {},
			addRightButton: function (buttonObject)
			{
				this.buttons[buttonObject.id] = buttonObject;
				var id = buttonObject.id;
				var buttons = {};
				buttons[id] =
				{
					name: buttonObject.name,
					callback: buttonObject.callback,
					type: buttonObject.type
				};

				app.addButtons(buttons);
			},
			addLeftButton: function (buttonObject)
			{
				//TODO
			},
			title: {
				params: {
					imageUrl: "",
					text: "",
					detailText: "",
					callback: ""
				},
				timeout:0,
				isAboutToShow:false,
				show: function ()
				{
					this.isAboutToShow = (this.timeout > 0);

					if(!this.isAboutToShow)
						app.titleAction("show");
				},
				hide: function ()
				{
					app.titleAction("hide")
				},
				setImage: function (imageUrl)
				{
					this.params.imageUrl = imageUrl;
					this.redraw();
				},
				setText: function (text)
				{
					this.params.text = text;
					this.redraw();
				},
				setDetailText: function (text)
				{
					this.params.detailText = text;
					this.redraw();
				},
				setCallback: function (callback)
				{
					this.params.callback = callback;
					this.redraw();
				},
				redraw:function()
				{
					if(this.timeout > 0)
						clearTimeout(this.timeout);

					this.timeout = setTimeout(BX.proxy(this._applyParams, this), 10);
				},
				_applyParams:function()
				{
					app.titleAction("setParams", this.params);
					this.timeout = 0;

					if(this.isAboutToShow)
						this.show()
				}
			}
		},
		SlidingPanel: {
			buttons: {},
			hide: function ()
			{
				app.hideButtonPanel();
			},
			show: function (params)
			{
				app.showSlidingPanel(params);
			},
			addButton: function (buttonObject)
			{
				//TODO
			},
			removeButton: function (buttonId)
			{
				//TODO
			}
		},
		Refresh: {
			//on|off pull down action on the current page
			//params.pulltext, params.downtext, params.loadtext
			//params.callback - action on pull-down-refresh
			//params.enable - true|false
			params: {
				enable: false,
				callback: false,
				pulltext: "Pull to refresh",
				downtext: "Release to refresh",
				loadtext: "Loading...",
				timeout: "60"
			},
			setParams: function (params)
			{
				this.params.pulltext = (params.pullText ? params.pullText : this.params.pulltext);
				this.params.downtext = (params.releaseText ? params.releaseText : this.params.downtext);
				this.params.loadtext = (params.loadText ? params.loadText : this.params.loadtext);
				this.params.callback = (params.callback ? params.callback : this.params.callback);
				this.params.enable = (typeof params.enabled == "boolean" ? params.enabled : this.params.enable);
				this.params.timeout = (params.timeout ? params.timeout : this.params.timeout);
				app.pullDown(this.params);
			},
			setEnabled: function (enabled)
			{
				this.params.enable = (typeof enabled == "boolean" ? enabled : this.params.enable);
				app.pullDown(this.params);
			},
			start: function ()
			{
				app.exec("pullDownLoadingStart");
			},
			stop: function ()
			{
				app.exec("pullDownLoadingStop");
			}

		},
		BottomBar: {
			buttons: {},
			show: function ()
			{
				//TODO
			},
			hide: function ()
			{
				//TODO
			},
			addButton: function (buttonObject)
			{
				//TODO
			}
		},
		LoadingScreen: {
			show: function ()
			{
				app.showLoadingScreen();
			},
			hide: function ()
			{
				app.hideLoadingScreen();
			},
			setEnabled: function (enabled)
			{
				app.enableLoadingScreen(!((typeof enabled == "boolean" && enabled === false)))
			}
		},
		TextPanel: {
			defaultParams : {
				placeholder: "Text here...",
				button_name: "Send",
                mentionDataSource: {},
				action: function (){},
                smileButton:{},
				plusAction: "",
				callback:"-1",
				useImageButton: true
			},
			params:{},
			isAboutToShow: false,
			temporaryParams: {},
            timeout:0,
			setParams: function (params)
			{
				this.params = BXMobileApp.TOOLS.merge(this.defaultParams, params);
				if (this.isAboutToShow)
				{
                    this.redraw();
				}
			},
			show: function (params)
			{
				if (typeof params == "object")
				{
					this.setParams(params);
				}

				var showParams = this.getParams();
				if (!this.isAboutToShow)
				{
					for (var key in this.temporaryParams)
					{
						showParams[key] = this.temporaryParams[key];
					}

					this.temporaryParams = {};
				}

				if (BXMobileApp.apiVersion >= 10)
				{
					app.textPanelAction("show", showParams);
				}
				else
				{
					delete showParams['text'];
					app.showInput(showParams);
				}

				this.isAboutToShow = true;
			},
			hide: function ()
			{
				if (BXMobileApp.apiVersion >= 10)
					app.textPanelAction("hide");
				else
					app.hideInput();
			},
			focus: function ()
			{
				if (BXMobileApp.apiVersion >= 10)
					app.textPanelAction("focus", this.getParams());
			},
			clear: function ()
			{
				if (BXMobileApp.apiVersion >= 10)
					app.textPanelAction("clear", this.getParams());
				else
					app.clearInput();

			},
			setUseImageButton: function (use)
			{
				this.params.useImageButton = !((typeof use == "boolean" && use === false));
                this.redraw();
			},
			setAction: function (callback)
			{
				this.params.action = callback;
                this.redraw();
			},
			setText: function (text)
			{
				if (!this.isAboutToShow)
				{
					this.temporaryParams["text"] = text;
				}
				else
				{

                    var params = app.clone(this.params, true);
                    params.text = text;
					app.textPanelAction("setParams", params);
				}


			},
			showLoading: function (shown)
			{
				app.showInputLoading(shown);
			},
			getParams: function ()
			{
				var params = {};
				for (var key in this.params)
				{
					params[key] = this.params[key]
				}

				return params;
			},
            redraw:function()
            {
                if(this.timeout > 0)
                    clearTimeout(this.timeout);

                this.timeout = setTimeout(BX.proxy(this._applyParams, this), 100);
            },
            _applyParams:function()
            {
                app.textPanelAction("setParams", this.params);
                this.timeout = 0;

                if(this.isAboutToShow)
                    this.show()
            }

		},
		Scroll: {
			setEnabled: function (enabled)
			{
				app.enableScroll(enabled);
			}
		}

	};
})();




