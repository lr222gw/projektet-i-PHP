<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-16
 * Time: 18:40
 */

require_once("src/LoginComponent/Model/FileMaster.php");
class CookieJar {
    //Class "tagen" från lektionsexempel : https://github.com/dntoll/1dv408-HT14/blob/master/Like/src/CookieStorage.php
    private static $cookieMessage = "CookieMessage";
    private static $cookieUserName = "CookieUserName";
    private static $cookieUserPass = "CookieUserPass";
    private static $ytPlaylist = "ytPlaylist";
    private static $autoplay = "autoplay";
    private $fileMaster;

    public function __construct(){
         $this->fileMaster = new FileMaster();
    }

    public function save($stringToSave){

        setcookie(self::$cookieMessage, $stringToSave, -1);
        $_COOKIE["CookieMessage"] = "<p>".$stringToSave."</p></ b>"; //säkerhetsåtgärd... (tips från skolan..)
        //Sparar kakan i cookiearrayens nyckel "CookieMessage"
        //Värdet är värdet av $stringToSave
        //-1 = kakan försvinner när sessionen är klar.

    }

    public function load(){

        if(isset($_COOKIE[self::$cookieMessage])){
            //om det finns något i kakan så ska det returneras
            $returnThis = $_COOKIE[self::$cookieMessage];
        }else{
            $returnThis = ""; //annars returnerar vi tomsträng...
        }

        //nu när vi laddat kakan så vill vi se till att platsen är ledig
        //så vi slänger kakans värde, genom att sätta den till "".
        setcookie(self::$cookieMessage,"", time() -1);
        //Genom att ange "time() -1" så säger vi att detta hände för en sekund sen

        return $returnThis;
    }

    public function isCookieLegal($userToCheck){
        //kollar så att ingen har manipulerat kakans tidsstämpel...

        if(time() > $this->fileMaster->returnTimestamp($userToCheck)){
            return false;
        }else{
            return true;
        }

    }


    public function saveUserForRememberMe($userName, $userPass){
        $longTime = 50000*30; //typ 15 dagar?...
        $timestamp = $this->fileMaster->setAndGetTimestamp($userName, $longTime);

        setcookie(self::$cookieUserName, $userName, $timestamp);
        setcookie(self::$cookieUserPass, $userPass, $timestamp);
    }

    public function getUserOrPasswordFromCookie($trueForUser){

        if($trueForUser){
            return $_COOKIE["CookieUserName"];
        }else{
            return $_COOKIE["CookieUserPass"];//What is this??...
        }

    }

    public function clearUserForRememberMe(){
        setcookie(self::$cookieUserName, null, -1);
        setcookie(self::$cookieUserPass, null, -1);
    }

    public function setCookieForYTPlaylist($playlistToRemember){
        setcookie(self::$ytPlaylist,$playlistToRemember,-1);
        $_COOKIE[self::$ytPlaylist] = $playlistToRemember;
    }
    public function setCookieForYTAutoPlay(){

        if($_COOKIE[self::$autoplay] == true){
            setcookie(self::$autoplay,false,-1);
            $_COOKIE[self::$autoplay] = false;
        }else{
            setcookie(self::$autoplay,true,-1);
            $_COOKIE[self::$autoplay] = true;
        }

    }
    public function getCookieForYTAutoPlay(){

        if(isset($_COOKIE[self::$autoplay])){
            return $_COOKIE[self::$autoplay];
        }
        return false;
    }
    public function getYTplaylistFromCookie(){
        if(isset($_COOKIE[self::$ytPlaylist])){
            return $_COOKIE[self::$ytPlaylist];
        }
        return false;
    }
    public function isCookieForYTSet(){
        if(isset($_COOKIE[self::$ytPlaylist])){
            return true;
        }
        return false;
    }

}