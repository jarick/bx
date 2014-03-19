<?php 
namespace BX\main\widgets;
use BX;

class AdminFilterWidget
{
	public $entity;
	protected $sBuffer = '';
	protected $arLabel = array();
	
	public function show()
	{
		echo $this->sBuffer;
	}
	
	public function getLabels()
	{
		return $this->arLabel;
	}
	
	public function beginForm()
	{
		global $APPLICATION;
?>		
		<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?php 		
	}
	
	public function widget()
	{
		return $this;
	}
	
	public function endForm()
	{
?>
		</form>
<?php 		
	}
	
	public function getText($sField,$sValue,$sLabel)
	{
		$this->arLabel[] = $sLabel;
		ob_start();
?>
	<tr>
		<td><?php $this->entity->printLabel($sField);?>:</td>
		<td>
			<input type="text" name="filter_<?php echo ToLower($sField)?>" value="<?php BX::text($sValue)?>" size="40">
		</td>
	</tr>
<?php 
		$this->sBuffer .= ob_get_contents();
		ob_end_clean();
	}
	
	public function getSelect($sField,$sValue,$arValues,$sLabel)
	{
		$this->arLabel[] = $sLabel;
		ob_start();		
?>		
	<tr>
		<td><?php $this->entity->printLabel($sField);?>:</td>
		<td>
			<select name="filter_<?php echo ToLower($sField)?>">
				<?php foreach($arValues as $sKey => $sText):?>
				<option value="<?php BX::text($sKey)?>" <?php if($sValue === $sText):?>selected="selected"<?php endif?> >
					<?php BX::text($sText)?>
				</option>
				<?php endforeach?>
			</select>
		</td>
	</tr>
<?php
		$this->sBuffer .= ob_get_contents();
		ob_end_clean();
	}
}
?>