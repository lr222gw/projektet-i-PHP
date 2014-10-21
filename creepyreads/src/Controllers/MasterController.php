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
class MasterController {
    private $storyController;
    private $db;
    private $loginController;
    private $view;

    public function __construct(){
        $this->loginController = new LoginController();
        $this->db = new DOA_dbMaster();
        $this->storyController = new StoryController();
        $this->view = new MainView();
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
            if($this->view->hasUserSubmited()){
                //om användaren har submittat så ska vi försöka spara ner datan...
                $newStoryData = $this->view->retrieveSubmittedData();
                $this->view->clearPost();

                $this->db->addStory($this->db->getUserDetail($newStoryData["user"],2),(int)$newStoryData["language"],$newStoryData["story"],$newStoryData["title"],(int)$newStoryData["genre"],$newStoryData["author"]);
                $this->view->goToBegining();

            }
            else if($this->view->hasUserAcsessedUploadBox()){
                return $this->view->presentUploadForm($user);;
            }else{
                return $this->view->presentUploadBox($user);
            }
        }else{

            return '';
        }
    }


}