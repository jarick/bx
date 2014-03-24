<?php namespace BX\String\Manager;
use BX\Manager;

class StringManager extends Manager
{
	public function toUpper($string)
	{
		return mb_strtoupper($string,$this->getCharset());
	}
	public function toLower($string)
	{
		return mb_strtolower($string,$this->getCharset());
	}
	public function escape($string,$flags = ENT_COMPAT)
	{
		return htmlspecialchars($string,$flags,$this->getCharset());
	}
	public function length($string)
	{
		return mb_strlen($string,$this->getCharset());
	}
	public function ucwords($string)
	{
		return mb_convert_case($string,MB_CASE_TITLE,$this->getCharset());
	}
	public function countSubstr($haystack,$needle)
	{
		return mb_substr_count($haystack,$needle,$this->getCharset());
	}
	public function substr($str,$start,$length = null)
	{
		return mb_substr($str,$start,$length,$this->getCharset());
	}
	public function startsWith($haystack,$needle)
	{
		return $needle === '' || mb_strpos($haystack,$needle,0,$this->getCharset()) === 0;
	}
	public function endsWith($haystack,$needle)
	{
		return $needle === '' || mb_substr($haystack,-$this->length($needle),null,$this->getCharset()) === $needle;
	}
	/**
	 * Create a web friendly URL slug from a string.
	 *
	 * Although supported, transliteration is discouraged because
	 *     1) most web browsers support UTF-8 characters in URLs
	 *     2) transliteration causes a loss of information
	 *
	 * @author Sean Murphy <sean@iamseanmurphy.com>
	 * @copyright Copyright 2012 Sean Murphy. All rights reserved.
	 * @license http://creativecommons.org/publicdomain/zero/1.0/
	 *
	 * @param string $str
	 * @param array $options
	 * @return string
	 */
	public function getSlug($str,array $options = [])
	{
		$str = mb_convert_encoding((string) $str,$this->getCharset(),mb_list_encodings());
		$defaults = array(
			'delimiter'		 => '-',
			'limit'			 => null,
			'lowercase'		 => true,
			'replacements'	 => array(),
			'transliterate'	 => false,
		);
		$options = array_merge($defaults,$options);
		$char_map = array(
			'À'	 => 'A','Á'	 => 'A','Â'	 => 'A','Ã'	 => 'A','Ä'	 => 'A','Å'	 => 'A','Æ'	 => 'AE','Ç'	 => 'C',
			'È'	 => 'E','É'	 => 'E','Ê'	 => 'E','Ë'	 => 'E','Ì'	 => 'I','Í'	 => 'I','Î'	 => 'I','Ï'	 => 'I',
			'Ð'	 => 'D','Ñ'	 => 'N','Ò'	 => 'O','Ó'	 => 'O','Ô'	 => 'O','Õ'	 => 'O','Ö'	 => 'O','Ő'	 => 'O',
			'Ø'	 => 'O','Ù'	 => 'U','Ú'	 => 'U','Û'	 => 'U','Ü'	 => 'U','Ű'	 => 'U','Ý'	 => 'Y','Þ'	 => 'TH',
			'ß'	 => 'ss',
			'à'	 => 'a','á'	 => 'a','â'	 => 'a','ã'	 => 'a','ä'	 => 'a','å'	 => 'a','æ'	 => 'ae','ç'	 => 'c',
			'è'	 => 'e','é'	 => 'e','ê'	 => 'e','ë'	 => 'e','ì'	 => 'i','í'	 => 'i','î'	 => 'i','ï'	 => 'i',
			'ð'	 => 'd','ñ'	 => 'n','ò'	 => 'o','ó'	 => 'o','ô'	 => 'o','õ'	 => 'o','ö'	 => 'o','ő'	 => 'o',
			'ø'	 => 'o','ù'	 => 'u','ú'	 => 'u','û'	 => 'u','ü'	 => 'u','ű'	 => 'u','ý'	 => 'y','þ'	 => 'th',
			'ÿ'	 => 'y',
			'©'	 => '(c)',
			'Α'	 => 'A','Β'	 => 'B','Γ'	 => 'G','Δ'	 => 'D','Ε'	 => 'E','Ζ'	 => 'Z','Η'	 => 'H','Θ'	 => '8',
			'Ι'	 => 'I','Κ'	 => 'K','Λ'	 => 'L','Μ'	 => 'M','Ν'	 => 'N','Ξ'	 => '3','Ο'	 => 'O','Π'	 => 'P',
			'Ρ'	 => 'R','Σ'	 => 'S','Τ'	 => 'T','Υ'	 => 'Y','Φ'	 => 'F','Χ'	 => 'X','Ψ'	 => 'PS','Ω'	 => 'W',
			'Ά'	 => 'A','Έ'	 => 'E','Ί'	 => 'I','Ό'	 => 'O','Ύ'	 => 'Y','Ή'	 => 'H','Ώ'	 => 'W','Ϊ'	 => 'I',
			'Ϋ'	 => 'Y',
			'α'	 => 'a','β'	 => 'b','γ'	 => 'g','δ'	 => 'd','ε'	 => 'e','ζ'	 => 'z','η'	 => 'h','θ'	 => '8',
			'ι'	 => 'i','κ'	 => 'k','λ'	 => 'l','μ'	 => 'm','ν'	 => 'n','ξ'	 => '3','ο'	 => 'o','π'	 => 'p',
			'ρ'	 => 'r','σ'	 => 's','τ'	 => 't','υ'	 => 'y','φ'	 => 'f','χ'	 => 'x','ψ'	 => 'ps','ω'	 => 'w',
			'ά'	 => 'a','έ'	 => 'e','ί'	 => 'i','ό'	 => 'o','ύ'	 => 'y','ή'	 => 'h','ώ'	 => 'w','ς'	 => 's',
			'ϊ'	 => 'i','ΰ'	 => 'y','ϋ'	 => 'y','ΐ'	 => 'i',
			'Ş'	 => 'S','İ'	 => 'I','Ç'	 => 'C','Ü'	 => 'U','Ö'	 => 'O','Ğ'	 => 'G',
			'ş'	 => 's','ı'	 => 'i','ç'	 => 'c','ü'	 => 'u','ö'	 => 'o','ğ'	 => 'g',
			'А'	 => 'A','Б'	 => 'B','В'	 => 'V','Г'	 => 'G','Д'	 => 'D','Е'	 => 'E','Ё'	 => 'Yo','Ж'	 => 'Zh',
			'З'	 => 'Z','И'	 => 'I','Й'	 => 'J','К'	 => 'K','Л'	 => 'L','М'	 => 'M','Н'	 => 'N','О'	 => 'O',
			'П'	 => 'P','Р'	 => 'R','С'	 => 'S','Т'	 => 'T','У'	 => 'U','Ф'	 => 'F','Х'	 => 'H','Ц'	 => 'C',
			'Ч'	 => 'Ch','Ш'	 => 'Sh','Щ'	 => 'Sh','Ъ'	 => '','Ы'	 => 'Y','Ь'	 => '','Э'	 => 'E','Ю'	 => 'Yu',
			'Я'	 => 'Ya',
			'а'	 => 'a','б'	 => 'b','в'	 => 'v','г'	 => 'g','д'	 => 'd','е'	 => 'e','ё'	 => 'yo','ж'	 => 'zh',
			'з'	 => 'z','и'	 => 'i','й'	 => 'j','к'	 => 'k','л'	 => 'l','м'	 => 'm','н'	 => 'n','о'	 => 'o',
			'п'	 => 'p','р'	 => 'r','с'	 => 's','т'	 => 't','у'	 => 'u','ф'	 => 'f','х'	 => 'h','ц'	 => 'c',
			'ч'	 => 'ch','ш'	 => 'sh','щ'	 => 'sh','ъ'	 => '','ы'	 => 'y','ь'	 => '','э'	 => 'e','ю'	 => 'yu',
			'я'	 => 'ya',
			'Є'	 => 'Ye','І'	 => 'I','Ї'	 => 'Yi','Ґ'	 => 'G',
			'є'	 => 'ye','і'	 => 'i','ї'	 => 'yi','ґ'	 => 'g',
			'Č'	 => 'C','Ď'	 => 'D','Ě'	 => 'E','Ň'	 => 'N','Ř'	 => 'R','Š'	 => 'S','Ť'	 => 'T','Ů'	 => 'U',
			'Ž'	 => 'Z',
			'č'	 => 'c','ď'	 => 'd','ě'	 => 'e','ň'	 => 'n','ř'	 => 'r','š'	 => 's','ť'	 => 't','ů'	 => 'u',
			'ž'	 => 'z',
			'Ą'	 => 'A','Ć'	 => 'C','Ę'	 => 'e','Ł'	 => 'L','Ń'	 => 'N','Ó'	 => 'o','Ś'	 => 'S','Ź'	 => 'Z',
			'Ż'	 => 'Z',
			'ą'	 => 'a','ć'	 => 'c','ę'	 => 'e','ł'	 => 'l','ń'	 => 'n','ó'	 => 'o','ś'	 => 's','ź'	 => 'z',
			'ż'	 => 'z',
			'Ā'	 => 'A','Č'	 => 'C','Ē'	 => 'E','Ģ'	 => 'G','Ī'	 => 'i','Ķ'	 => 'k','Ļ'	 => 'L','Ņ'	 => 'N',
			'Š'	 => 'S','Ū'	 => 'u','Ž'	 => 'Z',
			'ā'	 => 'a','č'	 => 'c','ē'	 => 'e','ģ'	 => 'g','ī'	 => 'i','ķ'	 => 'k','ļ'	 => 'l','ņ'	 => 'n',
			'š'	 => 's','ū'	 => 'u','ž'	 => 'z'
		);
		$str = preg_replace(array_keys($options['replacements']),$options['replacements'],$str);
		if ($options['transliterate']){
			$str = str_replace(array_keys($char_map),$char_map,$str);
		}
		$str = preg_replace('/[^\p{L}\p{Nd}]+/u',$options['delimiter'],$str);
		$str = preg_replace('/('.preg_quote($options['delimiter'],'/').'){2,}/','$1',$str);
		$str = mb_substr($str,0,($options['limit'] ? $options['limit'] : mb_strlen($str,$this->getCharset())),$this->getCharset());
		$str = trim($str,$options['delimiter']);
		return $options['lowercase'] ? mb_strtolower($str,$this->getCharset()) : $str;
	}
	public function convertNumber($number,$words)
	{
		$n = $number;
		if (count($words) === 2){
			$words[3] = $words[2];
		}
		list($s1,$s2,$s3) = $words;
		$m = $n % 10;$j = $n % 100;
		if ($m == 0 || $m >= 5 || ($j >= 10 && $j <= 20)){
			return $s3;
		}
		if ($m >= 2 && $m <= 4){
			return $s2;
		}
		return $s1;
	}
}