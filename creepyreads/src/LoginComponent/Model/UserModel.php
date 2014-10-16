<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:25
 */
require_once("src/LoginComponent/Model/FileMaster.php");
require_once("src/Models/DOA_dbMaster.php");
Class UserModel {
    private $fileMaster;
    static $clientOnline = "ClientOnline";
    static $clientIp = "ClientIp";
    static $clientBrowser = "ClientBrowser";
    private $db;

    public function __construct(){
        $this->fileMaster = new FileMaster();
        $this->db = new DOA_dbMaster();
    }

    public function isNOTSessionThief($clientIdentifier){

        if( $_SESSION[self::$clientIp] == $clientIdentifier[self::$clientIp]&&
            $_SESSION[self::$clientBrowser] == $clientIdentifier[self::$clientBrowser]){
        //tittar så att uppgifterna i sessionen stämmer med uppgifterna på klienten
            return true;
        }

        //om de ej stämde så loggas användare ut
        $this->logoutUser();
        return false;
    }

    public function doLogin($identifier){
        //Användarnamnet+lösenordet är rätt, nu kan vi räknas som inloggade

        $_SESSION[self::$clientOnline] = $identifier[self::$clientOnline];

        //Här ska vi även sätta spärrar som berättar vad anvädnare använde för ip eller webbläsare.
        $_SESSION[self::$clientIp] = $identifier[self::$clientIp];
        $_SESSION[self::$clientBrowser] = $identifier[self::$clientBrowser];

        //användaren tolkas nu som inloggad...
    }

    public function isUserOnline(){
        //om denna är satt så är användaren online...
        if(isset($_SESSION[self::$clientOnline])){
            return true;
        }

        return false;
    }

    public function logoutUser(){
        //tar bort sessionsdata och sätter allt till null (kanske lite onödigt, då jag ändå dödar sessionen)
        $_SESSION[self::$clientOnline] = null;
        $_SESSION[self::$clientBrowser] = null;
        $_SESSION[self::$clientIp] = null;
        session_destroy();
    }

    public function cryptPass($passwordToCrypt, $rounds = 9){
        //krypterar lösenordet med blowfish, har följt denna guide https://www.youtube.com/watch?v=wIRtl8CwgIc
        //returnerar det krypterade lösenordet
        //OBS: blowfish verkar kräva nyare version av php. version.5.2.12 verkar ej funka...
        $salt = "";

        //skapar en lång array med alla (typ) tecken från alfabetet + siffrorna 0-9
        $saltChars = array_merge(range("A","Z"), range("a","z"), range(0,9));

        for($i=0;$i < 22;$i++){ // for loop som ska utföras 22 gånger, Blowfish behöver 22 blandade tecken..
            $salt .= $saltChars[array_rand($saltChars)];
            //För varje "varv" så läggs en slumpad karaktär från arrayen in i strängen $salt (22blandade tecken)
        }

        //Nu ska vi kryptera och returnera det krypterade lösenordet!
        return crypt($passwordToCrypt, sprintf("$2y$%02d$", $rounds) . $salt);//
        // "$2y$%02d$" är den delen som gör att vi krypterar genom blowfish, endast "$2y$" är nödvändigt
        // det andra är "extra saker" som killen i videon sa var bra (lite svårare att kryptera...)
    }

    public function checkIfPasswordIstrue($inputFromUser, $usersHashedPassword){
        //vi krypterar det inmatade lösenordet, testar om det är samma som användarens lösenord
        //om så är fallet så stämde lösenordet...

        $shouldBeSameAsHashed = crypt($inputFromUser, $usersHashedPassword);

        if($shouldBeSameAsHashed == $usersHashedPassword){
            return true;
        }else{
            return false;
        }
    }

    public function tryLogin($username, $password, $loginTroughCookies = false){
    //användaren har tryckt på login knappen, tagit emot användardatan och ska testa den

        //Först ska vi kolla om det finns en anvnädare med det angivna användarnamnet
        //1. hämta ner arrayen med användarnamn
        //$users = $arrOfUsersAndPassword; //$this->fileMaster->getUserOrPasswordList("username");

        if($this->db->checkIfUserNameAlreadyExist($username)){

            $usersPassword = $this->db->getUserDetail($username, 0);

            if($loginTroughCookies){
            //om vi loggar in genom kakor så ska vi inte hasha lösenordet, bara jämföra...

                if( $usersPassword == $password){
                    return true;
                }else{
                    //om detta ej stämmer så är det något fel på kakan
                    //den är troligtvis manipulerad, vi ska ta bort allt då...(görs i kakmetoden)
                    return false;
                }

            }else{
            // om $loginTroughCookies inte används, så är det en vanlig inloggning

                //crypterar det angivna lösenordet och testar om det är samma som användarens
                if($this->checkIfPasswordIstrue($password, $usersPassword)){
                    return $this->db->getUserDetail($username, 0);
                    //vi returnerar det hashade lösenordet om
                    //det stämde, då kan vi (om vi vill) lägga den i en kaka...
                }
            }

        }


        return false;
    }

    private function userDoesExist($usernameToCheck, $listOfAllUserNames){
        //hämtar ner array med användarnamn
        for($i = 0; $i < count($listOfAllUserNames); $i++){
            if(trim($listOfAllUserNames[$i]) === $usernameToCheck ){
                return true;
            }
        }
        return false;

    }
    public function checkForInvalidChars($stringToCheckMeanCharsIn){

        if(preg_match("/[^-a-z0-9åäö_]/i", $stringToCheckMeanCharsIn)){
            return true;
        }
        return false;
    }

    public function checkValidRegistrationData($userDetails, $arrayOfAllUsers){
        $username = $userDetails["username"];
        $password = $userDetails["password"];
        $rpassword = $userDetails["repeatpassword"];
        $firstname = $userDetails["firstname"];
        $lastname = $userDetails["lastname"];

        $arrWithErrorMessages = array();
        if($this->userDoesExist($username, $arrayOfAllUsers)){
            array_push($arrWithErrorMessages, "Username is in use");

        }else if($this->checkForInvalidChars($username)){
            array_push($arrWithErrorMessages, "Username contains illegal characters");

        }else if(strlen($username) < 3){
            array_push($arrWithErrorMessages, "Username has not enough characters. Pick a name with at least 3");

        }

        if($this->checkForInvalidChars($firstname)){
            array_push($arrWithErrorMessages, "Firstname contains illegal characters");

        }else if(strlen($firstname) < 3){
            array_push($arrWithErrorMessages, "Firstname has not enough characters. Pick a name with at least 3");

        }
        if($this->checkForInvalidChars($lastname)){
            array_push($arrWithErrorMessages, "Lastname contains illegal characters");

        }else if(strlen($lastname) < 3){
            array_push($arrWithErrorMessages, "Lastname has not enough characters. Pick a name with at least 3");

        }


        if(strlen($password) < 6){
            array_push($arrWithErrorMessages, "Password has not enough characters. Pick a name with at least 6");
        }else if($password !== $rpassword){

            array_push($arrWithErrorMessages, "Repeated password was not same as password");

        }

        if(count($arrWithErrorMessages) == 0){
            return true;
        }else{

            return $arrWithErrorMessages;
        }
    }

    public function getOnlineUser()
    {
        return $_SESSION[self::$clientOnline];
    }

}