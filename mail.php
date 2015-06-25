<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);

  $mailer = imap_open("{imap.gmail.com:993/imap/ssl}INBOX","sendy@lendabook.co","karynkireina");

  $mailCount = imap_num_msg($mailer);

  for ($mailID = 1; $mailID <= $mailCount; $mailID++)
  {
    $mailHeader = imap_headerinfo($mailer,$mailID);
    $mailBody   = imap_fetchbody($mailer, $mailID, 1);

    /*
    if($mailHeader->Unseen = 'U')
    {
      echo "<div>".$mailBody."</div>";
    }
    */
    //echo $mailHeader->subject;
  }
    $mailUnread = imap_search($mailer,'UNSEEN');
    echo count($mailUnread);
