import MySQLdb as mdb
import _mysql as mysql
import re

class documents:

    __settings = {}
    __con = False

    def __init__(self):
        configfile = "sqlcreds.txt"
        f = open(configfile)
        for line in f:
            # skip comment lines
            m = re.search('^\s*#', line)
            if m:
                continue

            # parse key=value lines
            m = re.search('^(\w+)\s*=\s*(\S.*)$', line)
            if m is None:
                continue

            self.__settings[m.group(1)] = m.group(2)
        f.close()

        # create connection
        self.__con = mdb.connect(host=self.__settings['host'], user=self.__settings['username'], passwd=self.__settings['password'], db=self.__settings['database'])

    def add(self,suborganizationid,organizationid,sourceurl,documentdate,scrapedate,name,dochash):
        with self.__con:
            cur = self.__con.cursor()
            cur.execute("INSERT INTO documents(suborganizationid,organizationid,sourceurl,documentdate,scrapedate,name,dochash) VALUES(%s,%s,%s,%s,%s,%s,%s)",(suborganizationid,organizationid,sourceurl,documentdate,scrapedate,name,dochash))
            cur.close()

    def get(self,documentid):
        with self.__con:
            cur = self.__con.cursor()
            cur.execute("SELECT * FROM documents WHERE documentid = %s",(documentid))
            row = cur.fetchone()
            cur.close()

    def getall(self):
        with self.__con:
            cur = self.__con.cursor()
            cur.execute("SELECT * FROM documents")
            rows = cur.fetchall()
            cur.close()

        _documents = []
        for row in rows:
            _documents.append(row)

        return _documents

    def delete(self,documentid):
        with self.__con:
            cur = self.__con.cursor()
            cur.execute("DELETE FROM documents WHERE documentid = %s",(documentid))
            cur.close()

    def update(self,documentid,suborganizationid,organizationid,sourceurl,documentdate,scrapedate,name,dochash):
        with self.__con:
            cur = self.__con.cursor()
            cur.execute("UPDATE documents SET suborganizationid = %s,organizationid = %s,sourceurl = %s,documentdate = %s,scrapedate = %s,name = %s,dochash = %s WHERE documentid = %s",(suborganizationid,organizationid,sourceurl,documentdate,scrapedate,name,dochash,documentid))
            cur.close()

##### applilication functions #####

    def urlexists(self,url):
        with self.__con:
            cur = self.__con.cursor()
            cur.execute("SELECT count(documentid) as count FROM documents WHERE sourceurl = %s",(url))
            row = cur.fetchone()
            cur.close()
        
        _exists = False
        if int(row['count'] != 0):
            _exists = True

        return _exists


    def hashexists(self,url):
        with self.__con:
            cur = self.__con.cursor()
            cur.execute("SELECT count(documentid) as count FROM documents WHERE dochash = %s",(url))
            row = cur.fetchone()
            cur.close()

        _exists = False
        if int(row['count'] != 0):
            _exists = True

        return _exists

