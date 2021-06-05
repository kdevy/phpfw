<?php

use Framework\Action\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * common/CommonActionを実装することで、存在しない全てのページにアクセスした場合にここで処理することができる。
 * しかし、自分でHttpErrorを呼び出さない限り、notfoundが出なくなるので注意。
 */