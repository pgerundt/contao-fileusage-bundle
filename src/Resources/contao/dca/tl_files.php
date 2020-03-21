<?php
    $GLOBALS['TL_DCA']['tl_files']['list']['operations']['fileusage'] = array(
        'button_callback'   => array('tl_files_fileusage', 'generateButtonLink'),
        'href'              => 'contao_fileusage',
        'icon'              => 'bundles/contaofileusage/icons/fileusage.svg'
    );

    class tl_files_fileusage extends Contao\Backend
    {

        public function generateButtonLink($arrRow, $href, $label, $title, $icon, $attributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext) {
            if($arrRow['type'] == 'file') {
                $label = sprintf($label, '&quot;' . $arrRow['fileNameEncoded'] . '&quot;');
                $href = \System::getContainer()->get('router')->generate($href, array('id' => $arrRow['id'], 'popup' => '1'));
                return '<a href="' . $href . '" title="' . \StringUtil::specialchars($title) . '" onclick="Backend.openModalIframe({\'title\':\'' . \StringUtil::specialchars(str_replace("'", "\\'", $label)) . '\',\'url\':this.href});return false"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
            }
        }

    }
