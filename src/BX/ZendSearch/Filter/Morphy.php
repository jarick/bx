<?php namespace BX\ZendSearch\Filter;
use ZendSearch\Lucene\Analysis\TokenFilter\LowerCaseUtf8;
use ZendSearch\Lucene\Analysis\Token;

class Morphy extends LowerCaseUtf8
{
	use \BX\String\StringTrait;
	/**
	 * @var \phpMorphy
	 */
	private $morphy;
	/**
	 * @var string
	 */
	private $charset;
	/**
	 * Constructor
	 * @param array $dirs
	 * @param string $charset
	 */
	public function __construct($dirs, $charset)
	{
		$this->morphy = new \phpMorphy($dirs, 'ru');
		$this->charset = $charset;
	}
	/**
	 * Normalize
	 * @param \ZendSearch\Lucene\Analysis\Token $token
	 * @return null|\ZendSearch\Lucene\Analysis\Token
	 */
	public function normalize(Token $token)
	{
		$pseudo_root = $this->morphy->getPseudoRoot($this->string()->toUpper($token->getTermText()));
		if ($pseudo_root === false) {
			$new_str = $this->string()->toUpper($token->getTermText());
		} else {
			$new_str = $pseudo_root[0];
		}
		if (strlen($new_str) < 3) {
			return null;
		}
		$new_token = new Token(
			$new_str, $token->getStartOffset(), $token->getEndOffset()
		);
		$new_token->setPositionIncrement($token->getPositionIncrement());
		return $new_token;
	}
}