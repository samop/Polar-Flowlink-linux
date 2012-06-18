#include "parse_data.h"
#include<stdio.h>
#include<time.h>
#include<stdlib.h>

int hex2int(int i){
	char c[255];
	sprintf(c,"%x",i);
	return atoi(c);
}

int parseUserData(unsigned char *buf, int bufsize, PGconn *db){

	if(buf[2]!=0x14 && buf[3]!=0x0e){
		printf("Buffer does not consist of user data!\n");
		return 1;
	}
	sUserData ud;
	ud.weight=(float)((buf[6]<<8)+buf[5])*0.045359;
	ud.height=buf[7];
	struct tm time;	
	time.tm_sec=1;
	time.tm_min=0;
	time.tm_hour=0;
	time.tm_mday=buf[8];
	time.tm_mon=buf[9]-1;
	time.tm_year=buf[10];
	time.tm_isdst = -1;	
	ud.birthdate=(long) mktime(&time);
	ud.sex=buf[11];
	ud.activity=buf[12];
	ud.vo2max=buf[13];
	ud.HRMax=buf[14];
	time.tm_sec=0;
	time.tm_min=hex2int(buf[18]);
	time.tm_hour=hex2int(buf[19]);
	time.tm_mday=buf[20];
	time.tm_mon=buf[21]-1;
	time.tm_year=buf[22]+100;
	time.tm_isdst = -1;	
	ud.editdate=(long) mktime(&time);
	if(db!=NULL){
		db_insert_udata(db,&ud);	
	}

	printf("weight=%.1f kg (%.1f lbs)\n", (double)((buf[6]<<8)+buf[5])*0.045359,(double)((buf[6]<<8)+buf[5])/10 );
	printf("height=%d cm\n", buf[7]);
	printf("birthdate=%d.%d.%d\n",buf[8],buf[9],1900+buf[10]);
	printf("sex=%s\n", buf[11]?"Female":"Male");
	printf("Activity=%d\n",buf[12]);
	printf("VO2max=%d\n",buf[13]);
	printf("HRmax=%d\n",buf[14]);
	printf("Information last edited=%d.%d.%d %x:%x\n",buf[20],buf[21],buf[22],buf[19],buf[18]);

	return 0;
}

int parseCommand1(unsigned char *buf, int bufsize){
	if(buf[2]!=0x3c && buf[3]!=0x00){
		printf("Buffer does not consist of training data!\n");
		return -1;
	}

	printf("Number of saved trainings=%d\n",buf[11]);
	return buf[11];
}



int parseTrainingData(unsigned char *buf, int bufsize, PGconn *db){

#define FT80 3

	if(buf[2]!=0x2a && buf[3]!=0x06){
		printf("Buffer does not consist of training data!\n");
		return 1;
	}
	
	sTraining trn;
	struct tm time;	
	trn.id=buf[5]+1;
	trn.duration=hex2int(buf[8+FT80])*3600+hex2int(buf[7+FT80])*60+hex2int(buf[6+FT80]);

	time.tm_sec=hex2int(buf[12+FT80]);
	time.tm_min=hex2int(buf[13+FT80]);
	time.tm_hour=hex2int(buf[14+FT80]);
	time.tm_mday=buf[9+FT80];
	time.tm_mon=buf[10+FT80]-1;
	time.tm_year=buf[11+FT80]+100;
	time.tm_isdst = -1;	
	trn.starttime=(long) mktime(&time);
	trn.timeinzone1=hex2int(buf[18+2*FT80])*3600+hex2int(buf[17+2*FT80])*60+hex2int(buf[16+2*FT80]);
	trn.timeinzone2=hex2int(buf[21+2*FT80])*3600+hex2int(buf[20+2*FT80])*60+hex2int(buf[19+2*FT80]);
	trn.timeinzone3=hex2int(buf[24+2*FT80])*3600+hex2int(buf[23+2*FT80])*60+hex2int(buf[22+2*FT80]);
	trn.z1hr[0]=buf[25+2*FT80];	
	trn.z1hr[1]=buf[26+2*FT80];	
	trn.z2hr[0]=buf[27+2*FT80];	
	trn.z2hr[1]=buf[28+2*FT80];	
	trn.z3hr[0]=buf[29+2*FT80];	
	trn.z3hr[1]=buf[30+2*FT80];	
	trn.calories=(buf[32+2*FT80]<<8)+buf[31+2*FT80];
	trn.fatprocent=buf[33+2*FT80];
	trn.avgHr=buf[34+2*FT80];
	trn.maxHr=buf[35+2*FT80];
	trn.HRMax=buf[36+2*FT80];

	if(db!=NULL){
		db_insert_trn(db,&trn);
	}

	printf("training_id=%d\n",buf[5]);
	printf("training duration=%x:%x:%x\n",buf[8+FT80],buf[7+FT80],buf[6+FT80]);
	printf("start time=%d.%d.%d %x:%x:%x\n", buf[9+FT80],buf[10+FT80],2000+buf[11+FT80], buf[14+FT80],buf[13+FT80],buf[12+FT80]);
	printf("Time in zone 1=%x:%x:%x ",buf[18+2*FT80],buf[17+2*FT80],buf[16+2*FT80]);
	printf("in zone 2=%x:%x:%x ",buf[21+2*FT80],buf[20+2*FT80],buf[19+2*FT80]);
	printf("in zone 3=%x:%x:%x'\n",buf[24+2*FT80],buf[23+2*FT80],buf[22+2*FT80]);
	printf("Zone1 HR limits (%d,%d), Zone2 HR limits (%d,%d), Zone3 HR limits (%d,%d)\n",buf[25+2*FT80],buf[26+2*FT80],buf[27+2*FT80],buf[28+2*FT80],buf[29+2*FT80],buf[30+2*FT80]);
	printf("Calories burned=%d\n",(buf[32+2*FT80]<<8)+buf[31+2*FT80]);
	printf("Fat burn percentage=%d %\n",buf[33+2*FT80]);
	printf("Average HR=%d\n", buf[34+2*FT80]);
	printf("Max HR=%d\n",buf[35+2*FT80]);
	printf("HRmax=%d\n", buf[36+2*FT80]);
	return 0;
}

int parseVO2maxMeasurements(unsigned char *buf, int bufsize){
	int i;
	if(buf[2]!=0x21 && buf[3]!=0x01){
		printf("Buffer does not consist of VO2max data!\n");
		return 1;
	}
	printf("Number of measurements: %d\n", buf[5]);

	for(i=0;i<buf[5];i++){
	printf("%d.%d.%d %x:%x :: VO2max=%d\n", buf[6+6*i], buf[7+6*i], buf[8+6*i], buf[11+6*i], buf[10+6*i], buf[9+6*i]);
	}
	return 1;

}

int parseActiveProgram(unsigned char *buf, int bufsize){
	if(buf[2]!=0x31 && buf[3]!=0x04){
		printf("Buffer does not consist of active program data!\n");
		return 1;
	}
	printf("Active program has following targets!\n");
	printf("Total calories to be spent: %d\n", (buf[20]<<8)+buf[19]);
	printf("Time in zone 1: %x:%x:%x\n", buf[23],buf[22],buf[21]);
	printf("Time in zone 1: %x:%x:%x\n", buf[26],buf[25],buf[24]);
	printf("Time in zone 1: %x:%x:%x\n", buf[29],buf[28],buf[27]);
	return 1;
}
