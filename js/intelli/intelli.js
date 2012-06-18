/**
 * Main class
 * @class
 */
intelli = function()
{
	return {
		plugins: new Object(),
		/**
		 * Language array
		 */
		lang: new Object(),
		/**
		 * Clipboard object
		 */
		clipboard: null,
		/**
		 * Configuration array
		 */
		css: new Array(),
		/**
		 *  Exist value in the array
		 *  @param {Array} arr array
		 *  @param {String} value
		 *  @return {Boolean}
		 */
		inArray: function(val, arr)
		{
			if(typeof arr == 'object' && arr)
			{
				for(var i = 0; i < arr.length; i++) 
				{
					if(arr[i] == val) 
					{
						return true;
					}
				}

				return false;
			}

			return false;
		},
		/**
		 * Remove one item in the array
		 * @param {Array} arr array
		 * @param {String} val value
		 * @return {Array}
		 */
		remove: function(arr, val)
		{
			if(typeof arr == 'object')
			{
				for(var i = 0; i < arr.length; i++) 
				{
					if(arr[i] == val)
					{
						arr.splice(i, 1);
					}
				}
			}

			return arr;
		},
		/**
		 *  Load configuration or language phrases
		 *  @param {Array} array of parametrs lang|conf
		 *  TODO: store variables in the session. Use sessvars lib.
		 */
		loader: function(params)
		{
			var out = '';
			var url = '';

			url += (typeof params.conf != 'undefined') ? 'conf=' + params.conf : '';
			url += (typeof params.lang != 'undefined') ? '&lang=' + params.lang : '';

			$.ajax({
				type: 'POST', 
				url: 'loader.php?load=vars', 
				data: url,
				async: false,
				success: function(p)
				{
					out = eval('(' + p + ')');
				}
			});
			
			if(typeof out.conf != 'undefined')
			{
				if(null == intelli.conf)
				{
					intelli.conf = out.conf;
				}
				else
				{
					var keys = params.conf.split(',');
					
					for(var i = 0; i <= keys.length; i++)
					{
						intelli.conf[keys[i]] = out.conf[keys[i]];
					}
				}
			}

			if(typeof out.lang != 'undefined')
			{
				if(null == intelli.lang)
				{
					intelli.lang = out.lang;
				}
				else
				{
					var keys = params.lang.split(',');
					
					for(var i = 0; i <= keys.length; i++)
					{
						intelli.lang[keys[i]] = out.lang[keys[i]];
					}
				}
			}
		},
		/**
		 *  Hidding or showing some element
		 *  @param {String} obj Can be passed with # symbol
		 *  @param {String} action  show|hide|auto
		 */
		display: function(obj, action)
		{
			var obj = (typeof obj == 'object') ? obj : (-1 != obj.indexOf('#')) ? $(obj) : $('#' + obj);
			
			action = action ? action : 'auto';

			if('auto' == action)
			{
				action = ('none' == obj.css('display')) ? 'show' : 'hide';
			}
			
			if('hide' == action)
			{
				if($.browser.msie)
				{
					obj.hide();
				}
				else
				{
					obj.slideUp('fast');
				}
			}

			if('show' == action)
			{
				if($.browser.msie)
				{
					obj.show();
				}
				else
				{
					obj.slideDown('fast');
				}
			}
		},
		/**
		 * Return random letter
		 * TODO: get several letters. get letter in upper case.
		 */
		getRandomLetter: function()
		{
			return String.fromCharCode(97 + Math.round(Math.random() * 25));

			/* For upper case */
			//return String.fromCharCode(65 + Math.round(Math.random() * 25));
		},
		/**
		 * Show error message
		 */
		error: function(error)
		{
			alert(error);
		},
		/**
		 * Create new cookie
		 * @param {String} name The name of cookie
		 * @param {String} value The value of cookie
		 * @param {Integer} days The expire time of cookie
		 */
		createCookie: function(name, value, days)
		{
			if (days)
			{
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				var expires = "; expires=" + date.toGMTString();
			}
			else 
			{
				var expires = "";
			}
			
			document.cookie = name + "=" + value + expires + "; path=/";
		},
		/**
		 * Return the value of cookie
		 * @param {String} name The name of cookie
		 * @return {String}
		 */
		readCookie: function(name)
		{
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i = 0; i < ca.length; i++)
			{
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		},
		/**
		 * Clear cookie value
		 * @param {String} name The name of cookie
		 */
		eraseCookie: function(name)
		{
			createCookie(name, "", -1);
		},
		cssCapture: function(attr, val)
		{
			this.css.push(attr + ':' + val);
		},
		cssClear: function()
		{
			this.css = [];
		},
		cssExtract: function()
		{
			return this.css.join('; ') + ';';
		},
		urlVal: function( name )
		{
			name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
			
			var regexS = "[\\?&]"+name+"=([^&#]*)";
			var regex = new RegExp( regexS );
			var results = regex.exec(window.location.href);
			
			if(results == null)
			{
				return null;
			}
			else
			{
				return results[1];
			}
		},
		notifBox: function(o)
		{
			var obj = $("#" + o.id);
			var html = '';

			if(!obj.length)
			{
				this.error("Can't find element with ID: " + o.id);

				return false;
			}

			html += '<ul>';
			for(var i = 0; i < o.msg.length; i++)
			{
				html += '<li>' + o.msg[i] + '</li>';
			}
			html += '</ul>';

			obj.css("padding", "0").css("margin", "0").attr("class", "");
			obj.addClass(o.type).html(html).show();

			$('html, body').animate({scrollTop: obj.offset().top}, 'slow');
		},
		initCopy2clipboard: function()
		{
			ZeroClipboard.setMoviePath(intelli.config.esyn_url + 'js/utils/zeroclipboard/ZeroClipboard.swf');

			var text = Ext.get('htaccess_code').dom.innerHTML;
			
			text = text.replace(/\r\n|\r|\n/g, "");
			text = text.replace(/<br>/gi, "\r\n");

			text = text.replace(/&lt;/gi, '<');
			text = text.replace(/&gt;/gi, '>');
			text = text.replace(/&amp;/gi, '&');

			$("a.copybutton").each(function()
			{
				var clipboard = new ZeroClipboard.Client();

				clipboard.glue(this);
				clipboard.setText(text);
			});

			return false;
		},
		is_int: function(input)
		{
			return !isNaN(input) && parseInt(input) == input;
		},
		is_email: function(email)
		{
			var result = email.search(/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z]{2,3})+$/);
			
			if(result > -1)
			{
				return true;
			}
			else
			{
				return false;
			}
		},
		ckeditor: function(name, o)
		{
			var opts = {
				baseHref: intelli.config.esyn_url,
				filebrowserImageUploadUrl: intelli.config.esyn_url + 'ck_upload.php?Type=Image'
			};

			if(o)
			{
				$.each(o, function(i, p)
				{
					opts[i] = p;
				});
			}

			CKEDITOR.replace(name, opts);
		},
		trim: function(str)
		{
			return str.replace(/^\s+|\s+$/g,"");
		},
		// http://kevin.vanzonneveld.net
		// +   original by: Mirek Slugen
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   bugfixed by: Nathan
		// +   bugfixed by: Arno
		// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +    bugfixed by: Brett Zamir (http://brett-zamir.me)
		// +      input by: Ratheous
		// +      input by: Mailfaker (http://www.weedem.fr/)
		// +      reimplemented by: Brett Zamir (http://brett-zamir.me)
		// +      input by: felix
		// +    bugfixed by: Brett Zamir (http://brett-zamir.me)
		// %        note 1: charset argument not supported
		// *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
		// *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
		// *     example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
		// *     returns 2: 'ab"c&#039;d'
		// *     example 3: htmlspecialchars("my "&entity;" is still here", null, null, false);
		// *     returns 3: 'my &quot;&entity;&quot; is still here'
		htmlspecialchars: function(string, quote_style, charset, double_encode)
		{
			var optTemp = 0,
			i = 0,
			noquotes = false;
			if (typeof quote_style === 'undefined' || quote_style === null) {
				quote_style = 2;
			}
			string = string.toString();
			if (double_encode !== false) { // Put this first to avoid double-encoding
				string = string.replace(/&/g, '&amp;');
			}
			string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

			var OPTS = {
				'ENT_NOQUOTES': 0,
				'ENT_HTML_QUOTE_SINGLE': 1,
				'ENT_HTML_QUOTE_DOUBLE': 2,
				'ENT_COMPAT': 2,
				'ENT_QUOTES': 3,
				'ENT_IGNORE': 4
			};
			if (quote_style === 0) {
				noquotes = true;
			}
			if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
				quote_style = [].concat(quote_style);
				for (i = 0; i < quote_style.length; i++) {
					// Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
					if (OPTS[quote_style[i]] === 0) {
						noquotes = true;
					} else if (OPTS[quote_style[i]]) {
						optTemp = optTemp | OPTS[quote_style[i]];
					}
				}
				quote_style = optTemp;
			}
			if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
				string = string.replace(/'/g, '&#039;');
			}
			if (!noquotes) {
				string = string.replace(/"/g, '&quot;');
			}

			return string;
		}
	};
}();

function _t(key, def)
{
	if(intelli.admin.lang[key]) return intelli.admin.lang[key];
	else return (def ? (def === true ? key : def) : '{'+key+'}');
}
function _f(key, def)
{
	if(intelli.lang[key]) return intelli.lang[key];
	else return (def ? (def === true ? key : def) : '{'+key+'}');
}
