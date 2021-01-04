<?php
require_once(dirname(__DIR__).'/configs/web.php');
$dateFile = dirname(__DIR__).'/data.json';

//handling the post form action
if(
    isset($_POST, $_POST['shopStayMinutes'], $_POST['shopMax'])
    && !empty($_POST['shopStayMinutes']) && is_numeric($_POST['shopStayMinutes'])
    && !empty($_POST['shopMax']) && is_numeric($_POST['shopMax'])
)
{
    
    $update = 'failed';
    
    $fp = fopen($dateFile, 'w+');
    
    if($fp){
        
        $params = [
            'stay'      =>  $_POST['shopStayMinutes'],
            'max'       =>  $_POST['shopMax'],
            'domain'    =>  $domain
        ];
        
        fwrite($fp, json_encode($params));
        
        fclose($fp);
        
        //update to the waiting side
        $ch = curl_init($waitingPage.'/data.php');
        
        if($ch){
            
            $params['stay'] *= 60;
            
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER =>array(
                    'X-AUTH-KEY:staffsaleshk.com',
                    'X-AUTH-PASSWORD:IeDny3UaSb0yr6ADIq8XyT8IR4CAwr9f',
                ),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS=> http_build_query($params),
                CURLOPT_RETURNTRANSFER=>1,
                CURLOPT_FOLLOWLOCATION=> true
            ]);        

            $result = curl_exec($ch);
            
            var_dump($result);
            
            curl_close($ch);
            
            $update =  'Success';
            
        }
        
    }
    
    echo $update.'<br />';
    
}



//display content
$data = @file_get_contents($dateFile);

$json = json_decode($data);


//default setting
$shopStayMinutes = 10;

//default max
$shopMax = 1000;

//shopDomain
$shopDomain = "";

if(JSON_ERROR_NONE == json_last_error()){
    
    $shopMax = $json->max;
    $shopStayMinutes = $json->stay;
    
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title></title>
    </head>
    <body>
        <form method="post">
            <div>
                <label for="shopMax">Max Number</label>
                <input type="text" name="shopMax" id="shopMax" value="<?= $shopMax ?>"/>
            </div>
            <div>
                <label for="shopMax">StayMinutes</label>
                <input type="text" name="shopStayMinutes" id="shopStayMinutes" value="<?= $shopStayMinutes ?>"/>
            </div>
            
            <button>Update</button>
        </form>
    </body>
</html>