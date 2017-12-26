<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        
    </head>
    <body>
    <input type="hidden" name="user_id" id="user_id" value="{{ $user_id}}">
    <input type="hidden" name="name" id="name" value="{{ $name}}">
    <input type="hidden" name="opponent_id" id="opponent_id" value="{{ $opponent_id }}">
    <input type="hidden" name="opponent_name" id="opponent_name" value="{{ $opponent_name }}">

    
    <script type="text/javascript" src="script.js"></script>

    <script type="text/javascript">
       (function(d, m){var s, h;       
       s = document.createElement("script");
       s.type = "text/javascript";
       s.async=true;
       s.src="https://apps.applozic.com/sidebox.app";
       h=document.getElementsByTagName('head')[0];
       h.appendChild(s);
       window.applozic=m;
       m.init=function(t){m._globals=t;}})(document, window.applozic || {});
    </script>

    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script>
        $('document').ready(function () {

            var nameValue = document.getElementById("name").value;
            // var avatarURL = document.getElementById("avatar_url").value;
            var userId = document.getElementById("user_id").value;;
            var opponent_id = document.getElementById("opponent_id").value;
            var opponent_name = document.getElementById("opponent_name").value;
            
            if(nameValue.length > 0){
                window.applozic.init({
                    appId: '29d9da79ffdee368152253225c58ab27e',      //Get your application key from https://www.applozic.com
                    userId: userId,                     //Logged in user's id, a unique identifier for user
                    userName: nameValue,                 //User's display name
                    // imageLink : avatarURL,                     //User's profile picture url
                    email : '',                         //optional
                    contactNumber: '',                  //optional, pass with internationl code eg: +13109097458
                    desktopNotification: true,
                    source: '1',                          // optional, WEB(1),DESKTOP_BROWSER(5), MOBILE_BROWSER(6)
                    notificationIconLink: 'https://www.applozic.com/favicon.ico',    //Icon to show in desktop notification, replace with your icon
                    authenticationTypeId: 1,          //1 for password verification from Applozic server and 0 for access Token verification from your server
                    accessToken: '',                    //optional, leave it blank for testing purpose, read this if you want to add additional security by verifying password from your server https://www.applozic.com/docs/configuration.html#access-token-url
                    locShare: true,
                    googleApiKey: "AIzaSyDKfWHzu9X7Z2hByeW4RRFJrD9SizOzZt4",   // your project google api key 
                    googleMapScriptLoaded : false,   // true if your app already loaded google maps script
                    mapStaticAPIkey: "AIzaSyCWRScTDtbt8tlXDr6hiceCsU83aS2UuZw",
                    autoTypeSearchEnabled : true,     // set to false if you don't want to allow sending message to user who is not in the contact list
                    loadOwnContacts : false, //set to true if you want to populate your own contact list (see Step 4 for reference)
                    olStatus: false,         //set to true for displaying a green dot in chat screen for users who are online
                    onInit : function(response) {
                       if (response === "success") {
                          // window.applozic.sendMessage({
                          //   to:opponent_id
                          //   message:'Hello, How are you handsomeguy',
                          //   type:0
                          // });

                          $applozic.fn.applozic('sendMessage', {
                                      'to': opponent_id,            // userId of the receiver
                                      'message' : "{{ $message }}",       // message to send    
                                      'type' : 0                     //(optional) DEFAULT(0), TEXT_HTML(3)
                                    });

                       } else {
                          // error in user login/register (you can hide chat button or refresh page)
                       }
                   },
                   contactDisplayName: function(otherUserId) {
                         //return the display name of the user from your application code based on userId.
                         return "";
                   },
                   contactDisplayImage: function(otherUserId) {
                         //return the display image url of the user from your application code based on userId.
                         return "";
                   },
                   onTabClicked: function(response) {
                         // write your logic to execute task on tab load
                         //   object response =  {
                         //    tabId : userId or groupId,
                         //    isGroup : 'tab is group or not'
                         //  }
                   }
                });
            }
        });

    </script>
    </body>
</html>
