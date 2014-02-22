import pika

import simplejson
from time import strftime
import time
import threading
import urllib

import elasticsearch

from access import Access

from unpdfer import Unpdfer

#from models import *
#from monroeminutes_docprocessor import DocProcessor

class Archiver(object):

    def __init__(self,
                 address='localhost',
                 exchange='barkingowl',
                 downloaddir="./downloads",
                 DEBUG=False):

        self.exchange = exchange
        self.downloaddir = downloaddir
        self.DEBUG = DEBUG        
    
        self.access = Access()

        #setup message bus
        self.reqcon = pika.BlockingConnection(pika.ConnectionParameters(host=address))
        self.reqchan = self.reqcon.channel()
        self.reqchan.exchange_declare(exchange=self.exchange,type='fanout')
        result = self.reqchan.queue_declare(exclusive=True)
        queue_name = result.method.queue
        self.reqchan.queue_bind(exchange=self.exchange,queue=queue_name)
        self.reqchan.basic_consume(self.reqcallback,queue=queue_name,no_ack=True)

    def start(self):
        if self.DEBUG:
            print "Listening for new documents ..."
        self.reqchan.start_consuming()

    def reqcallback(self,ch,method,properties,body):
        response = simplejson.loads(body)

        if self.DEBUG:
            print "Archiver: Message Recieved ('{0}')".format(response['command'])

        # This means that a pdf document has been found, we need to put it in the database
        if response['command'] == 'found_doc':
            
            if self.DEBUG:
                print "Processing new document ..."

            # pull out our important information
            docurl = response['message']['docurl']
            linktext = response['message']['linktext']
            scrapedatetime = response['message']['scrapedatetime']
            urldata = response['message']['urldata']

            if self.DEBUG:
                print "Document meta data decoded ..."

            # download the document
            filename,datetime,success = self.download(docurl)
            if not success:
                if self.DEBUG:
                    print "Unable to download PDF."
                return

            # save doc to the database
            self.access.adddoc(docurl,linktext,filename,scrapedatetime,urldata)

            if self.DEBUG:
                print "New document added to the database."

        elif response['command'] == 'global_shutdown':
            if self.DEBUG:
                print "Global Shutdown Command Seen"
            raise Exception("Archiver Exiting.")

    def download(self,docurl):
        success = True
        #try:
        if True: 
            urlfile = docurl[docurl.rfind("/")+1:]
            t = time.time() 
            _filename = "{0}/{1}_{2}.download".format(self.downloaddir,urlfile,t)
            while self._fileexists(_filename):
                _filename = "{0}/{1}_{2}.download".format(dest,urlfile,t)
            filename,_headers = urllib.urlretrieve(docurl,_filename)
            if self.DEBUG:
                print "Download Successful: '{0}'".format(_filename)

        #except:
        #    filename = ""
        #    success = False
        isodatetime = str(strftime("%Y-%m-%d %H:%M:%S"))
        return (filename,isodatetime,success)

    def _fileexists(self,filename):
        exists = False
        if filename == None:
            exists = False
        else:
            try:
                with open(filename):
                    exists = True
                    pass
            except:
                exists = False
        return exists

if __name__ == '__main__':

    print "Archiver Starting ..."

    downloaddir="/home/administrator/dev/monroeminutes/downloads/"

    DEBUG = True
    standalone = False

    archiver = Archiver(address='localhost',exchange='monroeminutes',downloaddir=downloaddir,DEBUG=DEBUG)
    #try:
    if True:
        archiver.start()
    #except:
    #    if DEBUG:
    #        print "Archiver Exited."

