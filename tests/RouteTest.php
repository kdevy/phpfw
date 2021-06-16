<?php

use PHPUnit\Framework\TestCase;
use Framework\Route;

require_once("../config/config.php");
require_once("../src/functions/debug.php");
require_once("../src/functions/util.php");

class RouteTest extends TestCase
{
    /**
     * コンストラクタのテストケース
     *
     * @return void
     */
    public function testConstruct()
    {
        /**
         * ドキュメントルート
         */
        // パス文字列で初期化するパターン
        $route = new Route("/");
        $this->assertSame("/index/index", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("index", $route->getActionName());
        $this->assertSame("IndexAction", $route->getActionClassName());
        // 第二引数で初期化するパターン
        $route = new Route("index", "index");
        $this->assertSame("/index/index", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("index", $route->getActionName());
        $this->assertSame("IndexAction", $route->getActionClassName());
        // 配列で初期化するパターン
        $route = new Route(["index", "index"]);
        $this->assertSame("/index/index", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("index", $route->getActionName());
        $this->assertSame("IndexAction", $route->getActionClassName());
        // クエリパラメータ付きパス
        $route = new Route("/?key=value");
        $this->assertSame("/index/index", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("index", $route->getActionName());
        $this->assertSame("IndexAction", $route->getActionClassName());
        // 絶対パス
        $this->assertSame("index", $route->getTemplateName());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . TEMPLATES_DIRNAME . DS . "index.html", $route->getTemplateAbsPath());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . ACTIONS_DIRNAME . DS . $route->getActionClassName() . ".php", $route->getActionAbsPath());
        /**
         * 一階層
         */
        // パス文字列で初期化するパターン
        $route = new Route("/hoge");
        $this->assertSame("/index/hoge", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("hoge", $route->getActionName());
        $this->assertSame("HogeAction", $route->getActionClassName());
        // 第二引数で初期化するパターン
        $route = new Route("index", "hoge");
        $this->assertSame("/index/hoge", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("hoge", $route->getActionName());
        $this->assertSame("HogeAction", $route->getActionClassName());
        // 配列で初期化するパターン
        $route = new Route(["index", "hoge"]);
        $this->assertSame("/index/hoge", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("hoge", $route->getActionName());
        $this->assertSame("HogeAction", $route->getActionClassName());
        // クエリパラメータ付きパス
        $route = new Route("/hoge?key=value");
        $this->assertSame("/index/hoge", strval($route));
        $this->assertSame("index", $route->getModuleName());
        $this->assertSame("hoge", $route->getActionName());
        $this->assertSame("HogeAction", $route->getActionClassName());
        // 絶対パス
        $this->assertSame("hoge", $route->getTemplateName());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . TEMPLATES_DIRNAME . DS . "hoge.html", $route->getTemplateAbsPath());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . ACTIONS_DIRNAME . DS . $route->getActionClassName() . ".php", $route->getActionAbsPath());
        /**
         * 二階層
         */
        // パス文字列で初期化するパターン
        $route = new Route("/hoge/fuga");
        $this->assertSame("/hoge/fuga", strval($route));
        $this->assertSame("hoge", $route->getModuleName());
        $this->assertSame("fuga", $route->getActionName());
        $this->assertSame("FugaAction", $route->getActionClassName());
        // 第二引数で初期化するパターン
        $route = new Route("hoge", "fuga");
        $this->assertSame("/hoge/fuga", strval($route));
        $this->assertSame("hoge", $route->getModuleName());
        $this->assertSame("fuga", $route->getActionName());
        $this->assertSame("FugaAction", $route->getActionClassName());
        // 配列で初期化するパターン
        $route = new Route(["hoge", "fuga"]);
        $this->assertSame("/hoge/fuga", strval($route));
        $this->assertSame("hoge", $route->getModuleName());
        $this->assertSame("fuga", $route->getActionName());
        $this->assertSame("FugaAction", $route->getActionClassName());
        // クエリパラメータ付きパス
        $route = new Route("/hoge/fuga?key=value");
        $this->assertSame("/hoge/fuga", strval($route));
        $this->assertSame("hoge", $route->getModuleName());
        $this->assertSame("fuga", $route->getActionName());
        $this->assertSame("FugaAction", $route->getActionClassName());
        // 絶対パス
        $this->assertSame("fuga", $route->getTemplateName());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . TEMPLATES_DIRNAME . DS . "fuga.html", $route->getTemplateAbsPath());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . ACTIONS_DIRNAME . DS . $route->getActionClassName() . ".php", $route->getActionAbsPath());

        /**
         * キャメルケース
         */
        // ハイフン
        $route = new Route("/hoge/this-is-camel-case");
        $this->assertSame("/hoge/this-is-camel-case", strval($route));
        $this->assertSame("hoge", $route->getModuleName());
        $this->assertSame("this-is-camel-case", $route->getActionName());
        $this->assertSame("ThisIsCamelCaseAction", $route->getActionClassName());
        // 絶対パス
        $this->assertSame("this-is-camel-case", $route->getTemplateName());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . TEMPLATES_DIRNAME . DS . "this-is-camel-case.html", $route->getTemplateAbsPath());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . ACTIONS_DIRNAME . DS . $route->getActionClassName() . ".php", $route->getActionAbsPath());
        // アンダースコア
        $route = new Route("/hoge/this_is_camel_case");
        $this->assertSame("/hoge/this_is_camel_case", strval($route));
        $this->assertSame("hoge", $route->getModuleName());
        $this->assertSame("this_is_camel_case", $route->getActionName());
        $this->assertSame("ThisIsCamelCaseAction", $route->getActionClassName());
        // 絶対パス
        $this->assertSame("this-is-camel-case", $route->getTemplateName());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . TEMPLATES_DIRNAME . DS . "this-is-camel-case.html", $route->getTemplateAbsPath());
        $this->assertSame(MODULE_DIR . DS . $route->getModuleName() .
            DS . ACTIONS_DIRNAME . DS . $route->getActionClassName() . ".php", $route->getActionAbsPath());
    }

    /**
     * 不正なパス階層のテストケース
     *
     * @return void
     */
    public function testConstructTowHierarchy()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Root path is up to two hierarchy.");
        $route = new Route("/hoge/fuga/piyo");
    }

    /**
     * 不正な引数のテストケース
     *
     * @return void
     */
    public function testConstructInvalidArguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Arguments that cannot be parsed.");
        $route = new Route(null);
    }

    /**
     * 不正な引数のテストケー2
     *
     * @return void
     */
    public function testConstructInvalidArguments2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Arguments that cannot be parsed.");
        $route = new Route(true);
    }

    /**
     * 不正な引数のテストケース3
     *
     * @return void
     */
    public function testConstructInvalidArguments3()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Arguments that cannot be parsed.");
        $route = new Route(10);
    }
}
