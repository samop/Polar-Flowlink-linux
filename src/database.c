#include "database.h"
#include "parse_data.h"
#include <stdlib.h>
#include <string.h>
#include <time.h>
void close_db(PGconn *conn){
     PQfinish(conn);
}

PGconn *connect_db(char *database_name){
	PGconn     *conn;
    char *conninfo;
    conninfo=(char *)malloc(255*sizeof(char));
    strcpy(conninfo,"dbname = ");
    strcat(conninfo, database_name);
	conn = PQconnectdb(conninfo);
    free(conninfo);
	if (PQstatus(conn) != CONNECTION_OK)
        {
                printf("Connection to database failed: %s\n",
                        PQerrorMessage(conn));
        }
    return conn;
}




int test_db_connection(char *database_name){
	PGconn     *conn;

	conn = connect_db(database_name);
	if (PQstatus(conn) != CONNECTION_OK)
        {
                close_db(conn);
                return(1);
        }
    else {
        printf("Connection to database successful! Will use database for storage of data.\n");
    }

    close_db(conn);
	return(0);
}


int db_insert_udata(PGconn *conn, sUserData *ud){
    PGresult   *res;
    char *query=malloc(10000*sizeof(char));
	char editdate[255];
	char birthdate[255];
	struct tm tm;//=malloc(sizeof(struct tm));
	tm = *localtime(&ud->editdate);
	strftime(editdate,sizeof(editdate),"%Y-%m-%d %H:%M:%S %Z", &tm);
	tm = *localtime(&ud->birthdate);
	strftime(birthdate,sizeof(birthdate),"%Y-%m-%d", &tm);
    sprintf(query,"INSERT INTO user_data (user_data_changed, weight, height, birthdate, sex, activity, vo2max, hrmax)VALUES ('%s',%f,%d,'%s',%d,%d,%d,%d);",editdate,ud->weight, ud->height, birthdate, ud->sex, ud->activity, ud->vo2max, ud->HRMax);

    res=PQexec(conn,query);
    if (PQresultStatus(res) != PGRES_COMMAND_OK){
        printf("db_insert_trn error: %s\n", PQerrorMessage(conn));
        PQclear(res);
    	free(query);
        return 1;
    }
    PQclear(res);
    free(query);
    return 0;  
}


int db_insert_trn(PGconn *conn, sTraining *trn){
    PGresult   *res;
    char *query=malloc(10000*sizeof(char));
	char time[255];
	struct tm tm;//=malloc(sizeof(struct tm));
	tm = *localtime(&trn->starttime);
	char *md5=malloc(10000*sizeof(char));
	strftime(time,sizeof(time),"%Y-%m-%d %H:%M:%S %Z", &tm);
	sprintf(md5,"%d S,%s,%d S,%d S,%d S,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d);",trn->duration, time, trn->timeinzone1, trn->timeinzone2, trn->timeinzone3, trn->z1hr[0],trn->z1hr[1],trn->z2hr[0],trn->z2hr[1],trn->z3hr[0],trn->z3hr[1],trn->avgHr,trn->maxHr, trn->HRMax, trn->calories, trn->fatprocent);
    sprintf(query,"INSERT INTO training (training_number, duration, start_time, time_in_zone_1, time_in_zone_2, time_in_zone_3, z1hrl, z1hrh, z2hrl, z2hrh, z3hrl, z3hrh, avg_hr, max_hr, hrmax, calories, fat_burn, training_data_md5 ) VALUES (%d,'%d S','%s','%d S','%d S','%d S',%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d, md5('%s'));",trn->id,trn->duration, time, trn->timeinzone1, trn->timeinzone2, trn->timeinzone3, trn->z1hr[0],trn->z1hr[1],trn->z2hr[0],trn->z2hr[1],trn->z3hr[0],trn->z3hr[1],trn->avgHr,trn->maxHr, trn->HRMax, trn->calories, trn->fatprocent, md5);
	free(md5);
    res=PQexec(conn,query);
    if (PQresultStatus(res) != PGRES_COMMAND_OK){
        printf("db_insert_trn error: %s\n", PQerrorMessage(conn));
        PQclear(res);
    	free(query);
        return 1;
    }
    PQclear(res);
    free(query);
    return 0;  
}

