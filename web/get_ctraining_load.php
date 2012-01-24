<?php
include_once './dbf.php';
//get_training_duration();

function get_training_load($a){
$db=new baza();
$db->connect();
if($db->dbhandle===NULL){
        echo 'Napaka pri povezavi z bazo<BR>';
        exit(1);
}
//BUG previous week does not work if current week is 1!
//$a=$_GET['previous'];

if($a==0){
$query="select to_char(start_time,'DD.MM HH24:MI Dy') as start_time,avg_hr, max_hr,calories,extract(hours from time_in_zone_1*3600) as z1,extract(hours from time_in_zone_2*3600) as z2, extract(hours from time_in_zone_3*3600) as z3, extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*3600) as nozone, time_in_zone_1, time_in_zone_2, time_in_zone_3,duration-time_in_zone_1-time_in_zone_2-time_in_zone_3 as time_in_nozone  from training order by start_time;";
} elseif($a==1) {
$query="select to_char(date(start_time),'Dy DD.MM') as start_time, avg(avg_hr)::integer as avg_hr, max(max_hr) as max_hr ,sum(calories) as calories,sum(extract(hours from time_in_zone_1*3600)) as z1,sum(extract(hours from time_in_zone_2*3600)) as z2, sum(extract(hours from time_in_zone_3*3600)) as z3, sum(extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*3600)) as nozone, sum(time_in_zone_1) as time_in_zone_1, sum(time_in_zone_2) as time_in_zone_2, sum(time_in_zone_3) as time_in_zone_3,sum(duration-time_in_zone_1-time_in_zone_2-time_in_zone_3) as time_in_nozone  from training group by date(start_time) order by date(start_time);";
} else {
$query="select extract(week from start_time) as start_time, avg(avg_hr)::integer as avg_hr, max(max_hr) as max_hr ,sum(calories) as calories,sum(extract(hours from time_in_zone_1*3600)) as z1,sum(extract(hours from time_in_zone_2*3600)) as z2, sum(extract(hours from time_in_zone_3*3600)) as z3, sum(extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*3600)) as nozone, sum(time_in_zone_1) as time_in_zone_1, sum(time_in_zone_2) as time_in_zone_2, sum(time_in_zone_3) as time_in_zone_3,sum(duration-time_in_zone_1-time_in_zone_2-time_in_zone_3) as time_in_nozone  from training group by extract(week from start_time) order by extract(week from start_time);";
}

$db->query($query);
$arr=array();
for($i=0;$i<$db->rows;$i++){
$db->getrow(NULL);
$estcal=0;
$estcal=$estcal+((-95.7735+(0.634*123)+(0.404*54)+(0.394*85)+(0.271*31))/4.184)*60*$db->crow['z1']/3600;
$estcal=$estcal+((-95.7735+(0.634*140)+(0.404*54)+(0.394*85)+(0.271*31))/4.184)*60*$db->crow['z2']/3600;
$estcal=$estcal+((-95.7735+(0.634*158)+(0.404*54)+(0.394*85)+(0.271*31))/4.184)*60*$db->crow['z3']/3600;
//$load=1.62*$db->crow['z1']/60+2.12*$db->crow['z2']/60+3.16*$db->crow['z3']/60;
$load=1.5*$db->crow['z1']/60+2.15*$db->crow['z2']/60+3.1*$db->crow['z3']/60;


array_push($arr,array($db->crow['start_time'],$load,intval($db->crow['calories']),$estcal));

}
$db->close();
echo json_encode($arr);
}
?>
