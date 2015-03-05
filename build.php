<?php

shell_exec('./build.sh');

$dst = '/home/fbn/www/ff';
$hostname = 'localhost';
$username = 'root';
$password = 'ct532lr';
$db = 'ff';



try {
	$dbh = new PDO("mysql:host=$hostname;dbname=$db", $username, $password);
}
catch(PDOException $e){
	echo $e->getMessage();
}

$stmt = $dbh->prepare("UPDATE oc_modification SET xml=:xml WHERE code='fbn.github.io/cool-theme'");
$xml = file_get_contents('src/install.xml');
$stmt->bindParam(':xml', $xml, \PDO::PARAM_STR);
$stmt->execute();

$path = realpath('src/upload');
$baseSize = strlen($path);
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $name => $object){    
    $relPath = substr($name, $baseSize+1 );
    if(!$object->isDir()){
    	$to = $dst.'/'.$relPath;
    	$toDir =  $dst.'/'.substr($object->getPath(),  $baseSize+1 );
    	if(!file_exists($toDir)) mkdir($toDir, 0777, true);
    	copy( $name , $to);
    	echo $object->getBaseName()." => $to\n";
    }
}