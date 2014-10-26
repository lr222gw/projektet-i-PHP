<?php


require_once("src/Views/HTMLview.php");
require_once("src/Controllers/MasterController.php");

/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-14
 * Time: 18:57
 */
session_start();

$htmlview = new HTMLview();

$masterController = new MasterController($htmlview);

$htmlview->presentPage($masterController->getLoginModule(),
    $masterController->getContent(),
    $masterController->getUploadBox(),
    //$masterController->getEditStories(),
    $masterController->getYoutubePlayer(),
    $masterController->getMenu());

