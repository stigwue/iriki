<?php

namespace iriki;

class type
{
  public static function is_type($value, $type)
  {
    switch ($type)
    {
      case 'number':
        return is_numeric($value);
      break;

      case 'email':
        return \Mail::isValidEmail($value);
      break;

      case 'string':
        return true;
      break;

      default:
        return false;
      break;
    }
  }
}

?>
