<?php

namespace App\Http\Controllers;

use App\Services\Operations\OperationsService;
use Illuminate\Http\Request;
use Cache;
use App\Models\User;
use App\Models\Category;

class TelegramController extends Controller
{
    private $operationsService;
    public function __construct(OperationsService $operationsService)
    {
        $this->operationsService = $operationsService;
    }

    public function webhook(Request $request)
    {
        $keyboard = [['list'],['stat'],['search']]; //Клавиатура
        $content = trim($request->getContent());
        if ($content) {
            $data = json_decode($content, true);
            \Log::channel('info')->debug($content);
            $telegramId = (int)$data['message']['chat']['id'];

            if (isset($data['message']['text'])
                && preg_match('/\/start\s([A-Za-z0-9]+)$/', $data['message']['text'], $hashMatch)
            ) {
                $contacts = User::whereRaw('md5(id) = ?', [$hashMatch[1]])->first();
                $contactsId = $contacts->id ?? '';
                //\Log::channel('info')->debug("Contact by hash '{$hashMatch[1]}': {$contactsId}");
                if ($contactsId) {
                    $user = User::find($contactsId);
                    $user->telegram_chat_id = $telegramId;
                    $user->save();

                    $text = 'Вы успешно подключили свой телеграм-аккаунт к moneyinout.info!';
                    $this->sendMessage($telegramId, $text, [ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
                }
            } elseif(isset($data['message']['text'])) {
                $text = $data['message']['text'];
                $userId = User::where('telegram_chat_id', $telegramId)->first()->id ?? 0;
                \Log::channel('info')->debug("{$userId}: {$text}");
                if ($userId) {
                    if ($text == 'help') {
                        $this->sendMessage($telegramId, '', [ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
                    }
                    elseif ($text == 'list') {
                        $items = $this->operationsService->search([], $userId)->items();
                        //\Log::channel('info')->debug(json_encode($items));

                        $result = '';
                        foreach ($items as $row) {
                            $result .= "{$row['search']}\n";
                        }
                        $this->sendMessage($telegramId, $result);
                    }
                    elseif ($text == 'stat') {
                        $items = $this->operationsService->stat([], $userId, 'm', 'stat');
                        $result = '';
                        foreach ($items as $row) {
                            $date = date('m.Y', strtotime($row['group'] . '-01'));

                            $result .= "<b>{$date}</b> +{$row['income']} -{$row['outcome']} = {$row['total']}\n";
                        }
                        $this->sendMessage($telegramId, $result);
                    }

                    elseif (preg_match('/^(-*\d+)\s+(.+)$/', $text, $tmpArr)) {
                        $date = date('Y-m-d');
                        $summ = $tmpArr[1];
                        $comment = $tmpArr[2];
                        $this->addOperation($date, $summ, $comment);
                    }
                    elseif (preg_match('/^(\d{2}.\d{2}.\d{4})\s+(-*\d+)\s+(.+)$/', $text, $tmpArr)) {
                        $date = date('Y-m-d', strtotime($tmpArr[1]));
                        $summ = $tmpArr[2];
                        $comment = $tmpArr[3];
                        $this->addOperation($date, $summ, $comment);
                    }
                    elseif (preg_match('/^(s|search)\s+(.+)$/', $text, $tmpArr)) {
                        $items = $this->operationsService->search(['searchString' => $tmpArr[2]], $userId)->items();
                        \Log::channel('info')->debug(json_encode($items));
                        $result = '';
                        foreach ($items as $row) {
                            $result .= "{$row['search']}\n";
                        }
                        $this->sendMessage($telegramId, $result);
                    }
                    else {
                        $this->sendMessage(
                            $telegramId,
                            'Неизвестная команда! Используйте следующие команды:',
                            [ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]
                        );
                    }
                }
            }
        }
    }

    private function addOperation($date, $summ, $comment)
    {
        $cat = Category::where('name', $comment)->first();
        if (!empty($cat->id)) {
            $category_id = $cat->id;
            $category_name = $comment;
            $comment = '';
        }
        if (preg_match('/(.+):(.+)/', $comment, $tmpArr)) {
            $cat = Category::where('name', $tmpArr[1])->first();
            if (!empty($cat->id)) {
                $category_id = $cat->id;
                $category_name = $tmpArr[1];
                $comment = trim($tmpArr[2]);
            } else {
                DB::table('categories')->insert(array(
                    ['name' => $tmpArr[1], 'user_id' => 1],
                ));
                $category_name = $tmpArr[1];
                $category_id = Category::where('name', $tmpArr[1])->first()->id;
                $comment = trim($tmpArr[2]);
            }
        }
        $tags = '';
        if (preg_match('/(.*)\((.+)\)/' , $comment, $tmpArrT)) {
            $comment = trim($tmpArrT[1]);
            $tags = '#' . trim($tmpArrT[2]);
        }
        if (preg_match('/(.*)(wildberries|bonprix)/', $comment, $tmpArrT)) {
            $comment = trim($tmpArrT[1]);
            $tags = '#' . trim($tmpArrT[2]);
        }

        $item = [
            'date' => $date,
            'summ' => $summ,
            'category_id' => $category_id,
            'comment' => $comment,
            'tags' => $tags,
            'type' => 0,
            'user_id' => 1,
        ];
        //dump($item);
        $this->operationsService->store($item);
    }

    private function sendMessage($telegramId, $text, $replyMarkup = [])
    {
        $token = '1113800582:AAGDm_daAzqwGyKLTR5eTcn5nsx6tvz0tZE';
        if ($token) {
            $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
            $data = array(
                'chat_id' => $telegramId,
                'text' => $text,
                'parse_mode'=> 'HTML',
            );
            if (!empty($replyMarkup)) {
                $data['reply_markup'] = json_encode($replyMarkup);
            }
            $this->curlSend($url, $data);
        }
    }

    private function curlSend(string $url, array $data)
    {
        $Ch = curl_init();
        curl_setopt($Ch, CURLOPT_URL, $url);
        curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($Ch, CURLOPT_POST, 1);
        curl_setopt($Ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($Ch, CURLOPT_TIMEOUT, 30);
        $reply = curl_exec($Ch);
        curl_close($Ch);
        return $reply;
    }

}
