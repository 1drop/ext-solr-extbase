<?php
use Onedrop\SolrExtbase\Observer\EntityObserver;
use Onedrop\SolrExtbase\Service\ExtbaseForceLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Backend;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}


/** @var Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
// We must connect to the persistence backend in order to clear the session (cache of objects) when changing the language
$signalSlotDispatcher->connect(Backend::class, 'beforeGettingObjectData', ExtbaseForceLanguage::class, 'forceLanguageForQuery');

// We connect the model observer in case of the models are edited via an extbase repository (solr hook not working here)
$signalSlotDispatcher->connect(Backend::class, 'afterInsertObject', EntityObserver::class, 'afterCreateProduct');
$signalSlotDispatcher->connect(Backend::class, 'afterUpdateObject', EntityObserver::class, 'afterUpdateProduct');
$signalSlotDispatcher->connect(Backend::class, 'afterRemoveObject', EntityObserver::class, 'afterRemoveProduct');
