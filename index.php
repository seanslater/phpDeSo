<?php

// this file is a starter file to read and write to the DeSo Blockchain.
// modified resource: https://build.deso.com/#/main/welcome
// use at your own risk !!

// provide your own security !!
// provide your own dang favicon.ico

// version 1.1




// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//           MAIN PROGRAM
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


$ddatta = $_POST['ddatta'];



if ($ddatta != "")  {

$ddattaA = array();
$ddattaA[0] = explode("|",$ddatta[0]);
$ddattaA[0][1] = filter_var($ddattaA[0][1], FILTER_SANITIZE_STRING);
$ddattaA[0][2] = filter_var($ddattaA[0][2], FILTER_SANITIZE_STRING);
$ddattaA[0][3] = filter_var($ddattaA[0][3], FILTER_SANITIZE_STRING);
$ddattaA[0][4] = filter_var($ddattaA[0][4], FILTER_SANITIZE_STRING);
$ddattaA[0][5] = filter_var($ddattaA[0][5], FILTER_SANITIZE_STRING);

$d1Gap = $ddattaA[0][1];
$d2Gap = $ddattaA[0][2];
$d3Gap = $ddattaA[0][3];
$d4Gap = $ddattaA[0][4];
$d5Gap = $ddattaA[0][5];

$f1 = $d1Gap;
$f1 = explode("-",$f1);


if ($f1[1] == "10058")  { // DeSo Javascript Framework - (for Identity and API Message handling)
$echo = iE10058();
}


if ($f1[1] == "10060")  { // API (submit-transaction) Submit this Signed transaction
$echo = iE10060($d2Gap);
}


if ($f1[1] == "10061")  { // API (submit-post) Submit this Post
$echo = iE10061($d2Gap,$d3Gap,$d4Gap);
}


if ($f1[1] == "10064")  { // API (create-follow-txn-stateless) Follow this User
$echo = iE10064($d2Gap,$d3Gap);
}



if ($f1[1] == "10065")  { // API (create-follow-txn-stateless) UnFollow this User
$echo = iE10065($d2Gap,$d3Gap);
}




if ($f1[1] == "10067")  { // Following Feed
$echo = iE10067($d2Gap,$d3Gap,$d4Gap);
}




if ($f1[1] == "10068")  { // Comments of Post
$echo = iE10068($d2Gap,$d3Gap,$d4Gap);
}




} else  {
$echo = getIPO(); // Initial Page Output
}


















// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//           PHP FUNCTIONS
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


function iE10068($PostHashHex,$CommentLimit,$PublicKey)  { // Comments of Post

$allPosts = getPostviaHex($PostHashHex,$CommentLimit);
$CommentLimit3 = $CommentLimit;

$iiPP = 0;
$ii = 0;
while ($ii < $CommentLimit3)  {
$ii2 = (($CommentLimit3 - $ii) - 1);

$Posts = $allPosts->PostFound->Comments[$ii2];
$thisArray = $allPosts->PostFound->Comments[$ii2]->PostHashHex;

$profile = "";

if (!$thisArray == "")  {

$echo .= getComment($Posts,$profile,$PublicKey,$iiPP);
$iiPP = 5;
}

$ii++;
}


$ta = '<div id=ca-' . $PostHashHex . ' class=ca><div id=caT><div id=postMe class=pointer onclick="goPost(\'' . $PostHashHex . '\',\'' . $PublicKey . '\',\'ca-' . $PostHashHex . '\')">POST</div><div id=catTT>Your Own Comment:</div><div id=clear></div></div><div id=taT><textarea id="ta-' . $PostHashHex . '"></textarea></div></div>';
$echo = $ta . $echo;


return $echo;
}



function iE10067($PostHashHex,$PublicKey,$hexCount)  { // Following Feed

$hexCountNew = $hexCount + 1;
$allPosts = getPostsViaGlobal($PostHashHex,$PublicKey);
$Posts = $allPosts->PostsFound[0];
$profile = $allPosts->PostsFound[0]->ProfileEntryResponse;
$echo = getPost($Posts,$profile,$PublicKey,$widgetID);

$NextPostHashHex = $Posts->PostHashHex;
if ($noNextHex == "yes")  {
$echo .= "<br><script>doDiv(" . $hexCountNew . ");getII('" . $NextPostHashHex . "','" . $PublicKey . "','" . $hexCountNew . "');</script>";
} else  {
$echo .= "<br><script>doDiv(" . $hexCountNew . ");getKK('" . $NextPostHashHex . "','" . $PublicKey . "','" . $hexCountNew . "');</script>";
}

return $echo;
}







function iE10065($posterKey,$PublicKey)  { // API (create-follow-txn-stateless) UnFollow this User

$echo = '<div id=followMe class=pointer onclick="goFollow(\'' . $posterKey . '\', myPublicKey,\'c4-' . $posterKey . '\')">FOLLOW</div>';

$collection_name = 'create-follow-txn-stateless';
$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;
$data = [
"FollowerPublicKeyBase58Check" => $PublicKey,
"FollowedPublicKeyBase58Check" => $posterKey,
"IsUnfollow" => true,
"MinFeeRateNanosPerKB" => 1000
];

$response = curlme($request_url,$data);
$response = json_decode($response);
$TransactionHex = $response->TransactionHex;




$echo .= "<script>transHex = '$TransactionHex';doSigning(transHex);</script>";

return $echo;
}








function iE10064($posterKey,$PublicKey)  { // API (create-follow-txn-stateless) Follow this User

$echo = '<div id=followMe class=pointer onclick="goUnFollow(\'' . $posterKey . '\', myPublicKey,\'c4-' . $posterKey . '\')">UNFOLLOW</div>';

$collection_name = 'create-follow-txn-stateless';
$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;
$data = [
"FollowerPublicKeyBase58Check" => $PublicKey,
"FollowedPublicKeyBase58Check" => $posterKey,
"IsUnfollow" => false,
"MinFeeRateNanosPerKB" => 1000
];

$response = curlme($request_url,$data);
$response = json_decode($response);
$TransactionHex = $response->TransactionHex;




$echo .= "<script>transHex = '$TransactionHex';doSigning(transHex);</script>";

return $echo;
}




function iE10061($parentHex,$PublicKey,$cc)  {  // API (submit-post) Submit this Post

$str1 = " ";
$str2 = "\n";
$str3 = str_replace("<br>", $str2, $cc);
$str3 = str_replace("[zz]", $str1, $str3);
$cc = $str3;

$collection_name = 'submit-post';
$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;
$data = [
"UpdaterPublicKeyBase58Check" => $PublicKey,
"ParentStakeID" => $parentHex,
"BodyObj" => [
"Body" => $cc, 
"VideoURLs" => [],
"ImageURLs" => []
],      
"MinFeeRateNanosPerKB" => 1000 
];      

$response = curlme($request_url,$data);
$response = json_decode($response);
$TransactionHex = $response->TransactionHex;

$echo = "<div id=postpost><br>Improved Postings coming soon.<br><br>Larger area for text, emoji, multi-image upload, video, instant message display without reload, comment-specific notification. more.</div><script>transHex = '$TransactionHex';doSigning(transHex);</script>";

return $echo;
}








function iE10060($transHex)  {   // API (submit-transaction) Submit this Signed transaction

$collection_name = 'submit-transaction';
$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;
$data = [
"TransactionHex" => $transHex,
];

$response = curlme($request_url,$data);
$response = json_decode($response);




return $echo;

}






function iE10058()  {  // DeSo Javascript Framework - (for Identity and API Message handling) 


$forReturn = <<<EOD

<style>

.ca  {
position:relative;
background: #19b6d9;
color:black;
height: 126px;
}

#caT  {
position:relative;
height: 42px;
}

textarea  {
font-size: 22px;
width: 560px;
height: 74px;
}

#catTT  {
position:relative;
float:left;
top: 10px;
left: 34px;
}

#postMe  {
position: relative;
float: left;
top: 10px;
width: 48px;
box-shadow: 0px 3px 9px rgb(0 0 0);
text-align: center;
border-radius: 4px;
height: 22px;
background-color: aqua;
transition: all 0.5s ease;
left:24px;
}

#taT  {
position:relative;
height: 136px;
width: 100%;
text-align: center;
}


.thisComment  {
position: relative;
border: black;
border-style: solid;
border-width: 1px;
background: #5e7a7b;
}


.thisCommentC  {
position: relative;
background: #2099ff;
border: black;
border-style: solid;
border-width: 1px;
}


.thisComment1  {
position: relative;
border: black;
border-style: solid;
border-width: 1px;
background: #941029;
}

#postInside  {
position: relative;
background: #a2a2a2;
word-wrap: break-word;
overflow: hidden;
}

#postInside1  {
position: relative;
background: #58c430;
word-wrap: break-word;
overflow: hidden;
}

#postInsideC  {
position: relative;
word-wrap: break-word;
overflow: hidden;
margin:0px;
}


#blackSpace  {
position:relative;
height:40px;
}

#chain  {
position:relative;
width:60px;
-webkit-transform: rotate(90deg); -moz-transform: rotate(90deg); -o-transform: rotate(90deg); -ms-transform: rotate(90deg); transform: rotate(90deg);
background-size: 10px 10px;
background-image: url("/images/chain.png");
background-repeat: no-repeat, repeat;
background-color: #ffffff;
height:40px;
}

#post  {
position: relative;
overflow: hidden;
}

#postInsidePost  {
position: relative;
background: #d0d0d0;
}

#postInsidePost1  {
position: relative;
background: #6cee3b;
}


#insidePost  {
position: relative;
margin:10px;
overflow: hidden;
}

#postC  {
position: relative;
background: #ffffff;
}

#postInsidePostC  {
position: relative;
background: #ffffff;
border-style: solid;
border-width: 1px;
border-color: #111827;
}

#insidePostC  {
position: relative;
margin:10px;
overflow: hidden;
color:#000000;
}

.white  {
position: relative;
background: #ffffff;
}

#postInsideRepost  {
position: relative;
}

.commentIcon  {
fill: none;
height: 24px;
stroke: black;
}

.pointer  {
cursor: pointer;
}

#actions  {
position: relative;
background: #858b8d;
height:68px;
}

#actions1  {
position: relative;
background: #6cee3b;
height:188px;
color: #860000;
font-size: 16px;
font-weight: 500;
}

#acTop  {
position: relative;
width:100%;
height:36px;
}



#acBottom  {
position: relative;
width:100%;
top: -11px;
}


#USD1  {
position: relative;
width: 100%;
top:25px;
}

#DeSo1  {
position: relative;
width: 100%;
top:25px;
}

#ac233  {
position: relative;
width:20px;
margin:auto;
text-align:center;
}

#ac244  {
position: relative;
width:20px;
margin:auto;
text-align:center;
}

.ac2  {
position: relative;
width:99px;
margin:auto;
text-align:center;
float:left;
top:10px;
}

#ac221  {
}

#insideRepost  {
word-wrap: break-word;
overflow: hidden;
position: relative;
margin:10px;
}

#headerMess  {
position: relative;
width:100%;
box-shadow: 0px 3px 9px rgb(0 0 0);
height:100px;
border-bottom-color: black;
border-bottom-style: solid;
border-bottom-width: 1px;
}

#headerMess1  {
position: relative;
width:100%;
box-shadow: 0px 3px 9px rgb(0 0 0);
height:100px;
border-bottom-color: black;
border-bottom-style: solid;
border-bottom-width: 1px;
background: #6cee3b;
}

#headerMess2  {
position: relative;
width:100%;
box-shadow: 0px 3px 9px rgb(0 0 0);
height:100px;
border-bottom-color: black;
border-bottom-style: solid;
border-bottom-width: 1px;
background-color: #95b4b5;
}


#im  {
position: relative;
float:left;
margin:auto;
width:100px;
}

#username  {
position: relative;
overflow: hidden;
font-size: 3em;
text-align: center;
width: 478px;
height: 73px;
text-shadow: 2px 2px 3px #000000;
float: left;
}

#username2  {
position: relative;
overflow: hidden;     
font-size: 3em;
text-align: center;
width: 478px;
height: 56px;
text-shadow: 2px 2px 3px #000000;
float: left;
}


#belowH  {
position: relative;
text-align:center;
height:44px;
width: 478px;
float:left;
top: 3px;
}

#repost  {
position: relative;
width:100%;
text-align:center;
color:#626060;
}


#div1,#div2  {
position: relative;
background: #fff;
color: #000;
border-radius: 4px;
border: 1px solid #000000;
box-shadow: inset 1px 2px 8px rgb(0 0 0);
font-family: inherit;
font-size: 1em;
line-height: 1.45;
height: 150px;
width:150px;
background-position: center; /* Center the image */
background-repeat: no-repeat; /* Do not repeat the image */
background-size: cover; /* Resize the background image to cover the entire container */
}

#vert  {
float:left;
}

#div1  {
}

#div2  {
}

#div3  {
clear:both;
}

#div8  {
width:100%;
}

#MARQUEE0  {
margin:auto;
width:600px;
float:left;
}

#MARQUEE  {
margin:auto;
width:600px;
clear:both;
}

#MARQUEE2  {
margin:auto;
width:600px;
text-align:center;
}

#allMessages  {
margin:auto;
width:600px;
}

#clear  {
clear:both;
}

#part1  {
position: relative;
height: 56px;
float:left;
top: -5px;
}

#part2  {
position: relative;
width:100%;
height:22px;
}

#part3  {
position: relative;
width:100%;
height:22px;
}

#c1  {
position: relative;
width:121px;
float:left;
height: 22px;
}

#b1  {
position: relative;
width:246px;
float:left;
height: 22px;
}

#c2  {
position: relative;
width:100px;
float:left;
height: 22px;
}

#c3  {
position: relative;
width:121px;
float:left;
height: 22px;
font-size:12px;
}

#c4  {
position: relative;
width:246px;
float:left;
height: 22px;
top: -11px;
color: #4f88a9;
}

#c4b  {
position: relative;
width:246px;
float:left;
height: 22px;
top: -17px;
color: #003554;
}

#c5  {
position: relative;
width:100px;
float:left;
height: 22px;
font-size:12px;
}

#postpost  {
position: relative;
margin: 10px;
}


#notes  {
margin: auto;
width: 600px;
position: relative;
background: #fff;
color: #000;
border-radius: 4px;
border: 1px solid #000000;
}


#notesInner  {
margin: 10px;
width: 585px;	        
position: relative;
}


#adNote  {
margin: 10px;
width: 100%;
position: relative;
top: 27px;
color: #003ce2;
}

</style>





<script>

function handleMessage(event)  {
const { data } = event;
const { id, service, method} = data;

if (service !== 'identity') {
return;
}

if (method) {
handleRequest(event); // after login
} else {
handleResponse(event);  // getPost makePOst
}

}



function handleRequest(event)  {

const {data} = event;
const {id, method, payload} = data;
const {publicKeyAdded} = payload;

if (method == "initialize") {
handleInitialize(event);
} else if (method == "storageGranted") {
handleStorageGranted();
} else if (method == "login") {

handleLogin(event);


getKK("",publicKeyAdded,0);

} else if (method == "info") {
handleInfo(id,publicKeyAdded);
} else {
console.error("DiamondThumb says no valid Identity");
console.error(event);
}

return;
}



function handleInitialize(event)  {

const { data } = event;
const { service, method, id, payload} = data;
const {publicKeyAdded} = payload;

event.source.postMessage({
    id: id,
    service: 'identity',
    payload: {},
}, "https://identity.deso.org");

return;
}






function handleStorageGranted()  {
return;
}

function handleLogin(event)  {
const { data } = event;
const { service, method, id, payload} = data;
const { users, publicKeyAdded} = payload;

login.close();
myPublicKey = publicKeyAdded ;

IdentityUsersKey = users;
localStorage.setItem(IdentityUsersKey, JSON.stringify(users));
const users2 = JSON.parse(localStorage.getItem(IdentityUsersKey));

return;
}




function handleInfo(id,publicKeyAdded)  {

event.source.postMessage({
id
}, "https://identity.deso.org");

}



function doInfo(event)  {
id = uuid();
document.getElementById("identity").contentWindow.postMessage({
id,
service: 'identity',
method: 'info'
}, 'https://identity.deso.org');

return;
}



function doStorage()  {

id = uuid();

document.getElementById("identity").contentWindow.postMessage({
id,
service: 'identity',
method: 'storageGranted'
}, 'https://identity.deso.org');

return;
}



function uuid()  {
return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
return v.toString(16);
});
}



function handleResponse(event)  {

const { data } = event;
const { service, method, payload, id} = data;
const {browserSupported, hasCookieAccess, hasLocalStorageAccess, hasStorageAccess, transactionHex, signedTransactionHex} = payload;

ids = browserSupported + " - " + hasCookieAccess + " - " + hasLocalStorageAccess + " - " + hasStorageAccess;

if (signedTransactionHex)  {
$.ajaxSetup ({cache: false});
u = "-10060-10060-1-1-9";
v = signedTransactionHex;
z = Math.floor((Math.random() * 10000) + 1);


E3_ggg(u,v,"","","#MARQUEE");


}

return;
}



function doSigning(transHex)  {
const users2 = JSON.parse(localStorage.getItem(IdentityUsersKey));
const users3 = JSON.stringify(users2);

for(var key in users2) {
if (key == myPublicKey)  {
encryptedSeedHex = users2[key]['encryptedSeedHex'];
accessLevelHmac = users2[key]['accessLevelHmac'];
}
}

id = uuid();

document.getElementById("identity").contentWindow.postMessage({
id,
service: 'identity',
method: 'sign',
payload: {
accessLevel: 3,
accessLevelHmac: accessLevelHmac,
encryptedSeedHex: encryptedSeedHex,
transactionHex: transHex,
},
}, 'https://identity.deso.org');

}






function goPost(ParentHex,PublicKey,MARQ)  {
var cc = document.getElementById('ta-' + ParentHex).value;
cc = cc.replace(/(?:\\r\\n|\\r|\\n)/g, '<br>');
cc = cc.replace(/ /g, '[zz]');

let u = "-10061-10061-1-1-9";
let v = ParentHex;
let w = PublicKey;
let x = cc;

E3_ggg(u,v,w,x,"#ca-" + ParentHex);


return;
}





function goFollow(PosterKey,PublicKey,MARQ)  {

let u = "-10064-10064-1-1-9";
let v = PosterKey;
let w = PublicKey;
let x = MARQ;



E3_ggg(u,v,w,x,".c4-" + PosterKey);

const collection = document.getElementsByClassName("c2-" + PosterKey);

for (let i = 0; i < collection.length; i++) {
if (i == 0)  {
fCount = collection[i].innerHTML;
fCount = parseInt(fCount);
fCount = fCount + 1;
}
collection[i].innerHTML = fCount;
}

return;
}       


function getComments(PostHashHex,CommentLimit,PublicKey)  {

if (dtrig[PostHashHex] != '0')  {
u = "-10068-10068-1-1-9";
let v = PostHashHex;
let w = CommentLimit;
let x = PublicKey;
dtrig[PostHashHex] = '0';
E3_ggg(u,v,w,x,"#comments-" + PostHashHex);
}


return;
}       


function goUnFollow(PosterKey,PublicKey,MARQ)  {

let u = "-10065-10065-1-1-9";
let v = PosterKey;
let w = PublicKey;
let x = MARQ;

E3_ggg(u,v,w,x,".c4-" + PosterKey);

const collection = document.getElementsByClassName("c2-" + PosterKey);
for (let i = 0; i < collection.length; i++) {

if (i == 0)  {
fCount = collection[i].innerHTML;
fCount = parseInt(fCount);               
fCount = fCount - 1;
collection[i].innerHTML = fCount;
}
}

return;
}       


function getKK(hex,PublicKey,hexCount)  {

let u = "-10067-10067-1-1-9";
let v = hex;
let w = PublicKey;
let x = hexCount;

E3_ggg(u,v,w,x,"#message-" + hexCount);

return;
}



function getII(hex,PublicKey,hexCount)  {

document.getElementById("message-" + hexCount).innerHTML = "No More Messages";

return;
}       


function displayHex()  {

}


function doDiv(hexCount)  {

var g ;
var container_block ;
g = document.createElement('div');
g.setAttribute("id", "message-" + hexCount);
g.innerHTML = '' ;
container_block = document.getElementById( 'allMessages' );
container_block.appendChild( g );
}


function didDiv(ParentStakeID,postHashHexN)  {

const oldComm = document.getElementById("thisComment-" + ParentStakeID);
const newComm = document.getElementById("thisComment-" + postHashHexN);
oldComm.insertAdjacentElement("afterend", newComm);

}


const h = 1000;
const w = 800;
const y = window.outerHeight / 2 + window.screenY - h / 2;
const x = window.outerWidth / 2 + window.screenX - w / 2;
const login = window.open("https://identity.deso.org/log-in?accessLevelRequest=3", null, `toolbar=no, width=w, height=h, top=y, left=x`);
</script>



<div id=div8>
<br>
<div id=notes><div id=notesInner>

<center><b>Welcome to oneFileDeSo !!</b></center>
<br>
oneFileDeso is freeware and an open source starter kit, in one file, for you to connect to the DeSo API. Written in PHP, javascript, html, and css.



</div></div>
<div id=MARQUEE></div>
<div id="MARQUEE2"></div>
<div id="allMessages">
<div id="message-0"></div>
</div>
<br><br>




EOD;

return $forReturn;

}





function getIPO()  {



$jqueryPath = "https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js";
//$jqueryPath = "jquery.min.js";


$forReturn = <<<EOD
<!doctype html>
<html>
<head>
<title>oneFileDeSo v1</title>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0, width=device-width"/>
<meta name="author" content="SoftwareAlaCarte">
<meta name="description" content="freeware - an open source php Framework for DeSo Blockchain">
<meta name="description" content="for significant speed increase, use this file on a server running a DeSo Node and a php framework for mirrored off-chain data - ask me on the DeSo Chain - @DiamondThumb">
</head>
<body>

<iframe  id="identity"  frameborder="0"  src="https://identity.deso.org/embed"  style="height: 100vh; width: 100vw; display: block; position: fixed;z-index: 1000; left: 400; top: 400;">
</iframe>
<div id="fullscreen"><div id="starter"></div><div id=fGM></div></div>

<script src="$jqueryPath"></script>
<script>
myPublicKey = "";
window.addEventListener("message", (event) => this.handleMessage(event));
step = 1;
var dtrig = [];

\$.ajaxSetup ({cache: false});

function E3_ggg(u,v,w,x,y)  {z = Math.floor((Math.random() * 10000) + 1);
var thisVal = document.getElementById(x);
if (thisVal === null)  {} else  {x=document.getElementById(x).value;}
document.getElementById("fGM").style.zIndex = "2000";

const ddatta = [];
ddatta[0] = "|" + u + "|" + v + "|" + w + "|" + x + "|" + y + "|" + z + "|";

\$.ajax({type: "POST",url: "index.php",data: {ddatta:ddatta},dataType: "html",success: function(data) {\$(y).html(data);}});};

E3_ggg("-10058-10005-1-1-9","73-2","","","#starter");
</script>

<style>
html, body  {padding: 0;margin: 0;font-family: DM Sans,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,sans-serif;min-height: 100%;height: 100%;color: #fff;background: #000000;}
#identity  {display:block;visibility:hidden;}
</style>

</body></html>

EOD;

return $forReturn;
}



function curlme($request_url,$data)  {
$curl = curl_init($request_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json'
]);
$response = curl_exec($curl);
curl_close($curl);
return $response;

}



function getPostsViaGlobal($PostHashHex,$PublicKey)  {
$collection_name = 'get-posts-stateless';

$CommentLimit2 = $CommentLimit - 0;
$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;

if ($PostHashHex == "")  {
$data = [
'ReaderPublicKeyBase58Check' => $PublicKey,
'GetPostsforFollowFeed' => true,
'NumToFetch' => 2
];

} else  {

$data = [
'ReaderPublicKeyBase58Check' => $PublicKey,
'PostHashHex' => $PostHashHex,
'GetPostsforFollowFeed' => true,
'NumToFetch' => 1
];
}

$response = curlme($request_url,$data);
$response = json_decode($response);

return $response;
}



function getIsFollowing($PublicKey,$posterKey)  {
$collection_name = 'is-following-public-key';
$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;
$data = [
'PublicKeyBase58Check' => $PublicKey,
'IsFollowingPublicKeyBase58Check' => $posterKey
];
$response = curlme($request_url,$data);
$response = json_decode($response);
}



function getFolloweesbyKey($PublicKey,$stateF)  {   // followers = true following=false
$collection_name = 'get-follows-stateless';

$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;
$data = [
'PublicKeyBase58Check' => "$PublicKey",
'GetEntriesFollowingUsername' => $stateF,
'NumToFetch' =>1
];

$response = curlme($request_url,$data);
$response = json_decode($response);

return $response;
}



function getPostviaHex($PostHashHex,$CommentLimit)  {
$collection_name = 'get-single-post';

$CommentLimit2 = $CommentLimit - 0;
$url = 'https://node.deso.org/api/v0';
$request_url = $url . '/' . $collection_name;
$data = [
'PostHashHex' => $PostHashHex,
'CommentLimit' => $CommentLimit2
];

$response = curlme($request_url,$data);
$response = json_decode($response);

return $response;
}

	


function getPost($Posts,$profile,$PublicKey,$widgetID)  {

$echo = "";
$Username = $profile->Username;
$key = $profile->PublicKeyBase58Check;
$posterKey = $Posts->PosterPublicKeyBase58Check;

$nothing = "yes";
$hasQuote = "no";

$ac21 = $Posts->CommentCount;

$PostHashHex = $Posts->PostHashHex;
$CommentLimit = $ac21;

$postHashHex = $Posts->PostHashHex;


if ((!$Posts->Body == "") || (!$Posts->ImageURLs[0] == ""))  {
$adCount++;
$myBody = $Posts->Body;
$myBody = json_encode($myBody);

$str3 = $myBody;
$str1 = "<br>";    
$str3 = str_replace('\n', $str1, $str3);
$str3 = json_decode($str3);

if (!$Posts->ImageURLs[0] == "")  {
$echo5 =  "<br><br><image width=100% src=" . $Posts->ImageURLs[0] . ">";
}

$hasQuote = "yes";
$nothing = "no";
}

$stateF = false;
$getFollowees = getFolloweesbyKey($posterKey,$stateF);
$ersT = $getFollowees->NumFollowers;

$stateF = true;
$getFollowees = getFolloweesbyKey($posterKey,$stateF);
$eesT = $getFollowees->NumFollowers;

$followMe = '<div id=followMe class=pointer onclick="goUnFollow(\'' . $posterKey . '\', myPublicKey,\'c4-' . $posterKey . '\')">UNFOLLOW</div>';


if ((!$Posts->RepostedPostEntryResponse->Body == "") || (!$Posts->RepostedPostEntryResponse->ImageURLs[0] == ""))  {
$key22 = $Posts->RepostedPostEntryResponse->PosterPublicKeyBase58Check;
$Username2 = $Posts->RepostedPostEntryResponse->ProfileEntryResponse->Username;

if ($hasQuote == "yes")  {
$echo7 = "<br>";
}

$myBody = $Posts->RepostedPostEntryResponse->Body;
$myBody = json_encode($myBody);

$str4 = $myBody;
$str1 = "<br>";
$str4 = str_replace('\n', $str1, $str4);
$str4 = json_decode($str4);

$posterKeyR = $key22;

$stateF = false;
$getFollowees = getFolloweesbyKey($posterKeyR,$stateF);
$ersT = $getFollowees->NumFollowers;

$stateF = true;
$getFollowees = getFolloweesbyKey($posterKeyR,$stateF);
$eesT = $getFollowees->NumFollowers;


$response = getIsFollowing($PublicKey,$posterKeyR);
$isFollowing = $response->IsFollowing;
	
if ($isFollowing === FALSE)  {
$followMeR = '<div id=followMe class=pointer onclick="goFollow(\'' . $posterKeyR . '\', myPublicKey,\'c4-' . $posterKeyR . '\')">FOLLOW</div>';
} else  {
$followMeR = '<div id=followMe class=pointer onclick="goUnFollow(\'' . $posterKeyR . '\', myPublicKey,\'c4-' . $posterKeyR . '\')">UNFOLLOW</div>';
}



if (!$Posts->RepostedPostEntryResponse->ImageURLs[0] == "")  {
$echo6 = "<br><br><image width=100% src=" . $Posts->RepostedPostEntryResponse->ImageURLs[0] . ">";
}


$repostImage = "https://diamondapp.com/api/v0/get-single-profile-picture/" . $key22;

$echoRepost  = <<<EOD
<div id=repost>re-posted by: $Username</div></div><div id=postInsideRepost>
<div id=headerMess2>
<div id=im><img height=100px width=100px src=$repostImage></div>
<div id=part1><div id=username>$Username2</div></div><div id=belowH><div id=part2><div id=c1>$eesT</div>
<div id=b1>&nbsp;</div><div id=c2 class=c2-$posterKeyR>$ersT</div><div id=clear></div></div>
<div id=part3><div id=c3>followers</div><div class=c4-$posterKeyR id=c4>$followMeR</div><div id=c5>following</div>
<div id=clear></div></div></div></div>
<div id=insideRepost>$str4 $echo6</div></div>
EOD;

$nothing = "no";
}


$postImage = "https://diamondapp.com/api/v0/get-single-profile-picture/" . $key;

$echo  .= <<<EOD
<br><div id=thisComment-$PostHashHex class=thisComment>
<div id=post><div id=postInside>
<div id=postInsidePost>
<div id=headerMess>
<div id=im><img height=100px width=100px src=$postImage></div>
<div id=part1><div id=username>$Username</div></div>
<div id=belowH><div id=part2><div id=c1>$eesT</div><div id=b1>&nbsp;</div>
<div id=c2 class=c2-$posterKey>$ersT</div><div id=clear></div></div>
<div id=part3><div id=c3>followers</div><div class=c4-$posterKey id=c4>$followMe</div><div id=c5>following</div>
<div id=clear></div></div></div></div></div>
<div id=insidePost>$str3 $echo5</div>
$echoRepost</div>
<div id=actions><div id=acTop>
<div id=ac221  class="ac2 pointer" onClick="getComments('$PostHashHex','$CommentLimit','$PublicKey')">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="commentIcon"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
<div id=clear></div></div></div><div id=acBottom><div id=ac21 class=ac2>$ac21</div></div></div>
<div id=comments-$postHashHex class=white></div></div>
EOD;


if ($nothing == "yes")  {
$echo .= "<br><div id=post><br><div id=postInside>MISSING</div></div>";
} else  {
$echo .= "</div></div></div>";
}




return $echo;
}








function getComment($Posts,$profile,$PublicKey,$comCount)  {

$profile = $Posts->ProfileEntryResponse;
$Username = $profile->Username;
$key = $profile->PublicKeyBase58Check;
$posterKey = $Posts->PosterPublicKeyBase58Check;
$ac21 = $Posts->CommentCount;
$PostHashHex = $Posts->PostHashHex;
$CommentLimit = $ac21;
$ParentStakeID = $Posts->ParentStakeID;
$ac22 = $Posts->RepostCount + $Posts->QuoteRepostCount;
$ac23 = "0";
$ac24 = $Posts->LikeCount;
$ac25 = $Posts->DiamondCount;
$ac26 = "";

if ((!$Posts->Body == "") || (!$Posts->ImageURLs[0] == ""))  {
$myBody = $Posts->Body;
$myBody = json_encode($myBody);

$str3 = $myBody;
$str1 = "<br>";    
$str3 = str_replace('\n', $str1, $str3);
$str3 = json_decode($str3);

if (!$Posts->ImageURLs[0] == "")  {
$echo5 =  "<br><br><image width=100% src=" . $Posts->ImageURLs[0] . ">";
}

$hasQuote = "yes";
}


$stateF = false;
$getFollowees = getFolloweesbyKey($posterKey,$stateF);
$ersT = $getFollowees->NumFollowers;

$stateF = true;
$getFollowees = getFolloweesbyKey($posterKey,$stateF);
$eesT = $getFollowees->NumFollowers;

$response = getIsFollowing($PublicKey,$posterKey);
$isFollowing = $response->IsFollowing;

if ($isFollowing === FALSE)  {
$followMe = '<div id=followMe class=pointer onclick="goFollow(\'' . $posterKey . '\', myPublicKey,\'c4-' . $posterKey . '\')">FOLLOW</div>';
} else  {
$followMe = '<div id=followMe class=pointer onclick="goUnFollow(\'' . $posterKey . '\', myPublicKey,\'c4-' . $posterKey . '\')">UNFOLLOW</div>';
}

$commentImage = "https://diamondapp.com/api/v0/get-single-profile-picture/" . $posterKey;

$echo  .= <<<EOD
<div id=thisComment-$PostHashHex class=thisCommentC><div id=postC><div id=postInsideC>
<div id=postInsidePost><div id=headerMess><div id=im><img height=100px width=100px src=$commentImage></div>
<div id=part1><div id=username>$Username</div></div>
<div id=belowH>
<div id=part2><div id=c1>$eesT</div><div id=b1>&nbsp;</div><div id=c2 class=c2-$posterKey>$ersT</div><div id=clear></div></div>
<div id=part3><div id=c3>followers</div><div class=c4-$posterKey id=c4>$followMe</div>
<div id=c5>following</div><div id=clear></div></div></div></div></div>
<div id=insidePostC>$str3 $echo5</div></div></div>
<div id=actions><div id=acTop><div id=ac221  class="ac2 pointer" onClick="getComments('$PostHashHex','$CommentLimit','$PublicKey')">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="commentIcon"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
<div id=clear></div></div></div>
<div id=acBottom><div id=ac21 class=ac2>$ac21</div></div></div>
<div id=comments-$PostHashHex></div></div>
</div><script>didDiv('$ParentStakeID','$PostHashHex');</script></div>
EOD;

return $echo;
}











echo $echo;

exit;

?>
