<?php namespace BX\News;

/**
 * @property-read integer $id
 * @property integer $news_id
 * @property integer $category_id
 * @property-read string $timestamp_x
 */
class NewsCategoryLinkEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait;
	const C_ID = 'ID';
	const C_NEWS_ID = 'NEWS_ID';
	const C_CATEGORY_ID = 'CATEGORY_ID';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_NEWS_GUID = 'NEWS_GUID';
	const C_NEWS_NAME = 'NEWS_NAME';
	const C_CATEGORY_GUID = 'CATEGORY_GUID';
	const C_CATEGORY_NAME = 'CATEGORY_NAME';
	/**
	 * Return labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID				 => $this->trans('news.entity.link.id'),
			self::C_NEWS_ID			 => $this->trans('news.entity.link.news_id'),
			self::C_CATEGORY_ID		 => $this->trans('news.entity.link.category_id'),
			self::C_TIMESTAMP_X		 => $this->trans('news.entity.link.timestamp_x'),
			self::C_NEWS_GUID		 => $this->trans('news.entity.link.news_guid'),
			self::C_NEWS_CODE		 => $this->trans('news.entity.link.news_code'),
			self::C_CATEGORY_GUID	 => $this->trans('news.entity.link.category_guid'),
			self::C_CATEGORY_NAME	 => $this->trans('news.entity.link.category_name'),
		];
	}
	/**
	 * Return rules
	 *
	 * @return array
	 */
	protected function rules()
	{
		return [
			[self::C_NEWS_ID,self::C_CATEGORY_ID],
			$this->rule()->number()->integer()->setMin(1)->notEmpty(),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
		];
	}
}