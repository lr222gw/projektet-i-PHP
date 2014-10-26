<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:47
 */
require_once("src/Models/cookieJar.php");
class HTMLview{
    private $message;
    private $cookieJar;
    public function __construct(){
        $this->cookieJar = new CookieJar();
        //$this->message = $this->cookieJar->load();

    }

    public function addMessageToShow($message){
        $this->message = $message; //$_POST['message']
    }

    public function presentPage($loginBox, $content, $uploadstorybox,  $youTube, $menu){//$editStories
        $message1 = $this->cookieJar->load();
        if($message1 != null){
            $message = "<p id='messageToUser'>$message1</p>";//$_POST['message']

        }else{ $message = "";}

        //$editStories

        echo "
                <!DOCTYPE html>
                <html>
                <head>
                <meta charset=\"utf-8\" />
                <title>CreepyReads</title>
                <link rel='stylesheet' href='src/css/style.css'>
                </head>
                <body>
                    $menu
                    $message

                    <div id='loginBox'>
                    $loginBox
                    </div>
                    <div id='uploadStory'>
                    $uploadstorybox
                    </div>
                   <!-- <div id='editStory'>

                    </div>-->

                    $content
                    <div id='youtubeplayer'>
                    $youTube
                    </div>
                    <p id='aboutcookie' style='bottom: 0; position: fixed; color: red; font-weight: bold;'>Obs, sidan använder cookies, genom att använda applikationen godkänner du cookies.</p>
                </body>
                </html>

            ";
    }


}