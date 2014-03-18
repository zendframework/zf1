<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zend/View/Helper/Navigation/Links.php';

/**
 * Tests Zend_View_Helper_Navigation_Links
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_Navigation_LinksTest
    extends Zend_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zend_View_Helper_Navigation_Links';

    /**
     * View helper
     *
     * @var Zend_View_Helper_Navigation_Links
     */
    protected $_helper;

    private $_doctypeHelper;
    private $_oldDoctype;

    public function setUp()
    {
        parent::setUp();

        // doctype fix (someone forgot to clean up after their unit tests)
        $this->_doctypeHelper = $this->_helper->view->doctype();
        $this->_oldDoctype = $this->_doctypeHelper->getDoctype();
        $this->_doctypeHelper->setDoctype(
                Zend_View_Helper_Doctype::HTML4_LOOSE);

        // disable all active pages
        foreach ($this->_helper->findAllByActive(true) as $page) {
            $page->active = false;
        }
    }

    public function tearDown()
    {
        $this->_doctypeHelper->setDoctype($this->_oldDoctype);
    }

    public function testHelperEntryPointWithoutAnyParams()
    {
        $returned = $this->_helper->links();
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testHelperEntryPointWithContainerParam()
    {
        $returned = $this->_helper->links($this->_nav2);
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav2, $returned->getContainer());
    }

    public function testDoNotRenderIfNoPageIsActive()
    {
        $this->assertEquals('', $this->_helper->render());
    }

    public function testDetectRelationFromStringPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->addRel('example', 'http://www.example.com/');
        $found = $this->_helper->findRelation($active, 'rel', 'example');

        $expected = array(
            'type'  => 'Zend_Navigation_Page_Uri',
            'href'  => 'http://www.example.com/',
            'label' => null
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromPageInstancePropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->addRel('example', Zend_Navigation_Page::factory(array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        )));
        $found = $this->_helper->findRelExample($active);

        $expected = array(
            'type'  => 'Zend_Navigation_Page_Uri',
            'href'  => 'http://www.example.com/',
            'label' => 'An example page'
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromArrayPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->addRel('example', array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        ));
        $found = $this->_helper->findRelExample($active);

        $expected = array(
            'type'  => 'Zend_Navigation_Page_Uri',
            'href'  => 'http://www.example.com/',
            'label' => 'An example page'
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromConfigInstancePropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->addRel('example', new Zend_Config(array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        )));
        $found = $this->_helper->findRelExample($active);

        $expected = array(
            'type'  => 'Zend_Navigation_Page_Uri',
            'href'  => 'http://www.example.com/',
            'label' => 'An example page'
        );

        $actual = array(
            'type'  => get_class($found),
            'href'  => $found->getHref(),
            'label' => $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectMultipleRelationsFromArrayPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');

        $active->addRel('alternate', array(
            array(
                'label' => 'foo',
                'uri'   => 'bar'
            ),
            array(
                'label' => 'baz',
                'uri'   => 'bat'
            )
        ));

        $found = $this->_helper->findRelAlternate($active);

        $expected = array('type' => 'array', 'count' => 2);
        $actual = array('type' => gettype($found), 'count' => count($found));
        $this->assertEquals($expected, $actual);
    }

    public function testDetectMultipleRelationsFromConfigPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');

        $active->addRel('alternate', new Zend_Config(array(
            array(
                'label' => 'foo',
                'uri'   => 'bar'
            ),
            array(
                'label' => 'baz',
                'uri'   => 'bat'
            )
        )));

        $found = $this->_helper->findRelAlternate($active);

        $expected = array('type' => 'array', 'count' => 2);
        $actual = array('type' => gettype($found), 'count' => count($found));
        $this->assertEquals($expected, $actual);
    }

    public function testExtractingRelationsFromPageProperties()
    {
        $types = array(
            'alternate', 'stylesheet', 'start', 'next', 'prev', 'contents',
            'index', 'glossary', 'copyright', 'chapter', 'section', 'subsection',
            'appendix', 'help', 'bookmark'
        );

        $samplePage = Zend_Navigation_Page::factory(array(
            'label' => 'An example page',
            'uri'   => 'http://www.example.com/'
        ));

        $active = $this->_helper->findOneByLabel('Page 2');
        $expected = array();
        $actual = array();

        foreach ($types as $type) {
            $active->addRel($type, $samplePage);
            $found = $this->_helper->findRelation($active, 'rel', $type);

            $expected[$type] = $samplePage->getLabel();
            $actual[$type]   = $found->getLabel();

            $active->removeRel($type);
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindStartPageByTraversal()
    {
        $active = $this->_helper->findOneByLabel('Page 2.1');
        $expected = 'Home';
        $actual = $this->_helper->findRelStart($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindStartWhenGivenPageIsTheFirstPage()
    {
        $active = $this->_helper->findOneByLabel('Home');
        $actual = $this->_helper->findRelStart($active);
        $this->assertNull($actual, 'Should not find any start page');
    }

    public function testFindNextPageByTraversalShouldFindChildPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $expected = 'Page 2.1';
        $actual = $this->_helper->findRelNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindNextPageByTraversalShouldFindSiblingPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2.1');
        $expected = 'Page 2.2';
        $actual = $this->_helper->findRelNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindNextPageByTraversalShouldWrap()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2.2');
        $expected = 'Page 2.3';
        $actual = $this->_helper->findRelNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldFindParentPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2.1');
        $expected = 'Page 2';
        $actual = $this->_helper->findRelPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldFindSiblingPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2');
        $expected = 'Page 2.1';
        $actual = $this->_helper->findRelPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldWrap()
    {
        $active = $this->_helper->findOneByLabel('Page 2.3');
        $expected = 'Page 2.2.2';
        $actual = $this->_helper->findRelPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testShouldFindChaptersFromFirstLevelOfPagesInContainer()
    {
        $active = $this->_helper->findOneByLabel('Page 2.3');
        $found = $this->_helper->findRelChapter($active);

        $expected = array('Page 1', 'Page 2', 'Page 3', 'Zym');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindingChaptersShouldExcludeSelfIfChapter()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $found = $this->_helper->findRelChapter($active);

        $expected = array('Page 1', 'Page 3', 'Zym');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindSectionsWhenActiveChapterPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $found = $this->_helper->findRelSection($active);
        $expected = array('Page 2.1', 'Page 2.2', 'Page 2.3');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindSectionsWhenActivePageIsASection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2');
        $found = $this->_helper->findRelSection($active);
        $this->assertNull($found);
    }

    public function testDoNotFindSectionsWhenActivePageIsASubsection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2.1');
        $found = $this->_helper->findRelation($active, 'rel', 'section');
        $this->assertNull($found);
    }

    public function testFindSubsectionWhenActivePageIsSection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2');
        $found = $this->_helper->findRelSubsection($active);

        $expected = array('Page 2.2.1', 'Page 2.2.2');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindSubsectionsWhenActivePageIsASubSubsection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2.1');
        $found = $this->_helper->findRelSubsection($active);
        $this->assertNull($found);
    }

    public function testDoNotFindSubsectionsWhenActivePageIsAChapter()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $found = $this->_helper->findRelSubsection($active);
        $this->assertNull($found);
    }

    public function testFindRevSectionWhenPageIsSection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2');
        $found = $this->_helper->findRevSection($active);
        $this->assertEquals('Page 2', $found->getLabel());
    }

    public function testFindRevSubsectionWhenPageIsSubsection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2.1');
        $found = $this->_helper->findRevSubsection($active);
        $this->assertEquals('Page 2.2', $found->getLabel());
    }

    public function testAclFiltersAwayPagesFromPageProperty()
    {
        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role('member'));
        $acl->addRole(new Zend_Acl_Role('admin'));
        $acl->add(new Zend_Acl_Resource('protected'));
        $acl->allow('admin', 'protected');
        $this->_helper->setAcl($acl);
        $this->_helper->setRole($acl->getRole('member'));

        $samplePage = Zend_Navigation_Page::factory(array(
            'label'    => 'An example page',
            'uri'      => 'http://www.example.com/',
            'resource' => 'protected'
        ));

        $active = $this->_helper->findOneByLabel('Home');
        $expected = array(
            'alternate'  => false,
            'stylesheet' => false,
            'start'      => false,
            'next'       => 'Page 1',
            'prev'       => false,
            'contents'   => false,
            'index'      => false,
            'glossary'   => false,
            'copyright'  => false,
            'chapter'    => 'array(4)',
            'section'    => false,
            'subsection' => false,
            'appendix'   => false,
            'help'       => false,
            'bookmark'   => false
        );
        $actual = array();

        foreach ($expected as $type => $discard) {
            $active->addRel($type, $samplePage);

            $found = $this->_helper->findRelation($active, 'rel', $type);
            if (null === $found) {
                $actual[$type] = false;
            } elseif (is_array($found)) {
                $actual[$type] = 'array(' . count($found) . ')';
            } else {
                $actual[$type] = $found->getLabel();
            }
        }

        $this->assertEquals($expected, $actual);
    }

    public function testAclFiltersAwayPagesFromContainerSearch()
    {
        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role('member'));
        $acl->addRole(new Zend_Acl_Role('admin'));
        $acl->add(new Zend_Acl_Resource('protected'));
        $acl->allow('admin', 'protected');
        $this->_helper->setAcl($acl);
        $this->_helper->setRole($acl->getRole('member'));

        $oldContainer = $this->_helper->getContainer();
        $container = $this->_helper->getContainer();
        $iterator = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $page) {
            $page->resource = 'protected';
        }
        $this->_helper->setContainer($container);

        $active = $this->_helper->findOneByLabel('Home');
        $search = array(
            'start'      => 'Page 1',
            'next'       => 'Page 1',
            'prev'       => 'Page 1.1',
            'chapter'    => 'Home',
            'section'    => 'Page 1',
            'subsection' => 'Page 2.2'
        );

        $expected = array();
        $actual = array();

        foreach ($search as $type => $active) {
            $expected[$type] = false;

            $active = $this->_helper->findOneByLabel($active);
            $found = $this->_helper->findRelation($active, 'rel', $type);

            if (null === $found) {
                $actual[$type] = false;
            } elseif (is_array($found)) {
                $actual[$type] = 'array(' . count($found) . ')';
            } else {
                $actual[$type] = $found->getLabel();
            }
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindRelationMustSpecifyRelOrRev()
    {
        $active = $this->_helper->findOneByLabel('Home');
        try {
            $this->_helper->findRelation($active, 'foo', 'bar');
            $this->fail('An invalid value was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('Invalid argument: $rel', $e->getMessage());
        }
    }

    public function testRenderLinkMustSpecifyRelOrRev()
    {
        $active = $this->_helper->findOneByLabel('Home');
        try {
            $this->_helper->renderLink($active, 'foo', 'bar');
            $this->fail('An invalid value was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('Invalid relation attribute', $e->getMessage());
        }
    }

    public function testFindAllRelations()
    {
        $expectedRelations = array(
            'alternate'  => array('Forced page'),
            'stylesheet' => array('Forced page'),
            'start'      => array('Forced page'),
            'next'       => array('Forced page'),
            'prev'       => array('Forced page'),
            'contents'   => array('Forced page'),
            'index'      => array('Forced page'),
            'glossary'   => array('Forced page'),
            'copyright'  => array('Forced page'),
            'chapter'    => array('Forced page'),
            'section'    => array('Forced page'),
            'subsection' => array('Forced page'),
            'appendix'   => array('Forced page'),
            'help'       => array('Forced page'),
            'bookmark'   => array('Forced page'),
            'canonical'  => array('Forced page'),
            'home'       => array('Forced page')
        );

        // build expected result
        $expected = array(
            'rel' => $expectedRelations,
            'rev' => $expectedRelations
        );

        // find active page and create page to use for relations
        $active = $this->_helper->findOneByLabel('Page 1');
        $forcedRelation = new Zend_Navigation_Page_Uri(array(
            'label' => 'Forced page',
            'uri'   => '#'
        ));

        // add relations to active page
        foreach ($expectedRelations as $type => $discard) {
            $active->addRel($type, $forcedRelation);
            $active->addRev($type, $forcedRelation);
        }

        // build actual result
        $actual = $this->_helper->findAllRelations($active);
        foreach ($actual as $attrib => $relations) {
            foreach ($relations as $type => $pages) {
                foreach ($pages as $key => $page) {
                    $actual[$attrib][$type][$key] = $page->getLabel();
                }
            }
        }

        $this->assertEquals($expected, $actual);
    }

    private function _getFlags()
    {
        return array(
            Zend_View_Helper_Navigation_Links::RENDER_ALTERNATE  => 'alternate',
            Zend_View_Helper_Navigation_Links::RENDER_STYLESHEET => 'stylesheet',
            Zend_View_Helper_Navigation_Links::RENDER_START      => 'start',
            Zend_View_Helper_Navigation_Links::RENDER_NEXT       => 'next',
            Zend_View_Helper_Navigation_Links::RENDER_PREV       => 'prev',
            Zend_View_Helper_Navigation_Links::RENDER_CONTENTS   => 'contents',
            Zend_View_Helper_Navigation_Links::RENDER_INDEX      => 'index',
            Zend_View_Helper_Navigation_Links::RENDER_GLOSSARY   => 'glossary',
            Zend_View_Helper_Navigation_Links::RENDER_CHAPTER    => 'chapter',
            Zend_View_Helper_Navigation_Links::RENDER_SECTION    => 'section',
            Zend_View_Helper_Navigation_Links::RENDER_SUBSECTION => 'subsection',
            Zend_View_Helper_Navigation_Links::RENDER_APPENDIX   => 'appendix',
            Zend_View_Helper_Navigation_Links::RENDER_HELP       => 'help',
            Zend_View_Helper_Navigation_Links::RENDER_BOOKMARK   => 'bookmark',
            Zend_View_Helper_Navigation_Links::RENDER_CUSTOM     => 'canonical'
        );
    }

    public function testSingleRenderFlags()
    {
        $active = $this->_helper->findOneByLabel('Home');
        $active->active = true;

        $expected = array();
        $actual   = array();

        // build expected and actual result
        foreach ($this->_getFlags() as $newFlag => $type) {
            // add forced relation
            $active->addRel($type, 'http://www.example.com/');
            $active->addRev($type, 'http://www.example.com/');

            $this->_helper->setRenderFlag($newFlag);
            $expectedOutput = '<link '
                              . 'rel="' . $type . '" '
                              . 'href="http://www.example.com/">' . constant($this->_helperName.'::EOL')
                            . '<link '
                              . 'rev="' . $type . '" '
                              . 'href="http://www.example.com/">';
            $actualOutput = $this->_helper->render();

            $expected[$type] = $expectedOutput;
            $actual[$type]   = $actualOutput;

            // remove forced relation
            $active->removeRel($type);
            $active->removeRev($type);
        }

        $this->assertEquals($expected, $actual);
    }

    public function testRenderFlagBitwiseOr()
    {
        $newFlag = Zend_View_Helper_Navigation_Links::RENDER_NEXT |
                   Zend_View_Helper_Navigation_Links::RENDER_PREV;
        $this->_helper->setRenderFlag($newFlag);
        $active = $this->_helper->findOneByLabel('Page 1.1');
        $active->active = true;

        // test data
        $expected = '<link rel="next" href="page2" title="Page 2">'
                  . constant($this->_helperName.'::EOL')
                  . '<link rel="prev" href="page1" title="Page 1">';
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testIndenting()
    {
        $active = $this->_helper->findOneByLabel('Page 1.1');
        $newFlag = Zend_View_Helper_Navigation_Links::RENDER_NEXT |
                   Zend_View_Helper_Navigation_Links::RENDER_PREV;
        $this->_helper->setRenderFlag($newFlag);
        $this->_helper->setIndent('  ');
        $active->active = true;

        // build expected and actual result
        $expected = '  <link rel="next" href="page2" title="Page 2">'
                  . constant($this->_helperName.'::EOL')
                  . '  <link rel="prev" href="page1" title="Page 1">';
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testSetMaxDepth()
    {
        $this->_helper->setMaxDepth(1);
        $this->_helper->findOneByLabel('Page 2.3.3')->setActive(); // level 2
        $flag = Zend_View_Helper_Navigation_Links::RENDER_NEXT;

        $expected = '<link rel="next" href="page2/page2_3/page2_3_1" title="Page 2.3.1">';
        $actual = $this->_helper->setRenderFlag($flag)->render();

        $this->assertEquals($expected, $actual);
    }

    public function testSetMinDepth()
    {
        $this->_helper->setMinDepth(2);
        $this->_helper->findOneByLabel('Page 2.3')->setActive(); // level 1
        $flag = Zend_View_Helper_Navigation_Links::RENDER_NEXT;

        $expected = '';
        $actual = $this->_helper->setRenderFlag($flag)->render();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-8874
     */
    public function testRenderingWithoutWhitespace()
    {
        $active = $this->_helper->findOneByLabel('Page 1.1');
        $newFlag = Zend_View_Helper_Navigation_Links::RENDER_NEXT |
                   Zend_View_Helper_Navigation_Links::RENDER_PREV;
        $this->_helper->setRenderFlag($newFlag);
        $this->_helper->setIndent('  ');
        $active->active = true;

        $this->_helper->setFormatOutput(false);

        // build expected and actual result
        $expected = '<link rel="next" href="page2" title="Page 2">'
                  . '<link rel="prev" href="page1" title="Page 1">';
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }
}
