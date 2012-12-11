# # Less [BEGIN]

plugin.tx_t3less {
    phpcompiler {
        filesettings {
            t3bootstrap {
                compress = 1
                excludeFromConcatenation = 0
                sortOrder = 10
            }
            t3bootstrap-responsive {
                compress = 1
                excludeFromConcatenation = 0
                sortOrder = 20
            }
            styles {
                compress = 1
                excludeFromConcatenation = 0
                sortOrder = 30
            }
            # # Add more CSS files
        }
    }
}

# # Less [END]

# # RealURL [BEGIN]

config {
  simulateStaticDocuments = 0
  simulateStaticDocuments_pEnc= md5
  simulateStaticDocuments_pEnc_onlyP = L
  prefixLocalAnchors = all
  tx_realurl_enable = 1
}

# # RealURL [END]

# # fancybox [BEGIN]

page.jsFooterInline.800 = COA
page.jsFooterInline.800 {
	wrap = (function($) {$(document).ready(function() { $('a[class*=lightbox]').fancybox({|});  });})(jQuery);
    10 >
    10 = TEXT
    10.value (
        'padding' : {$plugin.cljqueryfancybox.padding},
        'margin' : {$plugin.cljqueryfancybox.margin},
        'width' : {$plugin.cljqueryfancybox.width},
        'height' : {$plugin.cljqueryfancybox.height},
        'minWidth' : {$plugin.cljqueryfancybox.minWidth},
        'minHeight' : {$plugin.cljqueryfancybox.minHeight},
        'maxWidth' : {$plugin.cljqueryfancybox.maxWidth},
        'maxHeight' : {$plugin.cljqueryfancybox.maxHeight},
        'autoSize' : {$plugin.cljqueryfancybox.autoSize},
        'fitToView' : {$plugin.cljqueryfancybox.fitToView},
        'aspectRatio' : {$plugin.cljqueryfancybox.aspectRatio},
        'topRatio' : {$plugin.cljqueryfancybox.topRatio},
        'fixed' : {$plugin.cljqueryfancybox.fixed},
        'scrolling' : '{$plugin.cljqueryfancybox.scrolling}',
        'wrapCSS' : '{$plugin.cljqueryfancybox.wrapCSS}',
        'arrows' : {$plugin.cljqueryfancybox.arrows},
        'closeBtn' : {$plugin.cljqueryfancybox.closeBtn},
        'closeClick' : {$plugin.cljqueryfancybox.closeClick},
        'nextClick' : {$plugin.cljqueryfancybox.nextClick},
        'mouseWheel' : {$plugin.cljqueryfancybox.mouseWheel},
        'loop' : {$plugin.cljqueryfancybox.loop},
        'modal' : {$plugin.cljqueryfancybox.modal},
        'autoPlay' : {$plugin.cljqueryfancybox.autoPlay},
        'playSpeed' : {$plugin.cljqueryfancybox.playSpeed},
        'index' : {$plugin.cljqueryfancybox.index},
        'type' : {$plugin.cljqueryfancybox.type},
        'href' : {$plugin.cljqueryfancybox.href},
        'content' : {$plugin.cljqueryfancybox.content},
        'openEffect' : '{$plugin.cljqueryfancybox.openEffect}',
        'closeEffect' : '{$plugin.cljqueryfancybox.closeEffect}',
        'nextEffect' : '{$plugin.cljqueryfancybox.nextEffect}',
        'prevEffect' : '{$plugin.cljqueryfancybox.prevEffect}',
        'openSpeed' : {$plugin.cljqueryfancybox.openSpeed},
        'closeSpeed' : {$plugin.cljqueryfancybox.closeSpeed},
        'nextSpeed' : {$plugin.cljqueryfancybox.nextSpeed},
        'prevSpeed' : {$plugin.cljqueryfancybox.prevSpeed},
        'openEasing' : '{$plugin.cljqueryfancybox.openEasing}',
        'closeEasing' : '{$plugin.cljqueryfancybox.closeEasing}',
        'nextEasing' : '{$plugin.cljqueryfancybox.nextEasing}',
        'prevEasing' : '{$plugin.cljqueryfancybox.prevEasing}',
        'openOpacity' : {$plugin.cljqueryfancybox.openOpacity},
        'closeOpacity' : {$plugin.cljqueryfancybox.closeOpacity},
        'openMethod' : '{$plugin.cljqueryfancybox.openMethod}',
        'closeMethod' : '{$plugin.cljqueryfancybox.closeMethod}',
        'nextMethod' : '{$plugin.cljqueryfancybox.nextMethod}',
        'prevMethod' : '{$plugin.cljqueryfancybox.prevMethod}',
        'groupAttr' : 'data-fancybox-group',
        'beforeShow' : function(opts) {
                this.title = (jQuery(this.group[this.index]).attr('title') != undefined ? jQuery(this.group[this.index]).attr('title') : jQuery(this.group[this.index]).find('img').attr('title'));
                $.fancybox.wrap.bind("contextmenu", function (e) {
                    return false; 
                });
        }
    )
  20 = TEXT
  20.value (
    ,tpl: {
      closeBtn: '<div title="Schließen" class="fancybox-item fancybox-close"></div>',
      next: '<a title="Nächstes" class="fancybox-nav fancybox-next"><span></span></a>',
      prev: '<a title="Voriges" class="fancybox-nav fancybox-prev"><span></span></a>'
    }
  )
}

# # English
[globalVar = GP:L = 1]

page.jsFooterInline.800 = COA
page.jsFooterInline.800 {
  20 = TEXT
  20.value (
    ,tpl: {
      closeBtn: '<div title="Close" class="fancybox-item fancybox-close"></div>',
      next: '<a title="Next" class="fancybox-nav fancybox-next"><span></span></a>',
      prev: '<a title="Previous" class="fancybox-nav fancybox-prev"><span></span></a>'
    }
  )
}

[global]

# # fancybox [END]

# # powermail [BEGIN]

page {
    includeJSFooterlibs {
        powermailJQuery >
        powermailJQueryUi >
        #powermailJQueryTabs >
    }
    includeCSS {
    	powermailJQueryUiTheme >
    }
}

# # Custom settings
plugin.tx_powermail {
    settings {
        setup {
            main {
                submitClass = {$t3bootstrap.powermailSubmitClasses}
            }
        }
    }
}

# # powermail [END]

# # facebook OG Tags [BEGIN]

plugin.tx_in2facebook.settings.image.20.file.20 >

# # facebook OG Tags [END]

# # News [BEGIN]

plugin.tx_news {
    settings {
        buttonBackClass = {$t3bootstrap.newsButtonBackClass}
        buttonMoreClass = {$t3bootstrap.newsButtonMoreClass}
        cropMaxCharacters = 250
        opengraph {
            site_name = {$t3bootstrap.fbOGSitename}
            admins = {$t3bootstrap.fbAdmins}
        }
        list {
            media {
                image {
                    maxWidth = 170c
                    maxHeight = 114c
                }
            }
        }
        detail {
            media {
                image {
                    lightbox = lightbox
                    maxWidth = 270c
                }
            }
        }
    }
}

# # News [END]

# # TemplaVoilà [BEGIN]

plugin {
    tx_templavoila_pi1 {
        disableExplosivePreview = 1
        TSconst {
            usr_BASEDOMAINDE = {$t3bootstrap.basedomain.de}
            usr_BASEDOMAINEN = {$t3bootstrap.basedomain.en}
        }
    }
}

# # TemplaVoilà [END]

# # Google Maps [BEGIN]

plugin.tx_rzgooglemaps2_pi1 {
    googleMapsSubmitClasses = {$t3bootstrap.googleMapsSubmitClasses}
}

# # Google Maps [END]

# # SEO [BEGIN]

page.1001 < plugin.tx_odsseo_pi1

# # SEO [END]