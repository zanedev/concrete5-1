<?php

require_once __DIR__ . "/ResolverTestCase.php";

class PageUrlResolverTest extends ResolverTestCase
{

    protected function setUp()
    {
        $path_resolver = new \Concrete\Core\Url\Resolver\PathUrlResolver();
        $this->urlResolver = new \Concrete\Core\Url\Resolver\PageUrlResolver($path_resolver);
    }

    public function testWithPage()
    {
        $path = '/some/collection/path';
        $page = $this->getMock('\Concrete\Core\Page\Page');
        $page->expects($this->once())
                ->method('getCollectionPath')
                ->willReturn($path);

        $this->assertEquals(
            (string) $this->canonicalUrlWithPath($path),
            (string) $this->urlResolver->resolve(array($page)));
    }

    public function testWithHome()
    {
        $page = $this->getMock('\Concrete\Core\Page\Page');
        $page->expects($this->once())
            ->method('getCollectionID')
            ->willReturn(HOME_CID);

        $this->assertEquals(
            (string) $this->canonicalUrlWithPath('/'),
            (string) $this->urlResolver->resolve(array($page)));
    }

    public function testUnapproved()
    {
        $page = $this->getMock('\Concrete\Core\Page\Page');
        $page->expects($this->exactly(2))
            ->method('getCollectionID')
            ->willReturn(1337);

        $this->assertEquals(
            (string) $this->canonicalUrlWithPath('/')->setQuery('cID=1337'),
            (string) $this->urlResolver->resolve(array($page)));
    }

    public function testAlreadyResolved()
    {
        $path = '/some/collection/path';
        $page = $this->getMock('\Concrete\Core\Page\Page');
        $page->expects($this->never())
            ->method('getCollectionPath')
            ->willReturn($path);

        $this->assertEquals($this, $this->urlResolver->resolve(array($page), $this));
    }

    public function testEmptyArguments()
    {
        $this->assertNull($this->urlResolver->resolve(array()));
    }
}
