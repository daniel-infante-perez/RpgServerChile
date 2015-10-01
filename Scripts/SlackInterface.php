<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 01-10-2015
 * Time: 7:56
 */
include('TiradorDados.php');

class WebhookOutgoingData
{
    public $token;
    public $team_id;
    public $team_domain;
    public $channel_id;
    public $channel_name;
    public $timestamp;
    public $user_id;
    public $user_name;
    public $text;
    public $trigger_word;
    public $frase_comando;

    const BOT_NAME = 'RpgServerChile';
    const ICON = ':dado:';
    const CMD_TIRO = '!roll';

    public function WebhookOutgoingData($request)
    {
        $this->token = $request['token'];
        $this->team_id = $request['team_id'];
        $this->team_domain = $request['team_domain'];
        $this->channel_id = $request['channel_id'];
        $this->channel_name = $request['channel_name'];
        $this->timestamp = $request['timestamp'];
        $this->user_id = $request['user_id'];
        $this->user_name = $request['user_name'];
        $this->text = $request['text'];
        $this->trigger_word = $request['trigger_word'];
        $this->frase_comando = trim(substr($this->text,strlen($this->trigger_word),strlen($this->text)-strlen($this->trigger_word)));

    }

    public function Responder($bot_name, $message, $icon, $attachments)
    {
        $data = array(
            'username'    => $bot_name,
            'text'        => $message,
            'icon_emoji'  => $icon,
            'attachments' => $attachments
        );
        return json_encode($data);
    }

    public function Actuar()
    {
        $respuesta = '';
        $attachments = null;
        $bot_name = self::BOT_NAME;
        $icon = self::ICON;

        if($this->trigger_word==self::CMD_TIRO)
        {
            $tirador = new TiradorDados();
            $respuesta = $tirador->TirarDados($this->frase_comando)->FormatoTexto();
        }
        else
        {
            $respuesta = 'Se ha producido un error: No se reconoce el comando ['.$this->trigger_word.']';
        }
        $attachments = array();/*[
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
    */
        return $this->Responder($bot_name,$respuesta,$icon,$attachments);
    }
}

$webHook = new WebhookOutgoingData($_REQUEST);
echo $webHook->Actuar();