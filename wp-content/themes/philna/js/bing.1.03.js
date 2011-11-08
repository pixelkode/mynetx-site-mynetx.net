WLBootstrap = function () {
	var scriptFileList = new Array('http://www.bing.com/DynamicScript.js', 'http://www.bing.com/JsonRequest.js', 'http://www.bing.com/WLUIPanel.js', 'http://www.bing.com/SearchBox/WLSearchBoxv11.js', 'http://www.bing.com/SearchBox/searchboxresources.js?market=en-us&charset=utf-8');
	var scriptCmdList = new Array([], [], [], [], []);
	var totalNumber = scriptFileList.length;
	var loadedNumber = 0;
	var scriptObjectList = new Array(totalNumber);
	var scriptStateList = new Array(totalNumber);
	var currentCharset = GetCurrentPageCharset();

	function GetCurrentPageCharset() {
		if ((document.charset != null) && (typeof(document.charset) != "undefined")) {
			return document.charset;
		}
		if ((document.defaultCharset != null) && (typeof(document.defaultCharset) != "undefined")) {
			return document.defaultCharset;
		}
		if ((document.characterset != null) && (typeof(document.characterset) != "undefined")) {
			return document.characterset;
		}
		if ((document.inputEncoding != null) && (typeof(document.inputEncoding) != "undefined")) {
			return document.inputEncoding;
		}
		if ((document.actualEncoding != null) && (typeof(document.actualEncoding) != "undefined")) {
			return document.actualEncoding;
		}
		return "utf-8";
	};

	function AppendCharset(strUrl) {
		if (strUrl.indexOf("?", strUrl.lastIndexOf("/")) >= 0) {
			return strUrl + "&charset=" + currentCharset;
		} else {
			return strUrl + "?charset=" + currentCharset;
		}
	};

	function GetScriptUrl(index) {
		var strUrl = scriptFileList[index];
		var len = scriptCmdList[index].length;
		for (var i = 0; i < len; ++i) {
			eval("strUrl = " + scriptCmdList[index][i] + "(strUrl);");
		}
		return strUrl;
	};
	for (var i = 0; i < totalNumber; i++) {
		scriptStateList[i] = new Object();
		scriptStateList[i].fileName = GetScriptUrl(i);
		scriptStateList[i].loadState = 'unload';
	}
	for (var i = 0; i < totalNumber; i++) {
		scriptObjectList[i] = document.createElement('script');
		scriptObjectList[i].type = "text/javascript";
		scriptObjectList[i].src = scriptStateList[i].fileName;
		scriptObjectList[i].charset = "utf-8";
		scriptObjectList[i].onreadystatechange = new Function("ScriptObjectReadyCallback(" + i + ")");
		scriptObjectList[i].onload = new Function("ScriptObjectLoadCallback(" + i + ")");
		scriptObjectList[i].onerror = new Function("ScriptObjectErrorCallback(" + i + ")");
		document.getElementsByTagName('head')[0].appendChild(scriptObjectList[i]);
	}
	ScriptObjectReadyCallback = function (index) {
		if (scriptObjectList[index].readyState == 'loaded' || scriptObjectList[index].readyState == 'complete') {
			scriptStateList[index].loadState = 'loaded';
			++loadedNumber;
			if (loadedNumber == totalNumber) {
				WLSearchBoxScriptReady(scriptStateList);
			}
		}
	};
	ScriptObjectLoadCallback = function (index) {
		scriptStateList[index].loadState = 'loaded';
		++loadedNumber;
		if (loadedNumber == totalNumber) {
			WLSearchBoxScriptReady(scriptStateList);
		}
	};
	ScriptObjectErrorCallback = function (index) {
		scriptStateList[index].loadState = 'error';
		++loadedNumber;
		if (loadedNumber == totalNumber) {
			WLSearchBoxScriptReady(scriptStateList);
		}
	};
	try {
		window.detachEvent('onload', WLSearchLoadBootstrap);
	} catch(e) {}
};

function WLSearchLoadBootstrap() {
	g_objWLBootstrap = new WLBootstrap();
}
try {
	window.attachEvent('onload', WLSearchLoadBootstrap);
} catch(e) {
	window.addEventListener('load', WLSearchLoadBootstrap, false);
}