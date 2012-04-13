WRITTEN BY:  James Barnett 2012
jbarnett@barnettech.com
http://www.barnettech.com

SCRIPTS PURPOSE:
A simple php script illustrating how to connect
to a BOSH server to send stanzas, payloads, etc. to
communicate with xmpp servers
  -- specifically I wrote this script to just get an
     rid and sid to then attach() to Strophe the 
     XMPP javascript library as described in the book
     PROFESSIONAL XMPP
  -- replace the jid, username, and passwords in the code
     with your own, it is hardcoded for now -- sorry.
  -- replace the test bosh server with your own
  -- right now it sends a test message as well.
  -- modify the code to fit your needs

USAGE:
  -- after changing the jid, password, and username, etc as described
     above just run this script from the command line ie:  > php bosh.php
