# # Include userFunc
includeLibs.t3bootstrap = fileadmin/classes/t3bootstrap/t3bootstrap.php

# # # # # # # # TEXT AND IMAGES FCE [BEGIN] # # # # # # # #

# # RTE [BEGIN]

lib.textpic.rte = TEXT
lib.textpic.rte {
    value = {field:field_text}
    parseFunc = < lib.parseFunc_RTE
    stdWrap {
        insertData = 1
    }
}

# # RTE [END]

# # Image [BEGIN]

lib.textpic.image = IMAGE
lib.textpic.image {
    file {
        import = uploads/tx_templavoila/
        import {
            current = 1
            listNum = 0
        }
        # # Width
        width.cObject = USER
        width.cObject {
            userFunc = user_t3bootstrap->imageWidth
            width.field = field_width
            height.field = field_height
            crop.field = field_crop
        }
        # # Height
        height.cObject = USER
        height.cObject {
            userFunc = user_t3bootstrap->imageHeight
            width.field = field_width
            height.field = field_height
            crop.field = field_crop
        }
    }
    # # Effect
    params.cObject = USER
    params.cObject {
        userFunc = user_t3bootstrap->imageEffect
        effect.field = field_effect
        blackwhite.field = field_blackwhite
    }

    stdWrap {
        # # Click enlarge (with lightbox)
        typolink {
            parameter {
                override {
                    cObject = USER
                    cObject {
                        userFunc = user_t3bootstrap->imageLink
                        file.data = TSFE:lastImageInfo|origFile
                        link.field = field_link
                        enlarge.field = field_enlarge
                    }
                }
            }
            ATagParams.cObject = USER
            ATagParams.cObject {
                userFunc = user_t3bootstrap->imageEnlarge
                enlarge.field = field_enlarge
                gallery.field = field_gallery
                link.field = field_link
                uid.data = register:tx_templavoila_pi1.parentRec.uid 
            }
        }
    }
}

# # Image [END]

# # Align [BEGIN]

lib.textpic.align = COA
lib.textpic.align {
    10 = TEXT
    10 {
        value = t3bootstrap-text-image-inner-image t3bootstrap-image-align-left
        stdWrap {
            if {
                equals = left
                value.field = field_align
            }
        }
    }
    20 = TEXT
    20 {
        value = t3bootstrap-text-image-inner-image t3bootstrap-image-align-middle
        stdWrap {
            if {
                equals = center
                value.field = field_align
            }
        }
    }
    30 = TEXT
    30 {
        value = t3bootstrap-text-image-inner-image t3bootstrap-image-align-right
        stdWrap {
            if {
                equals = right
                value.field = field_align
            }
        }
    }
}

# # Align [END]

# # Columns [BEGIN]

lib.textpic.cols = USER
lib.textpic.cols {
    userFunc = user_t3bootstrap->imageCols
    cols.field = field_cols
    uid.data = register:tx_templavoila_pi1.parentRec.uid
}

# # Columns [END]

# # Class [BEGIN]

lib.textpic.class = USER
lib.textpic.class {
    userFunc = user_t3bootstrap->imageClass
    uid.data = register:tx_templavoila_pi1.parentRec.uid
}

# # Class [END]

# # # # # # # # TEXT AND IMAGES FCE [BEGIN] # # # # # # # #

# # Button [BEGIN]

lib.button = TEXT
lib.button {
    current = 1
    stdWrap {
        wrap.cObject = USER
        wrap.cObject {
            userFunc = user_t3bootstrap->buttonAlign
            align.field = field_align
        }
        innerWrap.cObject = USER
        innerWrap.cObject {
            userFunc = user_t3bootstrap->buttonIcon
            icon.field = field_icon
            position.field = field_iconpos
            white.field = field_iconwhite
        }
        typolink {
            parameter.cObject = USER
            parameter.cObject {
                userFunc = user_t3bootstrap->buttonParameter
                parameter.field = field_link
                totop.field = field_totop
            }
            ATagParams.cObject = USER
            ATagParams.cObject {
                userFunc = user_t3bootstrap->buttonClasses
                style.field = field_style
                size.field = field_size
                block.field = field_block
                deactivate.field = field_deactivate
                custom.field = field_custom
                totop.field = field_totop
            }
        }
    }
}

# # Button [END]

# # Container [BEGIN]

lib.container = USER
lib.container {
    userFunc = user_t3bootstrap->containerClass
    background.field = field_background
    custom.field = field_custom
}

lib.containerstyle = USER
lib.containerstyle {
    userFunc = user_t3bootstrap->containerStyle
    custom.field = field_custom
}

# # Container [END]

# # 1 Column [BEGIN]

lib.1col = USER
lib.1col {
    userFunc = user_t3bootstrap->colSize
    size.field = field_size
    offset.field = field_offset
}

# # 1 Column [END]

# # 2 Columns [BEGIN]

lib.2col.left < lib.1col
lib.2col.left {
    size.field = field_leftsize
    offset.field = field_leftoffset
    test.field = field_test
}

lib.2col.right < lib.1col
lib.2col.right {
    size.field = field_rightsize
    offset.field = field_rightoffset
}

# # 2 Columns [END]

# # 3 Columns [BEGIN]

lib.3col.left < lib.1col
lib.3col.left {
    size.field = field_leftsize
    offset.field = field_leftoffset
}

lib.3col.middle < lib.1col
lib.3col.middle {
    size.field = field_middlesize
    offset.field = field_middleoffset
}

lib.3col.right < lib.1col
lib.3col.right {
    size.field = field_rightsize
    offset.field = field_rightoffset
}

# # 3 Columns [END]

# # 4 Columns [BEGIN]

lib.4col.left < lib.1col
lib.4col.left {
    size.field = field_leftsize
    offset.field = field_leftoffset
}

lib.4col.leftmiddle < lib.1col
lib.4col.leftmiddle {
    size.field = field_leftmiddlesize
    offset.field = field_leftmiddleoffset
}

lib.4col.rightmiddle < lib.1col
lib.4col.rightmiddle {
    size.field = field_rightmiddlesize
    offset.field = field_rightmiddleoffset
}

lib.4col.right < lib.1col
lib.4col.right {
    size.field = field_rightsize
    offset.field = field_rightoffset
}

# # 4 Columns [END]

# # # # # # # # CAROUSEL FCE [BEGIN] # # # # # # # #

# # Image [BEGIN]

lib.carousel.image = IMAGE
lib.carousel.image {
    file {
        import = uploads/tx_templavoila/
        import.current = 1
        import.listNum = 0
        width.cObject = USER
        width.cObject {
            userFunc = user_t3bootstrap->carouselWidth
            uid.data = register:tx_templavoila_pi1.parentRec.uid
        }
    }
}

# # Image [END]

# # Item [BEGIN]

lib.carousel.item = USER
lib.carousel.item {
    userFunc = user_t3bootstrap->carouselItem
    sectionPos.data = TSFE:register|tx_templavoila_pi1.sectionPos
}

# # Item [END]

# # Headline [BEGIN]

lib.carousel.headline = TEXT
lib.carousel.headline {
    stdWrap {
        wrap.cObject = USER
        wrap.cObject {
            userFunc = user_t3bootstrap->carouselHeadline
            headline.field = field_title
            text.field = field_desc
        }
    }
}

# # Headline [END]

# # Text [BEGIN]

lib.carousel.text = TEXT
lib.carousel.text {
    wrap.cObject = USER
    wrap.cObject {
        userFunc = user_t3bootstrap->carouselText
        headline.field = field_title
        text.field = field_desc
        text.parseFunc < lib.parseFunc_RTE
    }
}

# # Text [END]

# # ID [BEGIN]

lib.carousel.href = USER
lib.carousel.href {
    userFunc = user_t3bootstrap->carouselID
    uid.data = register:tx_templavoila_pi1.parentRec.uid 
}

lib.carousel.href2 = USER
lib.carousel.href2 {
    userFunc = user_t3bootstrap->carouselID
    uid.data = register:tx_templavoila_pi1.parentRec.uid 
    control = 1
}

# # ID [END]

# # JS [BEGIN]

lib.carousel.js = USER
lib.carousel.js {
    userFunc = user_t3bootstrap->carouselJS
    uid.data = register:tx_templavoila_pi1.parentRec.uid 
    interval.field = field_interval
    pause.field = field_pause
}

# # JS [END]

# # # # # # # # CAROUSEL FCE [END] # # # # # # # #

# # # # # # # # SPACER FCE [END] # # # # # # # #

lib.spacer.style = USER
lib.spacer.style {
    userFunc = user_t3bootstrap->spacerStyle
    top.field = field_top
    bottom.field = field_bottom
    custom.field = field_customcolor
}

lib.spacer.class = USER
lib.spacer.class {
    userFunc = user_t3bootstrap->spacerClass
    color.field = field_color
}

# # # # # # # # SPACER FCE [END] # # # # # # # #