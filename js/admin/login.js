$(function()
{
	$("div.tip .inner").corner("bevel 2px").parent().corner("bevel 3px");

	if($("div.tip").length > 0)
	{
		$("div.text-field").each(function()
		{
			$(this).addClass("error");
		});
	}

	$('input[type="text"], input[type="password"]').focus(function()
	{
		$("div.tip").fadeOut("slow");
		$("div.text-field").each(function()
		{
			$(this).removeClass("error");
		});
	});

	var forgot_win = null;
	var forgot_form = null;

	$("#forgot_password").click(function()
	{
		if(!forgot_form)
		{
			forgot_form = new Ext.FormPanel(
			{
				labelWidth: 35, // label settings here cascade unless overridden
				border: false,
				frame:true,
				bodyStyle:'padding:5px 5px 0',
				width: 350,
				defaults: {width: 230},
				defaultType: 'textfield',
				items: [
				{
					fieldLabel: intelli.admin.lang.en.email,
					name: 'email',
					emptyText: intelli.admin.lang.type_username_email,
					allowBlank: false
				}],
				buttons: [
				{
					text: intelli.admin.lang.en.email,
					handler: function()
					{
						if(forgot_form.form.isValid())
						{
							forgot_form.form.submit(
							{
								url: 'login.php',
								method: 'GET',
								params:
								{
									action: 'restore'
								},
								success: function(form, action)
								{
									var response = action.result;
									var type = response.error ? 'error' : 'notif';
									var msg = action.result.msg;

									forgot_form.form.reset();
									forgot_win.hide();

									Ext.Msg.show(
									{
										title: intelli.admin.lang.en.restore_password,
										msg: msg,
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.INFO
									});
								}
							});
						}
					}
				},{
					text: intelli.admin.lang.en.cancel,
					handler: function()
					{
						forgot_form.form.reset();
						forgot_win.hide();
					}
				}]
			});
		}

		if(!forgot_win)
		{
			forgot_win = new Ext.Window(
			{
				title: intelli.admin.lang.en.restore_password,
				width : 383,
				height : 135,
				modal: true,
				closeAction : 'hide',
				bodyStyle: 'padding: 10px;',
				items: forgot_form
			});
		}

		forgot_win.show();

		return false;
	});
});

function formSubmit()
{
	var password = $("#dummy_password").val();
	var salt = $("#md5Salt").val();

	var md5_password = hex_md5(password);
	var salted = hex_md5(md5_password+salt);

	var str = '';

	for(i = 0; i < $("#dummy_password").val().length; i++)
	{
		str += "*";
	}

	$("#dummy_password").val(str);
	$("#password").val(salted);

	$("#action").val('login');
}
