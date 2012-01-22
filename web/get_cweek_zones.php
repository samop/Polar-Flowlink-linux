<?php
include './dbf.php';
function add_zones($a){
$db=new baza();
$db->connect();
if($db->dbhandle===NULL){
        echo 'Napaka pri povezavi z bazo<BR>';
        exit(1);
}
//BUG previous week does not work if current week is 1!
//$a=$_GET['previous'];
$query="select extract(hours from sum(time_in_zone_1*3600)) as z1, extract(hours from sum(time_in_zone_2*3600)) as z2, extract(hours from sum(time_in_zone_3*3600)) as z3,extract(hours from (sum(duration)- (sum(time_in_zone_2)+sum(time_in_zone_1)+ sum(time_in_zone_3)))*3600) as nozone from training where EXTRACT(WEEK FROM start_time)=EXTRACT(WEEK FROM now())-$a;";
if($a<0){
	$query="select extract(hours from sum(time_in_zone_1*3600)) as z1, extract(hours from sum(time_in_zone_2*3600)) as z2, extract(hours from sum(time_in_zone_3*3600)) as z3, extract(hours from (sum(duration)- (sum(time_in_zone_2)+sum(time_in_zone_1)+ sum(time_in_zone_3)))*3600) as nozone from training;";
}
$db->query($query);
$db->getrow(NULL);
//$arr=array (array('Zone 1',1),array('Zone 2',2), array('Zone 3',3));	
$arr=array (array('Zone 1 (60%-70%) MaxHR',intval($db->crow['z1'])),array('Zone 2 (70%-80%) MaxHR',intval($db->crow['z2'])), array('Zone 3 (80%-90%) MaxHR',intval($db->crow['z3'])),array('Out of zone',intval($db->crow['nozone'])));	
$db->close();
echo json_encode($arr);
}
?>
