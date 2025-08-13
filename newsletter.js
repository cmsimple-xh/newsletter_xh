document.addEventListener("DOMContentLoaded", function () { 
    document.getElementById("subscribermail").focus();
});

var newsletterI18n = JSON.parse(document.querySelector("script[data-newsletter-i18n]").dataset.newsletterI18n);

    function hideFields(vfield) {
    if (vfield.selectedIndex == 1) { 
        document.getElementById('userinput').style.display='none';
    } 
    else {
        document.getElementById('userinput').style.display='block';
    }
}

function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g,"");
}

var busy=0;
function newsletter_EmptyField(field) {
if (busy) return;
        busy=1;
    if (trim(field.value)=="") {
            field.style.backgroundColor ="#FFAEAE";
        document.getElementById("err").innerHTML=newsletterI18n.fieldsEmpty; 
        field.focus();
        setTimeout(function () {busy=0}, 1);
        return true;
        }
        else {
            field.style.backgroundColor ="#FFFFFF";
        document.getElementById("err").innerHTML="&nbsp;";
        busy=0;
        return false;
    }  
}

function newsletter_ValidEmail(form){
    if (busy) return;
        busy=1;
    var validRegExp = /^[^\s()<>@,;:\"\/\[\]?=]+@\w[\w-]*(\.\w[\w-]*)*\.[a-z]{2,}$/i;
    if (form.subscribermail.value.search(validRegExp) == -1 ) {
        document.getElementById("err").innerHTML=newsletterI18n.emailEmpty;
            form.subscribermail.style.backgroundColor ="#FFAEAE";
            form.subscribermail.focus();
            form.subscribermail.select();
            setTimeout(function () {busy=0}, 1);
    return false;
    }
    form.subscribermail.style.backgroundColor ="#FFFFFF"; 
    document.getElementById('err').innerHTML='&nbsp;'; 
    busy=0;
return true; 
}

addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.newsletter_inputfield').forEach(function (input) {
        if (input.id === 'subscribermail') {
            input.onblur = function () {
                newsletter_ValidEmail(document.subscribe);
            };
        } else if (input.dataset.newsletterMandatory !== undefined) {
            input.onblur = function () {
                newsletter_EmptyField(this);
            };
        }
    });
});
