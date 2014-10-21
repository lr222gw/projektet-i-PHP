<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-14
 * Time: 19:08
 */

class MainView {

    public function __construct(){

    }

    public function showListOfStories($arrOfStories)
    {
        $ret = '';
        for($i=0; $i<count($arrOfStories);$i++){//loopar igenom alla stories och gör dem till html

            $title = $arrOfStories[$i]->getTitle();
            $story = $arrOfStories[$i]->getThisStory();
            $score = $arrOfStories[$i]->getScore();
            $uploader = $arrOfStories[$i]->getUserOwner();
            $author = $arrOfStories[$i]->getOtherAuthor();
            $genre = $arrOfStories[$i]->getGenre();
            $lanuage = $arrOfStories[$i]->getLangType();

            $ret .= "
            <div class='listStoryColumn''>
            <a class='title'>{$title} (language: {$lanuage})</a>
            <p class='storyDetails'>Uploaded by: {$uploader}</p>
            <p class='storyDetails'>Author: {$author}</p>
            <p class='storyDetails'>Genre: {$genre}</p>
            <p class='storyDetails'>score: {$score} out of 10</p>
                <div class='story'>
                    <p>
                    {$story}
                    </p>
                </div>


            </div>
            ";


        }


        return $ret;
    }

    public function hasUserAcsessedUploadBox()
    {
        if(isset($_GET['uploadStory'])){
            return true;
        }
        return false;
    }
    public function hasUserbacked()
    {
        if(isset($_GET['back'])){
            header('Location: '. $_SERVER["PHP_SELF"]);
            die();
        }
    }

    public function presentUploadBox($user)
    {
        $ret = "
        <p>Hello {$user}! Upload a story!</p>
        <form action='' method='GET'>
            <input type='submit' name='uploadStory' value='To Upload!'>
        </form>
        ";

        return  $ret;
    }

    public function presentUploadForm($user)
    {//$thisStoryID,$userOwner, $thisStory, $title, $genre, $langType, $score, $listOfComments = [], $otherAuthor = ""
        $ret = "
        <form action='' method='post' id='regform'>
            <fieldset>
                <legend>Register - Fill in user details</legend>
                <label for='author' >Author :</label>
                <input type='text' size='20' name='author' id='author' placeholder='Leave blank if its you'>
                <label for='title'>Title :</label>
                <input type='text' size='20' name='title' id='title' required>
                <label for='genre'>Genre :</label>
                <select name='genre'>
                  <option value=1>Gore</option>
                  <option value=2>The supernatural</option>
                  <option value=3>Lifelike</option>
                  <option value=4>Urban legend</option>
                  <option value=5>Poetry</option>
                </select>
                <label for='lastName'>Language :</label>
                <select name='language'>
                  <option value=1>EN</option>
                  <option value=2>SV</option>
                  <option value=3>DE</option>
                  <option value=4>FR</option>
                  <option value=5>ES</option>
                  <option value=6>RU</option>
                </select>
                <label for='story'>Story to submit :</label>
                <textarea type='story' name='story'  id='story' maxlength='50000' minlength='100' required rows='20' cols='160'></textarea>
                <input type='submit' name='submit' value='Submit Story!'>
                <input type='hidden' name='user' value='$user'>
            </fieldset>
        </form>
        <form action='' method='GET'>
            <input type='submit' name='back' value='Close!'>
        </form>
        ";

        return  $ret;
    }

    public function hasUserSubmited()
    {
        return isset($_POST["submit"]);
    }

    public function retrieveSubmittedData()
    {
        $ArrToReturn = array();
        $ArrToReturn["user"] = $_POST["user"];
        $ArrToReturn["author"] = $_POST["author"];
        $ArrToReturn["title"] = $_POST["title"];
        $ArrToReturn["genre"] = $_POST["genre"];
        $ArrToReturn["language"] = $_POST["language"];
        $ArrToReturn["story"] = $_POST["story"];
        return $ArrToReturn;
    }

    public function clearPost()
    {
        $_POST = [];
    }

    public function goToBegining()
    {
        header("Location: ".$_SERVER["PHP_SELF"]);
        die();
    }

}