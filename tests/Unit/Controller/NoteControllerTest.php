<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Andrei Zheksi <andrewsanser@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\MyNotesApp\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Http;
use OCP\IRequest;

use OCA\MyNotesApp\Service\NoteNotFound;
use OCA\MyNotesApp\Service\NoteService;
use OCA\MyNotesApp\Controller\NoteController;
use OCA\MyNotesApp\Db\Note;

class NoteControllerTest extends TestCase {
	protected NoteController $controller;
	protected string $userId = 'john';
	protected $service;
	protected $request;

	public function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(NoteService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new NoteController($this->request, $this->service, $this->userId);
	}

	public function testUpdate(): void {
		$note = [
			'id' => 3,
			'title' => 'title',
			'content' => 'content'
		];//'just check if this value is returned correctly';
		$this->service->expects($this->once())
			->method('update')
			->with($this->equalTo(3),
				$this->equalTo('title'),
				$this->equalTo('content'))//,
				//    $this->equalTo($this->userId))
			// ->will($this->returnValue($note));
			->will($this->returnValue(new Note()));

		$result = $this->controller->update(3, 'title', 'content');
		
		var_dump($result->getData());

		$this->assertEquals((object)$note, gettype($result->getData()));
	}


	public function testUpdateNotFound(): void {
		// test the correct status code if no note is found
		$this->service->expects($this->once())
			->method('update')
			->will($this->throwException(new NoteNotFound()));

		$result = $this->controller->update(3, 'title', 'content');

		$this->assertEquals(Http::STATUS_NOT_FOUND, $result->getStatus());
	}
}
