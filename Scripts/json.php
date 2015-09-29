<?php

$canal = $_REQUEST['channel'];
//Options
$token    = 'YOUR_TOKEN_HERE';
$domain   = 'polerasrojas';
$channel  = '#general';
$bot_name = 'Webhook';
$icon     = ':alien:';
$message  = 'Your message';
$attachments = array([
    'fallback' => 'Lorem ipsum',
    'pretext'  => 'Lorem ipsum',
    'color'    => '#ff6600',
    'fields'   => array(
        [
            'title' => 'Title',
            'value' => 'Lorem ipsum',
            'short' => true
        ],
        [
            'title' => 'Notes',
            'value' => 'Lorem ipsum',
            'short' => true
        ]
    )
]);
$data = array(
    'channel'     => $canal,//$channel,
    'username'    => $bot_name,
    'text'        => $message,
    'icon_emoji'  => $icon,
    'attachments' => $attachments
);
$data_string = json_encode($data);
//$ch = curl_init('https://'.$domain.'.slack.com/services/hooks/incoming-webhook?token='.$token);
$ch=curl_init('https://hooks.slack.com/services/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');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
);
//Execute CURL
$result = curl_exec($ch);
return $result;
?>