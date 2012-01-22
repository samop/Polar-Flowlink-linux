<?php
#include 'funkcije.php';
include '../databasefunc/dbf.php';
include '../databasefunc/statistikanalog.php';


$fd=fopen("../data/csv_data.csv","w");
$db=new baza();
$db->connect();
if($db->dbhandle===NULL){
	echo 'Napaka pri povezavi z bazo<BR>';
	exit(1);
} 
$db1=new baza();
$db1->connect();
if($db1->dbhandle===NULL){
	echo 'Napaka pri povezavi z bazo<BR>';
	exit(1);
} 

$preverjanje_id=$_POST['rok_id'];
if($preverjanje_id==NULL){
	$preverjanje_id=getlastrok($db); 
}

fprintf($fd,"Priimek in ime, Vpisna st., St. tock, Procenti,  Smer studija,\n");

$getsql="SELECT * FROM pola LEFT JOIN studenti USING (student_id) LEFT JOIN resitve USING (ser_st) LEFT JOIN preverjanje USING (preverjanje_id) LEFT JOIN smer USING (smer_id) where (preverjanje_id=".$preverjanje_id.") ORDER BY regexp_replace(regexp_replace(regexp_replace(regexp_replace(regexp_replace(studenti.ime,'Č','CZ'),'Š','SZ'), 'Ž', 'ZZ'),'Ć','CZZ'),'Đ','DZ') ASC;";


$db->query($getsql);
if($db->rows===0) {
	echo('ni vrstic!');
	exit(1);
	}
	for($i=0;$i<$db->rows;$i++){
	$db->getrow(NULL);
	// DODATEK: gremo poracunat kako so resevali naloge po vrstnem redu!
	// najprej ektrahirajmo odgovore.
	$odgovori=getrezstring($db->crow['pola_id']);
	$retstring=getanswerstring($db1,$db->crow['resitve_id'],$db->crow['pravilni'],$odgovori);
	fprintf($fd,"%s,%s,%s,%s,%s%s\n",trim($db->crow['ime']),$db->crow['student_id'],$db->crow['ocena'],$db->crow['procenti'],$db->crow['smer_name'],$retstring);
}

$db->close();
$db1->close();
fclose($fd);
send_file('../data/csv_data.csv');

function send_file($file){
    header("Content-type: application/force-download");
    header("Content-Transfer-Encoding: Binary");
    header("Content-length: ".filesize($file));
    header("Content-disposition: attachment; filename=\"".basename($file)."\"");
    readfile("$file");
}

?>
