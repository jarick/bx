<?php
namespace BX\main\behaviors;
use BX;
use BX\Behavior;
use BX\IEntity;
use BX\main\managers\DbManager;

class NestedSetsBehavior extends Behavior
{
	public $clParentId = 'PARENT_ID';
	public $clLeftMargin = 'LEFT_MARGIN';
	public $clRightMargin = 'RIGHT_MARGIN';
	public $clDepthLevel = 'DEPTH_LEVEL';
	public $clId = 'ID';
	public $clRootId = 'ROOT_ID';
	
	public function init()
	{
		global $USER;
		$oEntity = $this->owner;
		$oEntity->appendAttributeLabels(array(
			$this->clLeftMargin => 'Левая граница',
			$this->clRightMargin => 'Правая граница',
			$this->clDepthLevel => 'Глубина вложенности',
			$this->clRootId => 'Родительский раздел',
		));
		$oEntity->appendFilterRules(array(
			"{$this->clLeftMargin},{$this->clRootId},{$this->clRightMargin},{$this->clDepthLevel}" => 'numeric',
		));
		$oEntity->appendFilterFields(array(
			$this->clRootId => 'T.'.$this->clRootId,
			$this->clLeftMargin => 'T.'.$this->clLeftMargin,
			$this->clRightMargin => 'T.'.$this->clRightMargin,
			$this->clDepthLevel => 'T.'.$this->clDepthLevel,
		));
		if($sEvent = $this->owner->getEvent())
		{
			AddEventHandler($this->owner->getModule(), 'OnAfter'.$sEvent.'Update',array($this,'onAfterUpdate'));
			AddEventHandler($this->owner->getModule(), 'OnAfter'.$sEvent.'Add',array($this,'onAfterAdd'));
			AddEventHandler($this->owner->getModule(), 'OnAfter'.$sEvent.'Delete',array($this,'onAfterDelete'));
		}
	}
	
	public function onAfterUpdate($iID, &$arFields)
	{
		if(intval($arFields[$this->clParentId]) === 0)
			$this->moveAsRoot($iID);
		else 
			$this->moveNode($arFields[$this->clParentId], $iID, false,1);
	}
	
	public function onAfterAdd($iID, &$arFields)
	{
		if(intval($arFields[$this->clParentId]) === 0)
			$this->makeRoot($iID);
		else
			$this->addNode($arFields[$this->clParentId], $iID, false,1);
	}
	
	public function onAfterDelete($iID)
	{
		$this->delete($iID);
	}
	
	private function shiftLeftRight($iKey,$delta,$arNode)
	{
		global $DB;
		/* @var $owner IEntity */
		$owner = $this->owner;
			
		foreach(array($this->clLeftMargin,$this->clRightMargin) as $attribute)
		{
			$condition = ' WHERE '.$attribute.'>='.$iKey.' AND '.$this->clRootId.'='.$arNode[$this->clRootId];
			$bResult = $DB->Update(
				$owner->getDbTable(),
				array($attribute => $attribute.sprintf('%+d',$delta)),
				$condition
			);
			if($bResult === false)
				return false;
		}
		return true;
	}
	
	public function delete($iID)
	{
		global $DB;
		/* @var $owner IEntity */
		$owner = $this->owner;
		$arNode = $DB->Query(
			'SELECT '.
				$this->clId.','.$this->clLeftMargin.','.$this->clRightMargin.','.$this->clRootId.
			' FROM '.
				$owner->getDbTable().
			' WHERE '.
				'ID='.intval($iID).
			' LIMIT 1'
		)->Fetch();
		if(!$arNode)
			throw new \Exception('The node should be exists.');
		$DB->StartTransaction();
		if($arNode[$this->clRightMargin] - $arNode[$this->clLeftMargin] === 1)
		{
			$result = $DB->Query("DELETE FROM `".$owner->getDbTable()."` WHERE ID = ".intval($iID));
		}
		else
		{
			$condition= $this->clLeftMargin.'>='.$arNode[$this->clLeftMargin].' AND '.
				$this->clRightMargin.'<='.$arNode[$this->clRightMargin].
				' AND '.$this->clRootId.'='.$arNode[$this->clRootId];
			$result = $DB->Query("DELETE FROM `".$owner->getDbTable()."` WHERE ".$condition);
		}
		if($result !== false)
		{
			$DB->Rollback();
			return false;
		}
		$this->shiftLeftRight(
			$arNode[$this->clRightMargin] + 1,
			$arNode[$this->clLeftMargin] - $arNode[$this->clRightMargin] - 1,
			$arNode
		);
		$DB->Commit();
		return true;
	}
	
	public function moveAsRoot($iID)
	{
		global $DB;
		/* @var $owner IEntity */
		$owner = $this->owner;
		$arNode = $DB->Query(
			'SELECT '.
				$this->clId.','.$this->clLeftMargin.','.$this->clRightMargin.','.$this->clRootId.
				','.$this->clDepthLevel.
			' FROM '.
				$owner->getDbTable().
			' WHERE '.
				'ID='.intval($iID).
			' LIMIT 1'
		)->Fetch();
		if(!$arNode)
			throw new \Exception('The node should be exists.');
		$arNode[$this->clRootId] = intval($arNode[$this->clRootId]);
		if($arNode[$this->clRootId] === 0)
			return true;
		$DB->StartTransaction();		
		
		$left = intval($arNode[$this->clLeftMargin]);
		$right = intval($arNode[$this->clRightMargin]);
		$levelDelta = 1 - intval($arNode[$this->clDepthLevel]);
		$delta = 1-$left;
		
		$arUpdate = array(
			$this->clLeftMargin => $this->clLeftMargin.sprintf('%+d',$delta),
			$this->clRightMargin => $this->clRightMargin.sprintf('%+d',$delta),
			$this->clDepthLevel => $this->clDepthLevel.sprintf('%+d',$levelDelta),
			$this->clRootId => $arNode[$this->clId],
		);
		$strWhere = ' WHERE '.$this->clLeftMargin.'>='.$left.' AND '.
			$this->clRightMargin.'<='.$right.' AND '.
			$this->clRootId.'='.$arNode[$this->clRootId];
		if(!$DB->Update($owner->getDbTable(), $arUpdate,$strWhere))
		{
			$DB->Rollback();
			return false;
		}
		if(!$this->shiftLeftRight($right+1,$left-$right-1,$arNode))
		{
			$DB->Rollback();
			return false;
		}		
		$DB->Commit();
		return true;
	}
	
	private function addNode($iParentId,$iID,$iKey,$iLevelUp)
	{
		global $DB;
		/* @var $owner IEntity */
		$owner = $this->owner;
		$arUpdate = array();	
		if($iParentId == $iID)
			throw new \Exception('The target node should not be self.');
		$arNode = $DB->Query(
			'SELECT '.
			$this->clId.','.$this->clDepthLevel.','.$this->clRootId.','.
			$this->clRightMargin.','.$this->clLeftMargin.
			' FROM '.
			$owner->getDbTable().
			' WHERE '.
			'ID='.intval($iParentId).
			' LIMIT 1'
		)->Fetch();
		if(!$arNode)
			throw new \Exception('The node should be exists.');
		if($iKey === false)
			$iKey = $arNode[$this->clRightMargin];
		
		$DB->StartTransaction();
		if(!$this->shiftLeftRight($iKey,2,$arNode))
		{
			$DB->Rollback();
			return false;
		}
		$arUpdate[$this->clLeftMargin] = $iKey;
		$arUpdate[$this->clRightMargin] = $iKey+1;
		$arUpdate[$this->clDepthLevel] = $arNode[$this->clDepthLevel] + $iLevelUp;
		$arUpdate[$this->clRootId] = $arNode[$this->clRootId];
		if(!$DB->Update($owner->getDbTable(), $arUpdate , " WHERE ID = ".intval($iID)))
		{
			$DB->Rollback();
			return false;
		}
		$DB->Commit();
		return true;
	}
	
	public function moveNode($iParentId,$iID,$iKey,$iLevelUp)
	{
		global $DB;
		$owner=$this->owner;
		$arUpdate = array();
		if($iParentId == $iID)
			throw new CException('The target node should not be self.');
		$arOldNode = $DB->Query(
			'SELECT '.
			$this->clId.','.$this->clDepthLevel.','.$this->clRootId.','.$this->clLeftMargin.','.
			$this->clRightMargin.','.$this->clParentId.
			' FROM '.
			$owner->getDbTable().
			' WHERE '.
			'ID='.intval($iID).
			' LIMIT 1'
		)->Fetch();
		if(!$arOldNode)
			throw new \Exception('The old node should be exists.');
		if($arOldNode[$this->clParentId] == $iParentId)
			return true;
		
		$arNode = $DB->Query(
			'SELECT '.
			$this->clId.','.$this->clDepthLevel.','.$this->clRootId.','.$this->clLeftMargin.','.
			$this->clRightMargin.
			' FROM '.
			$owner->getDbTable().
			' WHERE '.
			'ID='.intval($iParentId).
			' LIMIT 1'
		)->Fetch();
		if(!$arNode)
			throw new \Exception('The node should be exists.');
		if($iKey === false)
			$iKey = $arNode[$this->clRightMargin];
		
		$DB->StartTransaction();
		$iLeft = $arOldNode[$this->clLeftMargin];
		$iRight = $arOldNode[$this->clRightMargin];
		$levelDelta = $arNode[$this->clLeftMargin]- $arOldNode[$this->clLeftMargin]+$levelUp;
		if($arNode[$this->clRootId] !== $arOldNode[$this->clRootId])
		{
			foreach(array($this->clLeftMargin,$this->clRightMargin) as $attribute)
			{
				if(!$DB->Update(
					$owner->getDbTable(), 
					array($attribute => $attribute.sprintf('%+d',$right-$left+1)),
					' WHERE '.$attribute.'>='.$iKey.' AND '.$this->clRootId.'='.$arNode[$this->clRootId]
				))
				{
					$DB->Rollback();
					return false;
				}	
			}
			$delta=$iKey-$left;
			$bResult = $DB->Update(
				$owner->getDbTable(),
				array(
					$this->clLeftMargin = $this->clLeftMargin.sprintf('%+d',$delta),
					$this->clRightMargin = $this->clRightMargin.sprintf('%+d',$delta),
					$this->clRootId = $this->clRootId.sprintf('%+d',$levelDelta),
				),
				' WHERE '.$this->clLeftMargin.'>='.$left.' AND '.
				$this->clRightMargin.'<='.$right.' AND '.
				$this->clRootId = $arOldNode[$this->clRootId]
			);
			if(!$bResult)
			{
				$DB->Rollback();
				return false;
			}	
			if(!$this->shiftLeftRight($right+1,$left-$right-1,$arOldNode))
			{
				$DB->Rollback();
				return false;
			}
		}
		else
		{
			$delta=$right-$left+1;
			$bResult = $this->shiftLeftRight($iKey,$delta,$arOldNode);
			if(!$bResult)
			{
				$DB->Rollback();
				return false;
			}
			if($left>=$iKey)
			{
				$left+=$delta;
				$right+=$delta;
			}
			$condition=' WHERE '.$this->clLeftMargin.'>='.$left.' AND '.$this->clRightMargin.'<='.$right.
				' AND '.$this->clRootId.'='.$arNode[$this->clRootId];
			$bResult = $DB->Update(
				$owner->getDbTable(), 
				array($this->clLeftMargin=>$this->clLeftMargin.sprintf('%+d',$levelDelta)),
				$condition
			);
			if(!$bResult)
			{
				$DB->Rollback();
				return false;
			}
			foreach(array($this->clLeftMargin,$this->clRightMargin) as $attribute)
			{
				$condition = ' WHERE '.$attribute.'>='.$left.' AND '.$attribute.'<='.$right.
					' AND '.$this->clRootId.'='.$arOldNode[$this->clRootId];
				$bResult = $owner->update(
					$owner->getDbTable(),
					array($attribute=>$attribute.sprintf('%+d',$iKey-$left)),
					$condition
				);
				if(!$bResult)
				{
					$DB->Rollback();
					return false;
				}
			}
			$bResult = $this->shiftLeftRight($right+1,-$delta,$arNode);
			if(!$bResult)
			{
				$DB->Rollback();
				return false;
			}
		}
		$DB->Commit();
		return true;
	}
	
	private function makeRoot($iID)
	{
		global $DB;
		/* @var $owner IEntity */
		$owner = $this->owner;
		$arUpdate = array();
		$arUpdate[$this->clLeftMargin] = 1;
		$arUpdate[$this->clRightMargin] = 2;
		$arUpdate[$this->clDepthLevel] = 1;
		
		$DB->StartTransaction();
		$iResult = $DB->Update($owner->getDbTable(), $arUpdate, " WHERE ID = ".intval($iID));
		if(!$iResult)
		{
			$DB->Rollback();
			return false;
		}
		if(!$DB->Update($owner->getDbTable(), array($this->clRootId => $iResult), " WHERE ID = ".intval($iID)))
		{
			$DB->Rollback();
			return false;
		}
		$DB->Commit();
		return true;
	}
}
