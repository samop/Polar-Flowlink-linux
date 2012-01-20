#include "parse_data.h"
#include<stdio.h>
int parseUserData(unsigned char *buf, int bufsize){

	if(buf[2]!=0x14 && buf[3]!=0x0e){
		printf("Buffer does not consist of user data!\n");
		return 1;
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

int parseTrainingData(unsigned char *buf, int bufsize){

	if(buf[2]!=0x2a && buf[3]!=0x06){
		printf("Buffer does not consist of training data!\n");
		return 1;
	}
	
	printf("training_id=%d\n",buf[5]);
	printf("training duration=%x:%x:%x\n",buf[8],buf[7],buf[6]);
	printf("start time=%d.%d.%d %x:%x:%x\n", buf[9],buf[10],2000+buf[11], buf[12],buf[13],buf[14]);
	printf("Time in zone 1=%x:%x:%x ",buf[18],buf[17],buf[16]);
	printf("in zone 2=%x:%x:%x ",buf[21],buf[20],buf[19]);
	printf("in zone 3=%x:%x:%x'\n",buf[24],buf[23],buf[22]);
	printf("Zone1 HR limits (%d,%d), Zone2 HR limits (%d,%d), Zone3 HR limits (%d,%d)\n",buf[25],buf[26],buf[27],buf[28],buf[29],buf[30]);
	printf("Calories burned=%d\n",(buf[32]<<8)+buf[31]);
	printf("Fat burn percentage=%d %\n",buf[33]);
	printf("Average HR=%d\n", buf[34]);
	printf("Max HR=%d\n",buf[35]);
	printf("HRmax=%d\n", buf[36]);
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
