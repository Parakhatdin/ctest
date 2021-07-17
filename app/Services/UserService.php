<?php


namespace App\Services;


class UserService
{
    public function storeUser($telegram_id): void
    {
    }

    public function checkUserAuth($telegram_id)
    {
        return false;
    }

    public function hasFIO($user)
    {
        return false;
    }

    public function hasBirthday($user)
    {
        return false;
    }

    public function hasGender($user)
    {
        return false;
    }

    public function hasAddress($user)
    {
        return false;
    }

    public function hasPhoneNumber($user)
    {
        return false;
    }

    public function fillFIO($user): void
    {
    }

    public function fillBirthday($user): void
    {
    }

    public function fillGender($user): void
    {
    }

    public function fillAddress($user): void
    {
    }

    public function fillPhoneNumber($user): void
    {
    }
}
