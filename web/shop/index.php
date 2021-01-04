<?php
session_start();

require_once(__DIR__.'/configs/web.php');

//check the current status from the user
$ch = curl_init($waitingPageStatusUrl);

$sid = md5(session_id());

$time = time();

if($ch){

    $time = time();

    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER=>[
            'X-AUTH-KEY:'.$apiAuthKey,
            'X-AUTH-PASSWORD:'.md5($time."|".$sid."|".$domain."|".$apiAuthKey."|".$apiAuthToken)
        ],
        CURLOPT_POST=>1,
        CURLOPT_POSTFIELDS=> http_build_query([
            'success_url'=>$domain,
            'sid'=>$sid,
            'time'=>$time
        ]),
        CURLOPT_RETURNTRANSFER=>true
    ]);

    $content = curl_exec($ch);
    
    
    
    curl_close($ch);
    
    //check whether there is any content back
    if($content){

        $data = json_decode($content);
        //check whether the json return correct and 
        if(
            json_last_error() === JSON_ERROR_NONE 
            //check json instore is correct
            && isset($data->inStore) 
            //check json value in store is true
            && $data->inStore
        ){

            $valid = true;

        }
    }

    //check whether the user allow to stay in store
    if(!$valid){

        //not in store list'
        header("Location: ".$waitingPage."?q={$sid}", 302);

        exit();
    }
    
    
}
//end of server side checking

?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        
            
    </head>
    <body>
        <div>Hello, <?=$sid ?></div>
        <p>You are in shop now and you still have <span id="time-left">--</span> second to stay at the shop</p>
        
        
        <audio src="<?=$waitingPageUrl ?>/assets/sounds/n1.mp3" style='display: none' id='notification-audio'></audio>  
        <script type='text/javascript'>
            
            var WaitingPage = function(){
            
                var checkStatusTimer = null;    
                
                var loop = 5000;
                
                var alertLevel = <?= $alertLevel ?>;
                
                var alertMessage = '<?=$alertMessage ?>';
                
                var expiredMessage = '<?= $expireMessage ?>';
                
                var waitingPageUrl = '<?= $waitingPage ?>';
                
                var waitingPageStatusUrl = '<?= $waitingPageStatusUrl ?>';

                function setCookie(cname, cvalue, second) {
                    var d = new Date();
                    d.setTime(d.getTime() + ((second) * 1000));
                    var expires = "expires="+d.toUTCString();
                    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
                }

                function getCookie(cname) {
                    var name = cname + "=";
                    var ca = document.cookie.split(';');
                    for(var i = 0; i < ca.length; i++) {
                      var c = ca[i];
                      while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                      }
                      if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                      }
                    }
                    return "";
                }


                var checkStatus = function(){

                    $.ajax({
                        url: waitingPageStatusUrl,
                        type:'post',
                        dataType: 'json',
                        success: function(r){
                            
                            //update the loop frequent
                            loop = r.timeLeft % (loop / 1000) == 0 ?  5000 : (r.timeLeft % (loop / 1000) * 1000) ;
                            
                            var timeLeft = 0;
                            
                            if(!isNaN(r.timeLeft)){
                                
                                timeLeft = r.timeLeft;
                                
                            }   
                            
                            $('#time-left').html(timeLeft);
                            
                            if(r.timeLeft  <= (alertLevel*60)){

                                if(!getCookie('expire_alerted')){
                                    setCookie('expire_alerted', 1, (alertLevel*120000));
                                    alert(alertMessage);

                                }

                            }
                            
                            if(r){

                                    if(!r.inStore){

                                         clearInterval(checkStatusTimer);

                                         $('#notification-audio').get(0).play();

                                         if(getCookie('expire_alerted')){
                                               
                                            setCookie('expire_alerted', 1, -1);
                                            
                                            alert(expiredMessage);

                                            window.location = waitingPageUrl;
                                               
                                         }

                                    }
                             }
                        }
                    });

                };
                
                this.start = function(){
                    
                    checkStatus();
                    
                    checkStatusTimer = setInterval(checkStatus, loop);
                    
                };
                    
                return this;
                
            };
            
            var waitingPage = new WaitingPage;
            
            waitingPage.start();
            
        </script>
    </body>
</html>
