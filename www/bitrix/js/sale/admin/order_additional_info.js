BX.namespace("BX.Sale.Admin.OrderAdditionalInfo");

BX.Sale.Admin.OrderAdditionalInfo =
{
	choosePerson: function(formName, languageId)
	{
		window.open(
			'/bitrix/admin/user_search.php?lang='+languageId+'&FN='+formName+'&FC=RESPONSIBLE_ID',
			'',
			'scrollbars=yes,resizable=yes,width=840,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 840)/2-5)
		);
	},

	changePerson: function()
	{
		var node = BX("order_additional_info_responsible"),
			responsibleId = BX('RESPONSIBLE_ID').value;

		if(node)
			node.href = '/bitrix/admin/user_edit.php?lang='+BX.lang+'&ID='+responsibleId;

		var params = {
			action : 'changeResponsibleUser',
			user_id : responsibleId,
			callback: function(result)
						{
							BX.Sale.Admin.OrderAdditionalInfo.setResponsible(result.RESPONSIBLE);
							BX.Sale.Admin.OrderAdditionalInfo.setEmpResponsible(result.EMP_RESPONSIBLE);
							BX.Sale.Admin.OrderAdditionalInfo.setDateResponsible(result.DATE_RESPONSIBLE);
						}
		};

		BX.Sale.Admin.OrderAjaxer.sendRequest(params);
	},

	setResponsible: function(responsible)
	{
		var span = BX("order_additional_info_responsible"),
			name = (!!responsible[0]) ? responsible[0] : '',
			lastName = (!!responsible[1]) ? responsible[1] : '';

		if (span)
			BX.html(span, lastName + " " + name);
	},

	setEmpResponsible: function(empResponsible)
	{
		var span = BX("order_additional_info_emp_responsible");
		var name = (!!empResponsible[0]) ? empResponsible[0] : '';
		var lastName = (!!empResponsible[1]) ? empResponsible[1] : '';

		if (span)
			BX.html(span, lastName + " " + name);
	},

	setDateResponsible: function(dateResponsible)
	{
		var span = BX("order_additional_info_date_responsible");

		if (span)
			BX.html(span, dateResponsible);
	},

	showCommentsDialog: function(orderId, commentNode)
	{
		var comments = commentNode.innerHTML,
			dialog = new BX.CDialog({
			'content': '<textarea style="width:710px;height:260px;" name="COMMENTS" id="COMMENTS">'+
				comments+
			'</textarea>',
			'title': BX.message("SALE_ORDER_ADDITIONAL_INFO_COMMENT_EDIT"),
			'width': 750,
			'height': 300,
			'resizable': true
		});

		dialog.ClearButtons();
		dialog.SetButtons([
			{
				'title': BX.message("SALE_ORDER_ADDITIONAL_INFO_COMMENT_SAVE"),
				'action': function() {

					var commentsTa = BX("COMMENTS");

					if(commentsTa)
						comments = commentsTa.value;
					else if(comments == BX.message("SALE_ORDER_ADDITIONAL_INFO_NO_COMMENT"))
						comments = "";

					BX.Sale.Admin.OrderAdditionalInfo.saveComment(comments, orderId, commentNode);
					this.parentWindow.Close();
				}
			},
			BX.CDialog.prototype.btnCancel
		]);

		BX.addCustomEvent(dialog, 'onWindowClose', function(dialog) {
			dialog.DIV.parentNode.removeChild(dialog.DIV);
		});

		dialog.Show();
	//	dialog.adjustSizeEx();
	},

	saveComment: function(comments, orderId, commentNode)
	{
		BX.Sale.Admin.OrderAjaxer.sendRequest({
			action : 'saveComments',
			comments: comments,
			orderId: orderId,
			callback: function(result)
			{
				if(result && typeof result.COMMENTS !== 'undefined' && commentNode)
					commentNode.innerHTML = result.COMMENTS;
			}
		});
	}
};
