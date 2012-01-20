#ifndef __PROTOCOL_H
#define __PROTOCOL_H

#include "hidapi.h"
#define MAX_STR 255
#define TRUE 1
#define FALSE 0

hid_device *openHID(int vid,int pid);

void closeHID(hid_device *handle);
int poolPresence(hid_device *handle);
int executeCommand1(hid_device *handle, unsigned char *buf, int bufsize, unsigned char *command, int commandsize, int showdata);
int readData(hid_device *handle, unsigned char *buf, int bufsize, int showdata);
#endif
