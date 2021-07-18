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
                                $this->sendMessage($telegram_id, "you are already registered");
                            } else {
                                if ($this->user_service->fillPhoneNumber($user, $text)) {
                                    $this->sendMessage($telegram_id, "thanks !");
                                } else {
                                    $this->sendMessage($telegram_id, "invalid phone number");
                                }
                            }
                        } else {
                            if ($this->user_service->fillAddress($user, $text)) {
                                $this->sendMessageWithKeyboard($telegram_id, "send me your phone number", $this->removeKeyboard());
                            } else {
                                $this->sendMessageWithKeyboard($telegram_id, "invalid address, resend", $this->cityButton());
                            }
                        }
                    } else {
                        if ($this->user_service->fillGender($user, $text)) {
                            $this->sendMessageWithKeyboard($telegram_id, "send me your address", $this->cityButton());
                        } else {
                            $this->sendMessageWithKeyboard($telegram_id, "invalid gender, resend", $this->maleOrFemaleButton());
                        }
                    }
                } else {
                    if ($this->user_service->fillBirthday($user, $text)) {
                        $this->sendMessageWithKeyboard($telegram_id, "send me your gender in format male or female", $this->maleOrFemaleButton());
                    } else {
                        $this->sendMessage($telegram_id, "invalid birthday");
                    }
                }
            } else {
                if ($this->user_service->fillFIO($user, $text)) {
                    $this->sendMessage($telegram_id, "пришлите дату рождения в формате 31.12.1999");
                } else {
                    $this->sendMessage($telegram_id, "invalid fio");
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
