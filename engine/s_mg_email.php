<?php

namespace iriki;

//note  that this file is named with an s_ prefix so that it
//appears before request.php in autoload.php

class mg_email extends \iriki\request
{

	public static function send($request, $wrap = true)
    {

		if (isset($GLOBALS['APP']['constants']))
		{
			$constants = $GLOBALS['APP']['constants'];

			$domain = @$constants['mailgun_domain'];
			$secret_key = @$constants['mailgun_secret'];
			$params = $request->getData();

			$client = new \Http\Adapter\Guzzle6\Client(); 
	        $mgClient = new \Mailgun\Mailgun($secret_key, $client);

	        # Make the call to the client.
	        /*$result = $mgClient->sendMessage($domain,
	            array(
	                'from'    => 'Excited User <mailgun@YOUR_DOMAIN_NAME>',
	                'to'      => 'Baz <YOU@YOUR_DOMAIN_NAME>',
	                'subject' => 'Hello',
	                'text/html'    => 'Testing some Mailgun awesomness!',
	                'cc'      => 'baz@example.com',
	                'bcc'     => 'bar@example.com',
	            ),
	            array(
	                'attachment' => array('/path/to/file1.txt', '/path/to/file2.txt')
	            )
	        );*/


            $body_descriptor = 'text';
	        if ($params['use_html'] == 'true')
	        {
	            $body_descriptor = 'html';
	        }

	        $mail_options = array(
	            'from'    => $params['from_name'] . '<' . $params['from_email'] . '>',
	            'to'      => $params['to_name'] . '<' . $params['to_email'] . '>',
	            'subject' => $params['subject'],
	            $body_descriptor    => $params['body']
	        );

	        /*if ($cc != '')
	            $mail_options['cc'] = $cc;
	        if ($bcc != '')
	            $mail_options['bcc'] = $bcc;

	        if (is_null($attachments))
	        {
	            $result = $mgClient->sendMessage($domain,
	                $mail_options
	            );
	        }
	        else
	        {
	            $result = $mgClient->sendMessage($domain,
	                $mail_options,
	                array(
	                    'attachment' => $attachments
	                )
	            );
	        }*/


            $result = $mgClient->sendMessage($domain,
                $mail_options
            );


	        //interprete result
            $status = $result->http_response_code == 200;

            if ($status)
			{
				return \iriki\response::information('true', $wrap);
			}
			else {
				return \iriki\response::information('false', $wrap);
			}
	    }
    }

}

?>