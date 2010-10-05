<?php

require_once dirname(__FILE__) . '/../../common.php';


$mailMerge = new Zend_Service_LiveDocx_MailMerge();

$mailMerge->setUsername(DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME)
          ->setPassword(DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

/**
 * Image Source:
 * iStock_000003413016Medium_business-man-with-hands-up.jpg
 */
$photoFilename = 'dailemaitre.jpg';

if (!$mailMerge->imageExists($photoFilename)) {
    $mailMerge->uploadImage($photoFilename);
}

$mailMerge->setLocalTemplate('template.docx');

$mailMerge->assign('name',        'Daï Lemaitre')
          ->assign('company',     'Megasoft Co-operation')
          ->assign('date',        Zend_Date::now()->toString(Zend_Date::DATE_LONG))
          ->assign('image:photo', $photoFilename);

$mailMerge->createDocument();

$document = $mailMerge->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

$mailMerge->deleteImage($photoFilename);

unset($mailMerge);