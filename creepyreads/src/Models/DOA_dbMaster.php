<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-14
 * Time: 19:08
 */

class DOA_dbMaster{

    private static $pdoString = 'mysql:host=creepyreads-199508.mysql.binero.se;dbname=199508-creepyreads;';
    private static $pdoUserName = '199508_kb29675';
    private static $pdoUserPass = 'Gran14gran14';

    public function __construct(){

    }
    private function getMySQLQuery($queryString){ //används till de flesta....

        try{
            $databaseHandler = new PDO(self::$pdoString, self::$pdoUserName, self::$pdoUserPass);
            $databaseHandler->beginTransaction();
            $databaseHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Om det skickas in en array så måste forloop utföras..
            if(count($queryString) >= 2){ //finns det 2 eller mer så är det en array...
                for($i=0;$i<count($queryString); $i++){
                    $stmt = $databaseHandler->prepare($queryString[$i]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
                }
            }else{ $stmt = $databaseHandler->prepare($queryString); }

            //Om koden är godkänd här så exekveras den och alternativt så får vi ett värde tillbaka.


            if(count($queryString) >= 2){
                for($i=0;$i<count($queryString); $i++){
                    $final = $databaseHandler->query($queryString[$i]);

                }
            }else{ $final = $databaseHandler->query($queryString); }
            $databaseHandler->commit();
            return $final;



            return $result;
        }catch(PDOException $e) {
            $databaseHandler->rollBack();
            die("Sorry Database Error..." . $e->getMessage());
        }
    }

    public function regUser($userName, $userPassword, $firstName, $lastName){
        //if(checkIfUserNameAlreadyExist($userName)){
            $queryString = 'INSERT INTO member(userName, userPassword, firstName, lastName) values("'.$userName.'", "'.$userPassword.'", "'.$firstName.'", "'.$lastName.'")';

            $this->getMySQLQuery($queryString);
        //}else{
        //    return false;
        //}

    }


    /**
     *
     * @param $userName
     * @param $whatField : a value between 0 to 2. 0=password, 1=username, 2=memberid
     * @return mixed
     */
    public function getUserDetail($userName, $whatField){
        $queryString = "
SELECT userPassword, userName, memberID
FROM member
WHERE userName = '{$userName}';";

        $query = $this->getMySQLQuery($queryString);

        return $query->fetch()[$whatField]; //return lösenord...
                // ^= array, [0] = lösenord. enda saken i arrayen...
    }

    public function checkIfUserNameAlreadyExist($userName){

        $userInDB = $this->getUserDetail($userName, 1);

        return $userInDB == $userName; //return lösenord...
        // ^= array, [0] = lösenord. enda saken i arrayen...
    }

    public function getAllUsers(){

        $query = "
SELECT userName
FROM member;";

        $story = $this->getMySQLQuery($query);
        return $story->fetchAll(PDO::FETCH_COLUMN);
    }


    public function getAllStoriesAndDetails(){
        $query = "SELECT story.storyID, story, userName, (select detailValue from detailTypeOnStory where detailTypeID = 3 AND story.storyID = detailTypeOnStory.storyID) as title, genreTypeValue as genre, langType.typeOfLangType, (select detailValue from detailTypeOnStory where detailTypeID = 1 AND story.storyID = detailTypeOnStory.storyID) as Author
FROM story
	left JOIN genre On  genre.storyID = story.storyID
	left Join typeOfGenre ON typeOfGenre.typeOfGenreID = genre.typeOfGenreID
	left join member ON story.memberID = member.memberID
	left join langType on story.langTypeID = langType.langTypeID
;";

        $story = $this->getMySQLQuery($query);
        //$story->fetch()

        return $story->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScoreDataFromStoryID($storyID){
        $query = "
        select story.storyID, scoreValue.scoreValue
        from story
	    left join score	on score.storyID = story.storyID
	    left JOIN scoreValue on score.scoreValueID = scoreValue.scoreValueID
        where story.storyID = '{$storyID}';";

        $scoredata = $this->getMySQLQuery($query);
        //$story->fetch()
        $scoreData = $scoredata->fetchAll(PDO::FETCH_ASSOC);

        return $scoreData;

    }
    public function getCommentsFromStoryID($storyID){
        $query = "
        select story.storyID, comments.comment
        from story
	    left join comments	on comments.storyID = story.storyID
        where story.storyID = '{$storyID}';";

        $comment = $this->getMySQLQuery($query);
        //$story->fetch()
        $commentsOfStory = $comment->fetchAll(PDO::FETCH_ASSOC);

        return $commentsOfStory;
    }

    public function getStoryByID($thisStoryID){
        $query = "
        SELECT story
        FROM story
        WHERE storyID = $thisStoryID;";

        $story = $this->getMySQLQuery($query);
        return $story->fetch()[0];
    }

    public function replaceStory($storyID, $newStory){
        $query = "
Update story
SET story = '{$newStory}'
WHERE storyID = '{$storyID}' ;";

        $this->getMySQLQuery($query);

    }

    public function addStory($memberID, $thisLangTypeID, $thisStory, $title, $typeOfGenreID, $author = ""){

        $queryString = [];
        try{
            $databaseHandler = new PDO(self::$pdoString, self::$pdoUserName, self::$pdoUserPass);
            $databaseHandler->beginTransaction();
            $databaseHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $queryStringInsertStory = "
INSERT INTO story (memberID, langTypeID, story)
VALUES('{$memberID}','{$thisLangTypeID}','{$thisStory}')";
            $stmt = $databaseHandler->prepare($queryStringInsertStory); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt->execute();

            $LastInsertedID = $databaseHandler->lastInsertId();

            if($author == ""){ // om inget användarnamn skickas med så används användarens användarnamn...
                $que = "select userName from member where memberID = '{$memberID}'";
                $stmt = $databaseHandler->prepare($que);
                $userName = $databaseHandler->query($que);

                $author = $userName->fetch()[0];
            }
            $queryString[0] = "
INSERT INTO detailTypeOnStory (storyID, detailTypeID, detailValue)
VALUES('{$LastInsertedID}', 1,'{$author}')";


            $queryString[1] = "
INSERT INTO detailTypeOnStory (storyID, detailTypeID, detailValue)
VALUES('{$LastInsertedID}', 3,'{$title}')";

            $queryString[2] = "
INSERT INTO genre(storyID, typeOfGenreID)
VALUES('{$LastInsertedID}', '{$typeOfGenreID}');";

            $stmt2 = $databaseHandler->prepare($queryString[0]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt2->execute();

            $stmt3 = $databaseHandler->prepare($queryString[1]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt3->execute();

            $stmt4 = $databaseHandler->prepare($queryString[2]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt4->execute();

            //Om koden är godkänd här så exekveras den och alternativt så får vi ett värde tillbaka.

            $databaseHandler->commit();


        }catch(PDOException $e) {
            $databaseHandler->rollBack();
            die("Sorry Database Error..." . $e->getMessage());
        }
    }


}