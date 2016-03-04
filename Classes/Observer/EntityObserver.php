<?php
namespace Onedrop\SolrExtbase\Observer;

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


use ApacheSolrForTypo3\Solr\GarbageCollector;
use ApacheSolrForTypo3\Solr\IndexQueue\Queue;
use Onedrop\SolrExtbase\Domain\Model\IndexableEntity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Class EntityObserver
 *
 * @package Onedrop\SolrExtbase\Observer
 */
class EntityObserver
{
    /**
     * @var DataMapper
     * @inject
     */
    protected $dataMapper;
    /**
     * @var Queue
     */
    protected $indexQueue;
    /**
     * @var GarbageCollector
     */
    protected $garbageCollector;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->garbageCollector = GeneralUtility::makeInstance(GarbageCollector::class);
        $this->indexQueue = GeneralUtility::makeInstance(Queue::class);
    }

    /**
     * @param DomainObjectInterface $object
     */
    public function afterCreateProduct($object)
    {
        if ($object instanceof IndexableEntity && $object->isIndexable()) {
            $this->indexQueue->updateItem(
                $this->dataMapper->convertClassNameToTableName(get_class($object)),
                $object->getUid()
            );
        }
    }

    /**
     * @param DomainObjectInterface $object
     */
    public function afterUpdateProduct($object)
    {
        if ($object instanceof IndexableEntity) {
            if ($object->isIndexable()) {
                $this->indexQueue->updateItem(
                    $this->dataMapper->convertClassNameToTableName(get_class($object)),
                    $object->getUid()
                );
            } else {
                $this->removeFromIndexAndQueue(
                    $this->dataMapper->convertClassNameToTableName(get_class($object)),
                    $object->getUid()
                );
            }
        }
    }

    /**
     * @param DomainObjectInterface $object
     */
    public function afterRemoveProduct($object)
    {
        if ($object instanceof IndexableEntity) {
            $this->removeFromIndexAndQueue(
                $this->dataMapper->convertClassNameToTableName(get_class($object)),
                $object->getUid()
            );
        }
    }

    /**
     * Removes record from the index queue and from the solr index
     *
     * @param string $recordTable Name of table where the record lives
     * @param int $recordUid Id of record
     */
    protected function removeFromIndexAndQueue($recordTable, $recordUid)
    {
        $this->garbageCollector->collectGarbage($recordTable, $recordUid);
    }
}
