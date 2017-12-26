<html>

<head></head>

<body>
<p> Reddit Link Scrapper and JSON parser</p>


<?php
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 2400);

Subreddit with link to scrape
runProcess("https://www.reddit.com/r/GifRecipes/new.json?limit=1000");
?>

</body>
</html>


<?php


function runProcess($scanURL){

$results = fetchJSON($scanURL);

scrapePage($results, $scanURL);

}


function fetchJSON($testURL){
$string_reddit = file_get_contents($testURL);
$json = json_decode($string_reddit, true);

return $json;
}



function scrapePage($json, $newURL){


//Scrape 24 links per page then goto the next page till there aren't anymore; download links(gifs) to folder on script host.
for($x = 0; $x <= 24; $x++)
{


//print_r($json[data][children][$x][data][title]);
echo "<br>";

$fTitle = $json['data']['children']["$x"]['data']['title'];

$splitName = explode(" ", $fTitle);
$endName = end($splitName);
mkdir("recipies/".$endName, 0755, true);
$structure = "recipies/".$endName."/".$fTitle;
mkdir($structure, 0755, true);

$testUrl = $json['data']['children']["$x"]['data']['url'];
//test if link host is imugur or gyfcat; scrape file differently depending on file host.
$testResults = testURL($testUrl);

echo $testResults;
$parsedName = parse_url("$testResults");
$image = file_get_contents($testResults);
$pathExplode = explode("/", $parsedName['path']);
echo "<br>";
//print_r($pathExplode);
echo "<br>";

if($pathExplode[1] == "download")
{
file_put_contents($structure."/".$pathExplode[2].".gif", $image);
}else{
file_put_contents($structure."/".$parsedName['path'].".gif", $image);
}
echo "<br><br>";


if($x == 24)
{
$nameVar = "&after=".$json['data']['children']["$x"]['data']['name'];
runProcess($newURL.$nameVar);

}
}

}



function testURL($uTest)
{

$parsedLink = parse_url("$uTest");

if($parsedLink['host'] == "gfycat.com")
{

//Load the HTML page
$html = file_get_contents("$uTest");
//Create a new DOM document
$dom = new DOMDocument;
 
//Parse the HTML. The @ is used to suppress any parsing errors
//that will be thrown if the $html string isn't valid XHTML.
@$dom->loadHTML($html);
 
//Get all links. You could also use any other tag name here,
//like 'img' or 'table', to extract other tags.
$links = $dom->getElementsByTagName('source');
 
 
foreach ($links as $link){

if($link->getAttribute('type' ) == 'video/mp4')
    {
    //Extract and show the "href" attribute. 
    $token[] = $link->getAttribute('src');
    }
} 

return $token[0];

}else{
$imgurPath = pathinfo($uTest);
$buildPath = "https://imgur.com/download/".$imgurPath['filename'];
return $buildPath;
}




}






?>