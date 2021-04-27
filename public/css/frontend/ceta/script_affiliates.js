function hrv_register_init_affiliate(urlReferrer, callback) {
	var parseQueryString = function() {
		var str = url_parent.toLowerCase();
		var objURL = {};
		str.replace(
			new RegExp("([^?=&]+)(=([^&]*))?", "g"),
			function($0, $1, $2, $3) {
				objURL[$1] = $3;
			});
		return objURL;
	};
	var params = parseQueryString();
	var setCookieQueryString = function() {
		var x = 2;
		var CurrentDate = new Date();
		CurrentDate.setMonth(CurrentDate.getMonth() + x);
		/***** INDEX OF GOOGLE, HOCVIEN, FACEBOOK ORGANIC *****/
		if (urlReferrer.indexOf("google") >= 0 && $.cookie("referharavan") == undefined) {
			$.cookie("referharavan", "organic", {
				expires: CurrentDate,
				path: '/',
				domain: '.haravan.com'
			});
			$('input[name="HChan"]').val('organic');
		} 
		else if (urlReferrer.indexOf("facebook") >= 0 && $.cookie("referharavan") == undefined && params["hchan"] != undefined) {
			$.cookie("referharavan", "facebook", {
				expires: CurrentDate,
				path: '/',
				domain: '.haravan.com'
			});
			$('input[name="HChan"]').val('facebook');
		}
		else if (urlReferrer.indexOf("hocvien") >= 0 && $.cookie("referharavan") == undefined && params["hchan"] != undefined) {
			$.cookie("referharavan", "hocvien", {
				expires: CurrentDate,
				path: '/',
				domain: '.haravan.com'
			});
			$('input[name="HChan"]').val('hocvien');
		}
		/***** END INDEX OF GOOGLE, HOCVIEN, FACEBOOK ORGANIC  *****/
		/******* HChan *****/
		if (params["hchan"] != undefined && ($.cookie("referharavan") == undefined || $.cookie("referharavan") == "hocvien" || $.cookie("referharavan") == "organic" || $.cookie("referharavan") == "facebook")) {
			if (params["hchan"] == "hocvien") {
				$.cookie("referharavan", "hocvien", {
					expires: CurrentDate,
					path: '/',
					domain: '.haravan.com'
				});
				//$('.typeregis').val("Affiliate");
				$('input[name="HChan"]').val("hocvien");
			}
			else {
				$.cookie("referharavan", params["hchan"], {
					expires: CurrentDate,
					path: '/',
					domain: '.haravan.com'
				});
				//$('.typeregis').val("Affiliate");
				$('input[name="HChan"]').val(params["hchan"])
			}
		}
		else if ($.cookie("referharavan") != undefined && params["hchan"] == undefined) {
			//$('.typeregis').val("Affiliate");
			$('input[name="HChan"]').val($.cookie("referharavan"));
		} 
		else if ($.cookie("referharavan") != undefined && params["hchan"] != undefined) {
			//$('.typeregis').val("Affiliate");
			$('input[name="HChan"]').val(params["hchan"]);
			$.cookie("referharavan", params["hchan"], {
				expires: CurrentDate,
				path: '/',
				domain: '.haravan.com'
			});
		}
		if (params["hchan"] != undefined) {
			//$('.typeregis').val("HChan");
			$('input[name="HChan"]').val(params["hchan"])
		}
		/******* End HChan *****/
		/******* Ref *****/
		if (params["ref"] != undefined && ($.cookie("shop_ref") == undefined || $.cookie("shop_ref") == "hocvien" || $.cookie("shop_ref") == "organic" || $.cookie("shop_ref") == "facebook")) {
			if (params["ref"] == "hocvien") {
				$.cookie("shop_ref", "hocvien", {
					expires: CurrentDate,
					path: '/',
					domain: '.haravan.com'
				});
				$('input[name="Ref"]').val("hocvien");
			} 
			else {
				$.cookie("shop_ref", params["ref"], {
					expires: CurrentDate,
					path: '/',
					domain: '.haravan.com'
				});
				$('input[name="Ref"]').val(params["ref"]);
			}
		} 
		else if ($.cookie("shop_ref") != undefined && params["ref"] == undefined) {
			$('input[name="Ref"]').val($.cookie("shop_ref"));
		}
		else if ($.cookie("shop_ref") != undefined && params["ref"] != undefined) {
			$('input[name="Ref"]').val(params["ref"]);
			$.cookie("shop_ref", params["ref"], {
				expires: CurrentDate,
				path: '/',
				domain: '.haravan.com'
			});
		}
		/******** End Ref *********/		
		/******** Referrer ********/
		if (urlReferrer != '') {
			$.cookie("refer_affiliate", urlReferrer, {
				expires: CurrentDate,
				path: '/',
				domain: '.haravan.com'
			});
			$('input[name="Referrer"]').val(urlReferrer);
		}
		/******** End Referrer ****/
		if (params["is5giaystore"] != undefined) {
			$('#formregistry h2').text("Thông tin 5giay store của bạn");
			$('input[name="is5giaystore"]').val('true');
		}
		else if (params["iswttstore"] != undefined) {
			$('#formregistry h2').text("Thông tin Webtretho store của bạn");
			$('input[name="iswttstore"]').val('true');
		}
		if (sessionStorage.getItem("is5giaystore") != undefined) {
			$('#formregistry h2').text("Thông tin 5giay store của bạn");
			$('input[name="is5giaystore"]').val('true');
		}
		else if (sessionStorage.getItem("isWTTstore") != undefined) {
			$('#formregistry h2').text("Thông tin Webtretho store của bạn");
			$('input[name="iswttstore"]').val('true');
		}
		if (params["fbref"] != undefined) {
			$('#formregistry h2').text("Thông tin Facebook Store của bạn");
			$('input[name="FbRef"]').val(params["fbref"]);
			sessionStorage.setItem("fbref", params["fbref"]);
		}
		else if (sessionStorage.getItem("fbref") != undefined) {
			$('#formregistry h2').text("Thông tin Facebook Store của bạn");
			$('input[name="FbRef"]').val(sessionStorage.getItem("fbref"));
		}
		var myh = location.hash.replace('#', '');
		if (myh && myh != "") {
			$('input[name=HChan]').val(myh);
		}
		/******** ReferringSite - LandingSite - LandingSiteRef *********/
		if ($.cookie("_landing_page") != undefined && $.cookie("_landing_page") != ''){
			$('input[name="LandingSite"]').val($.cookie("_landing_page"));
		}
		if ($.cookie("_orig_referer") != undefined && $.cookie("_orig_referer") != ''){
			$('input[name="ReferringSite"]').val($.cookie("_orig_referer"));
		}
		if ($.cookie("shop_ref") != undefined && $.cookie("shop_ref") != ''){
			$('input[name="LandingSiteRef"]').val($.cookie("shop_ref"));
		}
		if ($.cookie("_haravan_utm_p") != undefined && $.cookie("_haravan_utm_p") != ''){
			$('input[name="HaravanUTM"]').val($.cookie("_haravan_utm_p"));
		}		
		/******** End ReferringSite - LandingSite - LandingSiteRef *********/
	};
	setCookieQueryString();
};