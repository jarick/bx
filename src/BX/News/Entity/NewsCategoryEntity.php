<?php namespace BX\News\Entity;

/**
 * @property-read integer $id
 * @property-read string $guid
 * @property string $active
 * @property string $name
 * @property string $code
 * @property string $text
 * @property integer $sort
 * @property-read string $timestamp_x
 * @property-read string $create_date
 * @property integer $user_id
 * @property-read integer $parent_id
 */
class NewsCategoryEntity implements \BX\Validator\IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait;
	const C_ID = 'ID';
	const C_GUID = 'GUID';
	const C_ACTIVE = 'ACTIVE';
	const C_NAME = 'NAME';
	const C_CODE = 'CODE';
	const C_TEXT = 'TEXT';
	const C_SORT = 'SORT';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_CREATE_DATE = 'CREATE_DATE';
	const C_USER_ID = 'USER_ID';
	const C_PARENT_ID = 'PARENT_ID';
	const C_PARENT_NAME = 'PARENT_NAME';
	const C_PARENT_GUID = 'PARENT_GUID';
	const C_USER_LOGIN = 'USER_LOGIN';
	const C_USER_GUID = 'USER_GUID';
	/**
	 * Return rules
	 *
	 * @return array
	 */
	protected function rules()
	{
		return [
			[self::C_GUID],
			$this->rule()->setter()->setValue(uniqid('news_category'))->setValidators([
				$this->rule()->string()->notEmpty()->setMax(50),
			])->onAdd(),
			[self::C_ACTIVE],
			$this->rule()->boolean()->setDefault('Y'),
			[self::C_NAME],
			$this->rule()->string()->setMax(255)->notEmpty(),
			[self::C_CODE],
			$this->rule()->setter()->setFunction([$this,'filterCode'])->setValidators([
				$this->rule()->string()->notEmpty()->setMax(50),
			]),
			[self::C_TEXT],
			$this->rule()->string()->setMax(65000),
			[self::C_SORT],
			$this->rule()->number()->integer()->setMin(0)->setDefault(500),
			[self::C_CREATE_DATE],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp())->onAdd(),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
			[self::C_USER_ID,self::C_PARENT_ID],
			$this->rule()->number()->integer(),
		];
	}
	/**
	 * Return labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('news.entity.category.id'),
			self::C_GUID		 => $this->trans('news.entity.category.guid'),
			self::C_ACTIVE		 => $this->trans('news.entity.category.active'),
			self::C_NAME		 => $this->trans('news.entity.category.name'),
			self::C_CODE		 => $this->trans('news.entity.category.code'),
			self::C_TEXT		 => $this->trans('news.entity.category.detail_text'),
			self::C_SORT		 => $this->trans('news.entity.category.sort'),
			self::C_TIMESTAMP_X	 => $this->trans('news.entity.category.timestamp_x'),
			self::C_CREATE_DATE	 => $this->trans('news.entity.category.create_date'),
			self::C_USER_ID		 => $this->trans('news.entity.category.user_id'),
			self::C_USER_GUID	 => $this->trans('news.entity.category.user_guid'),
			self::C_USER_LOGIN	 => $this->trans('news.entity.category.user_login'),
			self::C_PARENT_ID	 => $this->trans('news.entity.category.parent_id'),
			self::C_PARENT_GUID	 => $this->trans('news.entity.category.parent_guid'),
			self::C_PARENT_NAME	 => $this->trans('news.entity.category.parent_name'),
		];
	}
	/**
	 * Filter code
	 *
	 * @return string
	 */
	public function filterCode()
	{
		$value = $this->getValue(self::C_NAME);
		return $this->string()->substr($this->string()->getSlug($value),0,50);
	}
}