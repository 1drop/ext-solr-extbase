<?php
namespace Onedrop\SolrExtbase\Service;
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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class ExtbaseForceLanguage
 *
 * @package Onedrop\SolrExtbase\Service
 */
class ExtbaseForceLanguage implements SingletonInterface
{
    protected $overrideLanguage = false;
    protected $languageUid = 0;
    protected $languageMode = null;
    protected $languageOverlayMode = false;

    /**
     * This method is registered on the Signal beforeGettingObjectData of the Extbase Generic Backend
     * to override the returned model language
     *
     * @param QueryInterface $query
     * @return QueryInterface
     */
    public function forceLanguageForQueries(QueryInterface $query)
    {
        if (!$this->overrideLanguage) {
            return [$query];
        }
        $querySettings = $query->getQuerySettings();
        $querySettings->setLanguageUid($this->languageUid);
        $querySettings->setLanguageMode($this->languageMode);
        $querySettings->setLanguageOverlayMode($this->languageOverlayMode);
        $query->setQuerySettings($querySettings);

        return [$query];
    }

    /**
     * @return boolean
     */
    public function isOverrideLanguage()
    {
        return $this->overrideLanguage;
    }

    /**
     * When changing the override language, we clear the persistence session of extbase,
     * otherwise the non translated objects would be returned (cache).
     *
     * @param boolean $overrideLanguage
     * @return void
     */
    public function setOverrideLanguage($overrideLanguage)
    {
        $this->overrideLanguage = $overrideLanguage;
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var Session $persistenceSession */
        $persistenceSession = $objectManager->get(Session::class);
        $persistenceSession->destroy();
    }

    /**
     * @return int
     */
    public function getLanguageUid()
    {
        return $this->languageUid;
    }

    /**
     * @param int $languageUid
     * @return void
     */
    public function setLanguageUid($languageUid)
    {
        $this->languageUid = $languageUid;
    }

    /**
     * @return mixed
     */
    public function getLanguageMode()
    {
        return $this->languageMode;
    }

    /**
     * @param mixed $languageMode
     * @return void
     */
    public function setLanguageMode($languageMode)
    {
        $this->languageMode = $languageMode;
    }

    /**
     * @return mixed
     */
    public function getLanguageOverlayMode()
    {
        return $this->languageOverlayMode;
    }

    /**
     * @param mixed $languageOverlayMode
     * @return void
     */
    public function setLanguageOverlayMode($languageOverlayMode)
    {
        $this->languageOverlayMode = $languageOverlayMode;
    }

}
