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
    private function getMySQLQuery($queryString, $paramsArr){ //används till de flesta....

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
                     if($stmt->execute($paramsArr[$i])){
                         return false;
                     }

                }
                $databaseHandler->commit();
                return true;
            }else{ if($stmt->execute($paramsArr)){$databaseHandler->commit(); return $stmt;}; }
            $databaseHandler->rollBack();
            return false;



            return $result;
        }catch(PDOException $e) {
            $databaseHandler->rollBack();
            die("Sorry Database Error..." . $e->getMessage());
        }
    }

    public function regUser($userName, $userPassword, $firstName, $lastName){
        //if(checkIfUserNameAlreadyExist($userName)){
            $queryString = 'INSERT INTO member(userName, userPassword, firstName, lastName) values(?, ?, ?, ?)';
            $paramArr = [$userName, $userPassword, $firstName, $lastName];
            $this->getMySQLQuery($queryString,$paramArr);
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
WHERE userName = ?;";
        $param = [$userName];

        $query = $this->getMySQLQuery($queryString,$param);

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
        $emptyParam = [];

        $story = $this->getMySQLQuery($query,$emptyParam);
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
        $emptyParam = [];
        $story = $this->getMySQLQuery($query, $emptyParam);
        //$story->fetch()

        return $story->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScoreDataFromStoryID($storyID){
        $query = "
        select story.storyID, scoreValue.scoreValue
        from story
	    left join score	on score.storyID = story.storyID
	    left JOIN scoreValue on score.scoreValueID = scoreValue.scoreValueID
        where story.storyID = ?;";
        $param = [$storyID];
        $scoredata = $this->getMySQLQuery($query,$param);
        //$story->fetch()
        $scoreData = $scoredata->fetchAll(PDO::FETCH_ASSOC);

        return $scoreData;

    }
    public function getCommentsFromStoryID($storyID){
        $query = "
        select story.storyID, comments.comment
        from story
	    left join comments	on comments.storyID = story.storyID
        where story.storyID = ?;";
        $param = [$storyID];
        $comment = $this->getMySQLQuery($query,$param);
        //$story->fetch()
        $commentsOfStory = $comment->fetchAll(PDO::FETCH_ASSOC);

        return $commentsOfStory;
    }

    public function getStoryByID($thisStoryID){
        $query = "SELECT story.storyID, story, userName, (select detailValue from detailTypeOnStory where detailTypeID = 3 AND story.storyID = detailTypeOnStory.storyID) as title, genreTypeValue as genre, langType.typeOfLangType, (select detailValue from detailTypeOnStory where detailTypeID = 1 AND story.storyID = detailTypeOnStory.storyID) as Author
        FROM story
            left JOIN genre On  genre.storyID = story.storyID
            left Join typeOfGenre ON typeOfGenre.typeOfGenreID = genre.typeOfGenreID
            left join member ON story.memberID = member.memberID
            left join langType on story.langTypeID = langType.langTypeID
            WHERE story.storyID = ?
        ;";
        $param = [$thisStoryID];

        $story = $this->getMySQLQuery($query, $param);
        return $story->fetch(PDO::FETCH_ASSOC);
    }

    public function replaceStory($storyID, $newStory){
        $query = "
Update story
SET story = ?
WHERE storyID = ? ;";
        $params = [$newStory, $storyID];

        $this->getMySQLQuery($query, $params);

    }

    public function addStory($memberID, $thisLangTypeID, $thisStory, $title, $typeOfGenreID, $author = ""){

        $queryString = [];
        try{
            $databaseHandler = new PDO(self::$pdoString, self::$pdoUserName, self::$pdoUserPass);
            $databaseHandler->beginTransaction();
            $databaseHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $queryStringInsertStory = "
            INSERT INTO story (memberID, langTypeID, story)
            VALUES(?,?,?)";
            $params = [$memberID, $thisLangTypeID, $thisStory];

            $stmt = $databaseHandler->prepare($queryStringInsertStory); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt->execute($params);

            $LastInsertedID = $databaseHandler->lastInsertId();

            if($author == ""){ // om inget användarnamn skickas med så används användarens användarnamn...
                $que = "select userName from member where memberID = ?";
                $param = [$memberID];
                $stmt = $databaseHandler->prepare($que);
                if($stmt->execute($param)){
                    $author = $stmt->fetch()[0];
                }else{
                    throw new Exception("something went wrong with userName... in upload story section");
                }


            }
            $queryString[0] = "
INSERT INTO detailTypeOnStory (storyID, detailTypeID, detailValue)
VALUES(?, 1,?)";

            $queryString[1] = "
INSERT INTO detailTypeOnStory (storyID, detailTypeID, detailValue)
VALUES(?, 3,?)";

            $queryString[2] = "
INSERT INTO genre(storyID, typeOfGenreID)
VALUES(?, ?);";
            $param = [[$LastInsertedID,$author],[$LastInsertedID,$title],[$LastInsertedID,$typeOfGenreID]];


            $stmt2 = $databaseHandler->prepare($queryString[0]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt2->execute($param[0]);

            $stmt3 = $databaseHandler->prepare($queryString[1]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt3->execute($param[1]);

            $stmt4 = $databaseHandler->prepare($queryString[2]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt4->execute($param[2]);

            //Om koden är godkänd här så exekveras den och alternativt så får vi ett värde tillbaka.

            $databaseHandler->commit();


        }catch(PDOException $e) {
            $databaseHandler->rollBack();
            die("Sorry Database Error..." . $e->getMessage());
        }
    }

    public function getStoriesByUserID($userID)
    {
        $query = "SELECT story.storyID, story, userName, (select detailValue from detailTypeOnStory where detailTypeID = 3 AND story.storyID = detailTypeOnStory.storyID) as title, genreTypeValue as genre, langType.typeOfLangType, (select detailValue from detailTypeOnStory where detailTypeID = 1 AND story.storyID = detailTypeOnStory.storyID) as Author
        FROM story
            left JOIN genre On  genre.storyID = story.storyID
            left Join typeOfGenre ON typeOfGenre.typeOfGenreID = genre.typeOfGenreID
            left join member ON story.memberID = member.memberID
            left join langType on story.langTypeID = langType.langTypeID
            WHERE story.memberID = ?
        ;";
        $emptyParam = [$userID];
        $story = $this->getMySQLQuery($query, $emptyParam);
        //$story->fetch()

        return $story->fetchAll(PDO::FETCH_ASSOC);
    }

    public function EditStory($storyID, $memberID, $thisLangTypeID, $thisStory, $title, $typeOfGenreID, $author = "")
    {
        var_dump($storyID, $memberID, $thisLangTypeID, $thisStory, $title, $typeOfGenreID);
        //die();
        $queryString = [];
        try{
            $databaseHandler = new PDO(self::$pdoString, self::$pdoUserName, self::$pdoUserPass);
            $databaseHandler->beginTransaction();
            $databaseHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $queryStringInsertStory = "
            UPDATE story
            SET langTypeID = ?, story = ?
            Where storyID = ?;";
            $params = [$thisLangTypeID, $thisStory, $storyID];

            $stmt = $databaseHandler->prepare($queryStringInsertStory); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt->execute($params);

            if($author == ""){ // om inget användarnamn skickas med så används användarens användarnamn...
                $que = "select userName from member where memberID = ?";
                $param = [$memberID];
                $stmt = $databaseHandler->prepare($que);
                if($stmt->execute($param)){
                    $author = $stmt->fetch()[0];
                }else{
                    throw new Exception("something went wrong with userName... in upload story section");
                }


            }
            $queryString[0] = "
UPDATE detailTypeOnStory
set detailValue = ?
WHERE storyID = ? AND detailTypeID = 1;";

            $queryString[1] = "
UPDATE detailTypeOnStory
set detailValue = ?
WHERE storyID = ? AND detailTypeID = 3;";

            $queryString[2] = "
UPDATE genre
SET typeOfGenreID = ?
where storyID = ?;";
            $param = [[$author,$storyID],[$title,$storyID],[$typeOfGenreID, $storyID]];


            $stmt2 = $databaseHandler->prepare($queryString[0]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt2->execute($param[0]);

            $stmt3 = $databaseHandler->prepare($queryString[1]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt3->execute($param[1]);

            $stmt4 = $databaseHandler->prepare($queryString[2]); //Säkert för sql injection http://stackoverflow.com/questions/4700623/pdos-query-vs-execute
            $stmt4->execute($param[2]);

            //Om koden är godkänd här så exekveras den och alternativt så får vi ett värde tillbaka.

            $databaseHandler->commit();


        }catch(PDOException $e) {
            $databaseHandler->rollBack();
            die("Sorry Database Error..." . $e->getMessage());
        }

    }


}