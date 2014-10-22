<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-14
 * Time: 19:07
 */
require_once("src/Models/DOA_dbMaster.php");
require_once("src/LoginComponent/Controller/LoginController.php");
require_once("src/StoryComponent/Controller/StoryController.php");
require_once("src/Views/MainView.php");
require_once("src/Views/HTMLview.php");
require_once("src/LoginComponent/CookieJar.php");
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

            $this->htmlview->addMessageToShow($this->cookieJar->load());

            if($this->view->hasUserSubmited()){

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
                return $this->view->presentEditStories();
            }

        }
    }


}