<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Sendemail_library
 * @property Offers_model $offers_model
 * @property Users_Model $users_model
 */
class Sendemail_library
{
    private $CI;

    function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('home_model');
        /*$this->CI->load->model('offers_model');
        $this->CI->load->model('users_model');
        $this->CI->load->model('login_model');
        $this->CI->load->model('mailers_model');*/
    }
    //Done
    public function signUpWelcomeSendMail($userData)
    {
        $data['mailData'] = $userData;
        $data['breakfastCode'] = $this->generateBreakfastCode($userData['mugId']);

        $content = $this->CI->load->view('emailtemplates/signUpWelcomeMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        if(isset($userData['fromEmail']) && isset($userData['fromPass']))
        {
            $fromEmail = $userData['fromEmail'];
            $fromPass = $userData['fromPass'];
            $replyTo = $userData['fromEmail'];
        }

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';
        if(isset($this->CI->userFirstName))
        {
            $fromName = ucfirst($this->CI->userFirstName);
        }
        $subject = 'Breakfast for Mug #'.$userData['mugId'];
        $toEmail = $userData['emailId'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Not in Use
    public function memberWelcomeMail($userData, $eventPlace)
    {
        $mailRecord = $this->CI->users_model->searchUserByLoc($eventPlace);

        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/memberWelcomeMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $mailRecord['userData']['emailId'];
        //$fromEmail = ;

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';
        if(isset($mailRecord['userData']['firstName']))
        {
            $fromName = $mailRecord['userData']['firstName'];
        }

        $subject = 'Welcome to Doolally';
        $toEmail = $userData['creatorEmail'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Not in Use
    public function eventVerifyMail($userData)
    {
        $mailRecord = $this->CI->users_model->searchUserByLoc($userData[0]['eventPlace']);
        $senderUser = 'U-0';

        if($mailRecord['status'] === true)
        {
            $senderUser = 'U-'.$mailRecord['userData']['userId'];
        }
        $userData['senderUser'] = $senderUser;

        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/eventVerifyMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        //$fromEmail = 'events@doolally.in';

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';

        $subject = 'Event Details';
        $toEmail = 'events@doolally.in';

        if($mailRecord['status'] === true)
        {
            $toEmail = $mailRecord['userData']['emailId'];
        }

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Not in Use
    public function eventCancelMail($userData)
    {
        $mailRecord = $this->CI->users_model->searchUserByLoc($userData[0]['eventPlace']);

        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/eventCancelMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

       // $fromEmail = 'info@doolally.in';
        /*if(isset($userData[0]['creatorEmail']))
        {
            $fromEmail = $userData[0]['creatorEmail'];
        }*/
        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';

        $subject = 'Event Cancel';
        $toEmail = 'events@doolally.in';

        if($mailRecord['status'] === true)
        {
            $toEmail = $mailRecord['userData']['emailId'];
        }

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function eventCancelUserMail($userData)
    {
        $phons = $this->CI->config->item('phons');
        $mailRecord = $this->CI->users_model->searchUserByLoc($userData[0]['eventPlace']);
        if($mailRecord['status'] === true)
        {
            $senderName = $mailRecord['userData']['firstName'];
        }
        else
        {
            $senderName = 'Doolally';
        }
        $userData['senderName'] = $senderName;
        if(isset($phons[ucfirst($senderName)]))
        {
            $userData['senderPhone'] = $phons[ucfirst($senderName)];
        }
        else
        {
            $userData['senderPhone'] = '9999999999';
        }

        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/eventCancelUserMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        if(isset($userData['fromEmail']) && isset($userData['fromPass']))
        {
            $fromEmail = $userData['fromEmail'];
            $fromPass = $userData['fromPass'];
            $replyTo = $userData['fromEmail'];
        }

        /*if(isset($mailRecord['userData']['emailId']) && isStringSet($mailRecord['userData']['emailId']))
        {
            $replyTo = $mailRecord['userData']['emailId'];
        }*/
        /*if(isset($mailRecord['userData']['gmailPass']))
        {
            $fromPass = $mailRecord['userData']['gmailPass'];
        }*/
        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';
        if(isset($senderName) && isStringSet($senderName))
        {
            $fromName = ucfirst($senderName);
        }

        $subject = 'Event Cancel';
        $toEmail = $userData[0]['creatorEmail'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function attendeeCancelMail($userData)
    {
        $phons = $this->CI->config->item('phons');
        $mailRecord = $this->CI->users_model->searchUserByLoc($userData['eventPlace']);
        $senderName = 'Doolally';
        $senderEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $senderEmail;

        $senderPhone = $phons['Tresha'];

        if($mailRecord['status'] === true)
        {
            $senderName = $mailRecord['userData']['firstName'];
            $replyTo = $mailRecord['userData']['emailId'];
            //$senderEmail = $mailRecord['userData']['emailId'];
            if(isset($phons[ucfirst($senderName)]))
            {
                $senderPhone = $phons[ucfirst($senderName)];
            }
            else
            {
                $senderPhone = '9999999999';
            }
            //$senderPhone = $phons[$senderName];
            //$fromPass = $mailRecord['userData']['gmailPass'];
        }
        $userData['senderName'] = $senderName;
        $userData['senderEmail'] = $replyTo;
        $userData['senderPhone'] = $senderPhone;
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/attendeeCancelMailView', $data, true);

        $fromEmail = $senderEmail;

        if(isset($userData['fromEmail']) && isset($userData['fromPass']))
        {
            $fromEmail = $userData['fromEmail'];
            $fromPass = $userData['fromPass'];
            $replyTo = $userData['fromEmail'];
        }

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = $senderName;

        $subject = 'You have withdrawn from '.$userData['eventName'];
        $toEmail = $userData['emailId'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function eventApproveMail($userData)
    {
        $phons = $this->CI->config->item('phons');
        if(isset($phons[ucfirst($userData['senderName'])]))
        {
            $userData['senderPhone'] = $phons[ucfirst($userData['senderName'])];
        }
        else
        {
            $userData['senderPhone'] = '9999999999';
        }
        //$userData['senderPhone'] = $phons[ucfirst($userData['senderName'])];
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/eventApproveMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        if(isset($userData['fromEmail']) && isset($userData['fromPass']))
        {
            $fromEmail = $userData['fromEmail'];
            $fromPass = $userData['fromPass'];
            $replyTo = $userData['fromEmail'];
        }

        /*if(isset($userData['senderEmail']) && isStringSet($userData['senderEmail']))
        {
            $replyTo = $userData['senderEmail'];
            $userInfo = $this->CI->login_model->checkEmailSender($userData['senderEmail']);
            if(isset($userInfo) && myIsArray($userInfo))
            {
                $fromPass = $userInfo['gmailPass'];
                $fromEmail = $userData['senderEmail'];
            }
        }*/
        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';
        if(isset($userData['senderName']) && isStringSet($userData['senderName']))
        {
            $fromName = ucfirst($userData['senderName']);
        }

        $subject = 'Event Approved';
        $toEmail = $userData[0]['creatorEmail'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function eventDeclineMail($userData)
    {
        $phons = $this->CI->config->item('phons');
        if(isset($phons[ucfirst($userData['senderName'])]))
        {
            $userData['senderPhone'] = $phons[ucfirst($userData['senderName'])];
        }
        else
        {
            $userData['senderPhone'] = '';
        }
        //$userData['senderPhone'] = $phons[$userData['senderName']];
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/eventDeclineMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        if(isset($userData['fromEmail']) && isset($userData['fromPass']))
        {
            $fromEmail = $userData['fromEmail'];
            $fromPass = $userData['fromPass'];
            $replyTo = $userData['fromEmail'];
        }

        /*if(isset($userData['senderEmail']) && isStringSet($userData['senderEmail']))
        {
            $replyTo = $userData['senderEmail'];
            $userInfo = $this->CI->login_model->checkEmailSender($userData['senderEmail']);
            if(isset($userInfo) && myIsArray($userInfo))
            {
                $fromPass = $userInfo['gmailPass'];
                $fromEmail = $userData['senderEmail'];
            }
        }*/

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';
        if(isset($userData['senderName']) && isStringSet($userData['senderName']))
        {
            $fromName = $userData['senderName'];
        }

        $subject = 'Sorry, '.$userData[0]['eventName'].' has not been approved';
        $toEmail = $userData[0]['creatorEmail'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function newEventMail($userData)
    {
        $phons = $this->CI->config->item('phons');
        $mailRecord = $this->CI->users_model->searchUserByLoc($userData['eventPlace']);
        $senderName = 'Doolally';
        $senderEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $userData['senderEmail'];
        $senderPhone = $phons['Tresha'];

        if($mailRecord['status'] === true)
        {
            /*$userInfo = $this->CI->login_model->checkEmailSender($mailRecord['userData']['emailId']);
            if(isset($userInfo) && myIsArray($userInfo))
            {
                $fromPass = $userInfo['gmailPass'];
                $senderEmail = $userData['senderEmail'];
            }*/
            $senderName = $mailRecord['userData']['firstName'];
            //$senderEmail = $mailRecord['userData']['emailId'];
            if(isset($phons[ucfirst($senderName)]))
            {
                $senderPhone = $phons[ucfirst($senderName)];
            }
            else
            {
                $senderPhone = '9999999999';
            }
            //$senderPhone = $phons[$senderName];
        }
        $userData['senderName'] = $senderName;
        $userData['senderEmail'] = $replyTo;
        $userData['senderPhone'] = $senderPhone;
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/newEventMailView', $data, true);

        $fromEmail = $senderEmail;

        if(isset($userData['fromEmail']) && isset($userData['fromPass']))
        {
            $fromEmail = $userData['fromEmail'];
            $fromPass = $userData['fromPass'];
            $replyTo = $userData['fromEmail'];
        }

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = $senderName;

        $subject = 'Event Details';
        $toEmail = $userData['creatorEmail'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function membershipRenewSendMail($userData)
    {
        $userData['breakCode'] = $this->generateBreakfastTwoCode($userData['mugId']);
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/membershipRenewMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        if(isset($userData['fromEmail']) && isset($userData['fromPass']))
        {
            $fromEmail = $userData['fromEmail'];
            $fromPass = $userData['fromPass'];
            $replyTo = $userData['fromEmail'];
        }

        /*if(isset($this->CI->userEmail))
        {
            $replyTo = $this->CI->userEmail;
            $userInfo = $this->CI->login_model->checkEmailSender($this->CI->userEmail);
            if(isset($userInfo) && myIsArray($userInfo))
            {
                $fromPass = $userInfo['gmailPass'];
                $fromEmail = $this->CI->userEmail;
            }
        }*/
        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = 'Doolally';
        if(isset($this->CI->userFirstName))
        {
            $fromName = ucfirst($this->CI->userFirstName);
        }
        $subject = 'Mug #'.$userData['mugId'].' has been Renewed';
        $toEmail = $userData['emailId'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    public function generateBreakfastCode($mugId)
    {
        $allCodes = $this->CI->offers_model->getAllCodes();
        $usedCodes = array();
        $toBeInserted = array();
        if($allCodes['status'] === true)
        {
            foreach($allCodes['codes'] as $key => $row)
            {
                $usedCodes[] = $row['offerCode'];
            }
            $newCode = mt_rand(1000,99999);
            while(myInArray($newCode,$usedCodes))
            {
                $newCode = mt_rand(1000,99999);
            }
            $toBeInserted = array(
                'offerCode' => $newCode,
                'offerType' => 'Breakfast',
                'offerLoc' => null,
                'offerMug' => $mugId,
                'isRedeemed' => 0,
                'ifActive' => 1,
                'createDateTime' => date('Y-m-d H:i:s'),
                'useDateTime' => null
            );
        }
        else
        {
            $newCode = mt_rand(1000,99999);
            $toBeInserted = array(
                'offerCode' => $newCode,
                'offerType' => 'Breakfast',
                'offerLoc' => null,
                'offerMug' => $mugId,
                'isRedeemed' => 0,
                'ifActive' => 1,
                'createDateTime' => date('Y-m-d H:i:s'),
                'useDateTime' => null
            );
        }

        $this->CI->offers_model->setSingleCode($toBeInserted);
        return 'DO-'.$newCode;
    }

    public function otpSendMail($userData)
    {
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/otpMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;
        $cc = '';
        $fromName  = 'Doolally';

        $subject = 'Your Requested Otp '.$userData['otp'];
        $toEmail = $userData['emailId'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function mugEditSendMail($userData)
    {
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/mugEditMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;
        if(isset($userData['senderEmail']) && isStringSet($userData['senderEmail']))
        {
            $replyTo = $userData['senderEmail'];
            /*$userInfo = $this->CI->login_model->checkEmailSender($userData['senderEmail']);
            if(isset($userInfo) && myIsArray($userInfo))
            {
                $fromPass = $userInfo['gmailPass'];
                $fromEmail = $userData['senderEmail'];
            }*/
        }
        $cc = '';
        $fromName  = 'Doolally';
        if(isset($userData['senderName']) && isStringSet($userData['senderName']))
        {
            $fromName = ucfirst($userData['senderName']);
        }

        $subject = 'Mug Member Edited';
        $toEmail = 'tresha@brewcraftsindia.com';

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function checkinMissMail($userData)
    {
        $mailRecord = $this->CI->users_model->searchUserByLoc($userData['locId']);
        $senderName = 'Doolally';
        $senderEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $senderEmail;

        $toEmail = 'tresha@brewcraftsindia.com';
        if($mailRecord['status'] === true)
        {
            $toEmail = $mailRecord['userData']['emailId'];
        }
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/checkinMissInfoMailView', $data, true);

        $fromEmail = $senderEmail;

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = $senderName;

        $subject = 'Mug #'.$userData['mugId'].' has missing info';

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function checkinInfoFillMail($userData)
    {
        $mailRecord = $this->CI->users_model->searchUserByLoc($userData['locId']);
        $senderName = 'Doolally';
        $senderEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $senderEmail;

        $toEmail = 'tresha@brewcraftsindia.com';
        if($mailRecord['status'] === true)
        {
            $toEmail = $mailRecord['userData']['emailId'];
        }
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/checkinFilledMailView', $data, true);

        $fromEmail = $senderEmail;

        $cc        = implode(',',$this->CI->config->item('ccList'));
        $fromName  = $senderName;

        $subject = 'Mug #'.$userData['mugId'].' has missing info';

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    //Done
    public function instamojoFailMail($userData)
    {
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/mugEditMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        $cc = '';
        $fromName  = 'Doolally';

        $subject = 'Unknown Mug Renewed via Instamojo';
        $toEmail = 'tresha@brewcraftsindia.com';

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    public function teamBeerSendMail($userData)
    {
        $data['mailData'] = $userData;

        $startDate = str_replace(' ','T',date('Ymd His',strtotime('2017-05-20 11:00')));
        $endDate = str_replace(' ','T',date('Ymd His',strtotime('2017-05-20 19:00')));
        $data['calendar_url'] =
            'https://www.google.com/calendar/event?action=TEMPLATE'.
            '&text='.urlencode('Beer Olympics 2017').
            '&dates='.$startDate.'/'.$endDate.
            '&location='.urlencode('1st Brewhouse, Corinthians Resort and Club, NIBM Annexe, Mohmmadwadi, Pune').
            '&details='. urlencode('Doolally Beer Olympics 2017').
            '&sprop=&sprop=name:';

        $content = $this->CI->load->view('emailtemplates/teamBeerSignupMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = 'priyanka@brewcraftsindia.com';

        $cc = 'tresha@brewcraftsindia.com';
        if($userData['busCount'] > 0 )
        {
            $cc .= ',belinda@brewcraftsindia.com,saha@brewcraftsindia.com';
        }
        $fromName  = 'Doolally';

        $subject = 'We have registered Team '.$userData['teamName'].' to participate in the Doolally Beer Olympics 2017!';
        $toEmail = $userData['capEmail'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }


    public function teamBusSendMail($userData)
    {
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/teamBusSignupMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = 'belinda@brewcraftsindia.com';

        $cc = 'tresha@brewcraftsindia.com';
        if($userData['busSeats'] > 0 )
        {
            $cc .= ',priyanka@brewcraftsindia.com,saha@brewcraftsindia.com';
        }
        $fromName  = 'Doolally';

        $subject = 'You have booked '.$userData['busSeats'].' seats on the Doolally Wagon';
        $toEmail = $userData['busEmail'];

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    public function teamBeerDetailsSendMail($userData,$busCount)
    {
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/teamDetailsSignupMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        $cc = 'tresha@brewcraftsindia.com,saha@brewcraftsindia.com';
        if($busCount > 0 )
        {
            $cc .= ',belinda@brewcraftsindia.com';
        }
        $fromName  = 'Doolally';

        $subject = 'New Team Registration For Beer Olympics';
        $toEmail = 'priyanka@brewcraftsindia.com';

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    public function teamBusDetailsSendMail($userData)
    {
        $data['mailData'] = $userData;

        $content = $this->CI->load->view('emailtemplates/busDetailsSignupMailView', $data, true);

        $fromEmail = DEFAULT_SENDER_EMAIL;
        $fromPass = DEFAULT_SENDER_PASS;
        $replyTo = $fromEmail;

        $cc = 'tresha@brewcraftsindia.com,saha@brewcraftsindia.com,priyanka@brewcraftsindia.com';
        $fromName  = 'Doolally';

        $subject = 'New Bus Registration For Beer Olympics';
        $toEmail = 'belinda@brewcraftsindia.com';

        $this->sendEmail($toEmail, $cc, $fromEmail, $fromPass, $fromName,$replyTo, $subject, $content);
    }

    public function sendEmail($to, $cc = '', $from, $fromPass, $fromName,$replyTo, $subject, $content, $attachment = array())
    {
        $logDetails = array(
            'messageId' => null,
            'sendTo' => $to,
            'sendFrom' => $from,
            'sendFromName' => $fromName,
            'ccList' => $cc,
            'replyTo' => $replyTo,
            'mailSubject' => $subject,
            'mailBody' => $content,
            'attachments' => '',
            'sendStatus' => 'waiting',
            'failIds' => null,
            'sendDateTime' => null
        );

        $this->CI->home_model->saveWaitMailLog($logDetails);
        return true;
        //Create the Transport
        /*$CI =& get_instance();
        $CI->load->library('swift_mailer/swift_required.php');*/

        require_once APPPATH.'libraries/swift_mailer/swift_required.php';

        $transport = Swift_SmtpTransport::newInstance ('smtp.gmail.com', 465, 'ssl')
            ->setUsername($from)
            ->setPassword($fromPass);
        //$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

        $mailer = Swift_Mailer::newInstance($transport);

        //Create a message
        $message = Swift_Message::newInstance($subject)
            ->setSubject($subject)
            ->setReplyTo($replyTo)
            ->setReadReceiptTo($from)
            //->setCc($cc)
            ->setFrom(array($from => $fromName))
            ->setSender($replyTo)
            ->setTo($to) ->setBody($content, 'text/html');

        if($cc != '')
        {
            $message->setBcc(explode(',',$cc));
        }
        if(isset($attachment) && myIsMultiArray($attachment))
        {
            foreach($attachment as $key)
            {
                if($key != '')
                {
                    $message->attach(Swift_Attachment::fromPath($key));
                }
            }
        }
        //$message->attach($attachment);
        //Send the message
        $failedId = array();
        $status = 'Success';
        $errorMsg = implode(',',$failedId);

        try
        {
            $result = $mailer->send($message,$failedId);
            if(!$result)
            {
                $status = 'Failed';
                $errorMsg = implode(',',$failedId);
            }
        }
        catch(Swift_TransportException $st)
        {
            $status = 'Login Failed';
            $errorMsg = $st->getMessage();
        }
        catch(Exception $ex)
        {
            $status = 'Failed';
            $errorMsg = $ex->getMessage();
        }


        $logDetails = array(
            'messageId' => $message->getId(),
            'sendTo' => $to,
            'sendFrom' => $from,
            'sendFromName' => $fromName,
            'ccList' => $cc,
            'replyTo' => $replyTo,
            'mailSubject' => $subject,
            'mailBody' => $content,
            'attachments' => implode(',',$attachment),
            'sendStatus' => $status,
            'failIds' => $errorMsg,
            'sendDateTime' => date('Y-m-d H:i:s')
        );

        $this->CI->home_model->saveSwiftMailLog($logDetails);
        return $status;
        /*$CI =& get_instance();
        $CI->load->library('email');
        $config['mailtype'] = 'html';
        $CI->email->clear(true);
        $CI->email->initialize($config);
        $CI->email->from($from, $fromName);
        $CI->email->to($to);
        if ($cc != '') {
            $CI->email->bcc($cc);
        }
        if(isset($attachment) && myIsArray($attachment))
        {
            foreach($attachment as $key)
            {
                $CI->email->attach($key);
            }
        }

        $CI->email->subject($subject);
        $CI->email->message($content);
        return $CI->email->send();*/
    }

    public function sendWaitingEmail($to, $cc = '', $from, $fromPass, $fromName,$replyTo, $subject, $content, $attachment = array())
    {

        require_once APPPATH.'libraries/swift_mailer/swift_required.php';

        $transport = Swift_SmtpTransport::newInstance ('smtp.gmail.com', 465, 'ssl')
            ->setUsername($from)
            ->setPassword($fromPass);
        //$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

        $mailer = Swift_Mailer::newInstance($transport);

        //Create a message
        $message = Swift_Message::newInstance($subject)
            ->setSubject($subject)
            ->setReplyTo($replyTo)
            ->setReadReceiptTo($from)
            //->setCc($cc)
            ->setFrom(array($from => $fromName))
            ->setSender($replyTo)
            ->setTo($to) ->setBody($content, 'text/html');

        if($cc != '')
        {
            $message->setBcc(explode(',',$cc));
        }
        if(isset($attachment) && myIsMultiArray($attachment))
        {
            foreach($attachment as $key)
            {
                if($key != '')
                {
                    $message->attach(Swift_Attachment::fromPath($key));
                }
            }
        }
        //$message->attach($attachment);
        //Send the message
        $failedId = array();
        $status = 'Success';
        $errorMsg = implode(',',$failedId);

        try
        {
            $result = $mailer->send($message,$failedId);
            if(!$result)
            {
                $status = 'Failed';
                $errorMsg = implode(',',$failedId);
            }
        }
        catch(Swift_TransportException $st)
        {
            $status = 'Login Failed';
            $errorMsg = $st->getMessage();
        }
        catch(Exception $ex)
        {
            $status = 'Failed';
            $errorMsg = $ex->getMessage();
        }


        $logDetails = array(
            'messageId' => $message->getId(),
            'sendTo' => $to,
            'sendFrom' => $from,
            'sendFromName' => $fromName,
            'ccList' => $cc,
            'replyTo' => $replyTo,
            'mailSubject' => $subject,
            'mailBody' => $content,
            'attachments' => implode(',',$attachment),
            'sendStatus' => $status,
            'failIds' => $errorMsg,
            'sendDateTime' => date('Y-m-d H:i:s')
        );

        $this->CI->home_model->saveSwiftMailLog($logDetails);
        return $status;
    }

    public function generateBreakfastTwoCode($mugId)
    {
        $allCodes = $this->CI->offers_model->getAllCodes();
        $usedCodes = array();
        $toBeInserted = array();
        if($allCodes['status'] === true)
        {
            foreach($allCodes['codes'] as $key => $row)
            {
                $usedCodes[] = $row['offerCode'];
            }
            $newCode = mt_rand(1000,99999);
            while(myInArray($newCode,$usedCodes))
            {
                $newCode = mt_rand(1000,99999);
            }
            $toBeInserted = array(
                'offerCode' => $newCode,
                'offerType' => 'Breakfast2',
                'offerLoc' => null,
                'offerMug' => $mugId,
                'isRedeemed' => 0,
                'ifActive' => 1,
                'createDateTime' => date('Y-m-d H:i:s'),
                'useDateTime' => null
            );
        }
        else
        {
            $newCode = mt_rand(1000,99999);
            $toBeInserted = array(
                'offerCode' => $newCode,
                'offerType' => 'Breakfast2',
                'offerLoc' => null,
                'offerMug' => $mugId,
                'isRedeemed' => 0,
                'ifActive' => 1,
                'createDateTime' => date('Y-m-d H:i:s'),
                'useDateTime' => null
            );
        }

        $this->CI->offers_model->setSingleCode($toBeInserted);
        return 'BR-'.$newCode;
    }
}
/* End of file */