<?php


namespace App\Services;


use App\Models\User;
use App\Models\UserMetaData;

class UserService
{
    public function storeUser($telegram_id): void
    {
    }

    public function checkUserAuth($telegram_id): UserMetaData
    {
        return User::where("telegram_id", $telegram_id)->first();
    }

    /**
     * @param UserMetaData $user_meta
     * @return false
     */
    public function hasFIO(UserMetaData $user_meta)
    {
        return $user_meta->fio != null;
    }

    public function hasBirthday(UserMetaData $user_meta)
    {
        return $user_meta->birthday != null;
    }

    public function hasGender(UserMetaData $user_meta)
    {
        return $user_meta->gender != null;
    }

    public function hasAddress(UserMetaData $user_meta)
    {
        return $user_meta->address != null;
    }

    public function hasPhoneNumber(UserMetaData $user_meta)
    {
        return $user_meta->phone_number != null;
    }

    public function fillFIO(UserMetaData $user_meta, $data): void
    {
        $user_meta->fio = $data;
    }

    public function fillBirthday(UserMetaData $user_meta, $data): void
    {
        $user_meta->birthday = $data;
    }

    public function fillGender(UserMetaData $user_meta, $data): void
    {
        $user_meta->gender = $data;
    }

    public function fillAddress(UserMetaData $user_meta, $data): void
    {
        $user_meta->address = $data;
    }

    public function fillPhoneNumber(UserMetaData $user_meta, $data): void
    {
        $user_meta->phone_number = $data;
    }
}
