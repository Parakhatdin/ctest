<?php


namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public $user_service;
    public $bot;

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



        // user registered
        if ($user = $this->user_service->getUser($telegram_id)) {
            // user has FIO
            if ($this->user_service->hasFIO($user)) {
                // user has Birthday
                if ($this->user_service->hasBirthday($user)) {
                    // user has Gender
                    if ($this->user_service->hasGender($user)) {
                        // user has Address
                        if ($this->user_service->hasAddress($user)) {
                            // user has Phone Number
                            if (! $this->user_service->hasPhoneNumber($user)) {
                                $this->user_service->fillPhoneNumber($user, $content);
                            }
                        }

                        $this->user_service->fillAddress($user, $content);
                        $this->ask($telegram_id, "send me your phone number");
                    }

                    $this->user_service->fillGender($user, $content);
                    $this->ask($telegram_id, "send me your address");
                }

                $this->user_service->fillBirthday($user, $content);
                $this->ask($telegram_id, "send me your gender");
            }

            $this->user_service->fillFIO($user, $content);
            $this->ask($telegram_id, "send me your birthday");
        }


        $this->user_service->storeUser($telegram_id);
        $this->ask($telegram_id, "send me your fio");
    }
    public function ask($telegram_id, $message): void
    {
        $this->bot->method("sendMessage", [
            "chat_id" => $telegram_id,
            "text" => $message
        ]);
    }

}
