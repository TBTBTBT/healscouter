<?php

require_once __DIR__ . '/vendor/autoload.php';

require('image.php');
$result = ["超健康です！これはもしかしてCOMPですか？","健康です！","まあまあ健康です！","不健康です…！"];
$result2 = ["なんでもできますね！","今日も元気に頑張りましょう！","栄養には気をつけましょう","栄養が足りていませんね…COMPを食べてはどうですか？"];
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
        /*$bot->replyMessage($event->getReplyToken(),
          (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
            ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('「こんにちは」と呼びかけて下さいね！'))
            ->add(new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 4))
        );*/
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
      $rgywb =  imageLoder($directory_path . '/' . $filename . ".jpg");
      $sum = 0;
      foreach ($rgywb as $k) {
        $sum += $k;
        # code...
      }
      $sum = floor($sum / 5);
      
      $health = 0;
      foreach ($rgywb as $k) {
        $health += abs($k - $sum);
        # code...
      }

      $health2 = ((408 - $health) * 100 / 408);
      $anotherHealth = (($rgywb[0] + $rgywb[2]) / 2 )- $rgywb[1] ;
      $health2 -= $anotherHealth;
      $health2 = floor($health2);
      $resind = 0;
if($health2 < 85)$resind = 1;
      if($health2 < 60)$resind = 2;
      if($health2 < 35)$resind = 3;
      $bot->replyMessage($event->getReplyToken(),
          (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
            ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder(/*'saved at ' ."http://" . $_SERVER["HTTP_HOST"] . "/" . $directory_path . '/' . $filename . ".jpg" . */
              "この食べ物の健康度は・・・"."\n".$health2 ." / 100 !\n".$result[$resind]."\n今日のコード生産性は…".$health2 * 100."行！".$result2[$resind]))
            //->add(new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 4))
            );

      
    }
    continue;
  }
}

?>
