/**
 * Created by shara on 12.04.2016.
 */
var NO_JQUERY = {};
!function (e, t, a) {
    if (!("console" in e)) {
        var n = e.console = {};
        n.log = n.warn = n.error = n.debug = function () {
        }
    }
    t === NO_JQUERY && (t = {
        fn: {}, extend: function () {
            for (var e = arguments[0], t = 1, a = arguments.length; a > t; t++) {
                var n = arguments[t];
                for (var r in n)e[r] = n[r]
            }
            return e
        }
    }), t.fn.pm = function () {
        return this
    }, t.pm = e.pm = function (e) {
        r.send(e)
    }, t.pm.bind = e.pm.bind = function (e, t, a, n, s) {
        r.bind(e, t, a, n, s === !0)
    }, t.pm.unbind = e.pm.unbind = function (e, t) {
        r.unbind(e, t)
    }, t.pm.origin = e.pm.origin = null, t.pm.poll = e.pm.poll = 200;
    var r = {
        send: function (e) {
            var a = t.extend({}, r.defaults, e), n = a.target;
            if (a.target && a.type) {
                var s = {data: a.data, type: a.type};
                a.success && (s.callback = r._callback(a.success)), a.error && (s.errback = r._callback(a.error)), "postMessage" in n && !a.hash ? (r._bind(), n.postMessage(JSON.stringify(s), a.origin || "*")) : (r.hash._bind(), r.hash.send(a, s))
            }
        },
        bind: function (e, t, a, n, s) {
            r._replyBind(e, t, a, n, s)
        },
        _replyBind: function (a, n, s, o, i) {
            "postMessage" in e && !o ? r._bind() : r.hash._bind();
            var u = r.data("listeners.postmessage");
            u || (u = {}, r.data("listeners.postmessage", u));
            var c = u[a];
            c || (c = [], u[a] = c), c.push({fn: n, callback: i, origin: s || t.pm.origin})
        },
        unbind: function (e, t) {
            var a = r.data("listeners.postmessage");
            if (a)if (e)if (t) {
                var n = a[e];
                if (n) {
                    for (var s = [], o = 0, i = n.length; i > o; o++) {
                        var u = n[o];
                        u.fn !== t && s.push(u)
                    }
                    a[e] = s
                }
            } else delete a[e]; else for (var o in a)delete a[o]
        },
        data: function (e, t) {
            return t === a ? r._data[e] : (r._data[e] = t, t)
        },
        _data: {},
        _CHARS: "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz".split(""),
        _random: function () {
            for (var e = [], t = 0; 32 > t; t++)e[t] = r._CHARS[0 | 32 * Math.random()];
            return e.join("")
        },
        _callback: function (e) {
            var t = r.data("callbacks.postmessage");
            t || (t = {}, r.data("callbacks.postmessage", t));
            var a = r._random();
            return t[a] = e, a
        },
        _bind: function () {
            r.data("listening.postmessage") || (e.addEventListener ? e.addEventListener("message", r._dispatch, !1) : e.attachEvent && e.attachEvent("onmessage", r._dispatch), r.data("listening.postmessage", 1))
        },
        _dispatch: function (e) {
            function t(t) {
                a.callback && r.send({target: e.source, data: t, type: a.callback})
            }

            try {
                var a = JSON.parse(e.data)
            } catch (n) {
                return
            }
            if (a.type) {
                var s = r.data("callbacks.postmessage") || {}, o = s[a.type];
                if (o)o(a.data); else for (var i = r.data("listeners.postmessage") || {}, u = i[a.type] || [], c = 0, f = u.length; f > c; c++) {
                    var l = u[c];
                    if (l.origin && "*" !== l.origin && e.origin !== l.origin) {
                        if (a.errback) {
                            var p = {message: "postmessage origin mismatch", origin: [e.origin, l.origin]};
                            r.send({target: e.source, data: p, type: a.errback})
                        }
                    } else try {
                        l.callback ? l.fn(a.data, t, e) : t(l.fn(a.data, e))
                    } catch (n) {
                        if (!a.errback)throw n;
                        r.send({target: e.source, data: n, type: a.errback})
                    }
                }
            }
        }
    };
    r.hash = {
        send: function (t, a) {
            var n = t.target, s = t.url;
            if (s) {
                s = r.hash._url(s);
                var o, i = r.hash._url(e.location.href);
                if (e == n.parent)o = "parent"; else try {
                    for (var u = 0, c = parent.frames.length; c > u; u++) {
                        var f = parent.frames[u];
                        if (f == e) {
                            o = u;
                            break
                        }
                    }
                } catch (l) {
                    o = e.name
                }
                if (null != o) {
                    var p = {
                        "x-requested-with": "postmessage",
                        source: {name: o, url: i},
                        postmessage: a
                    }, g = "#x-postmessage-id=" + r._random();
                    n.location = s + g + encodeURIComponent(JSON.stringify(p))
                }
            }
        }, _regex: /^\#x\-postmessage\-id\=(\w{32})/, _regex_len: "#x-postmessage-id=".length + 32, _bind: function () {
            r.data("polling.postmessage") || (setInterval(function () {
                var t = "" + e.location.hash, a = r.hash._regex.exec(t);
                if (a) {
                    var n = a[1];
                    r.hash._last !== n && (r.hash._last = n, r.hash._dispatch(t.substring(r.hash._regex_len)))
                }
            }, t.pm.poll || 200), r.data("polling.postmessage", 1))
        }, _dispatch: function (t) {
            function a(e) {
                s.callback && r.send({target: u, data: e, type: s.callback, hash: !0, url: t.source.url})
            }

            if (t) {
                try {
                    if (t = JSON.parse(decodeURIComponent(t)), !("postmessage" === t["x-requested-with"] && t.source && null != t.source.name && t.source.url && t.postmessage))return
                } catch (n) {
                    return
                }
                var s = t.postmessage, o = r.data("callbacks.postmessage") || {}, i = o[s.type];
                if (i)i(s.data); else {
                    var u;
                    u = "parent" === t.source.name ? e.parent : e.frames[t.source.name];
                    for (var c = r.data("listeners.postmessage") || {}, f = c[s.type] || [], l = 0, p = f.length; p > l; l++) {
                        var g = f[l];
                        if (g.origin) {
                            var d = /https?\:\/\/[^\/]*/.exec(t.source.url)[0];
                            if ("*" !== g.origin && d !== g.origin) {
                                if (s.errback) {
                                    var h = {message: "postmessage origin mismatch", origin: [d, g.origin]};
                                    r.send({target: u, data: h, type: s.errback, hash: !0, url: t.source.url})
                                }
                                continue
                            }
                        }
                        try {
                            g.callback ? g.fn(s.data, a) : a(g.fn(s.data))
                        } catch (n) {
                            if (!s.errback)throw n;
                            r.send({target: u, data: n, type: s.errback, hash: !0, url: t.source.url})
                        }
                    }
                }
            }
        }, _url: function (e) {
            return ("" + e).replace(/#.*$/, "")
        }
    }, t.extend(r, {
        defaults: {
            target: null,
            url: null,
            type: null,
            data: null,
            success: null,
            error: null,
            origin: "*",
            hash: !1
        }
    })
}(this, "undefined" == typeof jQuery ? NO_JQUERY : jQuery), "JSON" in window && window.JSON || (JSON = {}), function () {
    function f(e) {
        return 10 > e ? "0" + e : e
    }

    function quote(e) {
        return escapable.lastIndex = 0, escapable.test(e) ? '"' + e.replace(escapable, function (e) {
            var t = meta[e];
            return "string" == typeof t ? t : "\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4)
        }) + '"' : '"' + e + '"'
    }

    function str(e, t) {
        var a, n, r, s, o, i = gap, u = t[e];
        switch (u && "object" == typeof u && "function" == typeof u.toJSON && (u = u.toJSON(e)), "function" == typeof rep && (u = rep.call(t, e, u)), typeof u) {
            case"string":
                return quote(u);
            case"number":
                return isFinite(u) ? String(u) : "null";
            case"boolean":
            case"null":
                return String(u);
            case"object":
                if (!u)return "null";
                if (gap += indent, o = [], "[object Array]" === Object.prototype.toString.apply(u)) {
                    for (s = u.length, a = 0; s > a; a += 1)o[a] = str(a, u) || "null";
                    return r = 0 === o.length ? "[]" : gap ? "[\n" + gap + o.join(",\n" + gap) + "\n" + i + "]" : "[" + o.join(",") + "]", gap = i, r
                }
                if (rep && "object" == typeof rep)for (s = rep.length, a = 0; s > a; a += 1)n = rep[a], "string" == typeof n && (r = str(n, u), r && o.push(quote(n) + (gap ? ": " : ":") + r)); else for (n in u)Object.hasOwnProperty.call(u, n) && (r = str(n, u), r && o.push(quote(n) + (gap ? ": " : ":") + r));
                return r = 0 === o.length ? "{}" : gap ? "{\n" + gap + o.join(",\n" + gap) + "\n" + i + "}" : "{" + o.join(",") + "}", gap = i, r
        }
    }

    "function" != typeof Date.prototype.toJSON && (Date.prototype.toJSON = function (e) {
        return this.getUTCFullYear() + "-" + f(this.getUTCMonth() + 1) + "-" + f(this.getUTCDate()) + "T" + f(this.getUTCHours()) + ":" + f(this.getUTCMinutes()) + ":" + f(this.getUTCSeconds()) + "Z"
    }, String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function (e) {
        return this.valueOf()
    });
    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, gap, indent, meta = {
        "\b": "\\b",
        "	": "\\t",
        "\n": "\\n",
        "\f": "\\f",
        "\r": "\\r",
        '"': '\\"',
        "\\": "\\\\"
    }, rep;
    "function" != typeof JSON.stringify && (JSON.stringify = function (e, t, a) {
        var n;
        if (gap = "", indent = "", "number" == typeof a)for (n = 0; a > n; n += 1)indent += " "; else"string" == typeof a && (indent = a);
        if (rep = t, t && "function" != typeof t && ("object" != typeof t || "number" != typeof t.length))throw new Error("JSON.stringify");
        return str("", {"": e})
    }), "function" != typeof JSON.parse && (JSON.parse = function (text, reviver) {
        function walk(e, t) {
            var a, n, r = e[t];
            if (r && "object" == typeof r)for (a in r)Object.hasOwnProperty.call(r, a) && (n = walk(r, a), void 0 !== n ? r[a] = n : delete r[a]);
            return reviver.call(e, t, r)
        }

        var j;
        if (cx.lastIndex = 0, cx.test(text) && (text = text.replace(cx, function (e) {
                return "\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4)
            })), /^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, "")))return j = eval("(" + text + ")"), "function" == typeof reviver ? walk({"": j}, "") : j;
        throw new SyntaxError("JSON.parse")
    })
}();