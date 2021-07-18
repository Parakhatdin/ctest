<?php


namespace App\Services;


use App\Models\TUser;
use DateTime;

class UserService
{
    public function storeUser($telegram_id): void
    {
        TUser::create(["telegram_id" => $telegram_id]);
    }

    public function checkUserAuth($telegram_id)
    {
        return TUser::where("telegram_id", $telegram_id)->first();
    }

    /**
     * @param TUser $user
     * @return false
     */
    public function hasFIO(TUser $user)
    {
        return $user->fio != null;
    }

    public function hasBirthday(TUser $user)
    {
        return $user->birthday != null;
    }

    public function hasGender(TUser $user)
    {
        return $user->gender != null;
    }

    public function hasAddress(TUser $user)
    {
        return $user->address != null;
    }

    public function hasPhoneNumber(TUser $user)
    {
        return $user->phone_number != null;
    }

    public function fillFIO(TUser $user, $data)
    {
        if (strlen($data) > 5) {
            $user->fio = $data;
            return $user->save();
        }
        return false;
    }

    public function fillBirthday(TUser $user, $data)
    {
        if (DateTime::createFromFormat("d.m.Y", $data) && DateTime::createFromFormat("d.m.Y", $data)->format("d.m.Y") == $data) {
            $user->birthday = $data;
            return $user->save();
        }
        return false;
    }

    public function fillGender(TUser $user, $data)
    {
        if (in_array($data, ["male", "female"])) {
            $user->gender = $data;
            return $user->save();
        }
        return false;
    }

    public function fillAddress(TUser $user, $data)
    {
        if (in_array(TelegramService::$city, $data)) {
            $user->address = $data;
            return $user->save();
        }
        return false;
    }

    public function fillPhoneNumber(TUser $user, $data)
    {
        if (strlen($data) > 9) {
            $user->phone_number = $data;
            return $user->save();
        }
        return false;
    }
}
