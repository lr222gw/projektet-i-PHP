<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:47
 */
class HTMLview{
    public function addMessageToShow($message){
        $_POST['message'] = $message;

    }
    public function presentPage($loginBox, $content, $uploadstorybox,  $youTube, $menu){//$editStories
        if(isset($_POST['message'])){
            $message = "<p id='messageToUser'>".$_POST['message']."</p>";
        }else{ $message = "";}


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
                    <div id='editStory'>
                    $editStories
                    </div>

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