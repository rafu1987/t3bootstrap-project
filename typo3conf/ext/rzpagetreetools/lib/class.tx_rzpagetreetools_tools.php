<?php

class tx_rzpagetreetools_tools {
    public function hidePageInMenu($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['nav_hide'] = 1;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    }
    
    public function showPageInMenu($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['nav_hide'] = 0;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    }  
    
    public function standardPage($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 1;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    } 
    
    public function backendPage($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 6;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    }          
    
    public function shortcutPage($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 4;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    } 
    
    public function mountPage($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 7;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    }   
    
    public function urlPage($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 3;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    } 
    
    public function storageFolder($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 254;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    }  
    
    public function trashPage($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 255;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    }  
    
    public function menuPage($nodeData) {
        /** @var $node t3lib_tree_pagetree_Node */
        $node = t3lib_div::makeInstance('t3lib_tree_pagetree_Node', (array) $nodeData);

        /** @var $dataProvider t3lib_tree_pagetree_DataProvider */
        $dataProvider = t3lib_div::makeInstance('t3lib_tree_pagetree_DataProvider');

        try {
            $data['pages'][$node->getWorkspaceId()]['doktype'] = 199;
            self::processTceCmdAndDataMap(array(), $data);
            
            $newNode = t3lib_tree_pagetree_Commands::getNode($node->getId());
            $newNode->setLeaf($node->isLeafNode());
            $returnValue = $newNode->toArray();
        } catch (Exception $exception) {
            $returnValue = array(
                'success' => FALSE,
                'message' => $exception->getMessage(),
            );
        }

        return $returnValue;
    }      

    protected static function processTceCmdAndDataMap(array $cmd, array $data = array()) {
        $tce = t3lib_div::makeInstance('t3lib_TCEmain');
        $tce->stripslashes_values = 0;
        $tce->start($data, $cmd);
        $tce->copyTree = t3lib_div::intInRange($GLOBALS['BE_USER']->uc['copyLevels'], 0, 100);

        if (count($cmd)) {
            $tce->process_cmdmap();
            $returnValues = $tce->copyMappingArray_merged;
        } elseif (count($data)) {
            $tce->process_datamap();
            $returnValues = $tce->substNEWwithIDs;
        }

        // check errors
        if (count($tce->errorLog)) {
            throw new Exception(implode(chr(10), $tce->errorLog));
        }

        return $returnValues;
    }

}

?>