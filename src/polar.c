#include <stdio.h>
#include <wchar.h>
#include <string.h>
#include <stdlib.h>
#include "hidapi.h"
#include <unistd.h>
#include "protocol.h"
#include "parse_data.h"

int main(int argc, char* argv[])
{
	unsigned char buf[256];
//	#define MAX_STR 255
//	wchar_t wstr[MAX_STR];
	hid_device *handle;
	int i, n,present;
	unsigned char cmd1[256]={0x01,0x00,0x02,0x00,0x00};
	unsigned char cmd2[256]={0x01,0x00,0x02,0x10,0x00};
	unsigned char cmdusr[256]={0x01,0x00,0x02,0x0E,0x00};
	unsigned char cmdtrain[256]={0x01,0x00,0x03,0x06,0x00,0x00};
	unsigned char cmd3[256]={0x01,0x00,0x02,0x01,0x00};
	unsigned char cmd4[256]={0x01,0x00,0x02,0x04,0x00};
	unsigned char cmd5[256]={0x01,0x00,0x02,0x14,0x00};
	unsigned char cmd6[256]={0x01,0x00,0x02,0x14,0x20};
	unsigned char cmd7[256]={0x01,0x00,0x02,0x14,0x30};
	//struct hid_device_info *devs, *cur_dev;

	handle=openHID(0x0da4,0x0003);
	if(handle==NULL){
		printf("Error, could not connect to Polar FlowLink. Is Flowlink connected and do you have priviledges?\n");
		exit(1);
	}
	present=poolPresence(handle);
	if(present){
//		usleep(500*1000);
		executeCommand1(handle,buf,256,cmd1,5, FALSE);
		n=parseCommand1(buf,256);
//		usleep(500*1000);
		executeCommand1(handle,buf,256,cmd2,5, FALSE);
		readData(handle,buf,256, FALSE);
//		usleep(500*1000);
		printf("Great, let's get personal data!");
		executeCommand1(handle,buf,256,cmdusr,5, FALSE);
		parseUserData(buf,256);
		printf("Let's get training data!");	
		for(i=0;i<n;i++){
		cmdtrain[5]=i;
		executeCommand1(handle,buf,256,cmdtrain,6, FALSE);
		parseTrainingData(buf,256);
		}
		executeCommand1(handle,buf,256,cmd3,5, FALSE);
		parseVO2maxMeasurements(buf,256);
		executeCommand1(handle,buf,256,cmd4,5, FALSE);
		parseActiveProgram(buf,256);
		executeCommand1(handle,buf,256,cmd4,5, FALSE);
		executeCommand1(handle,buf,256,cmd5,5, FALSE);
		while(buf[1]){
		readData(handle,buf,256, FALSE);
		}
		//readData(handle,buf,256);
		executeCommand1(handle,buf,256,cmd6,5, FALSE);
		while(buf[1]){
		readData(handle,buf,256, FALSE);
		}
	/*	executeCommand1(handle,buf,256,cmd7,5);
		while(buf[1]){
		readData(handle,buf,256);
		}
*/
	}	
	closeHID(handle);

	return 0;
}
