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
        if ($user_meta = $this->user_service->checkUserAuth($telegram_id)->metaData()) {
            // user has FIO
            if ($this->user_service->hasFIO($user_meta)) {
                // user has Birthday
                if ($this->user_service->hasBirthday($user_meta)) {
                    // user has Gender
                    if ($this->user_service->hasGender($user_meta)) {
                        // user has Address
                        if ($this->user_service->hasAddress($user_meta)) {
                            // user has Phone Number
                            if (! $this->user_service->hasPhoneNumber($user_meta)) {
                                $this->user_service->fillPhoneNumber($user_meta, $content);
                            }
                        }

                        $this->user_service->fillAddress($user_meta, $content);
                        $this->ask($telegram_id, "send me your phone number");
                    }

                    $this->user_service->fillGender($user_meta, $content);
                    $this->ask($telegram_id, "send me your address");
                }

                $this->user_service->fillBirthday($user_meta, $content);
                $this->ask($telegram_id, "send me your gender");
            }

            $this->user_service->fillFIO($user_meta, $content);
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
