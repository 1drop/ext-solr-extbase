<?php
namespace Onedrop\SolrExtbase\IndexQueue;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Hans Höchtl <hhoechtl@1drop.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\IndexQueue\Indexer;
use ApacheSolrForTypo3\Solr\IndexQueue\Item;
use ApacheSolrForTypo3\Solr\Util;
use Onedrop\SolrExtbase\Service\ExtbaseForceLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

class EntityIndexer extends Indexer
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var DataMapper
     */
    protected $dataMapper;
    /**
     * @var ExtbaseForceLanguage
     */
    protected $extbaseForceLanguageService;

    /**
     * Constructor
     *
     * @param array $options of indexer options
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->extbaseForceLanguageService = $this->objectManager->get(ExtbaseForceLanguage::class);
    }

    /**
     * @param Item $item
     * @param int $language
     * @return \TYPO3\CMS\Extbase\Persistence\Repository
     */
    protected function getRepositoryByItem(Item $item, $language = 0)
    {
        $solrConfiguration = Util::getSolrConfigurationFromPageId($item->getRootPageUid(), true, $language);
        $indexQueueConfiguration = $solrConfiguration['index.']['queue.'][$item->getIndexingConfigurationName() . '.'];
        if (!isset($indexQueueConfiguration['repository'])) {
            throw new \InvalidArgumentException('Missing repository in indexQueue configuration', 1457004659);
        }
        return $this->objectManager->get($indexQueueConfiguration['repository']);
    }

    /**
     * @param Item $item
     * @param int $language
     * @return \Apache_Solr_Document
     */
    protected function itemToDocument(Item $item, $language = 0)
    {
        $baseDocument = parent::itemToDocument($item, $language);
        if (!$baseDocument instanceof \Apache_Solr_Document) {
            return $baseDocument;
        }

        $this->extbaseForceLanguageService->setOverrideLanguage(true);
        $this->extbaseForceLanguageService->setLanguageUid($language);

        $repository = $this->getRepositoryByItem($item, $language);
        /** @var \Onedrop\SolrExtbase\Domain\Model\IndexableEntity $entity */
        $entity = $repository->findByUid($item->getRecordUid());
        if (!$entity->isIndexable()) {
            return null;
        }
        $document = $entity->addEntityFieldsToDocument($baseDocument);

        $this->extbaseForceLanguageService->setOverrideLanguage(false);
        return $document;
    }


}
