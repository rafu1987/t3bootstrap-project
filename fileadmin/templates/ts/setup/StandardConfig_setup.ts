# # Config [BEGIN]

config {
    admPanel = 1
    headerComment = 
    typolinkLinkAccessRestrictedPages = 1
    removeDefaultJS = 1
    inlineStyle2TempFile = 1
    spamProtectEmailAddresses = 0
    doctype = html5
    xmlprologue = none
    xhtml_cleaning = all
    stdWrap.htmlSpecialChars = 1
    disablePrefixComment = 1
}

# # Config [END]

# # Meta tags [BEGIN]

page.headerData.1 = COA
page.headerData.1 {
    10 = TEXT
    10 {
        value = <meta name="viewport" content="width=device-width, initial-scale=1.0">
    }
    20 = TEXT
    20 {
        value = <meta http-equiv="X-UA-Compatible" content="IE=edge">
    }
}

page.headerData.555 = TEXT
page.meta.description.field = description

# # Meta tags [END]

# # Favicon [BEGIN]

page.shortcutIcon = fileadmin/templates/project/images/icons/favicon.ico

# # Favicon [END]

# # Page [BEGIN]

page = PAGE
page.typeNum = 0
page.10 = USER
page.10 {
    userFunc = tx_templavoila_pi1->main_page
    disableExplosivePreview = 1
}

# # Page [END]

# # Robots [BEGIN]

page.meta.robots = noindex, nofollow

# # Robots [END]