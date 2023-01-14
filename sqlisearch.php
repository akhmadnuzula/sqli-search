<?php
$val = getopt("d:");
if ($val) {
    $dork = $val['d'];
}else {
    echo "Failed, insert dork ==> php scrap.php -d inurl:index.php\n";
    exit;
}

function getcurl($url){
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch,CURLOPT_TIMEOUT,1000);
    $output = curl_exec($ch); 
    curl_close($ch);    
    return $output;
}

$error = [
    'expects parameter 1 to be mysqli_result',
    'You have an error in your SQL syntax'
];

for ($i=1; $i < 20 ; $i++) { 
    $encode = urlencode($dork);
    $search = getcurl("https://www.ask.com/web?q=$encode&qo=pagination&page=$i");
    $dom = new DOMDocument;
    @$dom->loadHTML($search);
    $finder = new DOMXPath($dom);
    $links = $finder->query("//*[contains(@class, 'result-link')]");
    foreach ($links as $link){
        $url = $link->getAttribute('href');
        $geturl = getcurl("$url%27");
        $vuln = "";
        foreach($error as $item){
            if(strpos($geturl, $item)){
                $vuln = "<span color='green'>Vuln</span>";
            }
        }

        if($vuln){
            echo "\033[32m$url \033[0m\n";
        }else{
            echo "$url\n";
        }
        // echo "$url' <a href='$url%27' target='_blank'>Open Link</a> $vuln<br>";
    }
    if(!$links){
        break;
    }
}

?>