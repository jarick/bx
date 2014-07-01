<?php namespace BX\Form\Field;
use BX\Form\Field\TextField;

class TextareaField extends TextField
{
	public function renderSingle($css_class = '',$placeholder = '',$row = 3)
	{
		echo '<div class="form-group '.$css_class;
		if ($this->hasErrors()){
			echo ' has-error';
		}
		echo '">';
		echo '<label class="control-label" for="'.$this->getId().'">'
		.$this->string()->escape($this->label);
		if ($this->required){
			echo '<span class="text-red">*</span>';
		}
		echo '</label>'
		.'<textarea class="form-control" rows="'.intval($row).'"'
		.' name="'.$this->string()->escape($this->name).'"'
		.' id="'.$this->getId().'"'
		.' tabindex='.$this->tabindex
		.' placeholder="'.$placeholder.'"';
		if ($this->max > 0){
			echo ' maxlength="'.$this->max.'"';
		}
		echo '>';
		echo $this->string()->escape($this->value);
		echo '</textarea>';
		echo '</div>';
	}
}