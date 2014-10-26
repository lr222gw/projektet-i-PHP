/**
 * Created by Lowe on 2014-10-26.
 */
window.onbeforeunload = function(){
    saveUserStory();
    console.log(loadUserStory());
    if(loadUserStory() == ""){

    }else{
        return "You have not yet posted your Story, Sure you want to leave?";
    }
}
function toRun(){

    reziseLogin();
    reziseMusicPlayer();
    allowCookies();
    didUserActivateVoteSlide();
    userChangeStoryView();
    loadUserStory();


}
function loadUserStory(){
    $storyArea = document.getElementById("story");
    if($storyArea !== null){

        if($storyArea.parentNode.parentNode.id !== "editForm"){
            $storyArea.value = localStorage["storyContent"];
            return localStorage["storyContent"];
        }
    }
    return "";


}
function saveUserStory(){
    $storyArea = document.getElementById("story");

        if($storyArea !== null){

            if($storyArea.parentNode.parentNode.id !== "editForm"){
                localStorage["storyContent"] = $storyArea.value;
            }
        }



}

function userChangeStoryView(){
/*    if(localStorage["controllStory"] === undefined){
        localStorage["controllStory"] = [];
    }*/
    var storyToChange = document.getElementById("voteBox");
    if(storyToChange !== null){ //FulCheck, om kommentarer finns, då är vi på rätt sida...
        var storyToChange = document.getElementsByClassName("story")[0];
        var insideStory = document.getElementsByClassName("listStoryColumn")[0];

        var divForController = document.createElement("DIV");
        divForController.id = "controllStory"
        insideStory.insertBefore(divForController, storyToChange);
        //Nu har vi placerat ut diven där vi ska placera våra kontrollrar...

        if(localStorage["textSize"] == undefined){
            localStorage["textSize"] = 15;
            storyToChange.style.fontSize = localStorage["textSize"]+"px";
        }else{
            storyToChange.style.fontSize = localStorage["textSize"]+"px";
        }

        if(localStorage["lineHeight"] == undefined){
            localStorage["lineHeight"] = 20;
            storyToChange.style.lineHeight = localStorage["lineHeight"]+"px";
        }else{
            storyToChange.style.lineHeight = localStorage["lineHeight"]+"px";
        }



        var pageDimmer = document.createElement("DIV");
        pageDimmer.id = "page-cover";
        var dimButton = document.createElement("BUTTON");
        dimButton.innerHTML = "Dim site";
        dimButton.onclick = function(){
            if(pageDimmer.style.display != "block"){
                var corner = document.getElementById("body");
                pageDimmer.style.display = "block";
                corner.appendChild(pageDimmer);
                insideStory.style.zIndex = "9999";
                insideStory.style.position = "absolute";
            }else{
                pageDimmer.onclick();
            }
        }
        pageDimmer.onclick = function(){
            pageDimmer.style.display = "none";
            insideStory.style.zIndex = "100";
            insideStory.style.position = "static";
        }

        var DayOrNightMode = document.createElement("BUTTON");
        DayOrNightMode.innerHTML = "Day or Night mode";
        DayOrNightMode.onclick = function(){
            if(localStorage["DayOrNightMode"] === "night"){
                localStorage["DayOrNightMode"] = "day";
                storyToChange.style.backgroundColor = "rgb(229, 229, 229)";
                storyToChange.style.color = "Black";
            }else{
                localStorage["DayOrNightMode"] = "night";
                storyToChange.style.backgroundColor = "Black";
                storyToChange.style.color = "rgb(221, 221, 221)";

            }

        }
        var setnight = function(){
            localStorage["DayOrNightMode"] = "night";
            storyToChange.style.backgroundColor = "Black";
            storyToChange.style.color = "rgb(221, 221, 221)";
        }
        var setday = function(){
            localStorage["DayOrNightMode"] = "day";
            storyToChange.style.backgroundColor = "rgb(229, 229, 229)";
            storyToChange.style.color = "Black";
        }
        if(localStorage["DayOrNightMode"] == undefined){
            localStorage["DayOrNightMode"] = "night";
            setnight();
        }else if(localStorage["DayOrNightMode"] === "day"){
            setday();
        }


        var bigLineHeightButton = document.createElement("BUTTON");
        bigLineHeightButton.innerHTML = "Bigger Line Height";
        bigLineHeightButton.onclick = function(){
            localStorage["lineHeight"] = parseFloat(localStorage["lineHeight"]) +1;
            storyToChange.style.lineHeight = localStorage["lineHeight"]+"px";
        }

        var SmallLineHeightButton = document.createElement("BUTTON");
        SmallLineHeightButton.innerHTML = "Smaller Line Height";
        SmallLineHeightButton.onclick = function(){
            if(parseInt(localStorage["lineHeight"]) > 15 ){
                localStorage["lineHeight"] = parseFloat(localStorage["lineHeight"]) -1;
                storyToChange.style.lineHeight = localStorage["lineHeight"]+"px";
            }
        }

        var bigTextButton = document.createElement("BUTTON");
        bigTextButton.innerHTML = "Bigger Text";
        bigTextButton.onclick = function(){
            localStorage["textSize"] = parseInt(localStorage["textSize"]) +1;
            storyToChange.style.fontSize = localStorage["textSize"]+"px";
        }

        var SmallTextButton = document.createElement("BUTTON");
        SmallTextButton.innerHTML = "Smaller Text";
        SmallTextButton.onclick = function(){
            if(parseInt(localStorage["textSize"]) > 5 ){
                localStorage["textSize"] = parseInt(localStorage["textSize"]) -1;
                storyToChange.style.fontSize = localStorage["textSize"]+"px";
            }
        }

        divForController.appendChild(bigTextButton);
        divForController.appendChild(SmallTextButton);
        divForController.appendChild(bigLineHeightButton);
        divForController.appendChild(SmallLineHeightButton);
        divForController.appendChild(DayOrNightMode);
        divForController.appendChild(dimButton);

    }


}

function didUserActivateVoteSlide(){
    var slideScary = document.getElementById("slideScary");
    var checkScary = document.getElementById("checkScary");
    var slideRead = document.getElementById("slideRead");
    var checkRead = document.getElementById("checkRead");
    var slideShiver = document.getElementById("slideShiver");
    var checkShiver = document.getElementById("checkShiver");
    var slideUnique = document.getElementById("slideUnique");
    var checkUnique = document.getElementById("checkUnique");
    var slideCorresp = document.getElementById("slideCorresp");
    var checkCorresp = document.getElementById("checkCorresp");

     if(slideScary != null){
         slideScary.onfocus = function(){
             checkScary.setAttribute("checked", "true")
         }
         slideRead.onfocus = function(){
             checkRead.setAttribute("checked", "true")
         }
         slideShiver.onfocus = function(){
             checkShiver.setAttribute("checked", "true")
         }
         slideCorresp.onfocus = function(){
             checkCorresp.setAttribute("checked", "true")
         }
         slideUnique.onfocus = function(){
             checkUnique.setAttribute("checked", "true")
         }

     }

}

function allowCookies(){
    if(localStorage["cookieAgreement"] !== "true"){
        localStorage["cookieAgreement"] = confirm("To use this site you must accept cookies. Agree?");
    }


}
function reziseMusicPlayer(){
    var youtubeplayer = document.getElementById("youtubeplayer");
    var closeButton = document.createElement("BUTTON");
    var corner = document.getElementById("body");
    closeButton.innerHTML ="Close";

    var openButton = document.createElement("BUTTON");
    openButton.style.float = "right";
    openButton.innerHTML="Open Music player";
    openButton.style.position = "fixed";
    openButton.style.bottom = "0";
    openButton.style.right = "0";
    openButton.className = "openMusicbutton";

    closeButton.onclick = function resize(){
        youtubeplayer.style.display = "none";
        corner.insertBefore(openButton,youtubeplayer);
        localStorage["playlistStatus"] = "hidden";
    }
    openButton.onclick = function resize(){
        youtubeplayer.style.display = "block";
        youtubeplayer.appendChild(closeButton);
        corner.removeChild(openButton);
        localStorage["playlistStatus"] = "shown";
    }
    youtubeplayer.appendChild(closeButton);

    if(localStorage["playlistStatus"]=== "hidden"){
        closeButton.onclick();
    }


}

function reziseLogin(){
    var loginBox = document.getElementById("loginBox");
    var closeButton = document.createElement("BUTTON");
    var corner = document.getElementById("body");
    closeButton.innerHTML ="Close";

    var openButton = document.createElement("BUTTON");
    openButton.style.float = "right";
    openButton.innerHTML="Open Login";
    openButton.className = "openLoginButton";

    closeButton.onclick = function resize(){
        loginBox.style.display = "none";
        document.getElementById("menu").appendChild(openButton);
        localStorage["loginStatus"] = "hidden";
    }
    openButton.onclick = function resize(){
        loginBox.style.display = "block";
        loginBox.appendChild(closeButton);
        document.getElementById("menu").removeChild(openButton);
        localStorage["loginStatus"] = "shown";
    }
    loginBox.appendChild(closeButton);

    if(localStorage["loginStatus"]=== "hidden"){
        closeButton.onclick();
    }


}


window.onload = function() {
    toRun();
}