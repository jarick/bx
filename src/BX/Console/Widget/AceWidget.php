<?php namespace BX\Console\Widget;
use BX\MVC\Widget;

class AceWidget extends Widget
{
	/**
	 * @var string
	 */
	protected $dom = 'editor';
	/**
	 * @var string
	 */
	protected $theme = 'ace/theme/monokai';
	/**
	 * @var string
	 */
	protected $mode = 'ace/mode/php';
	/**
	 * @var string
	 */
	protected $editor = 'editor';
	/**
	 * @var integer
	 */
	protected $min_lines = 20;
	/**
	 * @var integer
	 */
	protected $max_lines = 20;
	/**
	 * @var boolean
	 */
	protected $auto_scroll_editor_into_view = true;
	/**
	 * @var boolean
	 */
	protected $focus = true;
	/**
	 * @var boolean
	 */
	protected $behaviours_enabled = false;
	/**
	 * @var integer
	 */
	protected $font_size = 15;
	/**
	 * Set theme
	 * @param string $theme
	 * @return \BX\Console\Widget\Ace
	 */
	public function setTheme($theme)
	{
		$this->theme = $theme;
		return $this;
	}
	/**
	 * Set dom
	 * @param string $dom
	 * @return \BX\Console\Widget\Ace
	 */
	public function setDom($dom)
	{
		$this->dom = $dom;
		return $this;
	}
	/**
	 * Set editor
	 * @param type $editor
	 * @return \BX\Console\Widget\Ace
	 */
	public function setEditor($editor)
	{
		$this->editor = $editor;
		return $this;
	}
	/**
	 * Run
	 */
	public function run()
	{
		$this->view()->meta['footer_js'][] = '/js/ace/ace.js';
		echo "<div id=\"$this->dom\"></div>\n";
		echo "<script type=\"text/javascript\">$(function(){\n";
		echo "window.$this->editor = ace.edit(\"$this->dom\");\n";
		echo "window.$this->editor.setTheme(\"$this->theme\");\n";
		echo "window.$this->editor.getSession().setMode(\"$this->mode\");\n";
		echo "window.$this->editor.setOption(\"maxLines\", $this->max_lines);\n";
		echo "window.$this->editor.setOption(\"minLines\", $this->min_lines);\n";
		echo "window.$this->editor.setAutoScrollEditorIntoView($this->auto_scroll_editor_into_view);\n";
		echo "window.$this->editor.setAutoScrollEditorIntoView($this->auto_scroll_editor_into_view);\n";
		echo "window.$this->editor.setBehavioursEnabled($this->behaviours_enabled);\n";
		echo "window.$this->editor.setFontSize($this->font_size);\n";
		if ($this->focus){
			echo "window.$this->editor.focus();var n = window.$this->editor.getSession().getValue().split(\"\\n\").length;window.$this->editor.gotoLine(n);\n";
		}
		echo "});</script>";
	}
}