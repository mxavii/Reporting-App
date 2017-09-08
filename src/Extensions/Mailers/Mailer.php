<?php

namespace App\Extensions\Mailers;


class Mailer
{
    public function send(array $data)
    {

		$transport = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
					->setUsername('reportingmit@gmail.com')
					->setPassword('masukaja123');

		$mailer = new \Swift_Mailer($transport);

		$message = (new \Swift_Message($data['subject']))
				  ->setFrom([ $data['from'] => $data['sender']])
				  ->setTo([$data['to'] => $data['receiver']])
				  ->setBody($data['content'], 'text/html')
				  ->addPart(strip_tags($data['content']), 'text/plain');

		$result = $mailer->send($message);
	}
}
