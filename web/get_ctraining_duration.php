<?php
include_once './dbf.php';
//get_training_duration();

function get_training_duration($a){
$db=new baza();
$db->connect();
if($db->dbhandle===NULL){
        echo 'Napaka pri povezavi z bazo<BR>';
        exit(1);
}
//BUG previous week does not work if current week is 1!
//$a=$_GET['previous'];

//individual training;
if($a==0){
$query="select start_time as order_time, to_char(start_time,'DD.MM HH24:MI Dy') as start_time,avg_hr, max_hr,calories,extract(hours from time_in_zone_1*60) as z1,extract(hours from time_in_zone_2*60) as z2, extract(hours from time_in_zone_3*60) as z3, extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*60) as nozone, time_in_zone_1, time_in_zone_2, time_in_zone_3,duration-time_in_zone_1-time_in_zone_2-time_in_zone_3 as time_in_nozone  from training order by order_time;";
} elseif($a==1) {
//training/day
$query="select to_char(date(start_time),'Dy DD.MM') as start_time, avg(avg_hr)::integer as avg_hr, max(max_hr) as max_hr ,sum(calories) as calories,sum(extract(hours from time_in_zone_1*60)) as z1,sum(extract(hours from time_in_zone_2*60)) as z2, sum(extract(hours from time_in_zone_3*60)) as z3, sum(extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*60)) as nozone, sum(time_in_zone_1) as time_in_zone_1, sum(time_in_zone_2) as time_in_zone_2, sum(time_in_zone_3) as time_in_zone_3,sum(duration-time_in_zone_1-time_in_zone_2-time_in_zone_3) as time_in_nozone  from training group by date(start_time) order by date(start_time);";
} else {
//training/week
$query="select extract(week from start_time) as start_time, avg(avg_hr)::integer as avg_hr, max(max_hr) as max_hr ,sum(calories) as calories,sum(extract(hours from time_in_zone_1*60)) as z1,sum(extract(hours from time_in_zone_2*60)) as z2, sum(extract(hours from time_in_zone_3*60)) as z3, sum(extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*60)) as nozone, sum(time_in_zone_1) as time_in_zone_1, sum(time_in_zone_2) as time_in_zone_2, sum(time_in_zone_3) as time_in_zone_3,sum(duration-time_in_zone_1-time_in_zone_2-time_in_zone_3) as time_in_nozone  from training group by extract(week from start_time) order by extract(week from start_time);";
}

$db->query($query);
$arr=array();
$estcal=0;
for($i=0;$i<$db->rows;$i++){
$db->getrow(NULL);
$estcal=0;
$estcal=$estcal+((-95.7735+(0.634*123)+(0.404*54)+(0.394*85)+(0.271*31))/4.184)*60*$db->crow['z1']/60;
$estcal=$estcal+((-95.7735+(0.634*140)+(0.404*54)+(0.394*85)+(0.271*31))/4.184)*60*$db->crow['z2']/60;
$estcal=$estcal+((-95.7735+(0.634*158)+(0.404*54)+(0.394*85)+(0.271*31))/4.184)*60*$db->crow['z3']/60;

array_push($arr,array($db->crow['start_time'],intval($db->crow['nozone']),$db->crow['time_in_nozone'],intval($db->crow['z1']),$db->crow['time_in_zone_1'],intval($db->crow['z2']),$db->crow['time_in_zone_2'],intval($db->crow['z3']),$db->crow['time_in_zone_3'],intval($db->crow['avg_hr']),intval($db->crow['max_hr']),intval($db->crow['calories']),$estcal));

}
#$arr=array (array('Zone 1 (60%-70%) MaxHR',intval($db->crow['z1'])),array('Zone 2 (70%-80%) MaxHR',intval($db->crow['z2'])), array('Zone 3 (80%-90%) MaxHR',intval($db->crow['z3'])),array('Out of zone',intval($db->crow['nozone'])));	
$db->close();
echo json_encode($arr);
}
?>
