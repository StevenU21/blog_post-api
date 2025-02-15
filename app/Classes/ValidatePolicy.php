<?php

namespace App\Classes;

class ValidatePolicy
{
    private $user;
    private $permission;
    /**
     * Create a new class instance.
     */
    public function __construct($user, $permission)
    {
        $this->user = $user;
        $this->permission = $permission;
    }
}
