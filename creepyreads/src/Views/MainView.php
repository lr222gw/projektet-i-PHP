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
    public function prepareToShowListOfAllStories($arrOfStories, $userOnlinebool){
        $ret = "<h1 id='sideTitle'>All Stories</h1>";
        $ret .= $this->showListOfStories($arrOfStories, false, $userOnlinebool);
        return $ret;

    }

    public function showListOfStories($arrOfStories,$useBackpack = false, $onlineUser = false)
    {

        $ret = "<div class='list'>";
        for($i=0; $i<count($arrOfStories);$i++){//loopar igenom alla stories och gör dem till html

            $ret .= $this->getStoryForView($arrOfStories[$i],$useBackpack,$onlineUser);
        }

        return $ret."</div>";
    }
    public function getStoryForView($storyToAdd, $isThisBackpack = false, $userOnline = false){

        $title = $storyToAdd->getTitle();
        $story = nl2br($storyToAdd->getThisStory());
        $score = $storyToAdd->getScore();
        $score = round(floatval($score),2);
        $uploader = $storyToAdd->getUserOwner();
        $author = $storyToAdd->getOtherAuthor();
        $genre = $storyToAdd->getGenre();
        $lanuage = $storyToAdd->getLangType();
        $storyID = $storyToAdd->getThisStoryID();

        if($isThisBackpack === true){
            $isThisBackpack = "
            <form method='post'>
                <input type='submit' name='removeFromBackpack' value='Remove!'>
                <input type='hidden' name='storyIDToRemove' value='$storyID'>
            </form>
            ";
        }else if($userOnline){ $isThisBackpack = "<a class='addToBackpack' href='?addToBackpack={$storyID}'>Add to backpack!</a>"; }

        $ret = "
            <div class='listStoryColumn''>
            <a class='title' href='?read={$storyID}'>{$title} (language: {$lanuage})</a>
            $isThisBackpack
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

        return $ret;


    }
    public function getCommentsForStory($storyToGetComments){

        $commentSection = '<h1>Comment Section</h1>';
        $commentList = $storyToGetComments->getListOfComments();
        for($i=0; $i<count($commentList); $i++){
            $comment = $commentList[$i]->getComment();
            $memberName = $commentList[$i]->getUserName();
            if($memberName != null){
            $commentSection .= "
            <div class='comment'>
            <h4>$memberName wrote:</h4>
            <div class='usercomment'>
                <p>$comment</p>
            </div>
            </div>
            ";
            }

        }

        if($commentList[0]->getUserName() == null){ // om ingen kommenterat...
            $commentSection .= "
            <div class='comment'>
            <h4>Sorry, no comments yet!</h4>

            </div>
            ";
        }



        return $commentSection;
    }

    public function getUserComment()
    {
        if(isset($_POST['commentButton'])){
            return $_POST['comment'];
        }
        return false;
    }

    public function getCommentBox()
    {
        $ret = "
        <div id='commentBox'>
        <form method='post'>
            <label for='comment'>Write a comment</label>
            <textarea type='text' name='comment'></textarea>
            <input type='submit' name='commentButton' value='Submit!'>
        </form>
        </div>

        ";
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
        <form action='' method='Post'>
            <input type='submit' name='uploadStory' value='To Upload!'>
        </form>
        ";

        return  $ret;
    }

    public function presentUploadForm($user)
    {//$thisStoryID,$userOwner, $thisStory, $title, $genre, $langType, $score, $listOfComments = [], $otherAuthor = ""
        $ret = "<h1 id='sideTitle'>Upload Story</h1>
        <div id='upload'>
        <form action='' method='post' id='uploadForm'>
            <fieldset>
                <legend>Upload Story</legend>
                <label for='author' >Author :</label>
                <input type='text' size='20' name='author' id='author' placeholder='Leave blank if its you'>
                <label for='title'>Title :</label>
                <input type='text' size='20' name='title' id='title' required maxlength='35' placeholder='Max 35 characters'>
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
                <textarea type='story' name='story'  id='story' maxlength='50000' minlength='100' required placeholder='Max 50 000 characters'></textarea>
                <input type='submit' name='submit' value='Submit Story!'>
                <input type='hidden' name='user' value='$user'>
            </fieldset>
        </form>
        <form action='' method='GET'>
            <input type='submit' name='back' value='Close!'>
        </form>
        </div>
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

 /*   public function presentEditStories(){
        //<h3>Edit Your Stories</h3> <-- tog bort denna... blev rörigt
        $ret = "

        <form action='' method='Post'>
            <input type='submit' name='editstories' value='Manage Stories'>
        </form>
        ";

        return  $ret;
    }*/

    public function hasUserAccessedEdit()
    {

        if(isset($_GET['editstories'])){
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
    public function didUserDeleteStory(){
        if(isset($_POST['delete'])){
            return true;
        }
        return false;
    }

    public function getLastStoryID()
    {
        return $StoryID = (int)$_POST["storyID"];
    }

    public function showEditStories($theListOfuserStories)
    {
        $ret = "<h1 id='sideTitle'>Manage your stories</h1>
        <div id='editList'>";
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

            $score = round(floatval($score),2);

            if($isLocked == 0){

                $isLocked = "<form method='post'><input type='submit' name='isLocked' value='Lock' ><input type='hidden' name='storyID' value='$storyId'></form>";
            }else{$isLocked = "<form method='post'><input type='submit' name='isLocked' value='unlock' ><input type='hidden' name='storyID' value='$storyId'></form>";}


            $ret .= "
            <div class='editListColumn''>
            <a class='title' href='?edit=$storyId'>{$title} (language: {$lanuage})</a>
            $isLocked<form method='post'><input type='submit' name='delete' value='Delete Story' ><input type='hidden' name='storyID' value='$storyId'></form>
            <p class='storyDetails'>Uploaded by: {$uploader}</p>
            <p class='storyDetails'>Author: {$author}</p>
            <p class='storyDetails'>Genre: {$genre}</p>
            <p class='storyDetails'>score: {$score} out of 10</p>

            </div>
            ";

        }
        return $ret."</div>";
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
        <h1 id='sideTitle'>Edit Story</h1>
        <div id='storyToEdit'>
            <form action='' method='post' id='editForm'>
                <fieldset>
                    <legend>Edit - Change storydetails</legend>
                    <label for='author' >Author :</label>
                    <input type='text' size='20' name='author' id='author' value='$author' placeholder='Leave blank if its you'>
                    <label for='title'>Title :</label>
                    <input type='text' size='20' name='title' id='title' required value='$title' maxlength='35' placeholder='Max 35 characters'>
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
                    <textarea type='story' name='story'  id='story' maxlength='50000' placeholder='Max 50 000 characters' minlength='100' required>$story</textarea>
                    <input type='submit' name='changed' value='Save Changes!'>
                    <input type='hidden' name='user' value='$owner'>
                    <input type='hidden' name='storyID' value='$storyId'>
                </fieldset>
            </form>
            <form action='' method='GET'>
                <input type='submit' name='back' value='Close!'>
            </form>
        </div>
        ";
        return $ret;
    }


    /**
     * @return bool
     *
     */
    public function readWhatStory(){
        if (isset($_GET["read"])){
            return $_GET["read"];
        }else{
            return false;
        }
    }

    public function getUserVoteData(){
        $voteDataArr = [];
        if(isset($_POST["ScaryBox"])){
            $voteDataArr["Scary"] = $_POST["Scary"];
        }
        if(isset($_POST["ReadabilityBox"])){
            $voteDataArr["Readability"] = $_POST["Readability"];
        }
        if(isset($_POST["ShiversBox"])){
            $voteDataArr["Shivers"] = $_POST["Shivers"];
        }
        if(isset($_POST["UniqueBox"])){
            $voteDataArr["Unique"] = $_POST["Unique"];
        }
        if(isset($_POST["CorrespondgenreBox"])){
            $voteDataArr["Correspondgenre"] = $_POST["Correspondgenre"];
        }
        return $voteDataArr;
    }
    public function didUserVote(){
        if(isset($_POST["voted"])){
            return true;
        }
        return false;
    }

    public function getVoteForStory()
    {
        $ret = "
        <h1>Don't forget to vote!</h1>
        <form method='post'>
            <label for='Scary'>Scary</label>
            <input type='checkbox' name='ScaryBox' checked='true'>
            <input type='range' name='Scary' min='1' max='10' step='1' value='5'>
            <label for='Readability'>Readability</label>
            <input type='checkbox' name='ReadabilityBox' >
            <input type='range' name='Readability' min='1' max='10' step='1' value='5'>
            <label for='Shivers'>Shivers</label>
            <input type='checkbox' name='ShiversBox'>
            <input type='range' name='Shivers' min='1' max='10' step='1' value='5'>
            <label for='Unique'>Unique</label>
            <input type='checkbox' name='UniqueBox' >
            <input type='range' name='Unique' min='1' max='10' step='1' value='5'>
            <label for='Correspondgenre'>Corresponds to the genre</label>
            <input type='checkbox' name='CorrespondgenreBox' >
            <input type='range' name='Correspondgenre' min='1' max='10' step='1' value='5'>
            <input type='submit' value='Place vote!' name='voted'>
        </form>
        ";
            return $ret;
    }

    public function didUserOpenBackpack()
    {
        if(isset($_GET["backpack"])){
            return true;
        }else{
            return false;
        }
    }

    public function getBackpackView($listOfStorysInBackpack)
    {
        $presentation = "<h1 id='sideTitle'>Stories in your backpack</h1>";
        return $presentation.$this->showListOfStories($listOfStorysInBackpack,true, false);
    }
    public function getStoryToRemoveFromBackpack(){

        if(isset($_POST["removeFromBackpack"])){
            return $_POST["storyIDToRemove"];
        }
        return false;
    }

    public function getAddedStoryToBackpackID()
    {
        if(isset($_GET["addToBackpack"])){
            return $_GET["addToBackpack"];
        }
        else{
            return false;
        }
    }

    public function hasUserChangedYTPlaylist()
    {
        if(isset($_POST["userChangedPlaylist"])){
            return true;
        }
        else{
            return false;
        }
    }
    public function getUserYTPlaylist()
    {
        if(isset($_POST["userChangedPlaylist"])){
            return $_POST["userChangedPlaylist"];
        }
        else{
            return false;
        }
    }
    public function getYTPlaylistInput(){
        $ret = "
        <form method='post' class='playlistchange'>
            <input type='text' name='userChangedPlaylist' placeholder='Add custom Youtube playlist-URL'>
            <input type='submit' value='Change Playlist'>
        </form>
        <form method='post'>
            <input type='submit' name='userChangedToStandardYT' value='Use Standard'>
        </form>
        <form method='post'>
            <input type='submit' name='Autoplay' value='Autoplay'>
        </form>
        ";
        return $ret;
    }
    public function hasUserChangedYTAutoplay()
    {
        if(isset($_POST["Autoplay"])){
            return true;
        }
        else{
            return false;
        }
    }
    public function hasUserChangedYTPlaylistToStandard()
    {
        if(isset($_POST["userChangedToStandardYT"])){
            return true;
        }
        else{
            return false;
        }
    }

    public function getStoryFromStoryIDStructor($story,$isUserOnline){

        $storyhtml = "<div id='storyView'>";
        $storyhtml .= $this->getStoryForView($story);
        if($isUserOnline != false){
            $storyhtml .= "<div id='voteBox'>";
            $storyhtml .= $this->getVoteForStory();
            $storyhtml .= "</div>";
            $storyhtml .= "<div id='commentSection'>";
            $storyhtml .= $this->getCommentsForStory($story);
            $storyhtml .= $this->getCommentBox();
            $storyhtml .= "</div>";
        }else{
            $storyhtml .= $this->getCommentsForStory($story);
        }
        $storyhtml .= "</div>";
        return $storyhtml;
    }

    public function getMenu($userIsOnline)
    {
        $ret = "<div id='menuHolder'><ul id='menu'>
                    <li>
                    <a href='?home'>Home</a>
                    </li>";
        if($userIsOnline){
            $ret .= "
                    <li>
                    <a href='?backpack'>My Backpack</a>
                    </li>
                    <li>
                    <a href='?editstories'>Manage Stories</a>
                    </li>
                    <li>
                    <a href='?uploadStory'>Upload Story</a>
                    </li>
            ";
        }

        /*<form action='' method='Post'>
            <input type='submit' name='editstories' value='Manage Stories'>
        </form>*/



        $ret .= "</ul></div>";

        return $ret;
    }


}