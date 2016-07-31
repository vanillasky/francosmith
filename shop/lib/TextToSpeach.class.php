<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TextToSpeach
{

	private $ttsURL = 'http://translate.google.com/translate_tts?ie=%s&tl=%s&q=%s&textlen=%s&idx=0&total=1';
	private $charset = 'UTF-8';
	private $language = 'ko';
	const MAX_TEXT_LENGTH = 100;

	public function __construct($charset = 'UTF-8')
	{
		$this->charset = $charset;
	}

	public function setLanguage($language)
	{
		$this->language = $language;
	}

	public function getURL($text)
	{
		$config = Core::config('global');
		$text = mb_substr(preg_replace('/\s+/', ' ', $text), 0, self::MAX_TEXT_LENGTH, $config['charset']);
		$urlEncodedText = str_replace(array('%2C', '%3A'), array(',', ':'), rawurlencode(iconv($config['charset'], $this->charset, $text)));
		return sprintf($this->ttsURL, $this->charset, $this->language, $urlEncodedText, mb_strlen($text, $config['charset']));
	}

}