<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-16
 * Time: 14:14
 */

class Comment {

    private $commentContent;
    private $userOwner;

    public function __construct($userOwner, $commentContent){
        $this->userOwner = $userOwner;
        $this->commentContent = $commentContent;
    }

    public function getCommentContent()
    {
        return $this->commentContent;
    }

    public function getUserOwner()
    {
        return $this->userOwner;
    }





}