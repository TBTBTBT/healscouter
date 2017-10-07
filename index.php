<?php

require_once __DIR__ . '/vendor/autoload.php';

require('image.php');

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);
$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
foreach ($events as $event) {

  $profile = $bot->getProfile($event->getUserId())->getJSONDecodedBody();
  $displayName = $profile['displayName'];

  if ($event instanceof \LINE\LINEBot\Event\MessageEvent) {
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
      
      if($event->getText() === 'こんにちは') {
        $bot->replyMessage($event->getReplyToken(),
          (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
            ->add(new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 17))
            ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('こんにちは！' . $displayName . 'さん'))
        );
      } else {
        $bot->replyMessage($event->getReplyToken(),
          (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
            ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('「こんにちは」と呼びかけて下さいね！'))
            ->add(new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 4))
        );
      }
    }else if($event instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage){
      $response = $bot->getMessageContent($event->getMessageId());
      $rawBody = $response->getRawBody();
      $im = imagecreatefromstring($rawBody);
      $resultString = "";
      if ($im !== false) {
      $filename = uniqid();
      $directory_path = "tmp";
      if(!file_exists($directory_path)) {
        if(mkdir($directory_path, 0777, true)) {
            chmod($directory_path, 0777);
        }
      }
      imagejpeg($im, $directory_path . '/' . $filename . ".jpg", 75);
      } else {
        $resultString = "upload failed";
      }
      $rgb =  imageLoder("./tmp/test.jpg");
$r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;

      $pix =  $r.",".$g.",".$b;
      $bot->replyMessage($event->getReplyToken(),
          (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
            ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('saved at ' ."http://" . $_SERVER["HTTP_HOST"] . "/" . $directory_path . '/' . $filename . ".jpg" . "\n" . $pix))
            ->add(new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 4))
            );

      
    }
    continue;
  }
}

?>
