<?php namespace BX\Translate;

interface ITranslate
{
	public function trans($message,array $params = [],$lang = false,$package = false,$service = false);
}