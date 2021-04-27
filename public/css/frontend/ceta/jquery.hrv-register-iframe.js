function funcSetEvent() {
	var effectControlFieldClass = '.field input';
	$('body')
	.on('focus', effectControlFieldClass, function () {
		funcFieldFocus($(this), true);
	})
	.on('blur', effectControlFieldClass, function () {
		funcFieldFocus($(this), false);
		funcFieldHasValue($(this), true);
	})
	.on('keyup input paste', effectControlFieldClass, function () {
		funcFieldHasValue($(this), false);
	})
};
function funcFieldFocus(fieldInputElement, isFocus) {
	if (fieldInputElement == undefined)
		return;
	var fieldElement = $(fieldInputElement).closest('.field');
	if (fieldElement == undefined)
		return;
	if (isFocus)
		$(fieldElement).addClass('field-active');
	else
		$(fieldElement).removeClass('field-active');
};
function funcFieldHasValue(fieldInputElement, isCheckRemove) {
	if (fieldInputElement == undefined)
		return;
	var fieldElement = $(fieldInputElement).closest('.field');
	if (fieldElement == undefined)
		return;
	if ($(fieldElement).find('.field-input-wrapper-select').length > 0) {
		var value = $(fieldInputElement).find(':selected').val();
		if (value == 'null')
			value = undefined;
	} else {
		var value = $(fieldInputElement).val();
	}
	if (!isCheckRemove) {
		if (value != $(fieldInputElement).attr('value'))
			$(fieldElement).removeClass('field-error');
	}
	var fieldInputBtnWrapperElement = $(fieldInputElement).closest('.field-input-btn-wrapper');
	if (value && value.trim() != '') {
		$(fieldElement).addClass('field-show-floating-label');
		$(fieldInputBtnWrapperElement).find('button:submit').removeClass('btn-disabled');
	} else if (isCheckRemove) {
		$(fieldElement).removeClass('field-show-floating-label');
		$(fieldInputBtnWrapperElement).find('button:submit').addClass('btn-disabled');
	} else {
		$(fieldInputBtnWrapperElement).find('button:submit').addClass('btn-disabled');
	}
};
function funcInit() {
	funcSetEvent();
};
function remove_unicode(str) {  
	str= str.toLowerCase(); 
	str= str.replace(/ /g, '-');
	str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");  
	str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");  
	str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");  
	str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");  
	str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");  
	str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");  
	str= str.replace(/đ/g,"d");  
	str= str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g,""); 
	str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1- 
	str= str.replace(/^\-+|\-+$/g,"");  
	return str;  
};
function init_iframe() {
	var source_init_iframe = {
		intIframe: 'true'
	};
	parent.postMessage('{ "functionName": "intIframe", "source": ' + JSON.stringify(source_init_iframe) + ' }', '*');
};
$(document).ready(function () {
	funcInit();
	// Event shopname
	$(document).on('keyup', '#hrv-shopname-register', function(){
		$this = $(this);
		setTimeout(function(){
			if($this.val() != "" && $this.val().length > 3){
				var str = remove_unicode($this.val());
				$('#formregistry .guide-shopname').find('strong').html(str + '.myharavan.com');
				$('#formregistry .guide-shopname').show();
			}
			else{
				$('#formregistry .guide-shopname').hide();
			}
		},200);
	});
	// Event submit form register
	$(document).on('submit', '#formregistry', function(e){
		e.preventDefault();
		var flag = true;
		if ($(".checkbox-agree > input[name='IsAcceptRule']").is(':checked') == false) {
			$(this).find('.list-error-register ul').html('').append("<li>Bạn chưa đồng ý với điều khoản và chính sách của Haravan</li>");
			$(this).find('.list-error-register').removeClass('hidden-register');
			return false;
		} else {
			$(this).find('.list-error-register').addClass('hidden-register');
		}
		if ($('#formregistry').valid()) {
			var url = "//myharavan.com/admin/check_availability.json?shop_name=" + $('#hrv-shopname-register').val() + "&email=" + $('#hrv-email-register').val();
			$.ajax({
				url: url,
				type: 'GET',
				async: false,
				success: function (res) {
					if (res.status == 'unavailable') {
						$('#formregistry').find('.list-error-register ul').html('').append("<li>Địa chỉ website không hợp lệ hoặc đã được sử dụng.</li>");
						$('#formregistry').find('.list-error-register').removeClass('hidden-register');
						flag = false;
					} else {
						$('#formregistry').find('.list-error-register').addClass('hidden-register');
					}
				}
			});
			if (flag) {
				$(".btncreateshop").addClass("btn-loading");
				var source = {
					submitUrl: $('#formregistry').attr('action'),
					inputEmail: $('#hrv-email-register').val(),
					inputPhone: $('#hrv-phone-register').val(),
					inputPassword: $('#hrv-password-register').val(),
					inputShopname: $('#hrv-shopname-register').val(),
					inputRefType: $('#formregistry input[name="Type"]').val(),
					inputRef: $('#formregistry input[name="Ref"]').val(),
					inputRefHChan: $('#formregistry input[name="HChan"]').val(),
					inputReferrer: $('#formregistry input[name="Referrer"]').val(),
					inputRefFbRef: $('#formregistry input[name="FbRef"]').val(),
					inputRefAppApiKey: $('#formregistry input[name="AppApiKey"]').val(),
					inputRefPartnerPaymentCode: $('#formregistry input[name="PartnerPaymentCode"]').val(),					
					inputRefReferringSite: $('#formregistry input[name="ReferringSite"]').val(),
					inputRefLandingSite: $('#formregistry input[name="LandingSite"]').val(),
					inputRefLandingSiteRef: $('#formregistry input[name="LandingSiteRef"]').val(),
					inputRefHaravanUTM: $('#formregistry input[name="HaravanUTM"]').val(),					
					inputRefis5giaystore: $('#formregistry input[name="is5giaystore"]').val(),
					inputRefiswttstore: $('#formregistry input[name="iswttstore"]').val(),
					inputRefisRegisterFacebook: $('#formregistry input[name="isRegisterFacebook"]').val()
				};
				parent.postMessage('{ "functionName": "onSubmit", "source": ' + JSON.stringify(source) + ' }', '*');
			}
		}
		return flag;
	});
	// get parent url
	init_iframe();
});
// Event register facebook + Ref
var url_parent = '';
$(document).on('click', '#fb-register', function(e){
	e.preventDefault();
	var parseQueryString = function () {
		var str = url_parent.toLowerCase();
		var objURL = {};
		str.replace(new RegExp("([^?=&]+)(=([^&]*))?", "g"), function ($0, $1, $2, $3) {
			objURL[$1] = $3;
		});
		return objURL;
	};
	var params = parseQueryString();
	var url = '//myharavan.com/admin/auth/register?isfb=true';
	if (params['ref'] != undefined) {
		url += '&ref=' + params['ref'];
	}
	if (params['ref'] == undefined && $.cookie("shop_ref") != undefined){
		url += '&ref=' + $.cookie("shop_ref");
	}
	if (params['hchan'] != undefined) {
		url += '&hchan=' + params['hchan'];
	}
	if (params['hchan'] == undefined && $.cookie("referharavan") != undefined){
		url += '&hchan=' + $.cookie("referharavan");
	}
	if (params['friend'] != undefined) {
		url += '&friend=' + params['friend'];
	}
	if (params['fbref'] != undefined) {
		url += '&fbref=' + params['fbref'];
	}
	if (params['href'] != undefined) {
		url += '&href=' + params['href'];
	}
	if ($.cookie("_landing_page") != undefined && $.cookie("_landing_page") != ''){
		url += '&referring_site=' + $.cookie("_landing_page");
	}
	if ($.cookie("_orig_referer") != undefined && $.cookie("_orig_referer") != ''){
		url += '&landing_site=' + $.cookie("_orig_referer");
	}
	if ($.cookie("shop_ref") != undefined && $.cookie("shop_ref") != ''){
		url += '&landing_site_ref=' + $.cookie("shop_ref");
	}
	if ($.cookie("_haravan_utm_p") != undefined && $.cookie("_haravan_utm_p") != ''){
		url += '&landing_site_ref=' + $.cookie("_haravan_utm_p");
	}
	var source = {
		fbUrl: url
	};
	parent.postMessage('{ "functionName": "onChangeFb", "source": ' + JSON.stringify(source) + ' }', '*');
	
	return false;
});

// Event get value email inside Iframe
$(document).on('ready', function () {
	window.addEventListener("message", function (data) {
		if (data && data.data) {
			var objData = data.data;
			if (typeof (objData) == "string") {
				try {
					objData = JSON.parse(objData);
				} 
				catch(ex) {
					return;
				}
			}
			switch (objData.functionName) {
				case "putEmailInIframe":
					$('#formregistry').find("input[type='email']").val(objData.source.emailRegister);
					$('#formregistry').find("input[type='email']").parents('.field').addClass('field-show-floating-label');
					$('#formregistry').find("input[type='email']").focus();
					break;
				case "urlParent":
					url_parent = objData.source.urlParent;
					var urlReferrer = objData.source.urlReferrer;
					hrv_register_init_affiliate(urlReferrer, function() {
						
					});
					break;
				default: break;
			}
		}
	});
});
