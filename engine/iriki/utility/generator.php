<?php
namespace iriki\engine;
/**
* Iriki Generator utility for randomization.
*
*/
class generator
{
    private static $_generator = null;
	//generate a sample
	public static function getSample($deck = '0123456789abcdefghijklmnopqrstuvwxyz', $length = 7)
	{
		if (is_null(Self::$_generator)) {
			Self::$_generator = (new \RandomLib\Factory)->getLowStrengthGenerator();
		}
		$sample = Self::$_generator->generateString($length, $deck);
		return $sample;
	}
}
?>