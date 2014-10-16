<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-16
 * Time: 13:01
 */
require_once("src/Models/DOA_dbMaster.php");
require_once("src/StoryComponent/Model/StoryList.php");
class StoryController {

    private $db;

    public function __construct(){
        $this->db =  new DOA_dbMaster();
    }

    public function getListOfStoreis(){
        $listOfStories = $this->db->getAllStoriesAndDetails(); //TODO: ändra så vi bara hämtar, typ 50... gör så denna funktion hämtar ut ett visst antal stories...
        $refinedList = $this->getScoreAndCommentsForStory($listOfStories);
        $storyList = new StoryList($refinedList);
        return $storyList;
    }

    private function getScoreAndCommentsForStory($listOfStories){

        for($i = 0; $i < count($listOfStories);$i++){
            $thisStoryCommentList = $this->db->getCommentsFromStoryID($listOfStories[$i]['storyID']);
            $thisStoryScoreData = $this->db->getScoreDataFromStoryID($listOfStories[$i]['storyID']);
            for($j = 0; $j < count($thisStoryCommentList); $j++){
                $listOfStories[$i]['listOfComments'][$j] = $thisStoryCommentList[$j]['comment'];
            }

            $finalScore = 0;
            for($j = 0; $j < count($thisStoryScoreData); $j++){
                $scoreToAdd = $thisStoryScoreData[$j]['scoreValue'];
                if($scoreToAdd == null ){//TODO: vad ska göras här? om historien ej har någon röst... ? ... hmm

                }
                $finalScore += $scoreToAdd;
            }
            $finalScore = $finalScore/count($thisStoryScoreData);

            $listOfStories[$i]['finalScore'] = $finalScore;

        }
        return $listOfStories;
    }

}