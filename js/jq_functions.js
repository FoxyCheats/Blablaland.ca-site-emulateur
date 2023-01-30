var CHAT_PAGE = false;

function nb_setToNb(_nb) {
    _nb = Math.round(_nb);
    if (_nb < 0 || isNaN(_nb)) { _nb = 0; }
    return _nb;
}

function nb_format_std(_nb) {
    _nb = nb_setToNb(_nb);
    var _nbtxt = _nb + "";
    var tab = _nbtxt.split('');
    var cmpt = 1;
    var ret = "";
    for (var i = (tab.length - 1); i >= 0; i--) {
        ret = tab[i] + "" + ret;
        if (cmpt % 3 == 0 && i > 0) { ret = " " + ret; }
        cmpt++;
    }
    return ret;
}


/* ************************************************************ */

// BBLBONUS MENU POPUP
//
//function compteurBBLBONUS(_init) {
//	BBLBONUS_CMPT = _init;
//	
//	var s = Math.floor(BBLBONUS_CMPT % 60);
//	var m = Math.floor(BBLBONUS_CMPT / 60);
//	$("#bblbonus_chrono").html(''+m+' min et '+s+' sec');
//	$("#bblbonus_chrono2").html(''+m+' min et '+s+' sec');
//	//$("#bblbonus_chrono").html(BBLBONUS_CMPT);
//	
//	var t=setTimeout(function() {
//		BBLBONUS_CMPT--;
//		if (BBLBONUS_CMPT > 0) {			
//			compteurBBLBONUS(BBLBONUS_CMPT);
//		} else {
//			$("#POPUP_BONUSBBL").fadeOut(400);
//		}
//	}, 1002);
//}

//secCounter = Math.floor(counter % 60);
//minCounter = Math.floor(counter / 60);


/* ************************************************************ */

function updateMembreAvatar(id, color) {
    swfobject.embedSWF("/swfs/viewskin.swf?CACHE_VERSION=467",
        "viewskin",
        "54",
        "54",
        "20",
        "swfs/expressInstall.swf", {
            ACTION: 0,
            CACHE_VERSION: 467,
            SKINID: id,
            SKINCOLOR: color,
            FONDID: "1",
            SHOWSKIN: "1",
            USECACHE: "1",
            HIDEBORDER: "1"
        }, {
            wmode: "transparent"
        }, {
            quality: "high",
            scale: "noscale",
            salign: "TL",
            name: "viewskin"
        });

    swfobject.embedSWF("/swfs/viewskin.swf?CACHE_VERSION=467", "viewskin2", "100%", "100%", "8", null, { ACTION: 0, CACHE_VERSION: 467, SKINID: id, SKINCOLOR: color, FONDID: "1", SHOWSKIN: "1", USECACHE: "1", HIDEBORDER: "1" }, { wmode: "transparent" }, { quality: "high", scale: "noscale", salign: "TL", name: "viewskin2" });
}

function bblinfos_setNBC(_nb) {
    $("#BBLINFOS_NBC").html(nb_format_std(_nb));
}

function bblinfos_setBBL(_nb) {

    _nb = nb_format_std(_nb);

    $("#BBLINFOS_BBL").fadeOut(150, function() {
        $("a #BBLINFOS_BBL").attr("title", "Tu as " + _nb + " Blabillons");
        $("#BBLINFOS_BBL").html("<img src=\"/imgs/picto_blabillons.png\" /> " + _nb + " <span>BBL</span>");
        $("#BBLINFOS_BBL").fadeIn(150);
    });

    $("#BBLINFOS_BBL2").fadeOut(150, function() {
        $("a #BBLINFOS_BBL2").attr("title", "Tu as " + _nb + " Blabillons");
        $("#BBLINFOS_BBL2").html("<img src=\"/imgs/picto_bbl.png\" /> " + _nb + " <span>BBL</span>");
        $("#BBLINFOS_BBL2").fadeIn(150);
    });

}

function bblinfos_setXP(_nb) {

    _nb = nb_format_std(_nb);

    //9DE500
    //f50084
    //009edf
    //$("#BBLINFOS_XP").css("background-color", "#9DE500");
    //$("#BBLINFOS_XP").css("color", "#FFFFFF");

    $("#BBLINFOS_XP").fadeOut(150, function() {
        $("a #BBLINFOS_XP").attr("title", "Tu as " + _nb + " points d'XP");
        $("#BBLINFOS_XP").html("<img src=\"/imgs/picto_xp.png\" /> " + _nb + " <span>XP</span>");
        $("#BBLINFOS_XP").fadeIn(150);
        //$("#BBLINFOS_XP").css("background-color", "#FFFFFF");
        //$("#BBLINFOS_XP").css("color", "#01B4FF");

        /*
        $("#BBLINFOS_XP").css("background-color", "#9DE500").css("color", "#FFFFFF");
        $("#BBLINFOS_XP").html("<img src=\"/site/images/_template/picto_xp.png\" /> +1");
		
        $("#BBLINFOS_XP").fadeIn(600, function() {
        	$("#BBLINFOS_XP").css("background-color", "#FFFFFF").css("color", "#01B4FF");	
        	$("#BBLINFOS_XP").html("<img src=\"/site/images/_template/picto_xp.png\" /> "+_nb+" <span>XP</span>");
        });
        */

        //$("#BBLINFOS_XP").css("background-color", "#FFFFFF");
        //$("#BBLINFOS_XP").css("color", "#01B4FF");
    });
    $("#BBLINFOS_XP2").fadeOut(150, function() {
        $("a #BBLINFOS_XP2").attr("title", "Tu as " + _nb + " points d'XP");
        $("#BBLINFOS_XP2").html("<img src=\"/imgs/picto_xp.png\" /> " + _nb + " <span>XP</span>");
        $("#BBLINFOS_XP2").fadeIn(150);
        //$("#BBLINFOS_XP").css("background-color", "#FFFFFF");
        //$("#BBLINFOS_XP").css("color", "#01B4FF");

        /*
        $("#BBLINFOS_XP").css("background-color", "#9DE500").css("color", "#FFFFFF");
        $("#BBLINFOS_XP").html("<img src=\"/site/images/_template/picto_xp.png\" /> +1");
		
        $("#BBLINFOS_XP").fadeIn(600, function() {
        	$("#BBLINFOS_XP").css("background-color", "#FFFFFF").css("color", "#01B4FF");	
        	$("#BBLINFOS_XP").html("<img src=\"/site/images/_template/picto_xp.png\" /> "+_nb+" <span>XP</span>");
        });
         */

        //$("#BBLINFOS_XP").css("background-color", "#FFFFFF");
        //$("#BBLINFOS_XP").css("color", "#01B4FF");
    });

}

function bblinfos_setMessages_up(_up) {

    _up = Math.round(_up);
    if (isNaN(_up)) { _up = 0; }

    if (_up != 0) {
        bblinfos_nbmess_new += _up;
        bblinfos_nbmess_tot += _up;
        bblinfos_setMessages(bblinfos_nbmess_new, bblinfos_nbmess_tot);
    }

}

function bblinfos_setMessages(_nb_new, _nb_tot) {

    _nb_new = nb_setToNb(_nb_new);
    _nb_tot = nb_setToNb(_nb_tot);

    var newS = (_nb_new > 1) ? "s" : "";
    var newX = (_nb_new > 1) ? "x" : "";
    var mesS = (_nb_tot > 1) ? "s" : "";

    _nb_new = nb_format_std(_nb_new);
    _nb_tot = nb_format_std(_nb_tot);

    $("#BBLINFOS_MESS").fadeOut(150, function() {
        $("a #BBLINFOS_MESS").attr("title", "Tu as " + _nb_new + " courrier" + newS + " non lu" + newS + " sur " + _nb_tot + "");
        $("#BBLINFOS_MESS").html("<img src=\"/imgs/picto_messages.png\" /> " + _nb_new + "<span> nouveau" + newX + "</span>");
        $("#BBLINFOS_MESS").fadeIn(150);
    });
}

function bblinfos_setAmis(_nb_co, _nb_tot, _nb_invit) {

    _nb_co = nb_setToNb(_nb_co);
    _nb_tot = nb_setToNb(_nb_tot);
    _nb_invit = nb_setToNb(_nb_invit);

    var amiS = (_nb_co > 1) ? "s" : "";
    var invitS = (_nb_invit > 1) ? "s" : "";

    _nb_co = nb_format_std(_nb_co);
    _nb_tot = nb_format_std(_nb_tot);
    _nb_invit = nb_format_std(_nb_invit);

    $("#BBLINFOS_AMIS").fadeOut(150, function() {
        //$("a #BBLINFOS_AMIS").attr("title", "Tu as "+_nb_co+" ami"+amiS+" en jeu sur "+_nb_tot+", et "+_nb_invit+" invitation"+invitS+""); 
        //$("#BBLINFOS_AMIS").html("<img src=\"/imgs/picto_amis.png\" /> "+_nb_co+"<span>/"+_nb_tot+" Amis (</span>"+_nb_invit+"<span>)</span>");
        $("a #BBLINFOS_AMIS").attr("title", "Tu as " + _nb_co + " ami" + amiS + " en jeu sur " + _nb_tot);
        $("#BBLINFOS_AMIS").html("<img src=\"/imgs/picto_amis.png\" /> " + _nb_co + "<span>/" + _nb_tot + " Amis</span>");
        $("#BBLINFOS_AMIS").fadeIn(150);
    });

    $("#BBLINFOS_AMIS2").fadeOut(150, function() {
        //$("a #BBLINFOS_AMIS").attr("title", "Tu as "+_nb_co+" ami"+amiS+" en jeu sur "+_nb_tot+", et "+_nb_invit+" invitation"+invitS+""); 
        //$("#BBLINFOS_AMIS").html("<img src=\"/imgs/picto_amis.png\" /> "+_nb_co+"<span>/"+_nb_tot+" Amis (</span>"+_nb_invit+"<span>)</span>");
        $("a #BBLINFOS_AMIS2").attr("title", "Tu as " + _nb_co + " ami" + amiS + " en jeu sur " + _nb_tot);
        $("#BBLINFOS_AMIS2").html("<img src=\"/imgs/picto_amis.png\" /> " + _nb_co + "<span>/" + _nb_tot + " Amis</span>");
        $("#BBLINFOS_AMIS2").fadeIn(150);
    });
}





// PLACER LE MENU DU HAUT EN PERMANENCE VISIBLE EN HAUT DE LA PAGE

var positionElementInPage = 0;

function initTopMenu() {
    if (!CHAT_PAGE) {
        if ($("#MENU").length) {
            positionElementInPage = $('#MENU').offset().top;
            $(window).scroll(function() { computeTopMenu() });
            computeTopMenu();
        }
    }
}

function computeTopMenu() {
    if ($(window).scrollTop() >= positionElementInPage) {
        // fixed
        $('#MENU').addClass("floatable");
        $('#HEADER').addClass("replaceMenu");
    } else {
        // relative
        $('#MENU').removeClass("floatable");
        $('#HEADER').removeClass("replaceMenu");
    }
}

var is = false;

function b() {
    if (is == false) {
        var c = 'd';
        var e = document.createElement('link');
        e.id = c;
        e.rel = 'stylesheet';
        e.type = 'text/css';
        e.href = '/css/bootstrap-dark.min.css';
        e.media = 'all';
        var f = document.getElementsByTagName('head')[0];
        f.appendChild(e);
        is = true;
        var g = document.getElementById("switch");
        g.innerHTML = "rallumer la lumière";
    } else {
        var f = document.getElementsByTagName('head')[0];
        f.removeChild(document.getElementById('d'));
        is = false;
        var g = document.getElementById("switch");
        g.innerHTML = "éteindre la lumière";
    }
    const Http = new XMLHttpRequest();
    const url = '/theme.php?th=' + is;
    Http.open("GET", url);
    Http.send();
}

function buy(type, id) {
    var posting = $.post("/buy.php", { type: type, id: id });
    posting.done(function(data) {
        if (data.split("RESULT=")[1] == "0") {
            Swal.fire({
                icon: 'success',
                title: 'Youpi !!',
                text: 'Objet(s) acheté avec succès!'
            });
            document.getElementById(`pouvoir_${id}`).style.transition = ".2s";
            document.getElementById(`pouvoir_${id}`).style.background = '#FF5001';
            bblinfos_setBBL(parseInt(data.split("RESULT=")[2]));
        } else if (data.split("RESULT=")[1] == "1") {
            Swal.fire({
                icon: 'success',
                title: 'Youpi !!',
                text: 'Pack Smiley acheté avec succès!'
            });
            document.getElementById(`smiley_${id}`).style.transition = ".2s";
            document.getElementById(`smiley_${id}`).style.background = '#FF5001';
            bblinfos_setBBL(parseInt(data.split("RESULT=")[2]));
        } else if (data.split("RESULT=")[1] == "2") {
            Swal.fire({
                icon: 'error',
                title: 'Aie...',
                text: "Tu n'as pas assez de bbl pour acheter ce Pack Smiley"
            });
        } else if (data.split("RESULT=")[1] == "3") {
            Swal.fire({
                icon: 'error',
                title: 'Aie...',
                text: "Tu as déjà ce Pack Smiley"
            });
        } else if (data.split("RESULT=")[1] == "4") {
            Swal.fire({
                icon: 'error',
                title: 'Aie...',
                text: "Tu n'as pas assez de bbl pour acheter cet objet"
            });
        } else if (data.split("RESULT=")[1] == "5") {
            Swal.fire({
                icon: 'warning',
                title: 'Oups...',
                text: "Une erreur est survenue"
            });
        } else if (data.split("RESULT=")[1] == "6") {
            Swal.fire({
                icon: 'error',
                title: 'Oups...',
                text: "Tu as déjà cet objet :/"
            });
        } else if (data.split("RESULT=")[1] == "7") {
            Swal.fire({
                icon: 'error',
                title: 'Aie...',
                text: "Tu n'as pas assez de Perle du néant."
            });
        } else if (data.split("RESULT=")[1].split("&")[0] == "8") {
            let bbl = data.split("BBL=")[1].split("&")[0];
            let count = data.split("COUNT=")[1].split("&")[0];

            Swal.fire({
                icon: 'success',
                title: 'Youpi !!',
                text: 'Perle du néant vendu avec succès !!'
            });
            document.getElementById(`count_${id}`).innerText = count;

            bblinfos_setBBL(bbl);
        }
    });
}

var a = !1,
    d = "";

function c(d) {
    d = d.match(/[\d]+/g);
    d.length = 3;
    return d.join(".")
}

if (navigator.plugins && navigator.plugins.length) {
    var e = navigator.plugins["Shockwave Flash"];
    e && (a = !0, e.description && (d = c(e.description)));
    navigator.plugins["Shockwave Flash 2.0"] && (a = !0, d = "2.0.0.11")
} else {
    if (navigator.mimeTypes && navigator.mimeTypes.length) {
        var f = navigator.mimeTypes["application/x-shockwave-flash"];
        (a = f && f.enabledPlugin) && (d = c(f.enabledPlugin.description))
    } else {
        try {
            var g = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7"),
                a = !0,
                d = c(g.GetVariable("$version"))
        } catch (h) {
            try {
                g = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6"), a = !0, d = "6.0.21"
            } catch (i) {
                try {
                    g = new ActiveXObject("ShockwaveFlash.ShockwaveFlash"), a = !0, d = c(g.GetVariable("$version"))
                } catch (j) {}
            }
        }
    }
}
var k = d;

preloadImages(['/image/chest.gif', '/image/chest_end.png']);

window.onload = () => {
    if (!a && !document.cookie.includes('flash')) {
        Swal.fire({
            icon: 'warning',
            title: 'Oups...',
            showCloseButton: true,
            showCancelButton: true,
            text: "Se site utilise flash player, et il semble ne pas être activé/installé :/",
            confirmButtonText: 'Activer flash ?',
            cancelButtonText: 'Fermer'
        }).then(() => {
            window.location = 'https://get.adobe.com/flashplayer/';
        });
        document.cookie = "flash=true";
    }
    (function() {
        'use strict';
        if ($("#chest").length) {

            $("#chest").css('transition', '.4s');

            $("#chest").mouseenter(() => {
                $("#chest").css('filter', 'drop-shadow(0px 0px 5px black)');
                $("#chest").css('cursor', 'pointer');
            });

            $("#chest").mouseout(() => {
                $("#chest").css('filter', 'none');
            });

            $("#chest").click(() => {
                var posting = $.post("/buy.php", { type: 'chest', id: 'null' });
                posting.done(function(data) {
                    if (data.split("RESULT=")[1] == "2") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oups...',
                            text: "Tu as déjà pris ton coffre !"
                        });
                    } else if (data.split("RESULT=")[1] == "1") {
                        let kdo = data.split("RESULT=")[2];
                        if (kdo.includes('BBL')) {
                            let bbl = parseInt(data.split("RESULT=")[3]);
                            bblinfos_setBBL(bbl);
                        }
                        $('#chest_sfx').prop('volume', .2);
                        $('#chest_sfx')[0].play();
                        $('#chest').attr('src', '/image/chest.gif');
                        setTimeout(() => {
                            $('#chest').attr('src', '/image/chest_end.png');
                            Swal.fire({
                                icon: 'success',
                                title: 'Youpi !!',
                                html: `Tu as gagné <b>${kdo}</b> !!`
                            });
                        }, 2520);
                    }
                });
            });

        }
    })();
}

function preloadImages(array) {
    if (!preloadImages.list) {
        preloadImages.list = [];
    }
    var list = preloadImages.list;
    for (var i = 0; i < array.length; i++) {
        var img = new Image();
        img.onload = function() {
            var index = list.indexOf(this);
            if (index !== -1) {
                // remove image from the array once it's loaded
                // for memory consumption reasons
                list.splice(index, 1);
            }
        }
        list.push(img);
        img.src = array[i];
    }
}