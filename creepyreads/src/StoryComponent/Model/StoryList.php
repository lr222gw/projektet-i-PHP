<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-16
 * Time: 13:01
 */
require_once("src/StoryComponent/Model/Story.php");
class StoryList{

    private $arrayOfStories;
    private $goBack;
    private $goNextPage;

    public function __construct($arrayOfStoryDetail){
        $arrayOfStories = $this->createListFromArrayOfStoryDetail($arrayOfStoryDetail);
        $this->arrayOfStories = $arrayOfStories;
    }

    public function getListOfStories()
    {
       return $this->arrayOfStories;
    }

    private function createListFromArrayOfStoryDetail($arrayOfStoryDetail){
        //Creates Story objekts from this array...
        $arrayOfStories = [];
        for($i = 0; $i < count($arrayOfStoryDetail); $i++){
            $arrayOfStories[] = $newStory= new Story(
                $arrayOfStoryDetail[$i]['storyID'],
                $arrayOfStoryDetail[$i]['userName'],
                $arrayOfStoryDetail[$i]['story'],
                $arrayOfStoryDetail[$i]['title'],
                $arrayOfStoryDetail[$i]['genre'],
                $arrayOfStoryDetail[$i]['typeOfLangType'],
                $arrayOfStoryDetail[$i]['finalScore'],
                $arrayOfStoryDetail[$i]['listOfComments'],
                $arrayOfStoryDetail[$i]['Author']);
        }

        return $arrayOfStories;

    }



}
