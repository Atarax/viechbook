<?php

namespace MSpace\Controller;
use MSpace\Model\Soundfiles;
use Phalcon\Exception;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\View;
use MSpace\Model\SecurityTokens;
use MSpace\Model\Users;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 16.12.14
 * Time: 00:24
 */

class SoundfilesController extends ControllerBase {

 	public function get_musicAction() {
		$tracks = [];

		/** @var Soundfiles[] $soundfiles */
		$soundfiles = Soundfiles::find();

		foreach($soundfiles as $file) {
			$newTrack = [
				'id' => $file->getId(),
				'name' => $file->getName(),
				'filename' => '/' . $file->getFilename(),
			];
			$tracks[] = $newTrack;
		}


		$this->setJsonResponse();
		return $tracks;
	}

	public function add_musicAction() {
		//Check if the user has uploaded files
		if ($this->request->hasFiles() == true) {
			//Print the real file names and their sizes
			foreach ($this->request->getUploadedFiles() as $file){
				$tempTame = $file->getTempName();

				if( empty($tempTame) ) {
					continue;
				}

				$soundfile = new Soundfiles();
				$saveResult = $soundfile->saveFromUpload($file);

				if($saveResult) {
					$this->flash->success('Successfully uploaded file!');
				} else {
					$this->flash->error('Error uploading file :(');
				}

			}
		}

		$this->dispatcher->forward([
			'controller' => 'users',
			'action' => 'music'
		]);
	}

	public function deleteAction($fileId) {
		/** @var Soundfiles $soundFile */
		$soundFile = Soundfiles::findFirst($fileId);

		unlink( $soundFile->getFilename() );

		$soundFile->delete();

		$this->dispatcher->forward([
			'controller' => 'users',
			'action' => 'music'
		]);
	}
}
