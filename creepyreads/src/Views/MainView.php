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
        if(isset($_POST['uploadStory'])){
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
        <form action='' method='Post'>
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

    public function hasUserEditStory()
    {
        return isset($_POST["changed"]);
    }

    public function retrieveSubmittedData($forEdit = false)
    {
        $ArrToReturn = array();
        $ArrToReturn["user"] = $_POST["user"];
        $ArrToReturn["author"] = $_POST["author"];
        $ArrToReturn["title"] = $_POST["title"];
        $ArrToReturn["genre"] = $_POST["genre"];
        $ArrToReturn["language"] = $_POST["language"];
        $ArrToReturn["story"] = $_POST["story"];
        if($forEdit){
            $ArrToReturn["storyID"] = $_POST["storyID"];
        }
        return $ArrToReturn;
    }

    public function goToFirstPage($extraParam = "")
    {
        if($extraParam != ""){
            $extraParam = $extraParam."=true";
        }

        header("Location: ".$_SERVER["PHP_SELF"]."?".$extraParam);

        die();
    }
    public function clearPost()
    {
        $_POST = [];
    }

    public function storySucsessed()
    {
        if(isset($_GET["success"])){
            if($_GET["success"] == true){
                return true;
            }
        }
        return false;
    }

    public function didUserSelectStoryToEdit()
    {
        if(isset($_GET["edit"])){
            return $_GET["edit"];

        }
        return false;
    }

    public function presentEditStories(){
        $ret = "
        <h4>Edit Your Stories</h4>
        <form action='' method='Post'>
            <input type='submit' name='editstories' value='Manage Stories'>
        </form>
        ";

        return  $ret;
    }

    public function hasUserAccessedEdit()
    {
        if(isset($_POST['editstories'])){
            return true;
        }
        return false;
    }
    public function didUserLockOrUnlock(){
        if(isset($_POST['isLocked'])){
            return $_POST['isLocked'];
        }
        return false;
    }
    public function getUnlockedStoryInfo()
    {
        return $unOrlockStoryID = (int)$_POST["storyID"];
    }

    public function showEditStories($theListOfuserStories)
    {
        $ret = '';
        for($i=0; $i<count($theListOfuserStories);$i++){//loopar igenom alla stories och gör dem till html
            $title = $theListOfuserStories[$i]->getTitle();
            $story = $theListOfuserStories[$i]->getThisStory();
            $score = $theListOfuserStories[$i]->getScore();
            $uploader = $theListOfuserStories[$i]->getUserOwner();
            $author = $theListOfuserStories[$i]->getOtherAuthor();
            $genre = $theListOfuserStories[$i]->getGenre();
            $lanuage = $theListOfuserStories[$i]->getLangType();
            $storyId = $theListOfuserStories[$i]->getThisStoryID();
            $isLocked = $theListOfuserStories[$i]->getIsLocked();

            if($isLocked == 0){

                $isLocked = "<form method='post'><input type='submit' name='isLocked' value='Lock' ><input type='hidden' name='storyID' value='$storyId'></form>";
            }else{$isLocked = "<form method='post'><input type='submit' name='isLocked' value='unlock' ><input type='hidden' name='storyID' value='$storyId'></form>";}

            $ret .= "
            <div class='editListColumn''>
            <a class='title' href='?edit=$storyId'>{$title} (language: {$lanuage})</a>
            $isLocked
            <p class='storyDetails'>Uploaded by: {$uploader}</p>
            <p class='storyDetails'>Author: {$author}</p>
            <p class='storyDetails'>Genre: {$genre}</p>
            <p class='storyDetails'>score: {$score} out of 10</p>

            </div>
            ";

        }
        return $ret;
    }

    public function showEditThisStory($editThisStory)
    {
        $title = $editThisStory->getTitle();
        $story = $editThisStory->getThisStory();
        $author = $editThisStory->getOtherAuthor();
        $genre = $editThisStory->getGenre();
        $owner = $editThisStory->getUserOwner();
        switch($genre){
            case "Gore":
                $genre1 = "selected";
                break;
            case "The supernatural":
                $genre2 = "selected";
                break;
            case "Lifelike":
                $genre3 = "selected";
                break;
            case "Urban legend":
                $genre4 = "selected";
                break;
            case "Poetry":
                $genre5 = "selected";
                break;
        }
        $lanuage = $editThisStory->getLangType();
        switch($lanuage){
            case "EN":
                $lanuage1 = "selected";
                break;
            case "SV":
                $lanuage2 = "selected";
                break;
            case "DE":
                $lanuage3 = "selected";
                break;
            case "FR":
                $lanuage4 = "selected";
                break;
            case "ES":
                $lanuage5 = "selected";
                break;
            case "RU":
                $lanuage5 = "selected";
                break;
        }
        $storyId = $editThisStory->getThisStoryID();
        $ret = "
        <form action='' method='post' id='editForm'>
            <fieldset>
                <legend>Edit - Change storydetails</legend>
                <label for='author' >Author :</label>
                <input type='text' size='20' name='author' id='author' value='$author' placeholder='Leave blank if its you'>
                <label for='title'>Title :</label>
                <input type='text' size='20' name='title' id='title' required value='$title'>
                <label for='genre'>Genre :</label>
                <select name='genre'>
                  <option value=1 $genre1>Gore</option>
                  <option value=2 $genre2>The supernatural</option>
                  <option value=3 $genre3>Lifelike</option>
                  <option value=4 $genre4>Urban legend</option>
                  <option value=5 $genre5>Poetry</option>
                </select>
                <label for='lastName'>Language :</label>
                <select name='language'>
                  <option value=1 $lanuage1>EN</option>
                  <option value=2 $lanuage2>SV</option>
                  <option value=3 $lanuage3>DE</option>
                  <option value=4 $lanuage4>FR</option>
                  <option value=5 $lanuage5>ES</option>
                  <option value=6 $lanuage5>RU</option>
                </select>
                <label for='story'>Story to submit :</label>
                <textarea type='story' name='story'  id='story' maxlength='50000' minlength='100' required rows='20' cols='160'>$story</textarea>
                <input type='submit' name='changed' value='Save Changes!'>
                <input type='hidden' name='user' value='$owner'>
                <input type='hidden' name='storyID' value='$storyId'>
            </fieldset>
        </form>
        <form action='' method='GET'>
            <input type='submit' name='back' value='Close!'>
        </form>
        ";
        return $ret;
    }



}