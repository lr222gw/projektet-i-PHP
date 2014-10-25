<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-14
 * Time: 19:07
 */
require_once("src/Models/DOA_dbMaster.php");
require_once("src/Models/YoutubePlayer.php");
require_once("src/Models/cookieJar.php");
require_once("src/LoginComponent/Controller/LoginController.php");
require_once("src/StoryComponent/Controller/StoryController.php");
require_once("src/Views/MainView.php");
require_once("src/Views/HTMLview.php");
include_once("src/Models/cookieJar.php");
class MasterController {
    private $storyController;
    private $db;
    private $loginController;
    private $view;
    private $htmlview;
    private $cookieJar;

    public function __construct(){
        $this->loginController = new LoginController();
        $this->db = new DOA_dbMaster();
        $this->storyController = new StoryController();
        $this->view = new MainView();
        $this->htmlview = new HTMLview();
        $this->cookieJar = new CookieJar();
    }

    public function getLoginModule(){
        return $this->loginController->doControl();
    }
    public function getStoryContent(){
        $storyID = $this->view->readWhatStory();

        if($storyID != false){ //$storyID har ett id, om ej så är den false

            $submittedStory = $this->view->getUserComment();
            if($submittedStory != false){
                $this->storyController->addCommentToStory($storyID, $this->db->getUserDetail($this->loginController->checkForLoggedInAndReturnUserName(),2), $submittedStory);
                $this->cookieJar->save("Your comment was added!");
                header('Location: '.$_SERVER['HTTP_REFERER']);
                die;
            }
            if($this->view->didUserVote()){
                $voteData = $this->view->getUserVoteData();
                $this->db->addScoreToStory($voteData, $storyID, $this->db->getUserDetail($this->loginController->checkForLoggedInAndReturnUserName(),2));
                $this->cookieJar->save("Your vote was added!");
                header('Location: '.$_SERVER['HTTP_REFERER']);
                die;
            }
            $story = $this->storyController->getStoryFromStoryID($storyID);
            $storyhtml = $this->view->getStoryForView($story);
            if($this->loginController->checkForLoggedInAndReturnUserName() != false){
                $storyhtml .= $this->view->getVoteForStory();
                $storyhtml .= $this->view->getCommentsForStory($story);
                $storyhtml .= $this->view->getCommentBox();
            }else{
                $storyhtml .= $this->view->getCommentsForStory($story);
            }



            return $storyhtml;
        }
        return false;
    }
    public function getBackpackView(){
        $listOfStorysInBackpack = $this->storyController->getListOfBackpackFromUser($this->db->getUserDetail($this->loginController->checkForLoggedInAndReturnUserName(),2));
        $listOfStorysInBackpack = $listOfStorysInBackpack->getListOfStories();
        $listOfStorysInBackpack = array_reverse($listOfStorysInBackpack); // ser till att den nyaste ligger först...

        $backpackView = $this->view->getBackpackView($listOfStorysInBackpack);
        return $backpackView;
    }

    public function getContent(){
        $this->statusController();
        if($this->view->didUserOpenBackpack()){

            $backpackView = $this->getBackpackView();
            return $backpackView;
        }

        $returnThis = $this->getStoryContent();
        if($returnThis == false){ // om ingen specifik story har valts så ska vi hämta lista

            $returnThis = $this->getListContent();

        }

        return $returnThis;

    }

    public function getListContent(){
        $ListOfStories = $this->storyController->getListOfStoreis();

        $arrOfStories = $ListOfStories->getListOfStories();

        $arrOfStories = array_reverse($arrOfStories); // ser till att den nyaste ligger först...

        $result = $this->view->showListOfStories($arrOfStories);


        return $result;
    }

    public function getUploadBox()
    {
        $user = $this->loginController->checkForLoggedInAndReturnUserName();

        if($user != false){ // om user inte är false!
            $this->view->hasUserbacked();

            if($this->view->hasUserSubmited()){
                $this->htmlview->addMessageToShow($this->cookieJar->load());
                //om användaren har submittat så ska vi försöka spara ner datan...
                $newStoryData = $this->view->retrieveSubmittedData();

                $this->view->clearPost();

                $this->db->addStory($this->db->getUserDetail($newStoryData["user"],2),(int)$newStoryData["language"],$newStoryData["story"],$newStoryData["title"],(int)$newStoryData["genre"],$newStoryData["author"]);
                $this->cookieJar->save("Your story was added!");
                $this->view->goToFirstPage();

                return ;

            }
            else if($this->view->hasUserAcsessedUploadBox()){
                return $this->view->presentUploadForm($user);
            }else{
                return $this->view->presentUploadBox($user);
            }
        }else{

            return '';
        }
    }

    public function getEditStories(){
        $user = $this->loginController->checkForLoggedInAndReturnUserName();

        if($user != false){ // om user inte är false! alltså inloggad...

            $storyId = $this->view->didUserSelectStoryToEdit();
            if($this->view->hasUserEditStory()){

                $EditedStoryData = $this->view->retrieveSubmittedData(true);

                $this->view->clearPost();

                $this->db->EditStory((int)$EditedStoryData["storyID"], $this->db->getUserDetail($EditedStoryData["user"],2),(int)$EditedStoryData["language"],$EditedStoryData["story"],$EditedStoryData["title"],(int)$EditedStoryData["genre"],$EditedStoryData["author"]);
                $this->cookieJar->save("Your story was Edited!");
                $this->view->goToFirstPage();

                return ;
            }
            else if($storyId != false){

                $editThisStory = $this->storyController->getStoryFromStoryID($storyId);
                return $this->view->showEditThisStory($editThisStory);

            }else if($this->view->hasUserAccessedEdit()){

                //hämta ner alla användarens stories
                $userStories = $this->storyController->getListOfStoriesFromUser($this->db->getUserDetail($user,2));
                $theListOfuserStories = $userStories->getListOfStories();
                $theListOfuserStories = array_reverse($theListOfuserStories);
                return $this->view->showEditStories($theListOfuserStories);
            }else{

                $storyId = $this->view->didUserLockOrUnlock();// Kollar om användaren har försökt att låsa en story...
                if($storyId != false){//

                    $unOrLockedStoryID = $this->view->getLastStoryID();
                    $this->db->unOrLockStoryByStoryID($unOrLockedStoryID);
                    $this->cookieJar->save("Your story lock status was changed!");
                    $this->view->goToFirstPage();
                }
                if($this->view->didUserDeleteStory()){ // Användaren har påbörjat att radera en story

                    $isConfirmed = $this->loginController->DidUserConfirmWithPass(); //har användaren fyllt i lösenordet, annar presentera lösenordboxen
                    if($isConfirmed ){
                        $passwordToTest = $this->loginController->getUserPassForConfirm();
                        if($this->loginController->controlPassword($passwordToTest)){ // om lösenordet stämmer så tar vi bort historien, annars inte..
                            $StoryID = $this->view->getLastStoryID();
                            $this->db->removeStoryByID($StoryID);
                            $this->cookieJar->save("Your story was deleted!");
                            $this->view->goToFirstPage();
                        }else{
                            $this->cookieJar->save("Your story was not deleted!");
                            $this->view->goToFirstPage();
                        }

                    }else{
                        return $this->loginController->presentConfirmWihPass($user,$this->view->getLastStoryID());
                    }


                }

                return $this->view->presentEditStories();
            }

        }
    }

    private function statusController()
    {
        $storyToAddToBackpack = $this->view->getAddedStoryToBackpackID();
        if($storyToAddToBackpack != false){//kollar av om en story ska läggas till i backpack...
            $this->storyController->addStoryToBackpack($storyToAddToBackpack, $this->db->getUserDetail($this->loginController->checkForLoggedInAndReturnUserName(),2));
            // Litet meddelande här?
        }
        $storyToRemoveFromBackpack = $this->view->getStoryToRemoveFromBackpack();
        if($storyToRemoveFromBackpack  != false){
            $this->storyController->removeStoryFromBackpack($storyToRemoveFromBackpack, $this->db->getUserDetail($this->loginController->checkForLoggedInAndReturnUserName(),2));

        }
    }

    public function getYoutubePlayer()
    {
        $standardPlaylist = "?list=PL6674FE0F7323E5BC";
        $player = new YoutubePlayer($standardPlaylist, true);//strängen är standardlistan...

        if($this->view->hasUserChangedYTPlaylist()){
            $this->cookieJar->setCookieForYTPlaylist($this->view->getUserYTPlaylist());
        }

        if($this->cookieJar->isCookieForYTSet()){
            $player->changePlaylist($this->cookieJar->getYTplaylistFromCookie());
        }
        if($this->view->hasUserChangedYTPlaylistToStandard()){
            $this->cookieJar->setCookieForYTPlaylist($standardPlaylist);
        }
        if($this->view->hasUserChangedYTAutoplay()){
            $this->cookieJar->setCookieForYTAutoPlay();
        }
        $player->turnOnAutoPlay($this->cookieJar->getCookieForYTAutoPlay());

        return $player->getPlayListPlayer().$this->view->getYTPlaylistInput();
    }


}