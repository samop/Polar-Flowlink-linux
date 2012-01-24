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
$query="select to_char(date(start_time),'Dy DD.MM') as start_time, extract(epoch from date(start_time)) as epoch_start, avg(avg_hr)::integer as avg_hr, max(max_hr) as max_hr ,sum(calories) as calories,sum(extract(hours from time_in_zone_1*3600)) as z1,sum(extract(hours from time_in_zone_2*3600)) as z2, sum(extract(hours from time_in_zone_3*3600)) as z3, sum(extract(hours from (duration-time_in_zone_1-time_in_zone_2-time_in_zone_3)*3600)) as nozone, sum(time_in_zone_1) as time_in_zone_1, sum(time_in_zone_2) as time_in_zone_2, sum(time_in_zone_3) as time_in_zone_3,sum(duration-time_in_zone_1-time_in_zone_2-time_in_zone_3) as time_in_nozone  from training group by date(start_time) order by date(start_time);";
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

#$factor=-5.5113E-6;
#$factor=-4.0113E-6;
$factor=-9E-6;
$segments=100;
$now=time()+3600*24; //$tarr[$total_idx][0];
$deltat=($now-$tarr[0][0])/$segments;
$startt=$tarr[0][0];
$lowload=200;
$highload=400;

for($i=0;$i<$segments;$i++){
	$loadcurve[$i]=0;
	$timeline[$i]=$tarr[0][0]+$i*$deltat;
}
	$tarr[$total_idx+1][0]=$now+100;
	$sttime=0;
	$k=0;
	$j=-1;
	$offset=0;
	$offsettobe=0;

for($i=0;$i<$segments;$i++){
	$time=$i*$deltat+$startt;
	if($time>=($tarr[$j+1][0])){
		if($j>0) $offset=$offsettobe; //$tarr[$j][1]*exp($factor*($time-$sttime));
		$sttime=$time;
		$j++;
	}
	$loadcurve[$i]=($offset+$tarr[$j][1])*exp($factor*($time-$sttime));
	$offsettobe=$loadcurve[$i];
}
/*
for($i=$tarr[0][0]; $i<$now;$i=$i+$deltat){
	if($i>=$tarr[$j+1][0]-$deltat){
		if($j>-1) $offset=$tarr[$j][1]*(exp($factor*($i-$sttime)));
		$sttime=$i;
		 $j++;
	}
	if($sttime!=0){		
		$loadcurve[$k]=$offset+$tarr[$j][1]*(exp($factor*($i-$sttime)));
	}
//		echo $i-$sttime;
	$k++;
} */
//echo $tarr[0][0];
$arr=array();
for($i=0;$i<$segments;$i++){
array_push($arr,array(date('d.m.Y', $timeline[$i]),$lowload,$highload,$loadcurve[$i]));
}
echo json_encode($arr);
//array_push($arr,array($db->crow['start_time'],$load,intval($db->crow['calories']),$estcal));


$db->close();
//echo json_encode($arr);
}
?>
