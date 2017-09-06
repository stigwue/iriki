<?php

namespace iriki;

/**
* Iriki type manager.
* Model properties are of a type specified herein.
*
*/
class type
{
  /**
  * Checks to see if provided variable is of a certain type.
  *
  *
  * @param object Variable value to check
  * @param string Type name to compare
  * @return array Status of type match
  * @throw
  */
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
      
      case 'boolean':
        if (strtolower($value) == 'true' OR strtolower($value) == 'false')
        {
          return true;
        }
        else return false;
      break;

      case 'key':
        return is_string($value) && strlen($value) == 24 && ctype_xdigit($value);
      break;

      default:
        return false;
      break;
    }
  }

  /**
  * Convert provided variable to the desired type.
  * Do make sure an is_type test already returns true.
  *
  * @param object Variable value to check
  * @param string Type name to compare
  * @return object Desired type
  * @throw
  */
  public static function ctype($value, $type)
  {
    switch ($type)
    {
      case 'number':
        return $value + 0;
      break;

      case 'email':
      case 'string':
      case 'key':
        return $value;
      break;
      
      case 'boolean':
        if (strtolower($value) == 'true')
        {
          return true;
        }
        else return false;
      break;

      default:
        return $value;
      break;
    }
  }

}

?>
