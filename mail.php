<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);

  $mailer = imap_open("{imap.gmail.com:993/imap/ssl}INBOX","fahemimran@gmail.com","raspberrypi");

  $mailCount = imap_num_msg($mailer);

	echo "You have ".$mailCount." mail <br />";
	echo "Mail Body";
	echo "<hr />";

  for ($mailID = 1; $mailID <= $mailCount; $mailID++)
  {
    $mailHeader = imap_headerinfo($mailer,$mailID);
    $mailBody   = imap_fetchbody($mailer, $mailID, 1);
	$mailPrint	= imap_search($mailer,'SUBJECT "[print]"');
    if($mailPrint)
    {
      foreach($mailPrint as $mail)
		  {
		  echo print_r($mail);
	  }
    }

    //echo $mailHeader->subject;
  }
