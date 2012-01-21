
#ifndef _H_DATABASE
#define _H_DATABASE

#include<libpq-fe.h>
#include <libpq/libpq-fs.h>
#include "parse_data.h"

PGconn *connect_db(char *database_name);
void close_db(PGconn *conn);
int test_db_connection(char *database_name);
int db_insert_udata(PGconn *conn, sUserData *ud);
int db_insert_trn(PGconn *conn, sTraining *trn);
#endif
