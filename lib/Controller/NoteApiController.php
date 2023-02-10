<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Andrei Zheksi <andrewsanser@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\MyNotesApp\Controller;

use OCP\IUser;
use OCP\IGroup;
use OCP\IRequest;
use OCP\IGroupManager;
use OCP\AppFramework\ApiController;
use OCA\MyNotesApp\AppInfo\Application;
use OCA\MyNotesApp\Service\NoteService;
use OCP\AppFramework\Http\DataResponse;

class NoteApiController extends ApiController {
	private NoteService $service;
	private ?string $userId;
	private IGroupManager $groupManager;
	private IUser

	public function __construct(IRequest $request,
								NoteService $service,
								?string $userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function create(string $title, string $content): DataResponse {
		return new DataResponse($this->service->create($title, $content,
			$this->userId));
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title,
						   string $content): DataResponse {
		return $this->handleNotFound(function () use ($id, $title, $content) {
			return $this->service->update($id, $title, $content, $this->userId);
		});
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
