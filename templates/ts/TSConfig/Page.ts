# # Creation Mask [BEGIN]

TCEMAIN.permissions {
	# # Editor user group
        groupid = 3
	user = show,editcontent,edit,delete,new
	group = show,editcontent,edit,delete,new
	everybody = show,editcontent,edit,delete,new
}

# # Creation Mask [END]

# # Default Language [BEGIN]

mod.SHARED.defaultLanguageFlag = de

# # Default Language [END]      

# # Hide fields [BEGIN]

TCEFORM {
  tt_content {
    sectionIndex {
      disabled = 1
    }
    date {
      disabled = 1
    }
    header_link {
      disabled = 1
    }
    layout {
      disabled = 1
    }
    section_frame {
      disabled = 1
    }
    linkToTop {
      disabled = 1
    }
    longdescURL {
      disabled = 1
    }
    imagecaption {
      disabled = 1
    }
    image_compression {
      disabled = 1
    }
    image_effects {
      disabled = 1
    }
    imagecaption_position {
      disabled = 1
    }
    image_noRows {
      disabled = 1
    }
    imageborder {
      disabled = 1
    }
    subheader {
        disabled = 1
    }
    colPos {
        disabled = 1
    }
    rte_enabled {
        disabled = 1
    }
    spaceBefore {
        disabled = 1
    }
    spaceAfter {
        disabled = 1
    }
  }
  pages {
    tx_in2facebook_url {
        disabled = 1
    }
  }
  pages_language_overlay {
    media {
      disabled = 1
    }
  }
}
    
# # Hide fields [END]

# # Show TemplaVoilà Tabs and extra header content element [BEGIN]

templavoila.wizards.newContentElement {
    renderMode = tabs
    wizardItems {
        common {
            elements {
                header {
                    icon = gfx/c_wiz/regular_text.gif
                    title = Header
                    description = Fügt eine Überschrift hinzu.
                    tt_content_defValues {
                        CType = header
                    }
                }
            }
            show := addToList(header)
        }
    }
}

# # Show TemplaVoilà Tabs and extra header content element [BEGIN]

# # Show delete icon for content elements in page view [BEGIN]

mod {
    web_txtemplavoilaM1 {
        enableDeleteIconForLocalElements = 1
    }
}

# # Show delete icon for content elements in page view [END]

# # RTE Configuration [BEGIN]

RTE.default {
        # # CSS file
        contentCSS = fileadmin/templates/project/app/css/rte.css

        ## Markup options
        enableWordClean = 1
        removeTrailingBR = 1
        removeComments = 1
        removeTags = center, sdfield
        removeTagsAndContents = style,script

        # Buttons die gezeigt/versteckt werden
        showButtons = textstyle, textstylelabel, blockstyle, blockstylelabel, bold, italic, orderedlist, unorderedlist, insertcharacter, chMode, link, image, removeformat, toggleborders, tableproperties, subscript, superscript, rowproperties, rowinsertabove, rowinsertunder, rowdelete, rowsplit, columninsertbefore, columninsertafter, columndelete, columnsplit, cellproperties, cellinsertbefore, cellinsertafter, celldelete, cellsplit, cellmerge, insertcharacter, undo, redo, left, center, right
        hideButtons = fontstyle, formatblock, strikethrough,lefttoright, righttoleft, textcolor, bgcolor, textindicator, line, underline, emoticon, user, spellcheck, inserttag, justifyfull, acronym, table, copy, cut, paste, about, showhelp, outdent, indent, findreplace

        # Hält die RTE Icons gegroupt zusammen
        keepButtonGroupTogether = 1

        # blendet Statusbar in htmlarea aus
        showStatusBar =  0

        proc {
                # tags die erlaubt / verboten sind
                allowTags = table, tbody, tr, th, td, h1, h2, h3, h4, h5, h6, div, p, br, span, ul, ol, li, re, blockquote, strong, em, b, i, u, sub, sup, strike, a, img, nobr, hr, tt, q, cite, abbr, acronym, center
                denyTags = font

                # br wird nicht zu p konvertiert
                dontConvBRtoParagraph = 1

                # tags sind erlaubt außerhalt von p, div
                allowTagsOutside = img,hr

                # erlaubte attribute in p, div tags
                keepPDIVattribs = align,class,style,id

                # List all class selectors that are allowed on the way to the database
                allowedClasses (
                        muted,text-warning,text-error,text-info,text-success
                )       

                # html parser einstellungen
                HTMLparser_rte {

                        # tags die erlaubt/verboten sind
                        allowTags < RTE.default.proc.allowTags
                        denyTags < RTE.default.proc.denyTags

                        # tags die untersagt sind
                        removeTags = font

                        # entfernt html-kommentare
                        removeComments = 1

                        # tags die nicht übereinstimmen werden nicht entfernt (protect / 1 / 0)
                        keepNonMatchedTags = 0
                }


                # Content to database
                entryHTMLparser_db = 1
                entryHTMLparser_db {

                        # tags die erlaubt/verboten sind
                        allowTags < RTE.default.proc.allowTags
                        denyTags < RTE.default.proc.denyTags

                        # CLEAN TAGS
                        noAttrib = b, i, u, strike, sub, sup, strong, em, quote, blockquote, cite, tt, br, center

                        rmTagIfNoAttrib = span,div,font

                        # htmlSpecialChars = 1
       
                        ## align attribute werden erlaubt
                        tags {
                                p.fixAttrib.align.unset >
                                p.allowedAttribs = class,style,align

                                div.fixAttrib.align.unset >

                                hr.allowedAttribs = class

                                # b und i tags werden ersetzt (em / strong)
                                b.remap = strong
                                i.remap = em

                                ## img tags werden erlaubt
                                img >
                        }
                }

        }

        # Classes: Ausrichtung
        buttons.blockstyle.tags.div.allowedClasses (
                align-left, align-center, align-right
        )

        # Classes: Eigene Stile
        buttons.textstyle.tags.span.allowedClasses = muted,text-warning,text-error,text-info,text-success
        buttons.image.properties.class.allowedClasses= rte_image


        # Classes für Links (These classes should also be in the list of allowedClasses)
        #classesAnchor = 
        buttons.link.properties.class.allowedClasses = 
        
        classesAnchor.default {
                page = 
                url = 
                file = 
                mail = 
        }

        # zeigt alle CSS-Klassen die in rte.css vorhanden sind
        #showTagFreeClasses = 1
        buttons.blockstyle.showTagFreeClasses = 1
        buttons.textstyle.showTagFreeClasses = 1

        # Do not allow insertion of the following tags
        hideTags = font

        # Tabellen Optionen in der RTE Toolbar
        hideTableOperationsInToolbar = 0
        keepToggleBordersInToolbar = 1

        # Tabellen Editierungs-Optionen (cellspacing/ cellpadding / border)
        disableSpacingFieldsetInTableOperations = 1
        disableAlignmentFieldsetInTableOperations=1
        disableColorFieldsetInTableOperations=1
        disableLayoutFieldsetInTableOperations=1
        disableBordersFieldsetInTableOperations=0
}

# Use same processing as on entry to database to clean content pasted into the editor
RTE.default.enableWordClean.HTMLparser < RTE.default.proc.entryHTMLparser_db

# FE RTE configuration (htmlArea RTE only)
RTE.default.FE < RTE.default
RTE.default.FE.userElements >
RTE.default.FE.userLinks >

# Breite des RTE in Fullscreen-Ansicht
TCEFORM.tt_content.bodytext.RTEfullScreenWidth= 80%

# # RTE Configuration [END]

# # Hide content elements [BEGIN]

TCEFORM.tt_content.CType.removeItems = div,rte,splash,table,media,search,mailform,multimedia,script,textpic,bullets,uploads,image

# # Hide content elements [END]