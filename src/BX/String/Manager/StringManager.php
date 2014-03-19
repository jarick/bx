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