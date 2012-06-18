<?php
//##copyright##

if(!class_exists('iaDebug')){
class iaDebug
{
	var $style = 0;
	var $endtimer = '';
	
	function iaDebug()
	{
		$debug = 'view';
		if(isset($_COOKIE['debug']))
		{
			if($_COOKIE['debug'] == 'close') 
			{
				$debug = 'close'; 
			}
		}
		
		$this->endtimer = end_time_render();
		$this->debug_css();
		echo '<div class="_debug_" style="display:none;"><div id="debug" class="'.$debug.'">';
		$this->box('info');
		$this->box('timer');
		$this->box('sql');
		//$this->box('hook');
		if(isset($_SESSION['error']))
		{
			$this->box('error');
		}
		if(isset($_SESSION['debug']))
		{
			$this->box('debug', 'view');
		}
		echo '<div id="close_debug" onclick="document.getElementById(\'debug\').className=\'close\';dSetCookie(\'debug\', \'close\', 30*24*60*60, \'/\')">[X]</div>
<div id="open_debug" onclick="document.getElementById(\'debug\').className=\'view\';dSetCookie(\'debug\', \'view\', 30*24*60*60, \'/\')">[O]</div>
</div></div>';
		$this->debug_javascript();
	}
	
	function debug_css()
	{
		$url = ESYN_URL;
		$text = <<<HTML
<link rel="stylesheet" href="{$url}js/utils/highlightjs/default.min.css">
<style type="text/css">
._debug_{display:block !important;position:fixed;bottom:3px;right:5px;background:#FFFFFF;z-index:1000;}
#open_debug,#close_debug{width:15px;height:15px;font-size:8px;position:fixed;bottom:3px;right:3px;background:white;-moz-border-radius:10px;border-radius:10px;border:1px solid black;cursor:pointer;}
#close_debug{background:green;color:green;}
#open_debug{background:red;color:red;}
#debug{display:block !important;}
#debug.close div{display:none;}
#debug.view #close_debug,#debug.close #open_debug,._debug_ .view{display:block;}
#debug.close #close_debug,#debug.view #open_debug,._debug_ .close{display:none;}
.debug_btn,.debug_div{background: #70aad8;color: black;border:2px solid #3D7098;-moz-border-radius:5px;border-radius:5px;padding:4px;}
.debug_btn{width:100px;cursor:pointer;margin:1px 3px 1px -115px;font-family:Verdana;font-size:10px;z-index:999;}
.debug_div{width:80%;height:80%;z-index:1000;position:fixed;top:5%;left:10%;right:10%;}
.debug_text{width:100%;height:90%;overflow:auto;z-index:1;}
.debug_close{padding:2px 5px;}
.debug_div input[type=button]{width:100%;background:#FFFFFF;color:#3D7098;-moz-border-radius:4px;border-radius:4px;border:2px outset #70aad8;padding:3px 10px;font-weight:bold;}
.debug_div h2{margin:0px;cursor:pointer;font-size:14px;font-family:Verdana}
.e_notice{color: white}
.e_warning{color: orange}
.d_green{color: green;font-style: italic;font-weight:bold;}
.d_red{color: orange;font-style: italic;font-weight:bold;}
.array_title{background: white;color: black;font-weight: bold;font-style:italic;padding:0px 10px;}
.str_title{color: white;font-weight:bold;}
pre.close{margin:0px;display:none;font-size:12px;font-family:Verdana}
#dtext4{display:block;}
#dbtn_error{border-color:red;}
._debug_ .hr{margin-top:10px;border-top:1px dashed black;line-height:1px;font-size:1px;overflow:hidden;}
table.debug{width:100%;border-width:1px;border-spacing:0;border-style:dotted;}
table.debug td{border-width:1px;border-spacing:0;border-style:dotted;padding:5px;}
</style>
HTML;
		echo $text;
	}

	function debug_javascript()
	{
		$url = ESYN_URL;
		$text = <<<HTML
<script src="{$url}js/utils/highlightjs/highlight.min.js"></script>
<script type="text/javascript">
function dSetCookie (name, value, expires, path, domain, secure) {
	var exdate = new Date();
	exdate.setDate(exdate.getDate()+expires);
	document.cookie = name + "=" + escape(value) +
	((expires) ? "; expires=" + exdate.toGMTString() : "") +
	((path) ? "; path=" + path : "") +
	((domain) ? "; domain=" + domain : "") +
	((secure) ? "; secure" : "");
}

var debug_zindex=10;

function dBox(name, type)
{
	var div = document.getElementById(name);
	
	if (hasClass(div, 'view'))
	{
		type = 'close';
	}
	
	if (hasClass(div, 'close'))
	{
		debug_zindex++;
		
		type = 'view';
	}
	
	div.className='debug_div '+type;
	div.style.zIndex=debug_zindex;
	dSetCookie(name, type, 30*24*60*60, '/');
}
function hasClass (obj, className)
{
	var re = new RegExp(className);
	
	return re.test(obj.className);
}

hljs.initHighlightingOnLoad();
</script>
HTML;
		echo $text;
	}
	
	function box($type = 'info', $debug = 'none')
	{
		if($debug == 'none' || !in_array($debug, array('view', 'close')))
		{
			$debug = 'close';
			if(isset($_COOKIE['dtext_'.$type])) 
			{
				if($_COOKIE['dtext_'.$type] == 'view') 
				{
					$debug = 'view'; 
				}
			}
		}
		
		echo '<div class="debug_btn" id="dbtn_'.$type.'" onclick="dBox(\'dtext_'.$type.'\', \'view\');">'.ucfirst($type).'</div>
			<div class="debug_div ' . $debug . '" id="dtext_'.$type.'">
				<input type="button" onclick="dBox(\'dtext_'.$type.'\', \'close\');" value="Close div"><br>
				<div class="debug_text">';
		$func = 'debug_'.$type;
		$this->$func();	
		echo '</div></div>';
	}
	
	function debug_info()
	{
		v($_SERVER, '$_SERVER');
		v($_SESSION, '$_SESSION');
		v($_COOKIE, '$_COOKIE');
		v($_POST, '$_POST');
		v($_FILES, '$_FILES');
		v($_GET, '$_GET');
	}

	function debug_debug()
	{
		foreach($_SESSION['debug'] as $key => $val)
		{
			v($val, (!is_int($key) ? $key : ''));
		}
		unset($_SESSION['debug']);
	}
	
	function debug_error()
	{
		foreach($_SESSION['error'] as $key => $val)
		{
			v($val, (!is_int($key) ? $key : ''));
		}
		unset($_SESSION['error']);
	}

	function debug_sql()
	{
		echo '<table class="debug">';
		echo '<tr>';
		echo '<td>SQL Query</td>';
		echo '<td width="5%">TIME</td>';
		echo '</tr>';
		
		foreach ($GLOBALS['debug_sql'] as $key => $val)
		{
			$c = 'green';
			
			echo '<tr>';
			echo '<td>';
			echo '<pre><code class="sql">';
			echo $this->format_sql($val['sql']);
			echo '</pre></code>';
			echo '</td>';

			if ($val['time'] > ESYN_DEBUG_MAX_SQL_TIME)
			{
				$c = 'red';
			}
			
			echo '<td>';
			echo '<span style="color: '. $c . ';">' . $val['time'] . '</span>';
			echo '</td>';
			echo '</tr>';
		}
		
		echo '</table>';
		
		unset($GLOBALS['debug_sql']);
	}

	function debug_hook()
	{
		$type_map = array(
			'php'		=> 'php',
			'smarty'	=> 'html',
			'html'		=> 'html',
			'plain'		=> 'html'
		);

		echo '<table class="debug">';
		echo '<tr>';
		echo '<td width="20%">Name</td>';
		echo '<td>Plugin</td>';
		echo '<td width="5%">TIME</td>';
		echo '</tr>';

		foreach ($_SESSION['hook_debug'] as $key => $val)
		{
			$c = 'green';
			$t = $type_map[$val['type']];
			
			echo '<tr>';
			echo '<td>';
			echo $val['name'];
			echo '</td>';
			echo '<td>';
			echo $val['plugin'];
			echo '</td>';

			if ($val['time'] > ESYN_DEBUG_MAX_HOOK_TIME)
			{
				$c = 'red';
			}
			
			echo '<td>';
			echo '<span style="color: '. $c . ';">' . $val['time'] . '</span>';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td colspan="3">';
			echo '<pre><code class="' . $t . '">' . $val['code'] . '</code></pre>';
			echo '</td>';
			echo '</tr>';
		}

		echo '</table>';

		unset($_SESSION['hook_debug']);
	}
	
	function debug_timer()
	{
		echo $this->endtimer;
	}

	function format_sql($sql)
	{
		$nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
		
		$search = array(
			'FROM',
			'SELECT',
			' AS ',
			' LIKE ',
			' ON ',
			' AND ',
			' OR ',
			'WHERE',
			'INNER JOIN',
			'RIGHT JOIN',
			'LEFT JOIN',
			'LEFT OUTER',
			' JOIN',
			'ORDER BY',
			'GROUP BY',
			'LIMIT',
			'IF',
			'IN('
		);
		
		$replace = array(
			"<br><b>FROM</b>",
			"<b>SELECT</b>",
			"<b>AS</b> ",
			"<b>LIKE</b> ",
			"<br>{$nbsp}{$nbsp}<b>ON</b> ",
			"<b>AND</b> ",
			"<b>OR</b> ",
			"<br><b>WHERE</b>",
			"<br>{$nbsp}{$nbsp}<b>INNER</b> <b>JOIN</b>",
			"<br>{$nbsp}{$nbsp}<b>RIGHT</b> <b>JOIN</b>",
			"<br>{$nbsp}<b>LEFT</b> <b>JOIN</b>",
			"<br>{$nbsp}{$nbsp}<b>LEFT</b> <b>OUTER</b>",
			"<br>{$nbsp}{$nbsp}<b>JOIN</b>",
			"<br><b>ORDER BY</b>",
			"<br><b>GROUP BY</b>",
			"<br><b>LIMIT</b>",
			"<br>{$nbsp}<b>IF</b>",
			"<br>{$nbsp}<b>AND</b>",
			"<br>{$nbsp}<b>OR</b>",
			"<br>{$nbsp}<b>IN</b>("
		);
	
		$sql = preg_replace('#--(.*?)\n#i', '<span style="color:gray;">--\1</span>', $sql);
		$sql = str_replace($search, $replace, $sql);

		return $sql;
	}
}}

if(!stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') && !stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') && !stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0'))
{
	$iaDebug = new iaDebug();
}
