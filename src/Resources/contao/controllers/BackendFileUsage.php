<?php
    namespace Efficient\ContaoFileUsageBundle\Controller;

    use Symfony\Component\HttpFoundation\Response;

    class BackendFileUsage extends \Backend
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function run()
        {
            \System::loadLanguageFile('modules');
            \System::loadLanguageFile('tl_files');
            $data = array();
            $objFile = \FilesModel::findByPath(urldecode(\Input::get('id')));
            if(!is_null($objFile)) {
                if($objFile->uuid) {
                    foreach(preg_grep('/^tl_/', $this->Database->listTables(null, true)) as $strTable) {
                        $this->loadDataContainer($strTable);
                        foreach($GLOBALS['TL_DCA'][$strTable]['fields'] as $strField => $cnfField) {
                            switch($cnfField['inputType']) {
                                case 'fileTree':
                                    $strModelClass = \Model::getClassFromTable($strTable);
                                    if($cnfField['eval']['multiple']) {
                                        $objRows = $strModelClass::findBy(
                                            array($strField . ' LIKE \'%"' . addslashes($objFile->uuid) . '"%\''),
                                            array(null)
                                        );
                                    }
                                    else {
                                        $objRows = $strModelClass::findBy(
                                            $strField,
                                            $objFile->uuid
                                        );
                                    }
                                    if($objRows) {
                                        if(!isset($data[$strTable])) {
                                            $data[$strTable] = array();
                                        }
                                        foreach($objRows as $objRow) {
                                            $match = $this->_getMatchRows($objRow, $strTable);
                                            $data[$strTable][] = $match;
                                        }
                                    }
                                    break;
                                case 'textarea':
                                    $strModelClass = \Model::getClassFromTable($strTable);
                                    $objRows = $strModelClass::findBy(
                                        array($strField . ' LIKE \'%{{file::' . \StringUtil::binToUuid($objFile->uuid) . '%\''),
                                        array(null)
                                    );
                                    if($objRows) {
                                        if(!isset($data[$strTable])) {
                                            $data[$strTable] = array();
                                        }
                                        foreach($objRows as $objRow) {
                                            $match = $this->_getMatchRows($objRow, $strTable);
                                            $data[$strTable][] = $match;
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
                $objTemplate = new \BackendTemplate('be_fileusage');
                $objTemplate->data = $data;
                $objTemplate->theme = \Backend::getTheme();
                $objTemplate->base = \Environment::get('base');
                $objTemplate->language = $GLOBALS['TL_LANGUAGE'];
                $objTemplate->title = sprintf($GLOBALS['TL_LANG']['tl_files']['fileusage'][0], $objFile->name);
                $objTemplate->host = \Backend::getDecodedHostname();
                $objTemplate->charset = \Config::get('characterSet');
                return $objTemplate->getResponse();
            }
        }

        private function _getMatchRows($objRow, $strTable) {
            $rows = array();
            if($objRow->pid) {
                if(is_null($objRow->ptable)) {
                    $objRow->ptable = $GLOBALS['TL_DCA'][$strTable]['config']['ptable'];
                }
                $strParentModelClass = \Model::getClassFromTable($objRow->ptable);
                $objParentRow = $strParentModelClass::findByPk($objRow->pid);
                if($objParentRow) {
                    $rows = $this->_getMatchRows($objParentRow, $objRow->ptable);
                }
            }
            $rows[] = $this->_parseRow($objRow, $strTable);
            return $rows;
        }

        private function _parseRow($objRow, $strTable) {
            $row = array(
                'id' => $objRow->id,
                'location' => $strTable,
                'name' => '',
                'tstamp' => \Date::parse(\Config::get('datimFormat'), $objRow->tstamp),
                'published' => ''
            );
            switch(true) {
                case isset($GLOBALS['TL_LANG']['MOD'][substr($strTable, 3)]):
                    $location = $GLOBALS['TL_LANG']['MOD'][substr($strTable, 3)];
                    $row['location'] = (is_array($location)) ? $location[0] : $location;
                    break;
                case isset($GLOBALS['TL_LANG']['MOD'][$strTable]):
                    $location = $GLOBALS['TL_LANG']['MOD'][$strTable];
                    $row['location'] = (is_array($location)) ? $location[0] : $location;
                    break;
            }
            foreach(array('title', 'headline', 'type', 'name') as $field) {
                if(!is_null($objRow->$field)) {
                    switch($field) {
                        case 'headline':
                            if(is_array(\StringUtil::deserialize($objRow->$field))) {
                                $headline = \StringUtil::deserialize($objRow->$field);
                                if((isset($headline['value'])) && ($headline['value'] != '')) {
                                    $row['name'] .= $headline['value'];
                                    break 2;
                                }
                            }
                            else {
                                $row['name'] .= $objRow->$field;
                            }
                            break;
                        case 'type':
                            $row['name'] .= $GLOBALS['TL_LANG']['CTE'][$objRow->$field][0];
                            break;
                        default:
                            if($objRow->$field != '') {
                                $row['name'] .= $objRow->$field;
                                break 2;
                            }
                            break;
                    }
                }
            }
            if(!is_null($objRow->alias)) {
                $row['name'] .= ' (' . $objRow->alias . ')';
            }
            foreach(array('published', 'invisible', 'disable') as $field) {
                if(!is_null($objRow->$field)) {
                    switch($field) {
                        case 'invisible':
                        case 'disable':
                            $row['published'] = ($objRow->$field != '1') ? \Image::getHtml('visible.svg', '') : \Image::getHtml('visible_.svg', '');
                            break;
                        default:
                            $row['published'] = ($objRow->$field == '1') ? \Image::getHtml('visible.svg', '') : \Image::getHtml('visible_.svg', '');
                            break;
                    }
                }
            }
            return $row;
        }

    }