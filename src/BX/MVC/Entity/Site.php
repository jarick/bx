<?php namespace BX\MVC\Entity;
use BX\Entity;
use BX\Validator\Manager\String;
use BX\Validator\Manager\Multy;
use BX\Validator\Manager\Custom;

class Site extends Entity
{
	const C_NAME = 'NAME';
	const C_REGEX = 'REGEX';
	const C_FOLDER = 'FOLDER';
	const C_LAYOUT_RULE = 'LAYOUT_RULE';
	const C_URL_REWITE = 'URL_REWRITE';
	/**
	 * Labels
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_NAME		 => $this->trans('mvc.entity.site.name'),
			self::C_REGEX		 => $this->trans('mvc.entity.site.regex'),
			self::C_FOLDER		 => $this->trans('mvc.entity.site.folder'),
			self::C_LAYOUT_RULE	 => $this->trans('mvc.entity.site.layout_rule'),
			self::C_URL_REWITE	 => $this->trans('mvc.entity.site.url_rewrite'),
		];
	}
	/**
	 * Rules
	 * @return array
	 */
	protected function rules()
	{
		return [
			[[self::C_NAME,self::C_FOLDER],String::create()->notEmpty()],
			[self::C_REGEX,Multy::create(String::create()->notEmpty())],
			[self::C_LAYOUT_RULE,Custom::create([$this,'validateLayoutRule'],$this->trans('mvc.entity.site.layout_rule'))],
			[self::C_URL_REWITE,Custom::create([$this,'validateUrlRewrite'],$this->trans('mvc.entity.site.url_rewrite'))],
		];
	}
	/**
	 * Validate laout rule
	 * @param array $aLayoutRule
	 * @return bool
	 */
	public function validateLayoutRule($aLayoutRule)
	{
		return is_array($aLayoutRule);
	}
	/**
	 * Validate url rewrite
	 * @param array $aUrlRewrite
	 * @return bool
	 */
	public function validateUrlRewrite($aUrlRewrite)
	{
		return is_array($aUrlRewrite);
	}
}
