<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UnauthorisedProfessional;
use App\Models\UapNotificationMails;
use App\Models\UapEmailFrequencies;

class UnauthorizedProfessionalMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unauthorized-professional-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $uap =  UnauthorisedProfessional::where('status','unauthorise')->where('mark_as_unauthorized',0)->get()->pluck('id')->toArray();

        $uapEmails = UapNotificationMails::whereIn('uap_id',$uap)->get();

       
        foreach($uapEmails as $value){
            $count_values = UapNotificationMails::where('uap_id',$value->uap_id)->where('next_mail_sequence',NULL)->count();
            
            if($count_values == 0){
                $send_next_mail =  UapEmailFrequencies::where('mail_sequence',$value->next_mail_sequence)->first();

                   $send_mail = UnauthorisedProfessional::where('id',$value->uap_id)->first();
                   if(!empty($send_next_mail)){
                        if(date('Y-m-d') == $value->next_mail_date){
                            // send email to admin for report uap
                            $mailData = ['content' => $send_next_mail->mail_content];
                            $view = \View::make('emails.mark_unauthorized_report', $mailData);
                            $message = $view->render();
                            
                            $parameter = [
                                'to' => decryptVal($send_mail->email),
                                'to_name' => decryptVal($send_mail->first_name).' '. decryptVal($send_mail->last_name),
                                'message' => $message,
                                'subject' => $send_next_mail->subject,
                                'view' => 'emails.mark_unauthorized_report',
                                'data' => $mailData,
                            ];
                        
                            sendMail($parameter);
            
                            $next_email = UapEmailFrequencies::where('mail_sequence', $send_next_mail->mail_sequence + 1)->first();
            
                            $next_email_date = NULL;
                            $next_mail_sequence = NULL;
                            if(!empty($next_email)){
                                $next_email_date = date('Y-m-d', strtotime(date('Y-m-d') . ' + '.$next_email->mail_to_send_on.' days'));
                                $next_mail_sequence = $next_email->mail_sequence;
                            }
                            // save email data to table
                            UapNotificationMails::create([ 
                                'unique_id' => randomNumber(),
                                'uap_id' => $value->uap_id,
                                'mail_sent_on'=>date('Y-m-d'),
                                'mail_content' => $send_next_mail->mail_content,
                                'next_mail_date' => $next_email_date,
                                'next_mail_sequence' => $next_mail_sequence,
                                'mail_status' => 'sent',
                            ]);
                        }
        
                   }
            }else{
               
            }

        }
    }
}
