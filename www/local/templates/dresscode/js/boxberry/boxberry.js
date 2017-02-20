/**
 * Created by shara on 12.04.2016.
 */
function getCookie(t) {
    var e = document.cookie.match(new RegExp("(?:^|; )" + t.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)"));
    return e ? decodeURIComponent(e[1]) : void 0
}
Function.prototype.bind || (Function.prototype.bind = function (t) {
    if ("function" != typeof this)throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
    var e = Array.prototype.slice.call(arguments, 1), i = this, r = function () {
    }, n = function () {
        return i.apply(this instanceof r && t ? this : t, e.concat(Array.prototype.slice.call(arguments)))
    };
    return r.prototype = this.prototype, n.prototype = new r, n
});
var boxberry = {
    _callback_function: null, _overlay: null, _container: null, _frame: null, init: function () {
        var t = document.getElementsByTagName("HEAD")[0], e = document.createElement("LINK");
        e.rel = "stylesheet", e.type = "text/css", e.href = "https://saleader.com/bitrix/templates/dresscode/js/boxberry/boxberry.css", t.appendChild(e);
        var e = document.createElement("SCRIPT");
        e.src = "https://saleader.com/bitrix/templates/dresscode/js/boxberry/postmessage.js", e.onload = function () {
            pm.bind("boxberry-map-point-select", function (t) {
                this.callCallbackFunction(t), this.hideOverlay(), this.hideContainer()
            }.bind(this))
        }.bind(this), t.appendChild(e)
    }, makeUrl: function (t) {
        var e = "/?";
        for (var i in t)e = e + i + "=" + t[i] + "&";
        return e + "host=" + location.hostname
    }, open: function (t, e, i, r, n, o, a, s, c, l) {
        e % 1 === 0 ? (this.parameters = new Object, this.parameters.api_token = "", this.parameters.custom_city = encodeURIComponent(i), this.parameters.pricedelivery = r, this.parameters.target_start = "68", this.parameters.ordersum = void 0 !== n ? n : "", this.parameters.paysum = void 0 !== a ? a : "", this.parameters.weight = void 0 !== o ? o : 1, this.parameters.height = void 0 !== s ? s : 5, this.parameters.width = void 0 !== c ? c : 5, this.parameters.depth = void 0 !== l ? l : 5) : (this.parameters = new Object, this.parameters.api_token = void 0 !== e ? encodeURIComponent(e) : "", this.parameters.custom_city = void 0 !== i ? encodeURIComponent(i) : "", this.parameters.ordersum = void 0 !== n ? n : "", this.parameters.weight = void 0 !== o ? o : 1, this.parameters.weight = void 0 !== o ? o : 1, this.parameters.paysum = void 0 !== a ? a : "", this.parameters.height = void 0 !== s ? s : 5, this.parameters.width = void 0 !== c ? c : 5, this.parameters.depth = void 0 !== l ? l : 5, this.parameters.target_start = void 0 !== r ? r : "68"), this.parameters.calc = 1, "string" == typeof t && (t = window[t]), void 0 == t && (this.parameters.calc = 0), this._callback_function = t, this.showOverlay(), this.showContainer()
    }, callCallbackFunction: function () {
        this._callback_function && this._callback_function.apply(window, arguments)
    }, showOverlay: function () {
        this._overlay || (this._overlay = document.createElement("DIV"), this._overlay.className = "boxberry_overlay", document.getElementsByTagName("BODY")[0].appendChild(this._overlay)), this._overlay.style.display = "block"
    }, hideOverlay: function () {
        this._overlay && (this._overlay.style.display = "none")
    }, showContainer: function () {
        if (!this._container) {
            this._container = document.createElement("DIV"), this._container.className = "boxberry_container", document.getElementsByTagName("BODY")[0].appendChild(this._container);
            var t = document.createElement("DIV");
            t.className = "boxberry_toppanel";
            var e = document.createElement("A");
            e.href = "#", e.innerHTML = '<img src="https://saleader.com/bitrix/templates/dresscode/images/close_round_button.png" alt="close">', e.className = "boxberry_container_close", e.onclick = function () {
                return this.hideOverlay(), this.hideContainer(), !1
            }.bind(this), t.appendChild(e), this._container.appendChild(t);
            var i = document.createElement("DIV");
            i.className = "boxberry_content", this._container.appendChild(i), this._frame = document.createElement("IFRAME"), this._frame.src = "http://points.boxberry.ru/map" + this.makeUrl(this.parameters), this._frame.frameborder = "0", this._frame.style.border = "0", this._frame.style.height = "590px", this._frame.style.width = "100%", i.appendChild(this._frame)
        }
        this._container.style.display = "block", this._frame.contentWindow && pm({
            target: this._frame.contentWindow,
            type: "boxberry-map-init",
            data: {}
        });
        var r = this.getPageScroll();
        this._container.style.top = r.top + Math.max(0, (document.documentElement.clientHeight - this._container.offsetHeight) / 2) + "px", this._container.style.left = r.left + Math.max(0, (document.documentElement.clientWidth - this._container.offsetWidth) / 2) + "px"
    }, hideContainer: function () {
        this._frame && pm({
            target: this._frame.contentWindow,
            type: "boxberry-map-destroy",
            data: {}
        }), this._container && (this._container.style.display = "none"), this._container = null
    }, getPageScroll: function () {
        var t = document, e = t.documentElement, i = (t.body, t && t.scrollTop || t.body && t.body.scrollTop || e && e.scrollTop || 0);
        i -= e.clientTop;
        var r = t && t.scrollLeft || t.body && t.body.scrollLeft || e && e.scrollLeft || 0;
        return r -= e.clientLeft, {top: i, left: r}
    }
};
boxberry.init();