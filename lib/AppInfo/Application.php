<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Andrei Zheksi <andrewsanser@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\MyNotesApp\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'mynotesapp';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
