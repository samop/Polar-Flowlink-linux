<?php
include_once './dbf.php';
//get_training_loadcurve(0);

function get_training_loadcurve($a){
$db=new baza();
$db->connect();
if($db->dbhandle===NULL){
        echo 'Napaka pri povezavi z bazo<BR>';
        exit(1);
}
//BUG previous week does not work if current week is 1!
//$a=$_GET['previous'];

if($a==0){
$query="select to_char(start_time,'DD.MM HH24:MI Dy') as start_time, extract(epoch from start_time) as epoch_start,avg_hr, max_hr,calories,extract(hours from time_in_zone_1*3600) as z1,extract(hours from time_in_zone_2*3600) as z2, extract(hours from time_in_zone_3*3600) as z3, extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*3600) as nozone, time_in_zone_1, time_in_zone_2, time_in_zone_3,duration-time_in_zone_1-time_in_zone_2-time_in_zone_3 as time_in_nozone  from training order by start_time;";
} elseif($a==1) {
$query="select to_char(date(start_time),'Dy DD.MM') as start_time, extract(epoch from start_time) as epoch_start, avg(avg_hr)::integer as avg_hr, max(max_hr) as max_hr ,sum(calories) as calories,sum(extract(hours from time_in_zone_1*3600)) as z1,sum(extract(hours from time_in_zone_2*3600)) as z2, sum(extract(hours from time_in_zone_3*3600)) as z3, sum(extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*3600)) as nozone, sum(time_in_zone_1) as time_in_zone_1, sum(time_in_zone_2) as time_in_zone_2, sum(time_in_zone_3) as time_in_zone_3,sum(duration-time_in_zone_1-time_in_zone_2-time_in_zone_3) as time_in_nozone  from training group by date(start_time) order by date(start_time);";
} else {
$query="select extract(week from start_time) as start_time, avg(avg_hr)::integer as avg_hr, max(max_hr) as max_hr ,sum(calories) as calories,sum(extract(hours from time_in_zone_1*3600)) as z1,sum(extract(hours from time_in_zone_2*3600)) as z2, sum(extract(hours from time_in_zone_3*3600)) as z3, sum(extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*3600)) as nozone, sum(time_in_zone_1) as time_in_zone_1, sum(time_in_zone_2) as time_in_zone_2, sum(time_in_zone_3) as time_in_zone_3,sum(duration-time_in_zone_1-time_in_zone_2-time_in_zone_3) as time_in_nozone  from training group by extract(week from start_time) order by extract(week from start_time);";
}

$db->query($query);
$tarr=array();
$total_idx=$db->rows-1;
for($i=0;$i<$db->rows;$i++){
	$db->getrow(NULL);
	$load=1.5*$db->crow['z1']/60+2.15*$db->crow['z2']/60+3.1*$db->crow['z3']/60;
	array_push($tarr, array($db->crow['epoch_start'],$load));
}

$factor=-4.5113E-6;
#$factor=-4.0113E-6;
$now=$tarr[$total_idx][0];
for($i=0;$i<101;$i++){
	$loadcurve[$i]=0;
	$timeline[$i]=$tarr[0][0]+$i*($now-$tarr[0][0])/100;
}
for($j=0;$j<$total_idx;$j++){
	$sttime=0;
	$k=0;
for($i=$tarr[0][0]; $i<$now;$i=$i+($now-$tarr[0][0])/100){
	if($i>=$tarr[$j][0]){
		if($sttime==0) $sttime=$i;		
		$loadcurve[$k]=$loadcurve[$k]+$tarr[$j][1]*(exp($factor*($i-$sttime)));
//		echo $i-$sttime;
	}
	$k++;
}
}
//echo $tarr[0][0];
$arr=array();
for($i=0;$i<101;$i++){
array_push($arr,array(date('d.m.Y', $timeline[$i]),$loadcurve[$i]));
}
echo json_encode($arr);
//array_push($arr,array($db->crow['start_time'],$load,intval($db->crow['calories']),$estcal));


$db->close();
//echo json_encode($arr);
}
?>
