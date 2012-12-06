Ext.onReady(function() {
    Ext.apply(TYPO3.Components.PageTree.Actions, {
        hideInMenu: function(node, tree) { 
            TYPO3.hideShow.Menue.hidePageInMenu(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },
        showInMenu: function(node, tree) { 
            TYPO3.hideShow.Menue.showPageInMenu(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },  
        standardPage: function(node, tree) { 
            TYPO3.hideShow.Menue.standardPage(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },  
        backendPage: function(node, tree) { 
            TYPO3.hideShow.Menue.backendPage(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },  
        shortcutPage: function(node, tree) { 
            TYPO3.hideShow.Menue.shortcutPage(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },  
        mountPage: function(node, tree) { 
            TYPO3.hideShow.Menue.mountPage(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },  
        urlPage: function(node, tree) { 
            TYPO3.hideShow.Menue.urlPage(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },            
        storageFolder: function(node, tree) { 
            TYPO3.hideShow.Menue.storageFolder(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        }, 
        trashPage: function(node, tree) { 
            TYPO3.hideShow.Menue.trashPage(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        },    
        menuPage: function(node, tree) { 
            TYPO3.hideShow.Menue.menuPage(node.attributes.nodeData, function(response) {
                this.updateNode(node, node.isExpanded(), response);
            }, this);
        }        
    });
});