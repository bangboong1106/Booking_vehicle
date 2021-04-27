function initUrlParent(){
	var source_url = {
		urlParent: window.location.href,
		urlReferrer: document.referrer
	};
	var frame_post_href = document.getElementById('hrv-registry-frame');
	frame_post_href.contentWindow.postMessage('{ "functionName": "urlParent", "source": ' + JSON.stringify(source_url) + ' }', '*');
}
function decodeUrlCookie(url){
	var url1 = decodeURIComponent(url);
	var url2 = decodeURIComponent(url1);
	return url2;
}
// Init iframe
$(document).ready(function () {
	var url = "//haravan.com/services/store_create/";
	$('#hrv-registry').append('<iframe id="hrv-registry-frame" src=""></iframe>');
	var frame = document.getElementById('hrv-registry-frame');
	frame.src = url;
	initUrlParent();
});
// Event close modal
$(document).on('click','.hrv-closemodal',function(){
	$('#modal-register').removeClass('hrv-modal-active');
	$('body').removeClass('hrv-modal-open');
});
// Event ESC close modal
$(document).keydown(function(e){
	if(e.keyCode == 27){
		$('#modal-register').removeClass('hrv-modal-active');
		$('body').removeClass('hrv-modal-open');
	}
});
// Event register post value email and show modal
$(document).on('click','.hrv-btn-register', function(){
	if ( $('.hrv-email-input').length > 0 ) {
		initUrlParent();
		var email = '';
		$.each($('.hrv-email-input'),function(i,v){
			if ( v.value != '' ) {
				email = v.value;
			}
		});
		if ( email != '' ) {
			var source = {
				emailRegister: email
			};
			var frame = document.getElementById('hrv-registry-frame');
			frame.contentWindow.postMessage('{ "functionName": "putEmailInIframe", "source": ' + JSON.stringify(source) + ' }', '*');
		}
	}
	$('body').addClass('hrv-modal-open');
	$('#modal-register').addClass('hrv-modal-active');
});
// Event get value in iframe and submit form register
$(document).on("ready", function () {
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
				case "onSubmit":
					$('#hrv-registry').append($("<form id='hrv-form-register' style='display:none;' method='post' action='' />").attr("action",objData.source.submitUrl));
					var object = $('#hrv-form-register');
					object.append($("<input name='email' type='email' value=''/>").attr("value",objData.source.inputEmail));
					object.append($("<input name='phone' type='tel' value=''/>").attr("value",objData.source.inputPhone));
					object.append($("<input name='Password' type='password' value=''/>").attr("value",objData.source.inputPassword));
					object.append($("<input name='ShopName' type='text' value=''/>").attr("value",objData.source.inputShopname));					
					object.append("<input name='IsAcceptRule' type='checkbox' checked='checked'/>");
					object.append($("<input name='Type' type='hidden' value='' />").attr("value",objData.source.inputRefType));
					object.append($("<input name='Ref' type='hidden' value='' />").attr("value",objData.source.inputRef));
					object.append($("<input name='HChan' type='hidden' value='' />").attr("value",objData.source.inputRefHChan));
					object.append($("<input name='Referrer' type='hidden' value='' />").attr("value",objData.source.inputReferrer));
					object.append($("<input name='FbRef' type='hidden' value='' />").attr("value",objData.source.inputRefFbRef));
					object.append($("<input name='AppApiKey' type='hidden' value='' />").attr("value",objData.source.inputRefAppApiKey));
					object.append($("<input name='PartnerPaymentCode' type='hidden' value='' />").attr("value",objData.source.inputRefPartnerPaymentCode));					
					object.append($("<input name='ReferringSite' type='hidden' value='' />").attr("value",decodeUrlCookie(objData.source.inputRefReferringSite)));
					object.append($("<input name='LandingSite' type='hidden' value='' />']").attr("value",decodeUrlCookie(objData.source.inputRefLandingSite)));
					object.append($("<input name='LandingSiteRef' type='hidden' value='' />").attr("value",objData.source.inputRefLandingSiteRef));
					object.append($("<input name='HaravanUTM' type='hidden' value='' />").attr("value",objData.source.inputRefHaravanUTM));
					object.append($("<input name='is5giaystore' type='hidden' value='' />").attr("value",objData.source.inputRefis5giaystore));
					object.append($("<input name='iswttstore' type='hidden' value='' />").attr("value",objData.source.inputRefiswttstore));
					object.append($("<input name='isRegisterFacebook' type='hidden' value='' />").attr("value",objData.source.inputRefisRegisterFacebook));
					object.submit();				
					break;
				case "onChangeFb":
					window.location = objData.source.fbUrl;
					break;
				case "intIframe":
					if ( objData.source.intIframe == 'true' ) {
						initUrlParent();
					}
					break;
				default: break;
			}
		}
	});
});