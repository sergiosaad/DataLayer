<?php

namespace Example\Models;

use SergioSaad\DataLayer\DataLayer;

/**
 * Class User
 * @package Example\Models
 */
class User extends DataLayer
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct("users", ["first_name", "last_name"]);
    }
}