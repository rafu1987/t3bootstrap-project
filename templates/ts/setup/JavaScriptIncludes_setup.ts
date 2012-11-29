# # JavaScript Includes [BEGIN]

page {
    includeJSFooter {
        bootstrap = fileadmin/templates/default/bootstrap/js/bootstrap.min.js
        bootstrap_transition = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-transition.js
        bootstrap_alert = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-alert.js
        bootstrap_modal = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-modal.js
        bootstrap_dropdown = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-dropdown.js
        bootstrap_scrollspy = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-scrollspy.js
        bootstrap_tab = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-tab.js
        bootstrap_tooltip = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-tooltip.js
        bootstrap_popover = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-popover.js
        bootstrap_button = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-button.js
        bootstrap_collapse = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-collapse.js
        bootstrap_carousel = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-carousel.js
        bootstrap_typehead = fileadmin/templates/default/bootstrap/js/plugins/bootstrap-typeahead.js
        t3bootstrap = fileadmin/templates/default/js/t3bootstrap.min.js
        functions = fileadmin/templates/project/app/js/functions.js
    }
}

page.headerData.3 = TEXT
page.headerData.3 {
    value ( 
            <script type="text/javascript">var lang = {TSFE:sys_language_uid};</script>
        )
    insertData = 1
}

# # JavaScript Includes [END]