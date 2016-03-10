<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlTitle;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlTitle using URI's with
 * different format.
 */
class HtmlTitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Simulate home uri
     * @var string
     */
    private $emptyUri    = "/";

    /**
     * Simulate home uri with *.php ending.
     * @var string
     */
    private $homeUri     = "/Index.php";

    /**
     * Standard uri
     * @var string
     */
    private $standardUri = "https:://www.simphotonics.com/user/User-LoginForm.php";

    /**
     * Laravel style uri
     * @var string
     */
    private $laravelUri  = "/company/profile/about_us";

    /**
     * Uri containig query
     * @var string
     */
    private $queryUri    = "https:://simphotonics.com/post?url=http://google.com/&amp;message=This%20is%20my%20post";


    public function testEmptyUri()
    {
        $this->checktitle($this->emptyUri, 'SIMPHOTONICS - Home');
    }

    public function testHomeUri()
    {
        $this->checktitle($this->homeUri, 'SIMPHOTONICS - Home');
    }

    public function testStandardUri()
    {
        $this->checktitle($this->standardUri, 'SIMPHOTONICS - User Login Form');
    }

    public function testLaravelUri()
    {
        $this->checktitle($this->laravelUri, 'SIMPHOTONICS - About Us');
    }

    public function testQueryUri()
    {
        $this->checktitle($this->queryUri, 'SIMPHOTONICS - Post');
    }

    /**
     * Tests title string.
     * @param  string $uri Static variable
     * @return void
     */
    private function checkTitle($uri, $expectedTitle)
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $t = new HtmlTitle('SIMPHOTONICS');
        $this->assertEquals("<title>$expectedTitle</title>", "$t");
    }
}
