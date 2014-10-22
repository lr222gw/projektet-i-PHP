<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-16
 * Time: 13:05
 */
class Story {
    private $thisStory;
    private $thisStoryID;


    private $userOwner;
    private $title;
    private $genre;
    private $score;
    private $langType;
    private $otherAuthor;
    private $listOfComments;
    private $isLocked;

    public function __construct($thisStoryID,$userOwner, $thisStory, $title, $genre, $langType, $score, $listOfComments = [],$isLocked, $otherAuthor = ""){
        $this->thisStoryID = $thisStoryID;
        $this->userOwner = $userOwner;
        $this->thisStory = $thisStory;
        $this->title = $title;
        $this->genre = $genre;
        $this->langType = $langType;
        $this->score = $score;
        $this->listOfComments = $listOfComments;
        $this->otherAuthor = $otherAuthor;
        $this->isLocked = $isLocked;
    }
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    public function getGenre()
    {
        return $this->genre;
    }


    public function getLangType()
    {
        return $this->langType;
    }


    public function getOtherAuthor()
    {
        return $this->otherAuthor;
    }


    public function getScore()
    {
        return $this->score;
    }

    public function getThisStory()
    {
        return $this->thisStory;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUserOwner()
    {
        return $this->userOwner;
    }
    public function getThisStoryID()
    {
        return $this->thisStoryID;
    }

}