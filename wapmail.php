<? 
/***************************************************/
Andrise Wap Mailer.
andris@caritos.ee
CopyLeft 2002(L)

See programm pole müügiks, vaid tasuta jagamiseks.
Päis peab jääma muutmata.

Programmed by andris@caritos.ee
Portions from php.net (IMAP TEXT/HTML Selector)
Not for sale. Freeware. Header must remain as is.
/***************************************************/

// igasugu tekstid, muuda ära kui tahad.
$tmp_back = "<a href=\"$PHP_SELF?list=1\">Tagasi</a><br/>\n";
$tmp_tomain = "<a href=\"$PHP_SELF?main=1\">Pealehele</a><br/>\n";
$tmp_maintitle = "AWap";
$tmp_toinbox = "postkasti";
$tmp_subject = "Teema";
$tmp_from = "Kellelt";
$tmp_date = "Aeg";
$tmp_to = "Kellele";
$tmp_body = "Kiri";
$tmp_loginerror = "ei saa sisse.\n$tmp_back";
$tmp_nosubject = "[nil subjectos]";
$tmp_user = "Kasutaja";
$tmp_pass = "Parool";
$tmp_server = "Server";
$tmp_login = "Logi sisse";
$tmp_inbox = "Postkast";
$tmp_logout = "Logi välja";
$tmp_mainpage = "Pealeht";
$curtitle = $tmp_maintitle;
$tmp_vaata = "Loe";
$tmp_compose = "Uus";
$tmp_saada = "Saada";
$tmp_edasi = "Edasi";
$tmp_tagasi = "Tagasi";
$tmp_kokku = "Kokku";
// enam tekste pole.


$target = $PHP_SELF;

unset($session_username);
unset($session_password);
unset($session_server);
session_start();
session_register("session_username");
session_register("session_password");
session_register("session_server");

if ($session_username && $to && $subject && $body)
{
    $myemail = "$session_username@$session_server"; 
    $contactemail = $to;

    $message = $body; 
    if ($subject=="")$subject = $tmp_nosubject;

    $headers .= "MIME-Version: 1.0\r\n"; 
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
    $headers .= "From: ".$myemail."\r\n"; 
    $headers .= "To: ".$contactemail."\r\n"; 
    $headers .= "X-Priority: 1\r\n"; 
    $headers .= "X-Mailer: $tmp_maintitle"; 

    mail($contactemail, $subject, $message, $headers); 

    header("content-type:text/vnd.wap.wml"); 
    header("Location: $PHP_SELF?list=1");
    exit();
}


header("content-type:text/vnd.wap.wml"); 
?>
<? print '<?xml version="1.0"?>'; ?> 
<? print '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN"'; ?> 
<? print '"http://www.wapforum.org/DTD/wml_1.1.xml">'; ?> 

<?
if (!$folder) $folder="INBOX";

if (isset($logout))
{
    $session_username = "";
    $session_password = "";
    $session_server = "";
    session_destroy();
}

if($server && $user && $server)
{
    $session_server = $server;
    $session_username = $user;
    $session_password = $pass;
}

$mbox=@imap_open("{".$session_server.":110/pop3}".urldecode($folder), $session_username, $session_password);

if (!$mbox)
{
    global $tmp_maintitle, $tmp_login;
    $curtitle = "$tmp_maintitle: $tmp_login";
    wapheader();
    echo "$tmp_login<br/>\n";
    echo "$tmp_user:<input type=\"text\" name=\"user\" />\n";
    echo "$tmp_pass:<input type=\"password\" name=\"pass\" />\n";
    echo "$tmp_server:<input type=\"text\" name=\"server\" />\n";

    echo "<do type=\"accept\" label=\"$tmp_login\">";
    echo "<go href=\"$PHP_SELF\" method=\"post\">\n"; 

    echo '<postfield name="user" value="$user"/>';  
    echo '<postfield name="pass" value="$pass"/>';  
    echo '<postfield name="server" value="$server"/>';  
    echo "</go>";
    echo "</do>";

    goaway();
}

$main = 1;
if(isset($showmessage))
    {showmessage($showmessage);$main=0;}
if(isset($compose))
    {compose($compose);$main=0;}
if(isset($list))
    {messagelist();$main=0;}
if($main==1)
    {pealeht();}
goaway();

function pealeht()
{
    global $tmp_inbox, $tmp_logout, $tmp_maintitle, $tmp_mainpage, $curtitle, $tmp_compose;
    $curtitle = "$tmp_maintitle: $tmp_mainpage";
    wapheader();
    echo "<a href=\"$PHP_SELF?list=1\">$tmp_inbox</a><br/>\n";
    echo "<a href=\"$PHP_SELF?compose=aadress\">$tmp_compose</a><br/>\n";
    echo "<a href=\"$PHP_SELF?logout=1\">$tmp_logout</a><br/>\n";
}


function messagelist()
{
    global $mbox, $tmp_nosubject, $tmp_tomain, $tmp_maintitle, $tmp_inbox, $curtitle, $tmp_edasi, $tmp_tagasi, $tmp_kokku;
    $curtitle = "$tmp_maintitle: $tmp_inbox";
    wapheader();
    $mcheck = imap_check($mbox);
    $mnum = $mcheck->Nmsgs;
    $overview = imap_fetch_overview($mbox, "1:$mnum", 0);
    $s=sizeof($overview);
    $count = 0;
    echo "$tmp_tomain";
    for($i=$s-1; $i >= 0; $i--)
    {
        $count++;
        $val=$overview[$i];
        $rawsubject = imap_mime_header_decode($val->subject);
        $subject = $rawsubject[0]->text;
        if ($subject=='') {$subject = "$tmp_nosubject";}
        if (strlen($subject)>74) $subject = substr($subject,0,74)."...";
        if ($count>=$GLOBALS["list"] && $count<$GLOBALS["list"]+10)
            echo "$count) <a href=\"$PHP_SELF?showmessage=".($i+1)."\">$subject</a><br/>\n";
    }
    if ($GLOBALS["list"]>1)
        $tagasi = "<a href=\"$target?list=".($GLOBALS["list"]-10)."\">$tmp_tagasi</a>";
    else
        $tagasi = "$tmp_tagasi";
    if ($GLOBALS["list"]+10<$count)
        $edasi = "<a href=\"$target?list=".($GLOBALS["list"]+10)."\">$tmp_edasi</a>";
    else
        $edasi = "$tmp_edasi";
    echo "$tagasi $edasi &nbsp;$tmp_kokku: $count<br/>\n";
}


function showmessage($num)
{
    global $mbox, $tmp_subject, $tmp_from, $tmp_date, $tmp_back, $tmp_maintitle, $tmp_vaata, $curtitle;
    $curtitle = "$tmp_maintitle: $tmp_vaata";
    wapheader();

    imap_setflag_full($mbox,$num,'\\SEEN');
 
    echo "$tmp_back";
 
    $h=imap_headers($mbox);
    $info=imap_headerinfo($mbox,$num);
    $rawdsubject = imap_mime_header_decode($info->Subject);
    $subject = htmlspecialchars($rawdsubject[0]->text);

    $kellelt=$info->from[0];
    $nimiarray = imap_mime_header_decode($kellelt->personal);
    $nimi = $nimiarray[0]->text;
    if ($nimi!='') $from=$nimi." <a href=\"$PHP_SELF?compose=".$kellelt->mailbox ."@".$kellelt->host."\">".$kellelt->mailbox ."@".$kellelt->host."</a>";
		else $from= "<a href=\"$PHP_SELF?compose=".$kellelt->mailbox ."@".$kellelt->host."\">".$kellelt->mailbox ."@".$kellelt->host."</a>";
    echo "$tmp_from: $from<br/>\n";

    $dateNumber=strtotime($val->date);
    $date = date('d-m-y H:i',$info->udate);
    echo "$tmp_date: $date<br/>\n";

    echo "$tmp_subject: $subject<br/>\n";
   
    $body = get_part($mbox, $num, "TEXT/PLAIN"); 
    if($body=="")
        $body = get_part($mbox, $num, "TEXT/HTML"); 
    $body = htmlspecialchars($body);
    $body = str_replace("<br>","<br/>",$body);
    $body = eregi_replace("([\._a-z0-9-]+)@([\._a-z0-9-]+)","<a href=\"$send_url&amp;newto=\\1@\\2\">\\1@\\2</a>",$body);
    if (strlen($body)>1000) $body = substr($body,0,1000)."...";
    echo $body;
}

function compose($addr)
{
    global $tmp_maintitle, $tmp_compose, $curtitle, $tmp_back, $tmp_to, $tmp_body, $tmp_saada, $target, $tmp_subject;
    $curtitle = "$tmp_maintitle: $tmp_compose";
    wapheader();

    echo "$tmp_back";
    echo "$tmp_to:<input type=\"text\" name=\"to\" value=\"$addr\" />\n";
    echo "$tmp_subject:<input type=\"text\" name=\"subject\" /><br/>\n";
    echo "$tmp_body:<input type=\"text\" name=\"body\" />\n";

    echo "<do type=\"accept\" label=\"$tmp_saada\">";
    echo "<go href=\"$target\" method=\"post\">\n"; 
    echo '<postfield name="to" value="$to"/>';  
    echo '<postfield name="subject" value="$subject"/>';  
    echo '<postfield name="body" value="$body"/>';  
    echo "</go>";
    echo "</do>";
}

function wapheader()
{
    $tiitel = $GLOBALS["curtitle"];
    echo "<wml><card title=\"$tiitel\"><p>";
}

function goaway()
{
    echo "</p></card></wml>";
    exit();
}

// imap related functions from www.php.net
function get_mime_type(&$structure) 
{ 
$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"); 
if($structure->subtype) 
{ 
return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype; 
} 
return "TEXT/PLAIN"; 
} 

function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) 
{ 
if(!$structure) 
{ 
$structure = imap_fetchstructure($stream, $msg_number); 
} 
if($structure) 
{ 
if($mime_type == get_mime_type($structure)) 
 { 
if(!$part_number) 
  { 
  $part_number = "1"; 
  } 
 $text = imap_fetchbody($stream, $msg_number, $part_number); 
if($structure->encoding == 3) 
  { 
  return imap_base64($text); 
  } 
 else if($structure->encoding == 4) 
  { 
  return imap_qprint($text); 
  } 
 else 
  { 
  return $text; 
  } 
 } 
if($structure->type == 1) /* multipart */ 
 { 
 while(list($index, $sub_structure) = each($structure->parts)) 
  { 
  if($part_number) 
  { 
   $prefix = $part_number . '.'; 
   } 
  $data = get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1)); 
  if($data) 
   { 
   return $data; 
  } 
  } 
 } 
} 
return false; 
} 
