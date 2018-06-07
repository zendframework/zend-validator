<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use PHPUnit\Framework\TestCase;
use Zend\Validator\Hostname;

/**
 * @group      Zend_Validator
 */
class HostnameTest extends TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Hostname
     */
    protected $validator;

    /**
     * @var string
     */
    protected $origEncoding;

    public function setUp()
    {
        $this->origEncoding = PHP_VERSION_ID < 50600
            ? iconv_get_encoding('internal_encoding')
            : ini_get('default_charset');
        $this->validator = new Hostname();
    }

    /**
     * Reset iconv
     */
    public function tearDown()
    {
        if (PHP_VERSION_ID < 50600) {
            iconv_set_encoding('internal_encoding', $this->origEncoding);
        } else {
            ini_set('default_charset', $this->origEncoding);
        }
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = [
            [Hostname::ALLOW_IP, true, ['1.2.3.4', '10.0.0.1', '255.255.255.255']],
            [Hostname::ALLOW_IP, false, ['1.2.3.4.5', '0.0.0.256']],
            [Hostname::ALLOW_DNS, true, ['example.com', 'example.museum', 'd.hatena.ne.jp', 'example.photography']],
            [Hostname::ALLOW_DNS, false, ['localhost', 'localhost.localdomain', '1.2.3.4', 'domain.invalid']],
            [Hostname::ALLOW_LOCAL, true, ['localhost', 'localhost.localdomain', 'example.com']],
            [Hostname::ALLOW_ALL, true, ['localhost', 'example.com', '1.2.3.4']],
            [Hostname::ALLOW_LOCAL, false, ['local host', 'example,com', 'exam_ple.com']]
        ];
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals(
                    $element[1],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    public function testCombination()
    {
        $valuesExpected = [
            [Hostname::ALLOW_DNS | Hostname::ALLOW_LOCAL, true, ['domain.com', 'localhost', 'local.localhost']],
            [Hostname::ALLOW_DNS | Hostname::ALLOW_LOCAL, false, ['1.2.3.4', '255.255.255.255']],
            [Hostname::ALLOW_DNS | Hostname::ALLOW_IP, true, ['1.2.3.4', '255.255.255.255']],
            [Hostname::ALLOW_DNS | Hostname::ALLOW_IP, false, ['localhost', 'local.localhost']]
            ];
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals(
                    $element[1],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * Ensure the dash character tests work as expected
     *
     */
    public function testDashes()
    {
        $valuesExpected = [
            [Hostname::ALLOW_DNS, true, ['domain.com', 'doma-in.com']],
            [Hostname::ALLOW_DNS, false, ['-domain.com', 'domain-.com', 'do--main.com']]
            ];
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals(
                    $element[1],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * Ensure the underscore character tests work as expected
     *
     */
    public function testUnderscores()
    {
        $valuesExpected = [
            [Hostname::ALLOW_DNS, true, [
                '_subdomain.domain.com', 'subdomain_.domain.com', 'sub_domain.domain.com', 'sub__domain.domain.com'
            ]],
            [Hostname::ALLOW_DNS, false, ['_domain.com', 'domain_.com', 'do_main.com']]
        ];
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals(
                    $element[1],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensure the IDN check works as expected
     *
     */
    public function testIDN()
    {
        $validator = new Hostname();

        // Check IDN matching
        $valuesExpected = [
            [true, ['bürger.de', 'hãllo.de', 'hållo.se']],
            [true, ['bÜrger.de', 'hÃllo.de', 'hÅllo.se']],
            [false, ['hãllo.se', 'bürger.lt', 'hãllo.uk']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }

        // Check no IDN matching
        $validator->useIdnCheck(false);
        $valuesExpected = [
            [false, ['bürger.de', 'hãllo.de', 'hållo.se']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }

        // Check setting no IDN matching via constructor
        unset($validator);
        $validator = new Hostname(Hostname::ALLOW_DNS, false);
        $valuesExpected = [
            [false, ['bürger.de', 'hãllo.de', 'hållo.se']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * Ensure the IDN check works on resource files as expected
     *
     */
    public function testResourceIDN()
    {
        $validator = new Hostname();

        // Check IDN matching
        $valuesExpected = [
            [true, ['bürger.com', 'hãllo.com', 'hållo.com', 'plekitööd.ee']],
            [true, ['bÜrger.com', 'hÃllo.com', 'hÅllo.com', 'plekitÖÖd.ee']],
            [false, ['hãllo.lt', 'bürger.lt', 'hãllo.lt']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }

        // Check no IDN matching
        $validator->useIdnCheck(false);
        $valuesExpected = [
            [false, ['bürger.com', 'hãllo.com', 'hållo.com']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }

        // Check setting no IDN matching via constructor
        unset($validator);
        $validator = new Hostname(Hostname::ALLOW_DNS, false);
        $valuesExpected = [
            [false, ['bürger.com', 'hãllo.com', 'hållo.com']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * Ensure the TLD check works as expected
     *
     */
    public function testTLD()
    {
        $validator = new Hostname();

        // Check TLD matching
        $valuesExpected = [
            [true, ['domain.co.uk', 'domain.uk.com', 'domain.tl', 'domain.zw']],
            [false, ['domain.xx', 'domain.zz', 'domain.madeup']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }

        // Check no TLD matching
        $validator->useTldCheck(false);
        $valuesExpected = [
            [true, ['domain.xx', 'domain.zz', 'domain.madeup']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }

        // Check setting no TLD matching via constructor
        unset($validator);
        $validator = new Hostname(Hostname::ALLOW_DNS, true, false);
        $valuesExpected = [
            [true, ['domain.xx', 'domain.zz', 'domain.madeup']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * Ensures that getAllow() returns expected default value
     *
     * @return void
     */
    public function testGetAllow()
    {
        $this->assertEquals(Hostname::ALLOW_DNS, $this->validator->getAllow());
    }

    /**
     * Test changed with ZF-6676, as IP check is only involved when IP patterns match
     *
     * @group ZF-2861
     * @group ZF-6676
     */
    public function testValidatorMessagesShouldBeTranslated()
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $translations = [
            'hostnameInvalidLocalName' => 'The input does not appear to be a valid local network name',
        ];
        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = $translations;
        $translator = new TestAsset\Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', null);
        $this->validator->setTranslator($translator);

        $this->validator->isValid('0.239,512.777');
        $messages = $this->validator->getMessages();
        $found = false;
        foreach ($messages as $code => $message) {
            if (array_key_exists($code, $translations)) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found);
        $this->assertEquals($translations[$code], $message);
    }

    /**
     * @group ZF-6033
     */
    public function testNumberNames()
    {
        $validator = new Hostname();

        // Check TLD matching
        $valuesExpected = [
            [true, ['www.danger1.com', 'danger.com', 'www.danger.com']],
            [false, ['www.danger1com', 'dangercom', 'www.dangercom']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * @group ZF-6133
     */
    public function testPunycodeDecoding()
    {
        $validator = new Hostname();

        // Check TLD matching
        $valuesExpected = [
            [true, ['xn--brger-kva.com', 'xn--eckwd4c7cu47r2wf.jp']],
            [false, ['xn--brger-x45d2va.com', 'xn--bürger.com', 'xn--']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid([1 => 1]));
    }

    /**
     * @ZF-7323
     */
    public function testLatinSpecialChars()
    {
        $this->assertFalse($this->validator->isValid('place@yah&oo.com'));
        $this->assertFalse($this->validator->isValid('place@y*ahoo.com'));
        $this->assertFalse($this->validator->isValid('ya#hoo'));
    }

    /**
     * @group ZF-7277
     */
    public function testDifferentIconvEncoding()
    {
        if (PHP_VERSION_ID < 50600) {
            iconv_set_encoding('internal_encoding', 'ISO8859-1');
        } else {
            ini_set('default_charset', 'ISO8859-1');
        }

        $validator = new Hostname();

        $valuesExpected = [
            [true, ['bürger.com', 'hãllo.com', 'hållo.com']],
            [true, ['bÜrger.com', 'hÃllo.com', 'hÅllo.com']],
            [false, ['hãllo.lt', 'bürger.lt', 'hãllo.lt']]
            ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * @ZF-8312
     */
    public function testInvalidDoubledIdn()
    {
        $this->assertFalse($this->validator->isValid('test.com / http://www.test.com'));
    }

    /**
     * @group ZF-10267
     */
    public function testURI()
    {
        $valuesExpected = [
            [Hostname::ALLOW_URI, true, ['localhost', 'example.com', '~ex%20ample']],
            // @codingStandardsIgnoreStart
            [Hostname::ALLOW_URI, false, ['§bad', 'don?t.know', 'thisisaverylonghostnamewhichextendstwohundredfiftysixcharactersandthereforshouldnotbeallowedbythisvalidatorbecauserfc3986limitstheallowedcharacterstoalimitoftwohunderedfiftysixcharactersinsumbutifthistestwouldfailthenitshouldreturntruewhichthrowsanexceptionbytheunittest']],
            // @codingStandardsIgnoreEnd
        ];
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals(
                    $element[1],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * Ensure that a trailing "." in a local hostname is permitted
     *
     * @group ZF-6363
     */
    public function testTrailingDot()
    {
        $valuesExpected = [
            [Hostname::ALLOW_ALL, true, ['example.', 'example.com.', '~ex%20ample.']],
            [Hostname::ALLOW_ALL, false, ['example..']],
            [Hostname::ALLOW_ALL, true, ['1.2.3.4.']],
            [Hostname::ALLOW_DNS, false, ['example..', '~ex%20ample..']],
            [Hostname::ALLOW_LOCAL, true, ['example.', 'example.com.']],
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals(
                    $element[1],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) . $input
                );
            }
        }
    }

    /**
     * @group ZF-11334
     */
    public function testSupportsIpv6AddressesWhichContainHexDigitF()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);

        $this->assertTrue($validator->isValid('FEDC:BA98:7654:3210:FEDC:BA98:7654:3210'));
        $this->assertTrue($validator->isValid('1080:0:0:0:8:800:200C:417A'));
        $this->assertTrue($validator->isValid('3ffe:2a00:100:7031::1'));
        $this->assertTrue($validator->isValid('1080::8:800:200C:417A'));
        $this->assertTrue($validator->isValid('::192.9.5.5'));
        $this->assertTrue($validator->isValid('::FFFF:129.144.52.38'));
        $this->assertTrue($validator->isValid('2010:836B:4179::836B:4179'));
    }

    /**
     * Test extended greek charset
     *
     * @group ZF-11751
     */
    public function testExtendedGreek()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);
        $this->assertEquals(true, $validator->isValid('ῆὧὰῧῲ.com'));
    }

    /**
     * @group ZF-11796
     */
    public function testIDNSI()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);

        $this->assertTrue($validator->isValid('Test123.si'));
        $this->assertTrue($validator->isValid('țest123.si'));
        $this->assertTrue($validator->isValid('tĕst123.si'));
        $this->assertTrue($validator->isValid('tàrø.si'));
        $this->assertFalse($validator->isValid('رات.si'));
    }

    /**
     * @group Issue #5894 - Add .il IDN domain checking; add new TLDs
     */
    public function testIDNIL()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);

        // Check .IL TLD matching
        $valuesExpected = [
            [true, ['xn----zhcbgfhe2aacg8fb5i.org.il', 'מבחן.il', 'מבחן123.il']],
            [false, ['tבדיקה123.il', 'رات.il']] // Can't mix Latin and Hebrew character sets (except digits)
        ];
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals(
                    $element[0],
                    $validator->isValid($input),
                    implode("\n", $validator->getMessages()) .' - '. $input
                );
            }
        }
    }

    /**
     * Ensures that the validator follows expected behavior for UTF-8 and Punycoded (ACE) TLDs
     *
     * @dataProvider validTLDHostnames
     */
    public function testValidTLDHostnames($value)
    {
        $this->assertTrue(
            $this->validator->isValid($value),
            sprintf(
                '%s failed validation: %s',
                $value,
                implode("\n", $this->validator->getMessages())
            )
        );
    }

    public function validTLDHostnames()
    {
        // @codingStandardsIgnoreStart
        return [
            'ASCII label + UTF-8 TLD'                    => ['test123.онлайн'],
            'ASCII label + Punycoded TLD'                => ['test123.xn--80asehdb'],
            'UTF-8 label + UTF-8 TLD (cyrillic)'         => ['тест.рф'],
            'Punycoded label + Punycoded TLD (cyrillic)' => ['xn--e1aybc.xn--p1ai'],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * Ensures that the validator follows expected behavior for invalid UTF-8 and Punycoded (ACE) TLDs
     *
     * @dataProvider invalidTLDHostnames
     */
    public function testInalidTLDHostnames($value)
    {
        $this->assertFalse($this->validator->isValid($value));
    }

    public function invalidTLDHostnames()
    {
        // @codingStandardsIgnoreStart
        return [
            'Invalid mix of UTF-8 and ASCII in label'                              => ['சோதனை3.இலங்கை'],
            'Invalid mix of UTF-8 and ASCII in label (Punycoded)'                  => ['xn--3-owe4au9mpa.xn--xkc2al3hye2a'],
            'Invalid use of non-cyrillic characters with cyrillic TLD'             => ['رات.мон'],
            'Invalid use of non-cyrillic characters with cyrillic TLD (Punycoded)' => ['xn--mgbgt.xn--l1acc'],
        ];
        // @codingStandardsIgnoreEnd
    }

    public function testIDNIT()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);

        $this->assertTrue($validator->isValid('plainascii.it'));
        $this->assertTrue($validator->isValid('città-caffè.it'));
        $this->assertTrue($validator->isValid('edgetest-àâäèéêëìîïòôöùûüæœçÿß.it'));
        $this->assertFalse($validator->isValid('رات.it'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageVariables'), 'messageVariables', $validator);
    }

    public function testHostnameWithOnlyIpChars()
    {
        $validator = new Hostname();
        $this->assertTrue($validator->isValid('cafecafe.de'));
    }

    public function testValidCnHostname()
    {
        $validator = new Hostname();
        $this->assertTrue($validator->isValid('google.cn'));
    }

    public function testValidBizHostname()
    {
        $validator = new Hostname();
        $this->assertTrue($validator->isValid('google.biz'));
    }
}
