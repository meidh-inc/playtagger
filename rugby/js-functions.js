// Javascript for video.php


function seekTo(seconds) {  // This function is commanded after a tag is clicked.
    var tagPaddingSeconds = 3;  // This variable is the amount of seconds to pad each tag.
    // Make sure that the tag comes after the # of seconds to pad, if it does go ahead and seek to that point minus the padding.
    if (seconds >= tagPaddingSeconds) {  
        ytplayer.seekTo(seconds - tagPaddingSeconds, true);
    }else {  // Else seek to the beginning of the video
        ytplayer.seekTo(0, true);
    }
    // Check to see if the video is playing, if it is Do nothing (video will continue to play), if it isn't make it play.
    if (ytplayer.getPlayerState() == 1) {
        //Do Nothing
    }else {
        playVideo();
    }
}


function submitTag() {
    if (ytplayer.getPlayerState() == 2) {
        var playerDropDown = document.getElementById("tagPlayers");
        var teamDropDown = document.getElementById("team");
        var gameURL = document.getElementById("selectGames").value;
        var player = playerDropDown.options[playerDropDown.selectedIndex].text;
        var playerId = document.getElementById("tagPlayers").value;
        var whatHappened = document.getElementById("whatHappened").value;
        var team = teamDropDown.options[teamDropDown.selectedIndex].text;
        var time = ytplayer.getCurrentTime();
        if (team == "Select Team") {
            alert("Error: Please Select A Team");
        }else if (player == "Team not in system") {
            alert("Error: Unable to tag a team that's not in the system");
        }else if (whatHappened == "Pick Item") {
            alert("Error: Please select an event");
        }else {
            if (window.XMLHttpRequest) {
                xmlhttp=new XMLHttpRequest();
            }else {
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function() {
                if (xmlhttp.readyState==4 & xmlhttp.status==200) {
                    if (xmlhttp.responseText == "Success") {
                        document.getElementById("tagStatus").innerHTML="Tagged!";
                    }else {
                        document.getElementById("tagStatus").innerHTML="Unknown Error while tagging";
                    }
                }
            }
            xmlhttp.open("GET","ajax.php?action=writeTag&u=" + gameURL + "&p=" + player + "&w=" +
                         whatHappened + "&pid=" + playerId + "&team=" + team + "&t=" + time,true);
            xmlhttp.send();
            playVideo();
            updateUpcomingTags(gameURL);
        }
        updateUpcomingTags(gameURL);
    }else {
        alert("You must press the TAG Button First!");
    }
    updateUpcomingTags(gameURL);
}


function updateTagTeams(str) {
    var homeTeam = document.getElementById("team").options[1];
    var awayTeam = document.getElementById("team").options[2];
    if (str=="") {
        document.getElementByID("team").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest) {  // Code for IE7+, Firefox, Chrome, Opera, & Safari
        xmlhttp=new XMLHttpRequest();
        xmlhttp2=new XMLHttpRequest();
        xmlhttp3=new XMLHttpRequest();
        xmlhttp4=new XMLHttpRequest();
    }else {  // Code for IE6 & IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
        xmlhttp4=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 & xmlhttp.status==200) {
            homeTeam.text=xmlhttp.responseText;
        }
    }
    xmlhttp2.onreadystatechange=function() {
        if (xmlhttp2.readyState==4 & xmlhttp2.status==200) {
            awayTeam.text=xmlhttp2.responseText;
        }
    }
    xmlhttp3.onreadystatechange=function() {
        if (xmlhttp3.readyState==4 & xmlhttp3.status==200) {
            homeTeam.value=xmlhttp3.responseText;
        }
    }
    xmlhttp4.onreadystatechange=function() {
        if (xmlhttp4.readyState==4 & xmlhttp4.status==200) {
            awayTeam.value=xmlhttp4.responseText;
        }
    }
    xmlhttp.open("GET","getteams.php?q=" + str + "&t=0",true);
    xmlhttp.send();
    xmlhttp2.open("GET","getteams.php?q=" + str + "&t=1",true);
    xmlhttp2.send();
    xmlhttp3.open("GET","getteams.php?q=" + str + "&t=2",true);
    xmlhttp3.send();
    xmlhttp4.open("GET","getteams.php?q=" + str + "&t=3",true);
    xmlhttp4.send();
}


function updateTagPlayers() {
    var dropdown = document.getElementById("team");
    var teamID = dropdown.options[dropdown.selectedIndex].value;
    if (teamID=="") {
        document.getElementByID("tagPlayer").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest) {  // Code for IE7+, Firefox, Chrome, Opera, & Safari
        xmlhttp=new XMLHttpRequest();
    }else {  // Code for IE6 & IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 & xmlhttp.status==200) {
            document.getElementById("tagPlayer").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax.php?action=getPlayers&t=" + teamID,true);
    xmlhttp.send();
}


function updateUpcomingTags(str) {  // str is the ytchar and comes from: loadVideo, loadPlayer, submitTag.
    if (str=="") {
        document.getElementById("upcomingTags").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest) {
        upcomingTagsXML=new XMLHttpRequest();
    }else {
        upcomingTagsXML=new ActiveXObject("Microsoft.XMLHTTP");
    }
    upcomingTagsXML.onreadystatechange=function() {
        if (upcomingTagsXML.readyState==4 & upcomingTagsXML.status==200) {
            document.getElementById("upcomingTags").innerHTML=upcomingTagsXML.responseText;
        }
    }
    upcomingTagsXML.open("GET","ajax.php?action=getTags&q=" +str,true);
    upcomingTagsXML.send();
}


function loadVideo() {  // Loads the selected video into the player.
    var selectBox = document.getElementById("selectGames");
    var videoID = selectBox.options[selectBox.selectedIndex].value;
    if(ytplayer) {
        ytplayer.loadVideoById(videoID);
    }
    updateTagTeams(videoID);
    updateUpcomingTags(videoID);
}


function onPlayerError(errorCode) {  // This function is called when an error is thrown by the player
    alert("An error occured of type:" + errorCode);
}


function pauseVideo() {
    if (ytplayer) {
        ytplayer.pauseVideo();
    }
}


function playVideo() {
    if (ytplayer) {
        ytplayer.playVideo();
    }
}


function onYouTubePlayerReady(playerId) {  // This function is automatically called by the player once it loads
    ytplayer = document.getElementById("ytPlayer");
    ytplayer.addEventListener("onStateChange", "onPlayerStateChange");
    ytplayer.addEventListener("onError", "onPlayerError");
    playVideo();
}

/*
function loadPlayer() {  // The "main method" of this sample. Called when someone clicks "Run".
    var videoID = "1fbTssP0SW0"  // The video to load
    var params = { allowScriptAccess: "always" };  // Lets Flash from another domain call JavaScript
    var atts = { id: "ytPlayer" };  // The element id of the Flash embed
    // All of the magic handled by SWFObject (http://code.google.com/p/swfobject/)
    swfobject.embedSWF("http://www.youtube.com/v/" + videoID + 
                           "?version=3&enablejsapi=1&playerapiid=player1", 
                           "videoDiv", "480", "295", "9", null, null, params, atts);
    updateTagTeams(videoID);
    updateUpcomingTags(videoID);
}
*/

function _run() {
    loadPlayer();
}

google.setOnLoadCallback(_run);

