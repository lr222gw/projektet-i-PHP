<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-23
 * Time: 19:11
 */
class Comment{

    private $memberID; //anvädnaren som kommetnerat
    private $UserName;
    private $comment;
    private $storyID;

    public function __construct($memberID, $UserName, $storyID, $comment){
        $this->comment = $comment;
        $this->memberID = $memberID;
        $this->UserName = $UserName;
        $this->storyID = $storyID;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getMemberID()
    {
        return $this->memberID;
    }

    public function getUserName()
    {
        return $this->UserName;
    }

    public function getStoryID()
    {
        return $this->storyID;
    }

}