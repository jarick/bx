<?php namespace BX\News\Entity;
use BX\Validator\IEntity;

/**
 * @property-read integer $id
 * @property-read string $guid
 * @property string $active
 * @property string $name
 * @property-read string $code
 * @property string $preview_text
 * @property \BX\Validator\Upload\IUploadFile $picture
 * @property string $detail_text
 * @property integer $sort
 * @property-read string $timestamp_x
 * @property-read string $create_date
 * @property integer $user_id
 */
class NewsEntity implements IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait;
	const C_ID = 'ID';
	const C_GUID = 'GUID';
	const C_ACTIVE = 'ACTIVE';
	const C_NAME = 'NAME';
	const C_CODE = 'CODE';
	const C_PREVIEW_TEXT = 'PREVIEW_TEXT';
	const C_PICTURE = 'PICTURE';
	const C_DETAIL_TEXT = 'DETAIL_TEXT';
	const C_SORT = 'SORT';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_CREATE_DATE = 'CREATE_DATE';
	const C_USER_ID = 'USER_ID';
	/**
	 * Return rules
	 *
	 * @return array
	 */
	protected function rules()
	{
		return [
			[self::C_GUID],
			$this->rule()->setter()->setValue(uniqid('news'))->setValidators([
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
			[self::C_PREVIEW_TEXT],
			$this->rule()->string()->setMax(10000),
			[self::C_DETAIL_TEXT],
			$this->rule()->string()->setMax(65000),
			[self::C_PICTURE],
			$this->rule()->file()->setDirectory('news'),
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
			[self::C_USER_ID],
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
			self::C_ID			 => $this->trans('news.entuty.news.id'),
			self::C_GUID		 => $this->trans('news.entuty.news.guid'),
			self::C_ACTIVE		 => $this->trans('news.entuty.news.active'),
			self::C_NAME		 => $this->trans('news.entuty.news.name'),
			self::C_CODE		 => $this->trans('news.entuty.news.code'),
			self::C_PREVIEW_TEXT => $this->trans('news.entuty.news.preview_text'),
			self::C_DETAIL_TEXT	 => $this->trans('news.entuty.news.detail_text'),
			self::C_PICTURE		 => $this->trans('news.entuty.news.picture'),
			self::C_SORT		 => $this->trans('news.entuty.news.sort'),
			self::C_TIMESTAMP_X	 => $this->trans('news.entuty.news.timestamp_x'),
			self::C_CREATE_DATE	 => $this->trans('news.entuty.news.create_date'),
			self::C_USER_ID		 => $this->trans('news.entuty.news.user_id'),
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