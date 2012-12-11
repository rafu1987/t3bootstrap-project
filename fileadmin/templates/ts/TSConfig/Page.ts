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
    pi_flexform {
    	jfmulticontent_pi1 {
    		general {
    			tabCollapsible {
    				disabled = 1
    			}
    			tabOpen {
    				disabled = 1
    			}
    			tabRandomContent {
    				diabled = 1
    			}
    			tabEvent {
    				disabled = 1
    			}
    			tabHeightStyle {
    				disabled = 1
    			}
    			tabCookieExpires {
    				disabled = 1
    			}
    			tabRandomContent {
    				disabled = 1
    			}
    			tabCookieRoot {
    				disabled = 1
    			}
    			tabHideEffect {
    				disabled = 1
    			}
    			tabHideTransition {
    				disabled = 1
    			}
    			tabHideTransitiondir {
    				disabled = 1
    			}
    			tabHideTransitionduration {
    				disabled = 1
    			}
    			tabShowEffect {
    				disabled = 1
    			}
    			tabShowTransition {
    				disabled = 1
    			}
    			tabShowTransitiondir {
    				disabled = 1
    			}
    			tabShowTransitionduration {
    				disabled = 1
    			}
    		}
    		attribute {
    			attributes {
    				disabled = 1
    			}
    		}
    		special {
    			options {
    				disabled = 1
    			}
    			optionsOverride {
    				disabled = 1
    			}
    		}
    	}
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

        # Buttons to show/hide
        showButtons = textstyle, textstylelabel, blockstyle, blockstylelabel, bold, italic, orderedlist, unorderedlist, insertcharacter, chMode, link, image, removeformat, toggleborders, tableproperties, subscript, superscript, rowproperties, rowinsertabove, rowinsertunder, rowdelete, rowsplit, columninsertbefore, columninsertafter, columndelete, columnsplit, cellproperties, cellinsertbefore, cellinsertafter, celldelete, cellsplit, cellmerge, insertcharacter, undo, redo, left, center, right
        hideButtons = fontstyle, formatblock, strikethrough,lefttoright, righttoleft, textcolor, bgcolor, textindicator, line, underline, emoticon, user, spellcheck, inserttag, justifyfull, acronym, table, copy, cut, paste, about, showhelp, outdent, indent, findreplace

        # Group RTE Icons
        keepButtonGroupTogether = 1

        # Hide status bar
        showStatusBar =  0

        proc {
                # Allowed / Denied tags
                allowTags = table, tbody, tr, th, td, h1, h2, h3, h4, h5, h6, div, p, br, span, ul, ol, li, re, blockquote, strong, em, b, i, u, sub, sup, strike, a, img, nobr, hr, tt, q, cite, abbr, acronym, center
                denyTags = font

                # Don't convert <br> to <p>
                dontConvBRtoParagraph = 1

                # Tags allowed outside <p> and <div>
                allowTagsOutside = img,hr

                # Attributes allowed in <p> and <div>
                keepPDIVattribs = align,class,style,id

                # List all class selectors that are allowed on the way to the database
                allowedClasses (
                        muted,text-warning,text-error,text-info,text-success
                )       

                # html parser settings
                HTMLparser_rte {

                        # Allowed / Denied tags
                        allowTags < RTE.default.proc.allowTags
                        denyTags < RTE.default.proc.denyTags

                        # Remove tags
                        removeTags = font

                        # Remove html comments
                        removeComments = 1

                        # Tags that don't match, won't be removed
                        keepNonMatchedTags = 0
                }


                # Content to database
                entryHTMLparser_db = 1
                entryHTMLparser_db {

                        # Allowed / Denied tags
                        allowTags < RTE.default.proc.allowTags
                        denyTags < RTE.default.proc.denyTags

                        # CLEAN TAGS
                        noAttrib = b, i, u, strike, sub, sup, strong, em, quote, blockquote, cite, tt, br, center

                        rmTagIfNoAttrib = span,div,font

                        # htmlSpecialChars = 1
       
                        ## Align attributes are allowed
                        tags {
                                p.fixAttrib.align.unset >
                                p.allowedAttribs = class,style,align

                                div.fixAttrib.align.unset >

                                hr.allowedAttribs = class

                                # Convert <b> and <i> to <strong> and <em>
                                b.remap = strong
                                i.remap = em

                                ## Allow img tags
                                img >
                        }
                }

        }

        # Classes: Alignment
        buttons.blockstyle.tags.div.allowedClasses (
                align-left, align-center, align-right
        )

        # Classes: Own styles
        buttons.textstyle.tags.span.allowedClasses = muted,text-warning,text-error,text-info,text-success
        buttons.image.properties.class.allowedClasses= rte_image


        # Classes for Links (These classes should also be in the list of allowedClasses)
        #classesAnchor = 
        buttons.link.properties.class.allowedClasses = 
        
        classesAnchor.default {
                page = 
                url = 
                file = 
                mail = 
        }

        #showTagFreeClasses = 1
        buttons.blockstyle.showTagFreeClasses = 1
        buttons.textstyle.showTagFreeClasses = 1

        # Do not allow insertion of the following tags
        hideTags = font

        # Table options in RTE toolbar
        hideTableOperationsInToolbar = 0
        keepToggleBordersInToolbar = 1

        # Table editing
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

# Width of the RTE in fullscreen mode
TCEFORM.tt_content.bodytext.RTEfullScreenWidth= 80%

# # RTE Configuration [END]

# # Hide content elements [BEGIN]

TCEFORM.tt_content.CType.removeItems = div,rte,splash,table,search,mailform,multimedia,script,textpic,bullets,uploads,image

# # Hide content elements [END]