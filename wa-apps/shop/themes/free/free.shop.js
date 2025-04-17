var reviews = {
    reviewsBox: $(".js-reviews-carousel"), init: function () {
        var e = this;
        if (!e.reviewsBox.length) return !1;
        e.prepareReviewsSlider(), e.sliderInteraction(), $(window).one("resize", reviews.reviewsSlider)
    }, prepareReviewsSlider: function () {
        var e = this, i = e.reviewsBox.find(".owl-carousel"), t = i.outerWidth(),
            a = e.reviewsBox.find(".js-home-reviews-item"), s = a.length;
        a.first().outerWidth(!0) * s > t + 20 && ((e = e.reviewsBox.find(".js-reviews-carousel-direction")).append('<button data-index="prev" class="js-carousel-reviews-on-initialized owl-prev disabled"></button>'), e.append('<button data-index="next" class="js-carousel-reviews-on-initialized owl-next"></button>'));
        var r = i.offset().left + i.outerWidth();
        a.slice(0, 2).each(function () {
            $(this).offset().left < r && $(this).find(".owl-lazy").Lazy({
                afterLoad: function (e) {
                    e.removeClass("owl-lazy")
                }
            })
        })
    }, sliderInteraction: function () {
        var e = this;
        $(".js-carousel-reviews-on-initialized").on("click", function () {
            e.reviewsSlider($(this).data("index"))
        }), checkTouchDevice() && e.reviewsBox.find(".owl-carousel").swipe({
            allowPageScroll: "auto",
            threshold: 20,
            swipe: function (e, i, t, a, s, r) {
                "left" == i ? reviews.reviewsSlider("next") : "right" == i && reviews.reviewsSlider("prev")
            }
        })
    }, reviewsSlider: function (e) {
        if (reviews.reviewsBox.hasClass("carousel-init")) return !1;
        var i = reviews.reviewsBox.find(".owl-carousel"), t = reviews.reviewsBox.find(".js-reviews-carousel-direction");
        i.owlCarousel({
            loop: !1,
            margin: 20,
            nav: !0,
            dots: !1,
            navText: ["", ""],
            navElement: "div",
            navContainer: t,
            responsive: {0: {items: 1}, 901: {items: 2}, 1201: {items: 3}},
            autoHeight: !0,
            lazyLoad: !0,
            onInitialize: function (e) {
                reviews.reviewsBox.find(".js-carousel-reviews-on-initialized").remove()
            },
            onInitialized: function (e) {
                reviews.reviewsBox.addClass("carousel-init")
            }
        }), e && ("prev" == e ? i.trigger("prev.owl.carousel") : "next" == e && i.trigger("next.owl.carousel"))
    }
}, itemList = {
    init: function () {
        this.changeViewList(), this.setNumProductsOnPage()
    }, changeViewList: function () {
        var t = this, a = {expires: 7, path: "/"}, s = $(".js-switch-product-view");
        $("body").on("click", ".js-switch-product-view", function () {
            var e = $(this), i = e.data("view");
            i && $.cookie("CategoryViewProducts", i, a), $(".js-filters.js-ajax form"), t.ajaxUpdateItemList(window.location.search), s.removeClass("selected"), e.addClass("selected")
        })
    }, setNumProductsOnPage: function () {
        $("#set-per-page").change(function () {
            var e = $(this).val();
            $.cookie("products_per_page", e, {expires: 7, path: "/"});
            e = window.location.href.replace(/(page=)\w+/g, "page=1");
            window.location.href = e
        })
    }, ajaxUpListForm: function (e) {
        var i = filter.createUrl(e);
        this.ajaxUpdateItemList(i, !0), filter.subcatFilterAdd(e)
    }, ajaxUpdateItemList: function (i, t) {
        var a = $(".js-product-ajax-list"), s = $(".js-product-ajax-list-loader");
        $(window).lazyLoad && $(window).lazyLoad("sleep"), a.html(""), s.show();
        var e = i.indexOf("?") < 0 ? i + "?_" : i;
        e += "&_=_" + (new Date).getTime() + Math.random(), $.get(e, function (e) {
            e = $("<div></div>").html(e);
            a.html(e.find(".js-product-ajax-list").html()), itemsViewList.images(a.find(".js-preview-products")), s.hide(), history.pushState && void 0 !== history.state && t && ("?" == i && (i = window.location.pathname), window.history.pushState({}, "", i)), $(window).lazyLoad && $(window).lazyLoad("reload"), $(window).on("popstate", function (e) {
                location.reload()
            }), productViewGrid.init(), void 0 !== $.autobadgeFrontend && $.autobadgeFrontend.reinit(), void 0 !== $.pluginprotilegallery && $.pluginprotilegallery.lazyload(), filter.smartfilterRefrCloseFilter()
        })
    }
}, brandsSlider = {
    brandsBox: $(".js-brands-carousel"), init: function () {
        var e = this;
        if (!e.brandsBox.length) return !1;
        e.prepareCarouselSliders(), e.sliderInteraction(), $(window).one("resize", e.brandsSlider)
    }, prepareCarouselSliders: function () {
        var e = this, i = e.brandsBox.find(".owl-carousel"), t = i.outerWidth(!0),
            a = e.brandsBox.find(".js-homepage-brands-el"), s = a.length;
        a.first().outerWidth(!0) * s > t && ((e = e.brandsBox.find(".js-homepage-brands-direction")).append('<button data-index="prev" class="js-carousel-brands-on-initialized owl-prev disabled"></button>'), e.append('<button data-index="next" class="js-carousel-brands-on-initialized owl-next"></button>'));
        var r = i.offset().left + i.outerWidth();
        a.slice(0, 6).each(function () {
            $(this).offset().left < r && $(this).find(".owl-lazy").Lazy({
                afterLoad: function (e) {
                    e.removeClass("owl-lazy")
                }
            })
        })
    }, sliderInteraction: function () {
        var e = this;
        $(".js-carousel-brands-on-initialized").on("click", function () {
            e.brandsSlider($(this).data("index"))
        }), checkTouchDevice() && e.brandsBox.find(".owl-carousel").swipe({
            allowPageScroll: "auto",
            threshold: 20,
            swipe: function (e, i, t, a, s, r) {
                "left" == i ? brandsSlider.brandsSlider("next") : "right" == i && brandsSlider.brandsSlider("prev")
            }
        })
    }, brandsSlider: function (e) {
        if (brandsSlider.brandsBox.hasClass("carousel-init")) return !1;
        var i = brandsSlider.brandsBox.find(".owl-carousel"),
            t = brandsSlider.brandsBox.find(".js-homepage-brands-direction");
        i.owlCarousel({
            loop: !1,
            margin: 0,
            nav: !0,
            navContainer: t,
            navText: ["", ""],
            lazyLoad: !0,
            dots: !1,
            responsive: {0: {items: 1}, 401: {items: 2}, 801: {items: 3}, 1001: {items: 4}, 1201: {items: 8}},
            onInitialize: function (e) {
                brandsSlider.brandsBox.find(".js-carousel-brands-on-initialized").remove()
            },
            onInitialized: function (e) {
                brandsSlider.brandsBox.addClass("carousel-init")
            }
        }), e && ("prev" == e ? i.trigger("prev.owl.carousel") : "next" == e && i.trigger("next.owl.carousel"))
    }
}, createCountDown = {
    init: function () {
        $(".js-promo-countdown").each(function () {
            var i = $(this), e = i.data("end"), t = i.data("day-text"), a = i.data("wrap");
            i.createCountDown(e, function (e) {
                a ? (i.find(".js-countdown-days").html(e.strftime("%D")), i.find(".js-countdown-hours").html(e.strftime("%H")), i.find(".js-countdown-minutes").html(e.strftime("%M")), i.find(".js-countdown-seconds").html(e.strftime("%S"))) : i.text(e.strftime("%D " + t + " %H:%M:%S"))
            })
        })
    }
}, filter = {
    init: function () {
        var e = this;
        e.showElem(), e.showMobFilter(), e.send(), e.rangeFilter($(".js-filters .js-filter-range")), e.showMoreFilter(), e.smartfilterRefrCloseFilter(), e.selectedFiltersClear(), e.initialFilterSubcat()
    }, showElem: function () {
        $(".js-filter-title").on("click", function () {
            var e = $(this).closest(".js-filter-el"), i = e.find(".js-filter-options");
            i.is(":visible") ? (i.slideUp(), e.addClass("close")) : (i.slideDown(), e.removeClass("close"))
        })
    }, showMobFilter: function () {
        var e = $(".js-mobile-filter-head"), i = $(".js-mobile-filter-body");
        e.on("click", function () {
            i.toggle(), e.hasClass("filter-open") ? e.removeClass("filter-open") : e.addClass("filter-open")
        })
    }, send: function () {
        $(".js-filters.js-ajax form input").change(function () {
            if (!$(this).closest('.filter__search').length){
                var e = $(this).closest("form");
                itemList.ajaxUpListForm(e.serializeArray())
            }
        }),
        $(".js-filters.js-ajax form").submit(function () {
            var e = $(this);
            return itemList.ajaxUpListForm(e.serializeArray()), !1
        })
    }, rangeFilter: function (e) {
        e.each(function () {
            if (!$(this).find(".js-slider-range").length) {
                $(this).append('<div class="js-slider-range"></div>');
                var a = $(this).find(".js-min"), s = $(this).find(".js-max"), e = parseFloat(a.attr("placeholder")),
                    i = parseFloat(s.attr("placeholder")), t = 1, r = $(this).find(".js-slider-range");
                if (r.data("step")) t = parseFloat(r.data("step")); else {
                    var n = i - e;
                    if (Math.round(e) != e || Math.round(i) != i) {
                        t = n / 10;
                        for (var o = 0; t < 1;) t *= 10, o += 1;
                        t = Math.pow(10, -o), (o = Math.round(1e5 * Math.abs(Math.round(e) - e)) / 1e5) && o < t && (t = o), (o = Math.round(1e5 * Math.abs(Math.round(i) - i)) / 1e5) && o < t && (t = o)
                    }
                }
                r.slider({
                    range: !0,
                    min: parseFloat(a.attr("placeholder")),
                    max: parseFloat(s.attr("placeholder")),
                    step: t,
                    values: [parseFloat(a.val().length ? a.val() : a.attr("placeholder")), parseFloat(s.val().length ? s.val() : s.attr("placeholder"))],
                    slide: function (e, i) {
                        var t = i.values[0] == $(this).slider("option", "min") ? "" : i.values[0];
                        a.val(t), t = i.values[1] == $(this).slider("option", "max") ? "" : i.values[1], s.val(t)
                    },
                    stop: function (e, i) {
                        a.change()
                    }
                }), a.add(s).change(function () {
                    var e = "" === a.val() ? r.slider("option", "min") : parseFloat(a.val()),
                        i = "" === s.val() ? r.slider("option", "max") : parseFloat(s.val());
                    e <= i && r.slider("option", "values", [e, i])
                })
            }
        })
    }, showMoreFilter: function () {
        $(".js-filter-options-show-all").click(function (e) {
            e.preventDefault();
            var i = $(this), e = i.closest(".js-filter-options").find(".js-filter-options-el");
            e.is(":hidden") ? (e.removeClass("hide"), i.addClass("open")) : (e.addClass("hide"), i.removeClass("open"))
        })
    }, smartfilterRefrCloseFilter: function () {
        void 0 !== $.smartfiltersTheme && setTimeout(function () {
            var e = $(".js-filter-options-show-all");
            $(".js-filters").data("smartfilters-hide-option") && e.each(function () {
                var i = $(this), t = i.hasClass("open"), a = parseInt(i.data("max-show-params")),
                    e = i.closest(".js-filter-el").find("label"), s = 0;
                a && (i.hide(), e.each(function () {
                    var e = $(this);
                    e.removeClass("js-filter-options-el").removeClass("hide"), e.hasClass("sf-label-disabled") && e.is(":hidden") || s++, a < s && (i.show(), e.addClass("js-filter-options-el"), t || e.addClass("hide"))
                }))
            }), $(".js-filter-body").find('input[type="checkbox"], input[type="radio"], .js-select').trigger("refresh")
        }, 500)
    }, selectedFiltersClear: function () {
        var i = this;
        $("body").on("click", ".js-filter-checked-remove", function () {
            var e = $(this).data("code");
            $(".js-filters").length && (i.clearFilterItem(e), itemList.ajaxUpListForm($(".js-filters form").serializeArray()))
        })
    }, clearFilterItem: function (e) {
        e = $(".js-filters").find(".js-filter-el[data-code='" + e + "']");
        e.find('input[type="checkbox"], input[type="radio"]').prop("checked", !1).trigger("refresh"),
        e.find('input[type="text"]').val(""),
        $(".js-filters .js-slider-range").remove(),
        this.rangeFilter($(".js-filters .js-filter-range"))
    }, subcatFilterAdd: function (e) {
        var i, t = $(".js-cat_sub"), a = t.data("save-filters"), s = t.data("save-filters-aliases"), r = e,
            t = t.find(".js-cat-link");
        a && (!s || 0 < (s = s.split(",")).length && (r = [], e.forEach(function (e) {
            0 <= s.indexOf(e.name.replace("[]", "")) && r.push(e)
        })), r.length ? (i = filter.createUrl(r), t.length && (i.length < 2 && (i = ""), t.each(function () {
            var e = (e = $(this).attr("href")).split("?")[0];
            $(this).attr("href", e + i)
        }))) : t.each(function () {
            var e = (e = $(this).attr("href")).split("?")[0];
            $(this).attr("href", e)
        }))
    }, initialFilterSubcat: function () {
        var e = $(".js-filters form");
        e.length && this.subcatFilterAdd(e.serializeArray())
    }, createUrl: function (e) {
        for (var i = [], t = 0; t < e.length; t++) "" !== e[t].value && i.push(e[t].name + "=" + e[t].value);
        return "?" + i.join("&")
    }
}, categories = {
    init: function () {
        this.sidebar()
    }, sidebar: function () {
        this.sbInit(), $(".js-subcat-open").click(function () {
            var e = $(this), i = $(e.parent().find(".js-subcat")[0]);
            i.is(":visible") ? (i.slideUp(), e.removeClass("selected")) : (i.slideDown(), e.addClass("selected"))
        })
    }, sbInit: function () {
        var e = $(".js-sidebar-cats").find("[data-is-open]").parents(".js-subcat");
        e.removeClass("hide"), e.each(function () {
            $(this).parent().find(".js-subcat-open").first().addClass("selected")
        })
    }
}, paginationLazyLoad = {
    init: function () {
        var e, r, i, t, n;
        !$.fn.lazyLoad || (e = $(".lazy-paging")).length && (r = parseInt(e.data("times"), 10), i = e.data("loading-str") || "Loading...", "1" == (t = e.find("li.selected")).children("a").text() && (e.hide(), (n = $(window)).lazyLoad("stop"), t.next().length && n.lazyLoad({
            container: ".js-product-ajax-list .js-cat-list", load: function () {
                n.lazyLoad("sleep");
                var t, a, s = $(".lazy-paging").hide(), e = s.find("li.selected").next().find("a").attr("href");
                e ? (t = $(".js-product-ajax-list .js-cat-list"), a = $('<div class="lazy-paging-loader"><i class="icon16 loading"></i>' + i + "</div>").insertAfter(".js-product-ajax-list"), $.get(e, function (e) {
                    var i = $("<div></div>").html(e);
                    itemsViewList.images(i.find(".js-preview-products")), t.append(i.find(".js-product-ajax-list .js-cat-list").children());
                    e = i.find(".lazy-paging").hide();
                    s.replaceWith(e), (s = e).find("li.selected").next().length ? !isNaN(r) && r <= 0 ? (n.lazyLoad("sleep"), $(".lazyloading-load-more").length || $('<a href="#" class="lazyloading-load-more">' + link_text + "</a>").insertAfter(s).click(function () {
                        return a.show(), r = 1, n.lazyLoad("wake"), n.lazyLoad("force"), !1
                    })) : n.lazyLoad("wake") : (n.lazyLoad("stop"), $(".lazyloading-load-more").hide()), productViewGrid.init(), void 0 !== $.autobadgeFrontend && $.autobadgeFrontend.reinit(), void 0 !== $.pluginprotilegallery && $.pluginprotilegallery.lazyload(), a.remove(), i.remove()
                })) : n.lazyLoad("stop")
            }
        })))
    }
}, categoryText = {
    moreDetails: function () {
            var i, t, a = $(".js-category-desc-wrap"), s = a.data("max-height"), r = $(".js-category-desc");
        $(".js-category-desc-more-wrap").remove(), a.removeClass("close"), a.length && s && a.css("max-height", s + "px"), a.length && r.length && a.outerHeight() < r.outerHeight() ? (i = a.data("text-more"), t = a.data("text-hide"), a.addClass("close"), a.after("<div class='js-category-desc-more-wrap category-desc-more-wrap'><span class='js-category-desc-more category-desc-more sdColor link-half'>" + i + "</span></div>"), $(".js-category-desc-more").on("click", function () {
            var e = $(this);
            e.hasClass("open") ? (e.removeClass("open"), a.addClass("close"), e.text(i), a.animate({maxHeight: s}, 500)) : (a.animate({maxHeight: r.outerHeight() + "px"}, 500), e.addClass("open"), a.removeClass("close"), e.text(t))
        })) : a.removeAttr("style")
    }
};
$(document).ready(function () {
    filter.init(), itemList.init(), paginationLazyLoad.init(), createCountDown.init(), brandsSlider.init(), categories.init(), categoryText.moreDetails(), reviews.init()
}), $(function () {
    void 0 === window.seofilterOnFilterSuccessCallbacks && (window.seofilterOnFilterSuccessCallbacks = []), window.seofilterOnFilterSuccessCallbacks.push(function () {
        $(".js-filter-body").find('input[type="checkbox"], input[type="radio"], .js-select').trigger("refresh"), categoryText.moreDetails()
    })
});
