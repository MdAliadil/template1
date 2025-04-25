function getRootUrl() {
	return window.location.origin ? window.location.origin + "/" : window.location.protocol + "/" + window.location.host + "/"
}

function preventDefault(a) {
	a = a || window.event, a.preventDefault && a.preventDefault(), a.returnValue = !1
}

function preventDefaultForScrollKeys(a) {
	return keys[a.keyCode] ? (preventDefault(a), !1) : void 0
}

function disableScroll() {
	window.addEventListener && window.addEventListener("DOMMouseScroll", preventDefault, !1), window.onwheel = preventDefault, window.onmousewheel = document.onmousewheel = preventDefault, window.ontouchmove = preventDefault, document.onkeydown = preventDefaultForScrollKeys
}

function enableScroll() {
	window.removeEventListener && window.removeEventListener("DOMMouseScroll", preventDefault, !1), window.onmousewheel = document.onmousewheel = null, window.onwheel = null, window.ontouchmove = null, document.onkeydown = null
}

function getScrollBarWidth() {
	var c = document.createElement("p");
	c.style.width = "100%";
	c.style.height = "200px";
	var d = document.createElement("div");
	d.style.position = "absolute";
	d.style.top = "0px";
	d.style.left = "0px";
	d.style.visibility = "hidden";
	d.style.width = "200px";
	d.style.height = "150px";
	d.style.overflow = "hidden";
	d.appendChild(c);
	document.body.appendChild(d);
	var b = c.offsetWidth;
	d.style.overflow = "scroll";
	var a = c.offsetWidth;
	if (b == a) {
		a = d.clientWidth
	}
	document.body.removeChild(d);
	return (b - a)
}



function showNotification(type, msg) {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": false,
        "positionClass": "toast-top-full-width",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "3000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    if (type == "success") {
        toastr.success(msg);
    } else if (type == "error") {
        toastr.error(msg);
    } else if (type == "warning") {
        toastr.warning(msg);
    } else if (type == "info") {
        toastr.info(msg);
    }
}

$(".ajaxForm").ajaxForm({
    beforeSubmit: function (arr, options, $form) {
        $("#overlay").show();

        if (options.find('button[type="submit"]').hasClass('kt-spinner')) {
            return false;
        }


        //options.find('button[type="submit"]').addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');

        return true;
    },

    success: function (data, statusText, xhr, $form) {
        $form.find('button[type="submit"]').removeClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');
        $("#overlay").hide();

        if (data['elementValue']) {
            $("#" + data['elementValue']['element']).html(data['elementValue']['value']);
        }

        if (data['elementValue2']) {
            $("#" + data['elementValue2']['element']).html(data['elementValue2']['value']);
        }


        if (data['redirectURL']) {
            if (data['redirectDelay']) {

                setTimeout(function () {
                    window.location = data['redirectURL'];
                }, data['redirectDelay']);

                if (data['message']) {
                    showNotification(data['msgType'], data['message']);
                } else {
                    //showRedirectNotification(data['redirectURL'], data['redirectDelay']);
                }
            } else {
                window.location = data['redirectURL'];
            }
        } else if (data['message']) {
            showNotification(data['msgType'], data['message']);

        }

        if (data['resetForm']) {
            $(".ajaxForm").trigger('reset');
        }
    },
    dataType: 'json'
});

