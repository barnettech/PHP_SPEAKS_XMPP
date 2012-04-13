<?php
/**
 * @file
 * basic api for interracting with a bosh server and xmpp.
 *
 * Written by James Barnett 2012, Architect Babson College. 
 * This code will be part of the JABBER module
 * on drupal.org shortly and the code is usable under GNU General Public License
 * just like Drupal.  http://drupal.org/project/jabber
 *
 */

global $_jabber_rid_;
$_jabber_rid_ = rand() * 10000;
global $_jabber_sid_;

/**
 * Increments the rid by one to send the next payload of xml. If you don't do
 * this it wont be accepted by the server.
 */
function jabber_get_next_rid() {
  global $_jabber_rid_;
  $_jabber_rid_ = $_jabber_rid_ + 1;
  return $_jabber_rid_;
}

/**
 * Sends xml to the BOSH server.
 */
function jabber_send_xml ($xmlposts) {
  global $_jabber_rid_;
  global $_jabber_sid_;
  // Replace below test server with your own bosh server.
  // This code is to get an rid and sid to attach() using the Strophe library.
  // The below server is provided by the author of Professional XMPP for
  // testing, and I highly recommend his book!
  $bosh_url = 'http://bosh.metajack.im:5280/xmpp-httpbind';
  $xml_repsonse = array();
  $count = 0;
  foreach ($xmlposts as $xmlpost) {
    $count = $count + 1;
    $_jabber_rid_ = $_jabber_rid_ + 1;
    $ch = curl_init($bosh_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlpost);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $header = array('Accept-Encoding: gzip, deflate','Content-Type: text/xml; charset=utf-8');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    // Stops the dump to the screen and lets you capture it in a variable.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlpost);
    echo $xmlpost;
    echo '

      ';
    $response = curl_exec($ch);
    var_dump($response);
    echo '

      ';
    $xml_response[] = simplexml_load_string($response);
  }
  curl_close($ch);
  return $xml_response;
}

/**
 * Connects to BOSH and establishes a sid and rid for later use.
 */
function jabber_get_rid_sid() {
  global $_jabber_rid_;
  $_jabber_rid_ = rand() * 10000;
  global $_jabber_sid_;

  $xmlposts = array();
  $xmlposts[] = "<body rid='$_jabber_rid_' xmlns='http://jabber.org/protocol/httpbind' to='babson.edu' xml:lang='en' wait='60' hold='1' window='5' content='text/xml; charset=utf-8' ver='1.6' xmpp:version='1.0' xmlns:xmpp='urn:xmpp:xbosh'/>";
  $xml_response = jabber_send_xml($xmlposts);
  $_jabber_sid_ = $xml_response[0]['sid'];

  $xmlposts = array();
  $jid = 'youremail.gmail.com';
  $username = 'youremail';
  $domain = 'gmail.com';
  $password = 'password';
  // in strophe its jbarnett@babson.edu\u0000jbarnett\u0000password
  $thepw = base64_encode($jid . chr(0) . $username . chr(0) . $password);
  echo '

    thepw is ' . $thepw . '

    ';
  $xmlposts[] = '<body rid="'.$_jabber_rid_.'" xmlns="http://jabber.org/protocol/httpbind" sid="'.$_jabber_sid_.'"><auth xmlns="urn:ietf:params:xml:ns:xmpp-sasl" mechanism="PLAIN">'.$thepw.'</auth></body>';
  $xmlposts[] = "<body rid='" . jabber_get_next_rid() . "' xmlns='http://jabber.org/protocol/httpbind' sid='$_jabber_sid_' to='babson.edu' xml:lang='en' xmpp:restart='true' xmlns:xmpp='urn:xmpp:xbosh'/>";
  $xmlposts[] = "<body rid='" . jabber_get_next_rid() . "' xmlns='http://jabber.org/protocol/httpbind' sid='$_jabber_sid_'><iq type='set' id='_bind_auth_2' xmlns='jabber:client'><bind xmlns='urn:ietf:params:xml:ns:xmpp-bind'/></iq></body>";
  $xmlposts[] = "<body rid='" . jabber_get_next_rid() . "' xmlns='http://jabber.org/protocol/httpbind' sid='$_jabber_sid_'><iq type='set' id='_session_auth_2' xmlns='jabber:client'><session xmlns='urn:ietf:params:xml:ns:xmpp-session'/></iq></body>";
  $xmlposts[] = "<body rid='" . jabber_get_next_rid() . "' xmlns='http://jabber.org/protocol/httpbind' sid='$_jabber_sid_'><message to='barnettech@gmail.com' type='chat' xmlns='jabber:client'><body>test helloworld from my php xmpp api!</body><active xmlns='http://jabber.org/protocol/chatstates'/></message></body>";

  $xml_response = jabber_send_xml($xmlposts);
}

jabber_get_rid_sid();

?>
