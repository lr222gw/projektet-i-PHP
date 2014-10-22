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
    public function presentPage($loginBox, $content, $uploadstorybox, $editStories){
        if(isset($_POST['message'])){
            $message = "<p id='messageToUser'>".$_POST['message']."</p>";
        }else{ $message = "";}


        echo "
                <!DOCTYPE html>
                <html>
                <head>
                <meta charset=\"utf-8\" />
                <title>CreepyReads</title>
                </head>
                <body>
                    $message
                    <div id='editStory'>
                    $editStories
                    </div>
                    <div id='uploadStory'>
                    $uploadstorybox
                    </div>
                    $content
                    <div id='loginBox'>
                    $loginBox
                    </div>
                    <p style='bottom: 0; position: fixed; color: red; font-weight: bold;'>Obs, sidan använder cookies, genom att använda applikationen godkänner du cookies.</p>
                </body>
                </html>

            ";
    }


}