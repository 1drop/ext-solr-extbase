<?php
namespace Onedrop\SolrExtbase\Tests\Unit;
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

use Onedrop\SolrExtbase\Service\ExtbaseForceLanguage;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;

/**
 * Class ExtbaseForceLanguageTest
 *
 * @package Onedrop\SolrExtbase\Tests\Unit
 */
class ExtbaseForceLanguageTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Query
     */
    protected $query;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $querySettings;
    /**
     * @var ExtbaseForceLanguage|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $extbaseForceLanguage;
    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistenceSession;

    protected function setUp()
    {
        $this->extbaseForceLanguage = $this->getAccessibleMock(ExtbaseForceLanguage::class, ['dummy'], [], '', false);
        $this->query = $this->getAccessibleMock(\TYPO3\CMS\Extbase\Persistence\Generic\Query::class, ['dummy'], ['someType']);
        $this->querySettings = $this->getAccessibleMock(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class, ['dummy']);
        $this->query->_set('querySettings', $this->querySettings);
        $this->persistenceSession = $this->getMock(Session::class);
        $this->extbaseForceLanguage->_set('persistenceSession', $this->persistenceSession);
    }

    /**
     * @test
     */
    public function testSetLanguageOverrideDestroysPersistenceSession()
    {
        $this->persistenceSession->expects($this->once())->method('destroy');
        $this->extbaseForceLanguage->setOverrideLanguage(true);
    }

    /**
     * @test
     */
    public function testSetLanguageUid()
    {
        $this->extbaseForceLanguage->setLanguageUid(1);
        $this->assertEquals(1, $this->extbaseForceLanguage->getLanguageUid());
    }

    /**
     * @test
     */
    public function testSetLanguageMode()
    {
        $this->extbaseForceLanguage->setLanguageMode('strict');
        $this->assertEquals('strict', $this->extbaseForceLanguage->getLanguageMode());
    }

    /**
     * @test
     */
    public function setLanguageOverlayMode()
    {
        $this->extbaseForceLanguage->setLanguageOverlayMode('content_fallback');
        $this->assertEquals('content_fallback', $this->extbaseForceLanguage->getLanguageOverlayMode());
    }

    /**
     * @test
     */
    public function testForceLanguageForQuery()
    {
        $this->extbaseForceLanguage->setOverrideLanguage(true);
        $this->extbaseForceLanguage->setLanguageUid(2);
        $this->extbaseForceLanguage->forceLanguageForQuery($this->query);
        $this->assertEquals(2, $this->querySettings->getLanguageUid());
    }

}
