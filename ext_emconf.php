<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Extbase Solr indexation',
    'description' => 'Extbase indexing addition to Apache Solr for TYPO3',
    'version' => '0.0.1-dev',
    'state' => 'stable',
    'category' => 'backend',
    'author' => 'Hans Höchtl',
    'author_email' => 'hhoechtl@1drop.de',
    'author_company' => 'Onedrop Solutions GmbH & Co KG',
    'module' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'constraints' => array(
        'depends' => array(
            'scheduler' => '',
            'typo3' => '7.6.0-7.99.99',
            'solr' => ''
        ),
        'conflicts' => array(),
    ),
    'autoload' => array(
        'psr-4' => array(
            'Onedrop\\SolrExtbase\\' => 'Classes/',
            'Onedrop\\SolrExtbase\\Tests\\' => 'Tests/'
        )
    )
);
