<?php namespace BX\Validator\Upload;

interface IUploadFile
{
	public function __construct($file,$type);
	public function checkInvalid();
	public function isEmpty();
	public function checkSize($size);
	public function checkType();
	public function setUploadDir($upload_dir);
	public function getName();
	public function saveFile();
}