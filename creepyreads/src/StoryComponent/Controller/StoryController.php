<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-16
 * Time: 13:01
 */
require_once("src/Models/DOA_dbMaster.php");
require_once("src/StoryComponent/Model/StoryList.php");
require_once("src/StoryComponent/Model/Comment.php");
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
    public function getStoryFromStoryID($storyID){
        $storyToRetrieve[0] = $this->db->getStoryByID($storyID);
        $refinedStory = $this->getScoreAndCommentsForStory($storyToRetrieve);
        $story = new Story($refinedStory[0]["storyID"],$refinedStory[0]["userName"],$refinedStory[0]["story"],$refinedStory[0]["title"],$refinedStory[0]["genre"],$refinedStory[0]["typeOfLangType"],$refinedStory[0]["finalScore"],$refinedStory[0]["listOfComments"],(int)$refinedStory["isLocked"],$refinedStory[0]["Author"]);
        return $story;
    }

    public function getListOfStoriesFromUser($userID){
        $listOfStories = $this->db->getStoriesByUserID($userID);
        $refinedList = $this->getScoreAndCommentsForStory($listOfStories);
        $storyList = new StoryList($refinedList);
        return $storyList;
    }

    private function getScoreAndCommentsForStory($listOfStories){

        for($i = 0; $i < count($listOfStories);$i++){
            $thisStoryCommentList = $this->db->getCommentsFromStoryID($listOfStories[$i]['storyID']);
            $thisStoryScoreData = $this->db->getScoreDataFromStoryID($listOfStories[$i]['storyID']);

            for($j = 0; $j < count($thisStoryCommentList); $j++){
                $listOfStories[$i]['listOfComments'][$j] = new Comment(
                    $thisStoryCommentList[$j]["memberID"],$thisStoryCommentList[$j]["userName"],$thisStoryCommentList[$j]["storyID"],$thisStoryCommentList[$j]['comment']
                );
            }

            //$finalScore = 0;
            $memberArr = [];
            for($j = 0; $j < count($thisStoryScoreData); $j++){
                $scoreToAdd = $thisStoryScoreData[$j]['scoreValue'];
                //Här har vi skapat en array som innehåller flera arrayer med användarnas namn
                //Om arrayen redan skapats så lägger vi bara in flera värden i den arrayen
                if(array_key_exists("member_".$thisStoryScoreData[$j]['memberID'],$memberArr)){
                    $memberArr["member_".$thisStoryScoreData[$j]['memberID']][] =$thisStoryScoreData[$j]['scoreValue'];
                }else{
                    $memberArr["member_".$thisStoryScoreData[$j]['memberID']][] =$thisStoryScoreData[$j]['scoreValue'];
                }

               /* if($scoreToAdd == null ){//TODO: vad ska göras här? om historien ej har någon röst... ? ... hmm

                }
                $finalScore += $scoreToAdd;*/
            }

            $keyedKeys = array_keys($memberArr);
            $totalFinalScore = 0;
            for($j = 0; $j < count($memberArr);$j++){ //Här går vi igenom alla användare som har röstat
                $TotalSumFromOneUser = 0;
                for($k = 0; $k < count($memberArr[$keyedKeys[$j]]) ; $k++){
                    //Vi lägger ihop de sammanlagda värdet av en användares alla röster på en viss historia
                    $TotalSumFromOneUser += $memberArr[$keyedKeys[$j]][$k];


                }
                //Här tar vi det totala värdet av en användares röster på en viss historia och
                //Dividierar det med antalet röster den har lagt på historian
                //Då får vi alltså medelvärdet av använadrens röster
                $finalFromUser = $TotalSumFromOneUser /count($memberArr[$keyedKeys[$j]]);

                //Vi sparar ner det sammanlagda värdet av alla användares medelvärden
                $totalFinalScore += $finalFromUser;

            }

            //här räknar vi ut det totala medelvärdet på historien
            // vi tar alla användarens röster (deras medelvärde) och sen dividerar det med antalet
            //Personer som har röstat = medelvärde = finalScore!
            $finalScoreFromAllUsers =  $totalFinalScore / count($memberArr);

            $listOfStories[$i]['finalScore'] = $finalScoreFromAllUsers;

        }

        return $listOfStories;
    }

    public function addCommentToStory($storyID, $user, $submittedStory)
    {
        $this->db->addCommentToStory($storyID, $user, $submittedStory);
    }

    public function getListOfBackpackFromUser($userID)
    {
        $backpackOfStories = $this->db->getBackpackStories($userID);
        $refinedList = $this->getScoreAndCommentsForStory($backpackOfStories);
        $storyList = new StoryList($refinedList);
        return $storyList;
    }

    public function addStoryToBackpack($storyID, $userID)
    {
        $this->db->addStoryToBackpack($storyID, $userID);
    }

    public function removeStoryFromBackpack($storyID, $userID)
    {
        $this->db->removeStoryFromBackpack($storyID, $userID);
    }

}