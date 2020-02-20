<?php

namespace App\Contracts\Business;

/**
 * Profile Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2019/12/28
 */

interface MessageService
{
    public function fetchUserContact();

    public function addUserContact($contact_id);

    public function removeUserContact($contact_id);

    public function fetchUserContactMessage($contact_id);

    public function addUserContactMessage($contact_id, $message, $status);

    public function removeUserContactMessage($contact_id);

}