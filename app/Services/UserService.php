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
        if (DateTime::createFromFormat("d.m.Y", $data)->format("d.m.Y") == $data) {
            $user->birthday = $data;
            return $user->save();
        }
        return false;
    }

    public function fillGender(TUser $user, $data): void
    {
        $user->gender = $data;
        $user->save();
    }

    public function fillAddress(TUser $user, $data): void
    {
        $user->address = $data;
        $user->save();
    }

    public function fillPhoneNumber(TUser $user, $data): void
    {
        $user->phone_number = $data;
        $user->save();
    }
}
