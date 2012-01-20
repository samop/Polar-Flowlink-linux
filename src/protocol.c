#include "protocol.h"
#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <string.h>
hid_device *openHID(int vid,int pid){
	wchar_t wstr[MAX_STR];
	hid_device *handle;
	int res;
	unsigned char buf[256];
	// Set up the command buffer.
	memset(buf,0x00,sizeof(buf));
	buf[0] = 0x01;
	buf[1] = 0x81;

	handle = hid_open(vid, pid, NULL);
	if (!handle) {
		printf("unable to open device\n");
 		return NULL;
	}

	// Read the Manufacturer String
	wstr[0] = 0x0000;
	res = hid_get_manufacturer_string(handle, wstr, MAX_STR);
	if (res < 0)
		printf("Unable to read manufacturer string\n");
	printf("Manufacturer String: %ls\n", wstr);

	// Read the Product String
	wstr[0] = 0x0000;
	res = hid_get_product_string(handle, wstr, MAX_STR);
	if (res < 0)
		printf("Unable to read product string\n");
	printf("Product String: %ls\n", wstr);

	// Read the Serial Number String
	wstr[0] = 0x0000;
	res = hid_get_serial_number_string(handle, wstr, MAX_STR);
	if (res < 0)
		printf("Unable to read serial number string\n");
	printf("Serial Number String: (%d) %ls", wstr[0], wstr);
	printf("\n");

	// Read Indexed String 1
	wstr[0] = 0x0000;
	res = hid_get_indexed_string(handle, 1, wstr, MAX_STR);
	if (res < 0)
		printf("Unable to read indexed string 1\n");
	printf("Indexed String 1: %ls\n", wstr);

	// Set the hid_read() function to be non-blocking.
	hid_set_nonblocking(handle, 1);

	// Try to read from the device. There shoud be no
	// data here, but execution should not block.
	res = hid_read(handle, buf, 17);



	// Send a Feature Report to the device
	buf[0] = 0x2;
	buf[1] = 0xa0;
	buf[2] = 0x0a;
	buf[3] = 0x00;
	buf[4] = 0x00;
	res = hid_send_feature_report(handle, buf, 17);
	if (res < 0) {
		printf("Unable to send a feature report.\n");
	}

	


	return handle;
}

void closeHID(hid_device *handle){
	hid_close(handle);
	/* Free static HIDAPI objects. */
	hid_exit();
}

int poolPresence(hid_device *handle){
	unsigned char buf[256];
	memset(buf,0,sizeof(buf));
	int res,i;

	// Read a Feature Report from the device
	buf[0] = 0x2;
	res = hid_get_feature_report(handle, buf, sizeof(buf));
	if (res < 0) {
		printf("Unable to get a feature report.\n");
		printf("%ls", hid_error(handle));
	}
	else {
		// Print out the returned buffer.
		printf("Feature Report\n   ");
		for (i = 0; i < res; i++)
			printf("%02hhx ", buf[i]);
		printf("\n");
	}
	if(buf[2]==0x02 && buf[3]==0x0b){
		printf("HRM not present.\n");
		return 0;
	}
	printf("HRM present.\n");
	return 1;
}

int executeCommand1(hid_device *handle, unsigned char *buf, int bufsize, unsigned char *command, int commandsize, int showdata){
	int res;
	memset(buf,0,bufsize*sizeof(unsigned char));
	memcpy(buf,command,commandsize*sizeof(unsigned char));
	res = hid_write(handle, buf, 17);
	if (res < 0) {
		printf("Unable to write()\n");
		printf("Error: %ls\n", hid_error(handle));
		return 0;
	}
	res=readData(handle,buf,bufsize, showdata);
	return res;

}

int readData(hid_device *handle, unsigned char *buf, int bufsize, int showdata){
	int i,res;
	memset(buf,0,bufsize*sizeof(unsigned char));
	res = 0;
	for(i=1;i<5;i++) {
		res = hid_read(handle, buf, bufsize*sizeof(unsigned char));
		if (res > 1) break;
		if (res == 0)
			printf("waiting...\n");
		if (res < 0)
			printf("Unable to read()\n");
		usleep(500*1000);
	}
	if (res<=0) {
		printf("No data received!\n");
		return 0;
	}

	printf("Data received:\n   ");
	// Print out the returned buffer.
	if (showdata==TRUE){
		for (i = 0; i < res; i++)
			printf("%02hhx ", buf[i]);
		printf("\n");
	}
	return 1;


}

