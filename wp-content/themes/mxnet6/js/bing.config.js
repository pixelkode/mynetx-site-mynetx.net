var WLSearchBoxConfiguration = {
	global: {
		serverDNS: "www.bing.com",
		market: _e.mkt
	},
	appearance: {
		autoHideTopControl: false,
		width: 960,
		height: 600,
		theme: "Blue"
	},
	scopes: [{
		type: "web",
		caption: "mynetx - " + _e.WLE,
		searchParam: "site:" + _e.host
	},{
		type: "web",
		caption: "Windows Live",
		searchParam: "site:live.com"
	},{
		type: "web",
		caption: _e.EntireWeb,
		searchParam: ""
	}]
};