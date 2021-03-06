<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:09
 */
require_once("src/LoginComponent/Model/Date.php");
include_once("src/LoginComponent/Model/UserModel.php");
include_once("src/Models/cookieJar.php");

class view {
    private $model;
    public $CookieJar;

    public function __construct($model){
        $this->CookieJar = new CookieJar();
        $this->model = $model;
    }

    public function getClientidentifier($loginTroughCookies = false, $withoutUserName = false){ //parametrarna är som standard false...
    //returnerar det aktiva användarnamnet, ip och webbläsarinfo som identifierare...
        $arrayWithIdentifiers = array();
        $arrayWithIdentifiers[UserModel::$clientIp] = $_SERVER["REMOTE_ADDR"];// Clients IP
        $arrayWithIdentifiers[UserModel::$clientBrowser] = $_SERVER["HTTP_USER_AGENT"];//  Clients browserdetails

        if($withoutUserName){
            //om ej användarnamn behövs så returnerar vi här
            return $arrayWithIdentifiers;
        }

        if($loginTroughCookies){
            //om $loginTroughCookies är true så ska användarnamnet i kakan returneras...
            $arrayWithIdentifiers[UserModel::$clientOnline] = $this->CookieJar->getUserOrPasswordFromCookie(true); // 2: username
            return $arrayWithIdentifiers;

        }else{
            //om kakor inte används, så har användaren loggat in och då hämtas namnet via Post...
            $arrayWithIdentifiers[UserModel::$clientOnline] = $_POST["name"]; // 2: username
            return $arrayWithIdentifiers;
        }

    }

    public function loginTroughCookies(){
        //hämtar ut användaruppgifterna i kakorna
        $userName = $this->CookieJar->getUserOrPasswordFromCookie(true);
        $userPass = $this->CookieJar->getUserOrPasswordFromCookie(false);

        //Testar om användarnamnet och lösenordet är giltiga
        $shouldBeTrue = $this->model->tryLogin($userName, $userPass, true);

        //kollar så att kakan är giltig... (tidsstämpeln ej för gammal..)
        $cookieIsLegal = $this->CookieJar->isCookieLegal($userName);

        //Sessionstölder kollas också, men det görs genom
        //LoginController>doControl>...>is userOnline (rad68)> här görs sessionstöldskollarna


        if($shouldBeTrue && $cookieIsLegal){
            //header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . "&logintroughcookies");
            //header location förstörde, så jag satte GETs manuellt...
            $_GET["loggedin"] = "";
            $_GET["logintroughcookies"] = "";
            return true;
        }else{
            //om något inte stämmer så slängs datan och ett felmeddelande sparas för
            //att visas på loginskärmen...
            $this->CookieJar->clearUserForRememberMe();
            $this->CookieJar->save("<p>Felaktig information i cookie</p></ b>");

            return false;
        }

    }

    public function userIsOnlineView(){

        if(isset($_GET["loggedin"])){
            //om $_GET["loggedin"] finns så är det första gången sidan laddas
            //Då ska vi presentera ett meddelande.. här skapas meddelandet..
            $_GET["loggedin"] = null; //undesettar denna för att det inte påverkas nästa gång

            //Börjar på strängen som alltid kommer användas vid inloggning
            $welcomeString = "Inloggning lyckades";

            if(isset($_GET["rememberme"])){
               //Om rememberMe finns, så ska vi lägga till en ytterligare sak i välkommstexten...
                $welcomeString .= " och vi kommer ihåg dig nästa gång";
                $_GET["rememberme"] = null; // unsettar denna för att det inte påverkas nästa gång

            }
            if(isset($_GET["logintroughcookies"])){
                //samma som övre if-satsen..
                $welcomeString .= " via cookies";
                $_GET["logintroughcookies"] = null; //unsettar denna......
            }

            //sparar ner strängen för senare bruk
            $this->CookieJar->save($welcomeString);

            //när meddelandet är satt ska sidan laddas om utan "loggedin" (som vi nullade)...
            //då returneras detta och kör else-satsen istället...
            return $this->userIsOnlineView();
            //header("Location: " . $_SERVER["PHP_SELF"]); <-- detta är djävulen, jag lovar...

        }else{

            if($this->hasUserdemandLogout()){
                //Om en användare har tryckt på logga ut så ska vi tömma sessionsvariablarna som
                //identifierar inloggade användare...
                $this->model->logoutUser();

                //Vi vill också tömma eventuella kakor, om de finns...
                if($this->doesCookiesExist()){
                    $this->CookieJar->clearUserForRememberMe();
                }

                //sparar undan felmeddelande till senare
                $this->CookieJar->save("Du har nu loggat ut!");

                //vi laddar om sidan, och har förberett ett meddelande till clienten
                header("Location: " . $_SERVER["PHP_SELF"]);
                //^här är header(loc... ok då vi ändå inte behöver spara några variabler etc..
                //header(loc... börjar om från index.php och tömmer alla variabler som appen sparat...
            }else{
                //om ingen utloggning efterfrågas så presenteras eventuella meddelande och inloggadskärm.
                //$message = $this->CookieJar->load();
                //$message <-- har vart i $viewToReturn...
                $viewToReturn = "

                    <p>You are online!</p>
                    <a href='?logout'>Logout</a>
                    ";

                return $viewToReturn;
            }

        }

    }

    public function hasUserdemandLogout(){
        if(isset($_GET["logout"])){
           return true;
        }
        else{
            return false;
        }
    }

    public function ifPersonUsedLogin(){
        if(isset($_POST["loginButton"])){

            return true;
        }
        return false;
    }

    public function doesCookiesExist(){

        if(@isset($_COOKIE["CookieUserName"]) && @$_COOKIE["CookieUserPass"]){
            return true;
        }
        return false;

    }


    public function ifPersonTriedToLogin(){
    //Vi testar om det angivna uppgifterna stämmer
        //hämtar ner inloggningsuppfterna och testar dessa med tryLogin
        $hashedPassIfSucsess = $this->model->tryLogin(@$_POST["name"], @$_POST["password"]);

        if($hashedPassIfSucsess != false){
        // $hashedPassIfSucsess innehåller antingen det hashade lösenordet (om lyckad inloggning)
        // eller false, om lösenordet ej stämde...

            //Här kollar vi också om "rememberMe" är ikryssad.
            if(@isset($_POST["rememberme"]) && $_POST["rememberme"] == "on"){

                //Om den är det så ska vi spara undan lösen+användarnamn i kakor
                //som ska återanvändas nästa gång sidan besöks...
                $this->CookieJar->saveUserForRememberMe($_POST["name"],$hashedPassIfSucsess);

                //anger en get som bekräftelse för att rememberme används, denna kollas av i userIsOnlineView
                $_GET["rememberme"] = "";
            }

            //Vi lägger till GET så vi kan se när man precis loggat in...
            $_GET["loggedin"] = "";

            //header("Location: " . $_SERVER["PHP_SELF"] . "?loggedin" . $forRememberMe);
                //^djävul!!! förstörde allt ett tag..

            return true;
        }else{
            return false;
        }
    }

    public function presentLoginForm(){

        //hämtar ut datum
        $date = new Date();
        $date = $date->getDateTime(true);

        //kollar om vi ska varna för tomt lösen/användarnamn:
        //För användarnamnet
        //var_dump($_POST);
        if(@trim($_POST["name"]) == "" && @isset($_POST["name"])){
            $message = "<p>Username is missing!</p></ b>";
            $currentUserName = "";
        }else{
            $message = "";

            if(@$_POST["name"] != ""){
                //hämtar ut användarnamnet...
                $currentUserName = "value=" . @$_POST["name"];
            }else{
                //Om användarnamnet inte är "" och inte heller när den är trimmad fast ändå satt
                //Då sätter vi currentUserName till tomsträng ""...
                $currentUserName = "";
            }

        }


        //För lösenordet
        if(@trim($_POST["password"]) == "" && @isset($_POST["password"]) ){
            $message2 = "<p>Password is missing</p></ b>";

        }else{
            $message2 ="";
        }

        //om inget meddelande skickas och användarnamnen är satta så är det fel på lösenordet/användarnamnet
        if( $message == "" &&
            $message2 == "" &&
            @isset($_POST["password"]) &&
            @isset($_POST["name"])){
            $message2 = "";
            $message = "<p>Username or password is not correct!</p></ b>";
        }

        //Om det finns något att hämta i vår CookieMessage-kaka så ska den presenteras
        //$message3 = $this->CookieJar->load(); //gör detta på ett annat sätlle...
            //<!--$message3 -->
        //Htmln som ska åka ut på klienten
        $ToClient ="
                    <h3>Not online</h3>
                    <p>{$this->message}</p>
                    <form  method='post'>
                        <fieldset id='login'>
                        $message
                        $message2

                            <legend>Login - Fill username and password</legend>
                            <label for='name'>Username</label>
                            <input type='text' id='name' name='name' $currentUserName >
                            <label for='pass'>Password</label>
                            <input type='password' id='pass' name='password'>

                        </fieldset>
                        <input type='submit' value='Login' name='loginButton' >
                        <input type='checkbox' name='rememberme' id='rememberme'>
                        <label for='rememberme'>Rembember me.</label>
                    </form>

                    <form action='?register' method='post'>
                        <input type='submit' name='register' value='Register'>
                    </form>

                    <p>$date</p>
                    ";




        return $ToClient;

    }
    public function getUserName()
    {
        if(!empty($_POST['username']))
        {
            return trim($_POST['username']);
        }
        else
        {
            return "";
        }
    }
    public function getfirstName()
    {
        if(!empty($_POST['firstname']))
        {
            return trim($_POST['firstname']);
        }
        else
        {
            return "";
        }
    }
    public function getlastName()
{
    if(!empty($_POST['lastname']))
    {
        return trim($_POST['lastname']);
    }
    else
    {
        return "";
    }
}

    public function getRegisterForm(){

        $ret = "
                    <h3>You're not online, Register</h3>
                    <a href='{$_SERVER["PHP_SELF"]}'>Back</a>
                    <p>{$this->message}</p>
                    <form action='?register' method='post' id='regform'>
                        <fieldset id='register'>
                            <legend>Register - Fill in user details</legend>
                            <label for='UserNameID'>Username :</label>
                            <input type='text' size='20' name='username' id='UserNameID' value='{$this->getUserName()}'>
                            <label for='PasswordID'>Password :</label>
                            <input type='password' size='20' name='password' id='PasswordID' value>
                            <label for='RepeatPasswordID'>Repeat password :</label>
                            <input type='password' size='20' name='repeatpassword' id='RepeatPasswordID' value>
                            <label for='firstName'>Firstname :</label>
                            <input type='text' size='20' name='firstname' id='firstName' value='{$this->getfirstName()}'>
                            <label for='lastName'>Lastname :</label>
                            <input type='text' size='20' name='lastname' id='lastName' value='{$this->getlastName()}'>
                        </fieldset>
                        <input type='submit' name='regist' value='Register'>
                    </form>

                    <p>{$this->date}</p>
                 ";

        return $ret;

    }
    public function getRegistrationDetailFromForm()
    {
        $ArrToReturn = array();
        $ArrToReturn["password"] = $_POST["password"];
        $ArrToReturn["repeatpassword"] = $_POST["repeatpassword"];
        $ArrToReturn["username"] = $_POST["username"];
        $ArrToReturn["firstname"] = $_POST["firstname"];
        $ArrToReturn["lastname"] = $_POST["lastname"];
        return $ArrToReturn;
    }
    public function didUserPressRegister()
    {
        if(isset($_POST['register']) || isset($_GET['register'])){
            return true;
        }else{
            return false;
        }
    }
    public function didUserTryToRegister(){
        return isset($_POST["regist"]);
    }
    public function setMessage($message)
    {

        if(gettype($message) === "array"){
            $longstring ="";
            for($i=0; $i < count($message);$i++){
                $longstring .= $message[$i] . ". <br>";
            }
            $message = $longstring;
        }

        $this->message = $message;
    }

    public function presentConfirmWithPass($user, $storyID)
    {
        $owner = $this->getUserName();
        $ret = "
        <form action='' method='post' id='confirmForm'>
            <fieldset>
                <legend>Fill in your password to confirm!</legend>
                <label for='pass'>Password :</label>
                <input type='password' size='100' name='pass' id='pass' required>
                <input type='submit' name='confirmWithPass' value='Confirm!'>
                <input type='hidden' name='user' value='$user'>
                <input type='hidden' name='storyID' value='$storyID'>
                <input type='hidden' name='confirmWithPass' >
                <input type='hidden' name='delete' >
            </fieldset>
        </form>
        ";
        return $ret;
    }

    public function getUserPassFromConfirm()
    {
        return $_POST["pass"];
    }


}