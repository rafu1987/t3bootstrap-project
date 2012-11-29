# # Google Analytics [BEGIN]

page.jsFooterInline.100 = COA
page.jsFooterInline.100 {
    10 = TEXT
    10 {
        value (
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '{$t3bootstrap.googleAnalyticsCode}']);
            _gaq.push(['_trackPageview']);
        )
        stdWrap {
            insertData = 1
        }
    }
    20 = TEXT
    20 {
        value (
            (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();            
        )
    }
}

# # Google Analytics [END]