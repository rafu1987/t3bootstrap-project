# Limitation of picture widths in main content column:   
styles.content.imgtext.maxW = 1170     

# Setting link targets to nothing (no frames mode)    
PAGE_TARGET =     
content.pageFrameObj =  

# Making sure each line in image captions (of Image content elements) are applied to corresponding image number in the element:    
styles.content.imgtext.captionSplit = 1  
 
# # Custom settings [BEGIN]

t3bootstrap {
    # cat=t3bootstrap/t3b/010; type=string; label=Basedomain default language: The base domain for the default language
    basedomain.de = http://typo3.localhost:8888/  

    # cat=t3bootstrap/t3b/020; type=string; label=Basedomain english: The base domain for the english page
    basedomain.en = http://typo3.localhost:8888/   

    # cat=t3bootstrap/t3b/30; type=string; label=Google Analytics UA Code: The UA code from Google Analytics
    googleAnalyticsCode = UA-XXXXXXXX-Y

    # cat=t3bootstrap/t3b/40; type=string; label=Powermail Submit CSS Class: One or more CSS classes for the powermail submit button
    powermailSubmitClasses = btn btn-primary

    # cat=t3bootstrap/t3b/50; type=string; label=News back button CSS Class: One or more CSS classes for the news back button
    newsButtonBackClass = btn btn-primary

    # cat=t3bootstrap/t3b/60; type=string; label=News more button CSS Class: One or more CSS classes for the news more button
    newsButtonMoreClass = btn btn-primary

    # cat=t3bootstrap/t3b/50; type=string; label=Google Maps Submit CSS Class: One or more CSS classes for the Google Maps submit button
    googleMapsSubmitClasses = btn btn-primary   

    # cat=t3bootstrap/t3b/70; type=string; label=Facebook OG site name: For the news detail pages
    fbOGSitename = Project

    # cat=t3bootstrap/t3b/80; type=string; label=Facebook OG admins: For the news detail pages
    fbAdmins = 100000096245580
}

# # Custom settings [END]