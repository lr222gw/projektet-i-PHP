/**
 * Created by Lowe on 2014-10-26.
 */
function toRun(){
    reziseLogin();
    reziseMusicPlayer();
    allowCookies();
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
    closeButton.innerText="Close";

    var openButton = document.createElement("BUTTON");
    openButton.style.float = "right";
    openButton.innerText="Open Music player";
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
    closeButton.innerText="Close";

    var openButton = document.createElement("BUTTON");
    openButton.style.float = "right";
    openButton.innerText="Open Login";
    openButton.className = "openLoginButton";

    closeButton.onclick = function resize(){
        loginBox.style.display = "none";
        document.getElementById("menu").appendChild(openButton);
        localStorage["loginStatus"] = "hidden";
    }
    openButton.onclick = function resize(){
        loginBox.style.display = "block";
        loginBox.appendChild(closeButton);
        corner.removeChild(openButton);
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