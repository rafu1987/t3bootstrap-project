# # Indexed Search [BEGIN]

includeLibs.pagetitle >

page.config.index_enable = 1
plugin.tx_indexedsearch {
    templateFile = fileadmin/templates/default/templates/search/t3bootstrap-indexed-search.tmpl
    _CSS_DEFAULT_STYLE = 
    pageTitleAdd =
    pageTitlePage = Seite
    _DEFAULT_PI_VARS.results = 10
}

plugin.tx_indexedsearch {
    browseBoxWrap = <div class="browsebox">|</div>
    pageWrap = <span>|</span>
    activePageWrap = <span><strong>|</strong></span>
    _LOCAL_LANG.default {
        pi_list_browseresults_prev = &laquo;
        pi_list_browseresults_page =
        pi_list_browseresults_next = &raquo;
    } 
    _LOCAL_LANG.de {
        pi_list_browseresults_prev = &laquo;
        pi_list_browseresults_page =
        pi_list_browseresults_next = &raquo;
    }
} 

# # Indexed Search [END]

# # Searchbox [BEGIN]

plugin.tx_macinasearchbox_pi1 {
    pidSearchpage = 34
    templateFile = fileadmin/templates/default/templates/search/t3bootstrap-searchbox-de.html
}

# # English
[globalVar = GP:L = 1]

plugin.tx_macinasearchbox_pi1 {
    templateFile = fileadmin/templates/default/templates/search/t3bootstrap-searchbox-en.html
}

[global]

lib.suche < plugin.tx_macinasearchbox_pi1

# # Searchbox [END]