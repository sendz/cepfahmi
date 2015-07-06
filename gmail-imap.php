<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
// Buka file konfigurasi
require("config.php");
/**
 *	Mawa gmail attachment.
 *
 *	Downloads attachments ti Gmail jeung saves kana file.
 *	Make PHP IMAP ekstensi, tong poho enable keun ektensina dina php.ini,
 *	extension=php_imap.dll
 *
 */

// Set time limit eksekusi selama 30 detik, kalau gak ada respon selama 30 detik, eksekusi dibatalkan
set_time_limit(3000);

// Koneksi ke IMAP Server
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* get all new emails. If set to 'ALL' instead
 * of 'NEW' retrieves all the emails, but can be
 * resource intensive, so the following variable,
 * $max_emails, puts the limit on the number of emails downloaded.
 *
 */

// Mencari email unread dengan judul print
$emails = imap_search($inbox,'UNSEEN SUBJECT "print"');

/* useful only if the above search is set to 'ALL' */
// Maksimum email 10
$max_emails = 10;


/* if any emails found, iterate through each email */
if ($emails) {

    $count = 1;

    /* put the newest emails on top */
    rsort($emails);

    /* for every email... */
    foreach($emails as $email_number)
    {

        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox,$email_number,0);

        /* get mail message */
        $message = imap_fetchbody($inbox,$email_number,2);

        /* get mail structure */
        $structure = imap_fetchstructure($inbox, $email_number);

        $attachments = array();


        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts))
        {
            for($i = 0; $i < count($structure->parts); $i++)
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if($structure->parts[$i]->ifdparameters)
                {
                    foreach($structure->parts[$i]->dparameters as $object)
                    {
                        if(strtolower($object->attribute) == 'filename')
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if($structure->parts[$i]->ifparameters)
                {
                    foreach($structure->parts[$i]->parameters as $object)
                    {
                        if(strtolower($object->attribute) == 'name')
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if($attachments[$i]['is_attachment'])
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                    /* 4 = QUOTED-PRINTABLE encoding */
                    if($structure->parts[$i]->encoding == 3)
                    {
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 3 = BASE64 encoding */
                    elseif($structure->parts[$i]->encoding == 4)
                    {
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }

        /* iterate through each attachment and save it */
        foreach($attachments as $attachment)
        {
            if($attachment['is_attachment'] == 1)
            {
                $filename = $attachment['name'];
                if(empty($filename)) $filename = $attachment['filename'];

                if(empty($filename)) $filename = time() . ".dat";

                /* prefix the email number to the filename in case two emails
                 * have the attachment with the same file name.
                 */
                $fp = fopen("./file/".$email_number . "-" . $filename, "w+");
                fwrite($fp, $attachment['attachment']);
                fclose($fp);
            }

        }

        if($count++ >= $max_emails) break;


		// Print


		foreach ($overview as $overview)
		{

      $subject = $overview->subject;

      // Memisahkan filter dengan pipeline (|)
      list($filter,$printnumber,$mailtitle) = explode("|",$subject);

      // Judul yang akan disimpan menggunakan array ke-3 dari hasil filter
			$saveMailTitle = $mailtitle;
      echo $saveMailTitle;
			$saveMailFrom = "From: ".$overview->from."\n";
			$saveMailTo = "To: ".$overview->to."\n";
			$saveMailDate = "Date: ".$overview->date."\n";
        #$saveMailBody = $message;
        if($attachment['name'])
        {
          $saveMailBody = "attachment printed on separated paper";
        }
        else{
          $saveMailBody = quoted_printable_decode(imap_utf8($message));
        }
      /*
      $saveHTML = fopen("./file/".$overview->subject.".html","w+");
      fwrite($saveHTML, $saveMailTitle.$saveMailFrom.$saveMailTo.$saveMailDate.$saveMailBody);
      fclose($saveHTML);*/


      if($printnumber=="print1")
      {
        if($attachments)
        {
          foreach($attachments as $attachment)
          {
            if($attachment['name'])
            {

            #$saveMailBody = $email_number."-".$attachment['name'];
            $tmp = "./file/".$email_number."-".$attachment['name'];
            $dest = "./printer1/".$email_number."-".$attachment['name'];
            copy($tmp,$dest);
            #echo "<hr />";
            passthru('lp ./printer1/'.$email_number."-".$attachment['name']);
            }
          }
        }
        #elseif(!$attachments)
        #{
          $saveHTML = fopen("./printer1/".$mailtitle.".txt","w+");
          fwrite($saveHTML, $saveMailTitle.$saveMailFrom.$saveMailTo.$saveMailDate.$saveMailBody);
          fclose($saveHTML);
          passthru('lp ./printer1/'.$mailtitle.'.txt');
        #}
        #passthru('rm -f ./printer1/*');
      }
      elseif($printnumber=="print2")
      {
        if($attachments)
        {
          foreach($attachments as $attachment)
          {
            if($attachment['name'])
            {

            #$saveMailBody = $email_number."-".$attachment['name'];
            $tmp = "./file/".$email_number."-".$attachment['name'];
            $dest = "./printer2/".$email_number."-".$attachment['name'];
            copy($tmp,$dest);
            #echo "<hr />";
            passthru('lp ./printer2/'.$email_number."-".$attachment['name']);
            }
          }
        }
        #elseif(!$attachments)
        #{
          $saveHTML = fopen("./printer2/".$mailtitle.".txt","w+");
          fwrite($saveHTML, $saveMailTitle.$saveMailFrom.$saveMailTo.$saveMailDate.$saveMailBody);
          fclose($saveHTML);
          passthru('lp ./printer2/'.$mailtitle.'.txt');
        #}
        #passthru('rm -f ./printer2/*');
      }
    }
  }

}

/* close the connection */
imap_close($inbox);
?>
