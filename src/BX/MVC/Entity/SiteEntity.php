<?php namespace BX\MVC\Entity;
use BX\Validator\IEntity;
use BX\Validator\Collection\Custom;
use BX\Validator\Collection\Multy;
use BX\Validator\Collection\String;

/**
 * @property string $name
 * @property array $regex
 * @property string $folder
 * @property array $layout_rule
 * @property array $url_rewrite
 */
class SiteEntity implements IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Translate\TranslateTrait;
	const C_NAME = 'NAME';
	const C_TITLE = 'TITLE';
	const C_KEYWORDS = 'KEYWORDS';
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
			self::C_TITLE		 => $this->trans('mvc.entity.site.title'),
			self::C_KEYWORDS	 => $this->trans('mvc.entity.site.keywords'),
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
			[self::C_NAME,self::C_TITLE,self::C_FOLDER],
			String::create()->notEmpty(),
			[self::C_KEYWORDS],
			String::create(),
			[self::C_REGEX],
			Multy::create(String::create()->notEmpty())->notEmpty(),
			[self::C_LAYOUT_RULE],
			Custom::create([$this,'validateLayoutRule']),
			[self::C_URL_REWITE],
			Custom::create([$this,'validateUrlRewrite']),
		];
	}
	/**
	 * Validate laout rule
	 * @param array $value
	 * @return bool
	 */
	public function validateLayoutRule($value)
	{
		if (!is_array($value)){
			return $this->trans('mvc.entity.site.error_layout_rule');
		}
		return true;
	}
	/**
	 * Validate url rewrite
	 * @param array $value
	 * @return bool
	 */
	public function validateUrlRewrite($value)
	{
		if (!is_array($value)){
			return $this->trans('mvc.entity.site.error_url_rewrite');
		}
		return true;
	}
}