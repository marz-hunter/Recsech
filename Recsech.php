<?php
require_once("tools/sdata-modules.php");
require_once("tools/crt.php");
require_once("tools/Honeyscore.php");
require_once("tools/DomainTakeOver.php");
require_once("tools/TechDetected.php");
require_once("tools/EmailFinder.php");
/**
 * @Author: Eka Syahwan
 * @Date:   2017-12-11 17:01:26
 * @Last Modified by:   Nokia 1337
 * @Last Modified time: 2019-06-01 13:12:15
*/
$sdata = new Sdata;

echo "\n\n ╦═╗┌─┐┌─┐┌─┐┌─┐┌─┐┬ ┬ \r\n";
echo " ╠╦╝├┤ │  └─┐├┤ │  ├─┤ \r\n";
echo " ╩╚═└─┘└─┘└─┘└─┘└─┘┴ ┴ \r\n";
echo " Recsech - Recon And Research (BETA) \r\n\n";

if(empty($argv[1])){
	die(' use command : '.$argv[0]." domain.com\r\n");
}

function color($color = "default" , $text){
	$arrayColor = array(
		'grey' 		=> '1;30',
		'red' 		=> '1;31',
		'green' 	=> '1;32',
		'yellow' 	=> '1;33',
		'blue' 		=> '1;34',
		'purple' 	=> '1;35',
		'nevy' 		=> '1;36',
		'white' 	=> '1;0',
	);	
	return "\033[".$arrayColor[$color]."m".$text."\033[0m";
}
function stuck($msg){
    echo color("purple",$msg);
    $answer =  rtrim( fgets( STDIN ));
    return $answer;
}

$time_start = microtime(true); 
function secondsToTime($seconds) {
  $hours = floor($seconds / (60 * 60));
  $divisor_for_minutes = $seconds % (60 * 60);
  $minutes = floor($divisor_for_minutes / 60);
  $divisor_for_seconds = $divisor_for_minutes % 60;
  $seconds = ceil($divisor_for_seconds);
  $obj = array(
      "h" => (int) $hours,
      "m" => (int) $minutes,
      "s" => (int) $seconds,
   );
  return $obj;
}
 
echo color("grey","[i] Start scanning at ".date("d/m/Y h:i:m")."\r\n");
 
$Cert 		= new Cert($argv[1]);
$DomainList = $Cert->check();

$hit = 1;
foreach ($DomainList as $key => $domain) {
	echo "    [".($hit)."/".count($DomainList)."] ".color("green",$domain)."\r\n";
	$hit++;
}

echo color("yellow","[+] Domain Email @".$argv[1]." : \r\n");

$EmailFinder = new EmailFinder;
$getMAil  	 = $EmailFinder->Domain($argv[1]);
$hit = 1;
foreach ($getMAil as $keys => $email) {
	echo "    + ".color("green",$email)." \r\n"; 
}

$Honeyscore = new Honeyscore;

echo color("yellow","[+] Check Honeypot on all domains : \r\n");
$hit = 1;

foreach ($DomainList as $key => $domains) {
	$Honey = $Honeyscore->Domain($domains);
	echo "    + ".color("nevy",$Honey[ip])." ".color("green",$Honey[domain])." ".$Honey[score]."\r\n";
	$hit++;
}

$DomainTakeOver = new DomainTakeOver;

echo color("yellow","[+] Check Subdomain takeover : \r\n");
$hit = 1;
foreach ($DomainList as $keys => $domains) {
	$DomainTakeOvers = $DomainTakeOver->Domain($domains);
	foreach ($DomainTakeOvers as $key => $notice) {
		echo "    + ".color("green",$notice[domain])." ".$notice[status]."\r\n"; 
		$hit++;
	}
}

$TechDetected = new TechDetected;
echo color("yellow","[+] Check Technologies : \r\n");
$hit = 1;
foreach ($DomainList as $keys => $domains) {
	echo "    [".($keys+1)."/".count($DomainList)."] ".color("green",$domains)." \r\n"; 
	$TechDetecteds = $TechDetected->Domain($domains);
	
	foreach ($TechDetecteds as $key => $value) {
		echo "      + ".color("green",$value['name'])." \r\n"; 
	}
}

$checkMe  = secondsToTime(ceil((microtime(true) - $time_start)));
echo color("grey","\n\n[i] Scanning is complete in ".$checkMe['h']." hour ".$checkMe['m']." minutes ".$checkMe['s']." seconds\r\n");