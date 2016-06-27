jQuery && function (a) {
    function b(b, c) {
        var d = a('<span class="minicolors" />'), e = a.minicolors.defaultSettings;
        b.data("minicolors-initialized") || (c = a.extend(!0, {}, e, c), d.addClass("minicolors-theme-" + c.theme).addClass("minicolors-swatch-position-" + c.swatchPosition).toggleClass("minicolors-swatch-left", "left" === c.swatchPosition).toggleClass("minicolors-with-opacity", c.opacity), void 0 !== c.position && a.each(c.position.split(" "), function () {
            d.addClass("minicolors-position-" + this)
        }), b.addClass("minicolors-input").data("minicolors-initialized", !0).data("minicolors-settings", c).prop("size", 7).prop("maxlength", 7).wrap(d).after('<span class="minicolors-panel minicolors-slider-' + c.control + '">' + '<span class="minicolors-slider">' + '<span class="minicolors-picker"></span>' + "</span>" + '<span class="minicolors-opacity-slider">' + '<span class="minicolors-picker"></span>' + "</span>" + '<span class="minicolors-grid">' + '<span class="minicolors-grid-inner"></span>' + '<span class="minicolors-picker"><span></span></span>' + "</span>" + "</span>"), b.parent().find(".minicolors-panel").on("selectstart",function () {
            return!1
        }).end(), "left" === c.swatchPosition ? b.before('<span class="minicolors-swatch"><span></span></span>') : b.after('<span class="minicolors-swatch"><span></span></span>'), c.textfield || b.addClass("minicolors-hidden"), c.inline && b.parent().addClass("minicolors-inline"), i(b))
    }

    function c(a) {
        var b = a.parent();
        a.removeData("minicolors-initialized").removeData("minicolors-settings").removeProp("size").removeProp("maxlength").removeClass("minicolors-input"), b.before(a).remove()
    }

    function d(a) {
        i(a)
    }

    function e(a) {
        var b = a.parent(), c = b.find(".minicolors-panel"), d = a.data("minicolors-settings");
        !a.data("minicolors-initialized") || a.prop("disabled") || b.hasClass("minicolors-focus") || (f(), b.addClass("minicolors-focus"), c.stop(!0, !0).fadeIn(d.showSpeed, function () {
            d.show && d.show.call(a)
        }))
    }

    function f() {
        a(".minicolors-input").each(function () {
            var b = a(this), c = b.data("minicolors-settings"), d = b.parent();
            c.inline || d.find(".minicolors-panel").fadeOut(c.hideSpeed, function () {
                d.hasClass("minicolors-focus") && c.hide && c.hide.call(b), d.removeClass("minicolors-focus")
            })
        })
    }

    function g(a, b, c) {
        var m, n, o, p, d = a.parents(".minicolors").find("INPUT"), e = d.data("minicolors-settings"), f = a.find("[class$=-picker]"), g = a.offset().left, i = a.offset().top, j = Math.round(b.pageX - g), k = Math.round(b.pageY - i), l = c ? e.animationSpeed : 0;
        b.originalEvent.changedTouches && (j = b.originalEvent.changedTouches[0].pageX - g, k = b.originalEvent.changedTouches[0].pageY - i), 0 > j && (j = 0), 0 > k && (k = 0), j > a.width() && (j = a.width()), k > a.height() && (k = a.height()), a.parent().is(".minicolors-slider-wheel") && f.parent().is(".minicolors-grid") && (m = 75 - j, n = 75 - k, o = Math.sqrt(m * m + n * n), p = Math.atan2(n, m), 0 > p && (p += 2 * Math.PI), o > 75 && (o = 75, j = 75 - 75 * Math.cos(p), k = 75 - 75 * Math.sin(p)), j = Math.round(j), k = Math.round(k)), a.is(".minicolors-grid") ? f.stop(!0).animate({top: k + "px", left: j + "px"}, l, e.animationEasing, function () {
            h(d)
        }) : f.stop(!0).animate({top: k + "px"}, l, e.animationEasing, function () {
            h(d)
        })
    }

    function h(a) {
        function b(a, b) {
            var c, d;
            return a.length && b ? (c = a.offset().left, d = a.offset().top, {x: c - b.offset().left + a.outerWidth() / 2, y: d - b.offset().top + a.outerHeight() / 2}) : null
        }

        var c, d, e, f, h, i, j, k, m, o = a.parent(), p = a.data("minicolors-settings"), s = (o.find(".minicolors-panel"), o.find(".minicolors-swatch")), t = o.find(".minicolors-grid"), u = o.find(".minicolors-slider"), v = o.find(".minicolors-opacity-slider"), w = t.find("[class$=-picker]"), x = u.find("[class$=-picker]"), y = v.find("[class$=-picker]"), z = b(w, t), A = b(x, u), B = b(y, v);
        switch (p.control) {
            case"wheel":
                i = t.width() / 2 - z.x, j = t.height() / 2 - z.y, k = Math.sqrt(i * i + j * j), m = Math.atan2(j, i), 0 > m && (m += 2 * Math.PI), k > 75 && (k = 75, z.x = 69 - 75 * Math.cos(m), z.y = 69 - 75 * Math.sin(m)), d = n(k / .75, 0, 100), c = n(180 * m / Math.PI, 0, 360), e = n(100 - Math.floor(A.y * (100 / u.height())), 0, 100), h = q({h: c, s: d, b: e}), u.css("backgroundColor", q({h: c, s: d, b: 100}));
                break;
            case"saturation":
                c = n(parseInt(z.x * (360 / t.width())), 0, 360), d = n(100 - Math.floor(A.y * (100 / u.height())), 0, 100), e = n(100 - Math.floor(z.y * (100 / t.height())), 0, 100), h = q({h: c, s: d, b: e}), u.css("backgroundColor", q({h: c, s: 100, b: e})), o.find(".minicolors-grid-inner").css("opacity", d / 100);
                break;
            case"brightness":
                c = n(parseInt(z.x * (360 / t.width())), 0, 360), d = n(100 - Math.floor(z.y * (100 / t.height())), 0, 100), e = n(100 - Math.floor(A.y * (100 / u.height())), 0, 100), h = q({h: c, s: d, b: e}), u.css("backgroundColor", q({h: c, s: d, b: 100})), o.find(".minicolors-grid-inner").css("opacity", 1 - e / 100);
                break;
            default:
                c = n(360 - parseInt(A.y * (360 / u.height())), 0, 360), d = n(Math.floor(z.x * (100 / t.width())), 0, 100), e = n(100 - Math.floor(z.y * (100 / t.height())), 0, 100), h = q({h: c, s: d, b: e}), t.css("backgroundColor", q({h: c, s: 100, b: 100}))
        }
        f = p.opacity ? parseFloat(1 - B.y / v.height()).toFixed(2) : 1, a.val(l(h, p.letterCase)), p.opacity && a.attr("data-opacity", f), s.find("SPAN").css({backgroundColor: h, opacity: f}), h + f !== a.data("minicolors-lastChange") && (a.data("minicolors-lastChange", h + f), p.change && p.change.call(a, h, f))
    }

    function i(a, b) {
        var c, d, e, f, g, h, i, j = a.parent(), k = a.data("minicolors-settings"), o = j.find(".minicolors-swatch"), p = j.find(".minicolors-grid"), s = j.find(".minicolors-slider"), t = j.find(".minicolors-opacity-slider"), u = p.find("[class$=-picker]"), v = s.find("[class$=-picker]"), w = t.find("[class$=-picker]");
        switch (c = l(m(a.val(), !0), k.letterCase), c || (c = l(m(k.defaultValue, !0))), d = r(c), b || a.val(c), k.opacity && (e = "" === a.attr("data-opacity") ? 1 : n(parseFloat(a.attr("data-opacity")).toFixed(2), 0, 1), a.attr("data-opacity", e), o.find("SPAN").css("opacity", e), g = n(t.height() - t.height() * e, 0, t.height()), w.css("top", g + "px")), o.find("SPAN").css("backgroundColor", c), k.control) {
            case"wheel":
                h = n(Math.ceil(.75 * d.s), 0, p.height() / 2), i = d.h * Math.PI / 180, f = n(75 - Math.cos(i) * h, 0, p.width()), g = n(75 - Math.sin(i) * h, 0, p.height()), u.css({top: g + "px", left: f + "px"}), g = 150 - d.b / (100 / p.height()), "" === c && (g = 0), v.css("top", g + "px"), s.css("backgroundColor", q({h: d.h, s: d.s, b: 100}));
                break;
            case"saturation":
                f = n(5 * d.h / 12, 0, 150), g = n(p.height() - Math.ceil(d.b / (100 / p.height())), 0, p.height()), u.css({top: g + "px", left: f + "px"}), g = n(s.height() - d.s * (s.height() / 100), 0, s.height()), v.css("top", g + "px"), s.css("backgroundColor", q({h: d.h, s: 100, b: d.b})), j.find(".minicolors-grid-inner").css("opacity", d.s / 100);
                break;
            case"brightness":
                f = n(5 * d.h / 12, 0, 150), g = n(p.height() - Math.ceil(d.s / (100 / p.height())), 0, p.height()), u.css({top: g + "px", left: f + "px"}), g = n(s.height() - d.b * (s.height() / 100), 0, s.height()), v.css("top", g + "px"), s.css("backgroundColor", q({h: d.h, s: d.s, b: 100})), j.find(".minicolors-grid-inner").css("opacity", 1 - d.b / 100);
                break;
            default:
                f = n(Math.ceil(d.s / (100 / p.width())), 0, p.width()), g = n(p.height() - Math.ceil(d.b / (100 / p.height())), 0, p.height()), u.css({top: g + "px", left: f + "px"}), g = n(s.height() - d.h / (360 / s.height()), 0, s.height()), v.css("top", g + "px"), p.css("backgroundColor", q({h: d.h, s: 100, b: 100}))
        }
    }

    function j(b) {
        var c = m(a(b).val(), !0), d = t(c), e = a(b).attr("data-opacity");
        return d ? (void 0 !== e && a.extend(d, {a: parseFloat(e)}), d) : null
    }

    function k(b, c) {
        var d = m(a(b).val(), !0), e = t(d), f = a(b).attr("data-opacity");
        return e ? (void 0 === f && (f = 1), c ? "rgba(" + e.r + ", " + e.g + ", " + e.b + ", " + parseFloat(f) + ")" : "rgb(" + e.r + ", " + e.g + ", " + e.b + ")") : null
    }

    function l(a, b) {
        return"uppercase" === b ? a.toUpperCase() : a.toLowerCase()
    }

    function m(a, b) {
        return a = a.replace(/[^A-F0-9]/gi, ""), 3 !== a.length && 6 !== a.length ? "" : (3 === a.length && b && (a = a[0] + a[0] + a[1] + a[1] + a[2] + a[2]), "#" + a)
    }

    function n(a, b, c) {
        return b > a && (a = b), a > c && (a = c), a
    }

    function o(a) {
        var b = {}, c = Math.round(a.h), d = Math.round(255 * a.s / 100), e = Math.round(255 * a.b / 100);
        if (0 === d)b.r = b.g = b.b = e; else {
            var f = e, g = (255 - d) * e / 255, h = (f - g) * (c % 60) / 60;
            360 === c && (c = 0), 60 > c ? (b.r = f, b.b = g, b.g = g + h) : 120 > c ? (b.g = f, b.b = g, b.r = f - h) : 180 > c ? (b.g = f, b.r = g, b.b = g + h) : 240 > c ? (b.b = f, b.r = g, b.g = f - h) : 300 > c ? (b.b = f, b.g = g, b.r = g + h) : 360 > c ? (b.r = f, b.g = g, b.b = f - h) : (b.r = 0, b.g = 0, b.b = 0)
        }
        return{r: Math.round(b.r), g: Math.round(b.g), b: Math.round(b.b)}
    }

    function p(b) {
        var c = [b.r.toString(16), b.g.toString(16), b.b.toString(16)];
        return a.each(c, function (a, b) {
            1 === b.length && (c[a] = "0" + b)
        }), "#" + c.join("")
    }

    function q(a) {
        return p(o(a))
    }

    function r(a) {
        var b = s(t(a));
        return 0 === b.s && (b.h = 360), b
    }

    function s(a) {
        var b = {h: 0, s: 0, b: 0}, c = Math.min(a.r, a.g, a.b), d = Math.max(a.r, a.g, a.b), e = d - c;
        return b.b = d, b.s = 0 !== d ? 255 * e / d : 0, b.h = 0 !== b.s ? a.r === d ? (a.g - a.b) / e : a.g === d ? 2 + (a.b - a.r) / e : 4 + (a.r - a.g) / e : -1, b.h *= 60, 0 > b.h && (b.h += 360), b.s *= 100 / 255, b.b *= 100 / 255, b
    }

    function t(a) {
        return a = parseInt(a.indexOf("#") > -1 ? a.substring(1) : a, 16), {r: a >> 16, g: (65280 & a) >> 8, b: 255 & a}
    }

    a.minicolors = {defaultSettings: {animationSpeed: 100, animationEasing: "swing", change: null, control: "hue", defaultValue: "", hide: null, hideSpeed: 100, inline: !1, letterCase: "lowercase", opacity: !1, position: "default", show: null, showSpeed: 100, swatchPosition: "left", textfield: !0, theme: "default"}}, a.extend(a.fn, {minicolors: function (e, f) {
        switch (e) {
            case"destroy":
                return a(this).each(function () {
                    c(a(this))
                }), a(this);
            case"opacity":
                return void 0 === f ? a(this).attr("data-opacity") : (a(this).each(function () {
                    d(a(this).attr("data-opacity", f))
                }), a(this));
            case"rgbObject":
                return j(a(this), "rgbaObject" === e);
            case"rgbString":
            case"rgbaString":
                return k(a(this), "rgbaString" === e);
            case"settings":
                return void 0 === f ? a(this).data("minicolors-settings") : (a(this).each(function () {
                    var b = a(this).data("minicolors-settings") || {};
                    c(a(this)), a(this).minicolors(a.extend(!0, b, f))
                }), a(this));
            case"value":
                return void 0 === f ? a(this).val() : (a(this).each(function () {
                    d(a(this).val(f))
                }), a(this));
            case"create":
            default:
                return"create" !== e && (f = e), a(this).each(function () {
                    b(a(this), f)
                }), a(this)
        }
    }}), a(document).on("mousedown.minicolors touchstart.minicolors",function (b) {
        a(b.target).parents().add(b.target).hasClass("minicolors") || f()
    }).on("mousedown.minicolors touchstart.minicolors", ".minicolors-grid, .minicolors-slider, .minicolors-opacity-slider",function (b) {
        var c = a(this);
        b.preventDefault(), a(document).data("minicolors-target", c), g(c, b, !0)
    }).on("mousemove.minicolors touchmove.minicolors",function (b) {
        var c = a(document).data("minicolors-target");
        c && g(c, b)
    }).on("mouseup.minicolors touchend.minicolors",function () {
        a(this).removeData("minicolors-target")
    }).on("mousedown.minicolors touchstart.minicolors", ".minicolors-swatch",function () {
        var c = a(this).parent().find("INPUT"), d = c.parent();
        d.hasClass("minicolors-focus") ? f(c) : e(c)
    }).on("focus.minicolors", ".minicolors-input",function () {
        var c = a(this);
        c.data("minicolors-initialized") && e(c)
    }).on("blur.minicolors", ".minicolors-input",function () {
        var c = a(this), d = c.data("minicolors-settings");
        c.data("minicolors-initialized") && (c.val(m(c.val(), !0)), "" === c.val() && c.val(m(d.defaultValue, !0)), c.val(l(c.val(), d.letterCase)), f(c))
    }).on("keydown.minicolors", ".minicolors-input",function (b) {
        var c = a(this);
        if (c.data("minicolors-initialized"))switch (b.keyCode) {
            case 9:
                f();
                break;
            case 27:
                f(), c.blur()
        }
    }).on("keyup.minicolors", ".minicolors-input",function () {
        var c = a(this);
        c.data("minicolors-initialized") && i(c, !0)
    }).on("paste.minicolors", ".minicolors-input", function () {
        var c = a(this);
        c.data("minicolors-initialized") && setTimeout(function () {
            i(c, !0)
        }, 1)
    })
}(jQuery);