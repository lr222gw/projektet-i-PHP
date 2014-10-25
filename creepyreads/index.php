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

$htmlview->presentPage($masterController->getLoginModule(),$masterController->getContent(),$masterController->getUploadBox(), $masterController->getEditStories(), $masterController->getYoutubePlayer());

/*$some = $shit->addStory(46,1,'Yoooo bitch', 'storyBoutBitch', 1);//checkIfUserNameAlreadyExist('momb');//'; DROP TABLE shit; --';
$some = $shit->addStory(46,1,'Alfons Suger', 'OnceUpon Times in Roman', 1);
    $some = $shit->addStory(46,1,'Doktor Abraham', 'Juppsing', 1);*/
/*$some = $shit->getCommentsFromStoryID(90);
echo "<pre>";
print_r($some);
echo "</pre>";
$some = $shit->getScoreDataFromStoryID(90);
echo "<pre>";
print_r($some);
echo "</pre>";
$some = $shit->getAllStoriesAndDetails();*/

/*$shit2 = new StoryController();
echo "<pre>";
print_r($shit2->getListOfStoreis());
echo "</pre>";*/





/*echo "<pre>";
print_r($some);
echo "</pre>";*/