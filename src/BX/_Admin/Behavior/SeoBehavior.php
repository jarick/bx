<?php
namespace BX\main\behaviors;
use BX;
use BX\Behavior;
use BX\IEntity;
use BX\main\managers\DbManager;
use BX\main\behaviors\EntityBehavior;

class SeoBehavior extends Behavior
{
	public $clRobots = 'SEO_ROBOTS';
	public $clTitle = 'SEO_TITLE';
	public $clKeywords = 'SEO_KEYWORDS';
	public $clDescription = 'SEO_DESCRIPTION';
	
	public function init()
	{
		global $USER;
		$oEntity = $this->owner;
		$oEntity->appendAttributeLabels(array(
			$this->clRobots => 'Индексация страницы поисковиками',
			$this->clTitle => 'Заголовок страницы',
			$this->clKeywords => 'Ключевые слова',
			$this->clDescription => 'Описание страницы',
		));
		$oEntity->appendRules(array(
			array("{$this->clRobots}",'boolean','default'=>'Y'),
			array("{$this->clTitle},$this->clKeywords,$this->clDescription" , 'string' , 'max' => 255),
		));
		$oEntity->appendFilterRules(array(
			"{$this->clRobots}" => 'boolean',
			"{$this->clTitle},$this->clKeywords,$this->clDescription" => 'string',
		));
		$oEntity->appendFilterFields(array(
			$this->clRobots => 'T.'.$this->clRobots,
			$this->clTitle => 'T.'.$this->clTitle,
			$this->clKeywords => 'T.'.$this->clKeywords,
			$this->clDescription => 'T.'.$this->clDescription,
		));
	}
	
	public function __onRenderAdminListHeads(&$arHeads)
	{
		return array(
			array("id"=>$this->clRobots, "content"=>$this->owner->getLabel($this->clRobots), "sort"=>$this->clRobots, "default"=>true),
			array("id"=>$this->clTitle, "content"=>$this->owner->getLabel($this->clTitle), "sort"=>$this->clTitle, "default"=>true),
			array("id"=>$this->clKeywords, "content"=>$this->owner->getLabel($this->clKeywords), "sort"=>$this->clKeywords, "default"=>true),
			array("id"=>$this->clDescription, "content"=>$this->owner->getLabel($this->clDescription), "sort"=>$this->clDescription, "default"=>true),
		);
	}
	
	/**
	 * @param CAdminListRow $row
	 */
	public function __onRenderAdminList(&$row)
	{
		$row->AddCheckField($this->clRobots);
		$row->AddInputField($this->clTitle);
		$row->AddInputField($this->clKeywords);
		$row->AddInputField($this->clDescription);
	}
	
	public function __onRenderAdminFilter($oFilterWidget)
	{
		$oFilterWidget->getText($this->clRobots,get_request('filter_'.$this->clRobots),$this->owner->getLabel($this->clRobots));
		$oFilterWidget->getText($this->clTitle,get_request('filter_'.$this->clTitle),$this->owner->getLabel($this->clTitle));
		$oFilterWidget->getText($this->clKeywords,get_request('filter_'.$this->clKeywords),$this->owner->getLabel($this->clKeywords));
		$oFilterWidget->getText($this->clDescription,get_request('filter'.$this->clDescription),$this->owner->getLabel($this->clDescription));
	}
}
