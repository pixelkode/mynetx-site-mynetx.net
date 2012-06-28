var current_url = location.pathname,
	gas_content_iframe = "";
$(function () {
	$("#WLSearchBoxInput").focus(function () {
		$(this).val() == _e.sPrefill && $(this).val("");
		$(this).removeClass("bing")
	}).blur(function () {
		$(this).val() == "" && $(this).val(_e.sPrefill);
		$(this).addClass("bing")
	});
	$(".excerpt-clickable").click(function(e) {
		e.preventDefault();
		var a = $(this).find("h2 a[rel=bookmark]");
		a.length || (a = $(this).parent().find("h2 a[rel=bookmark]"));
		location.href = a.attr("href");
	}).css("cursor", "pointer");
	$(".section, #languages").live("mouseenter", function () {
		$(".section-box", $(this)).show()
	}).live("mouseleave", function () {
		$(".section-box", $(this)).hide()
	});
	setTimeout(function () {
		$("#WLSearchBoxInput").blur()
	}, 3E3);
	$(".pop-small").live("click", function () {
		window.open(this.href, "pop-small", "height=500,width=630");
		return false
	});
	$(".not-updated a").click(function () {
		location.href = "mailto:me" + String.fromCharCode(64) + "mynetx.net?subject=Author%2FTranslator%20Application";
		return false
	}).css("cursor", "pointer");
	$("<a></a>").addClass("close").attr("href", "#").html("X").click(function () {
		$.post("/", {
			hide_language_bar: true
		});
		$(this).parent().slideUp();
		return false
	}).appendTo(".language-bar");
	(function () {
		function a() {
			if (++b >= $(".sy").length) {
				b = 0
			}
			_e.ltr ? $(".sidebar-imgteaser").animate({
				marginLeft: -b * $(".sidebar-imgteaser").parent().width()
			}, 500) : $(".sidebar-imgteaser").animate({
				marginRight: -b * $(".sidebar-imgteaser").parent().width()
			}, 500)
		}
		var b = 0;
		$(".sidebar-imgteaser").hover(function () {
			clearInterval(c)
		}, function () {
			c = setInterval(a, 1E4)
		}).width($(".sy").length * $(".sidebar-imgteaser").width());
		var c = setInterval(a, 1E4)
	})();
	$("a.bnr-wiki").click(function () {
		_gaq.push(["_trackPageview", "/bnr/wiki"])
	});
});