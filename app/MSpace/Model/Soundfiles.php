<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 24.02.16
 * Time: 21:34
 */

namespace MSpace\Model;


class Soundfiles extends ModelBase {
	public $name;
	public $filename;
	public $type;

	const TYPE_OTHER = 1;
	const TYPE_MUSIC = 2;

	private $directory = 'uploads/';

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getFilename() {
		return $this->directory . $this->filename;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	private function getRandomFileName() {
		do {
			$randomName = md5( time() );
			$fullyQualifiedName = $this->directory . $randomName;
		} while( file_exists($fullyQualifiedName) );

		return $randomName;
	}

	public function beforeSave(){

	}

	private function getTypeFromName()  {
		if( strpos($this->name, '.mp3') == strlen($this->name) - 4 ) {
			return self::TYPE_MUSIC;
		}

		return self::TYPE_OTHER;
	}

	public function saveFromUpload($file) {
		$tempTame = $file->getTempName();

		if( empty($tempTame) ) {
			return false;
		}

		$this->filename = $this->getRandomFileName();
		$this->name = $file->getName();
		$this->type = $this->getTypeFromName();

		$uploadFileName = $this->directory . $this->filename;

		if(move_uploaded_file($tempTame, $uploadFileName)) {
			$this->save();

			return true;
		}
		else {
			return false;
		}
	}
} 