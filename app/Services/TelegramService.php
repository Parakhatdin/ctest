<?php


namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public $user_service;
    public $bot;

    public static $city = [
        "Nukus",
        "Tashkent",
        "Samarkand"
    ];


    /**
     * TelegramService constructor.
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service, Bot $bot)
    {
        $this->user_service = $user_service;
        $this->bot = $bot;
    }

    public function handle(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $telegram_id = Arr::get($content, "message.from.id");
        $text = Arr::get($content, "message.text", "no");

        if ($telegram_id == null) return;


        // user registered
        if ($user = $this->user_service->checkUserAuth($telegram_id)) {
            // user has FIO
            if ($this->user_service->hasFIO($user)) {
                // user has Birthday
                if ($this->user_service->hasBirthday($user)) {
                    // user has Gender
                    if ($this->user_service->hasGender($user)) {
                        // user has Address
                        if ($this->user_service->hasAddress($user)) {
                            // user has Phone Number
                            if ($this->user_service->hasPhoneNumber($user)) {
                                $this->sendMessage($telegram_id, "вы уже зарегистрированы");
                            } else {
                                if ($this->user_service->fillPhoneNumber($user, $text)) {
                                    $this->sendMessage($telegram_id, "поздравляю, все готово!");
                                    $firstname = Arr::get($content, "message.from.first_name");
                                    $lastname = Arr::get($content, "message.from.last_name", "");
                                    $username = Arr::get($content, "message.from.username", "");
                                    $anketa =
                                        "telegram_id: " . $telegram_id ."\n".
                                        "firstname: " . $firstname ."\n".
                                        "lastname: " . $lastname ."\n".
                                        "username: " . $username ."\n".
                                        "ФИО: " . $user->fio ."\n".
                                        "дата рождения: " . $user->birthday ."\n".
                                        "пол: " . $user->gender ."\n".
                                        "город/область: " . $user->address ."\n".
                                        "номер телефона: " . $user->phone_number;
                                    $this->sendMessage(-1001431010757, $anketa);
                                } else {
                                    $this->sendMessage($telegram_id, "неверный номер телефона, отправьте еще раз");
                                }
                            }
                        } else {
                            if ($this->user_service->fillAddress($user, $text)) {
                                $this->sendMessageWithKeyboard($telegram_id, "номер телефона, пример: 914565533", $this->removeKeyboard());
                            } else {
                                $this->sendMessageWithKeyboard($telegram_id, "неверный адрес, отправьте еще раз", $this->cityButton());
                            }
                        }
                    } else {
                        if ($this->user_service->fillGender($user, $text)) {
                            $this->sendMessageWithKeyboard($telegram_id, "адрес", $this->cityButton());
                        } else {
                            $this->sendMessageWithKeyboard($telegram_id, "неверный пол, отправьте еще раз", $this->maleOrFemaleButton());
                        }
                    }
                } else {
                    if ($this->user_service->fillBirthday($user, $text)) {
                        $this->sendMessageWithKeyboard($telegram_id, "пол", $this->maleOrFemaleButton());
                    } else {
                        $this->sendMessage($telegram_id, "неверный день рождения, отправьте еще раз");
                    }
                }
            } else {
                if ($this->user_service->fillFIO($user, $text)) {
                    $this->sendMessage($telegram_id, "пришлите дату рождения в формате 31.12.1999");
                } else {
                    $this->sendMessage($telegram_id, "неправильный ФИО, отправьте еще раз");
                }
            }
        } else {
            $this->user_service->storeUser($telegram_id);
            $this->sendMessage($telegram_id, "добро пожаловать в чат-бот Click. пожалуйста, пришлите свое ФИО");
        }


    }
    public function sendMessage($telegram_id, $message): void
    {
        $this->bot->method("sendMessage", [
            "chat_id" => $telegram_id,
            "text" => $message
        ]);
    }

    public function sendMessageWithKeyboard($telegram_id, $message, $keyboard): void
    {
        $this->bot->method("sendMessage", [
            "chat_id" => $telegram_id,
            "text" => $message,
            "reply_markup" => $keyboard
        ]);
    }

    public function maleOrFemaleButton()
    {
        $keyboard1 = [
            "text" => "male"
        ];
        $keyboard2 = [
            "text" => "female"
        ];
        $arrayOfKeyboard = [
            [$keyboard1, $keyboard2]        // row 1
        ];
        return json_encode([
            "keyboard" => $arrayOfKeyboard,
            "resize_keyboard" => true,
            "one_time_keyboard" => true
        ]);
    }

    public function cityButton()
    {
        $cities = [];
        foreach (self::$city as $city) {
            $cities[] = array([
                "text" => $city
            ]);
        }
        return json_encode([
            "keyboard" => $cities,
            "resize_keyboard" => true,
            "one_time_keyboard" => true
        ]);
    }
    public function removeKeyboard()
    {
        return json_encode([
            "remove_keyboard" => true,
        ]);
    }
}
