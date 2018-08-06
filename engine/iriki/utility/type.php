<?php

namespace iriki\engine;

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
  * @param value Variable value to check
  * @param type Type name to compare
  * @return Status of type match
  * @throw
  */
  public static function is_type($value, $type)
  {
    //there's a special case for requests which have been bundled together and as such are of type array
    //this had better not foul up the works anywhere!!!
    switch ($type)
    {
      case 'number':
        if (is_array($value))
        {
          $is_number = true;
          foreach ($value as $element)
          {
            $is_number = $is_number AND is_numeric($element);
          }
          return $is_number;
        }
        else
        {
          return is_numeric($value);
        }
      break;

      case 'email':
        if (is_array($value))
        {
          $is_email = true;
          foreach ($value as $element)
          {
            $is_email = $is_email AND \IsMail::isRFCValid($element);
          }
          return $is_email;
        }
        else
        {
          return \IsMail::isRFCValid($value);
        }
      break;

      case 'string':
        if (is_array($value))
        {
          $is_string = true;
          foreach ($value as $element)
          {
            $is_string = $is_string AND true;
          }
          return $is_string;
        }
        else
        {
          return true;
        }
      break;
      
      case 'boolean':
        if (is_array($value))
        {
          $is_bool = true;
          foreach ($value as $element)
          {
            $is_single_bool = false;
            if (is_bool($element)) $is_single_bool = $element;

            if (strtolower($elment) == 'true' OR strtolower($element) == 'false')
            {
              $is_single_bool = true;
            }
            else $is_single_bool = false;

            $is_bool = $is_bool AND $is_single_bool;
          }
          return $is_bool;
        }
        else
        {
          if (is_bool($value)) return $value;

          if (strtolower($value) == 'true' OR strtolower($value) == 'false')
          {
            return true;
          }
          else return false;
        }
      break;

      case 'key':
        if (is_array($value))
        {
          $is_key = true;
          foreach ($value as $element)
          {
            $is_key = $is_key AND (is_string($element) && strlen($element) == 24 && ctype_xdigit($element));
          }
          return $is_key;
        }
        else
        {
          return is_string($value) && strlen($value) == 24 && ctype_xdigit($value);
        }
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
  * @param value Variable value to check
  * @param type Type name to convert to
  * @return Desired object of type
  * @throw
  */
  public static function ctype($value, $type)
  {
    switch ($type)
    {
      //note the special 'array' case as described above
      //handle accordingly
      case 'number':
        if (is_array($value))
        {
          return $value;
        }
        else
        {
          return $value + 0;
        }
      break;

      case 'email':
      case 'string':
      case 'key':
        return $value;
      break;
      
      case 'boolean':
        if (is_array($value))
        {
          return $value;
        }
        else
        {
          if (is_bool($value)) return $value;
          
          if (strtolower($value) == 'true')
          {
            return true;
          }
          else return false;
        }
      break;

      default:
        return $value;
      break;
    }
  }

  /**
  * Generate a desired type for testing and such.
  *
  * @param type Type name to generate
  * @param options Options for configuring generation.
  * @return Desired object of type
  * @throw
  */
  public static function gen_type($type, $options)
  {
    switch ($type)
    {
      case 'number':
        //php random
        $gte = (isset($options['gte']) ? $options['gte'] : 0);
        $lt = (isset($options['lt']) ? $options['lt'] : null);
      break;

      case 'email':
        //valid or invalid
      break;

      case 'string':
        //length
        $length = (isset($options['length']) ? $options['length'] : 7);
      break;
      
      case 'boolean':
        //random true and false
        $pool = array(true, false);
        return $pool[ array_rand($pool) ];
      break;

      case 'key':
        //not sure
      break;

      default:
        return null;
      break;
    }
  }

}

?>
