#ifndef __PARSE_DATA_H
#define __PARSE_DATA_H


int parseCommand1(unsigned char *buf, int bufsize);
int parseUserData(unsigned char *buf, int bufsize);
int parseTrainingData(unsigned char *buf, int bufsize);
int parseVO2maxMeasurements(unsigned char *buf, int bufsize);
int parseActiveProgram(unsigned char *buf, int bufsize);
#endif
