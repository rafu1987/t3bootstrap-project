# # Less [BEGIN]

plugin.tx_t3less {
    files {
        pathToLessFiles = fileadmin/templates/default/less/
        outputFolder = fileadmin/templates/default/css/
    }
}

# # Less [END]

# # sourceopt [BEGIN]

sourceopt.formatHtml.tabSize = 2

# # sourceopt [END]

# # scriptmerger [BEGIN]

plugin.tx_scriptmerger {
	css {
		compress {
			enable = 1
		}
	}
    javascript {
        enable = 1
        parseBody = 1
        doNotRemoveInDocInBody = 0
        minify {
            enable = 1
            ignore = \?,jquery-
        }
        compress {
            enable = 1
            ignore = \?,jquery-
        }
        merge {
            enable = 1
            ignore = \?,jquery-
        }
    }
}

# # scriptmerger [END]

# # powermail [BEGIN]

plugin.tx_powermail {
    view {
        templateRootPath = fileadmin/templates/default/templates/powermail/Templates/
        partialRootPath = fileadmin/templates/default/templates/powermail/Partials/
        layoutRootPath = fileadmin/templates/default/templates/powermail/Layouts/
    }
}

# # powermail [END]

# # News [BEGIN]

plugin.tx_news {
    view {
        layoutRootPath = fileadmin/templates/default/templates/news/Layouts/
        partialRootPath = fileadmin/templates/default/templates/news/Partials/
        templateRootPath = fileadmin/templates/default/templates/news/Templates/
    }
}

# # News [END]