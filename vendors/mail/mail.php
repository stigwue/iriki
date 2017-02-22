<?php

class Mail
{
    //validation
    public static function isValidEmail($email_to_test)
    {
        require_once(__DIR__ . '/is_email.php');

        if (is_email($email_to_test))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
?>
