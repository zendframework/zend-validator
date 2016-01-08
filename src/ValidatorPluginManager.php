<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\I18n\Validator as I18nValidator;

class ValidatorPluginManager extends AbstractPluginManager
{
    /**
     * Default set of aliases
     *
     * @var array
     */
    protected $aliases = [
        'alnum'                    => I18nValidator\Alnum::class,
        'Alnum'                    => I18nValidator\Alnum::class,
        'alpha'                    => I18nValidator\Alpha::class,
        'Alpha'                    => I18nValidator\Alpha::class,
        'barcodecode25interleaved' => Barcode\Code25interleaved::class,
        'barcodeCode25interleaved' => Barcode\Code25interleaved::class,
        'BarcodeCode25interleaved' => Barcode\Code25interleaved::class,
        'barcodecode25'            => Barcode\Code25::class,
        'barcodeCode25'            => Barcode\Code25::class,
        'BarcodeCode25'            => Barcode\Code25::class,
        'barcodecode39ext'         => Barcode\Code39ext::class,
        'barcodeCode39ext'         => Barcode\Code39ext::class,
        'BarcodeCode39ext'         => Barcode\Code39ext::class,
        'barcodecode39'            => Barcode\Code39::class,
        'barcodeCode39'            => Barcode\Code39::class,
        'BarcodeCode39'            => Barcode\Code39::class,
        'barcodecode93ext'         => Barcode\Code93ext::class,
        'barcodeCode93ext'         => Barcode\Code93ext::class,
        'BarcodeCode93ext'         => Barcode\Code93ext::class,
        'barcodecode93'            => Barcode\Code93::class,
        'barcodeCode93'            => Barcode\Code93::class,
        'BarcodeCode93'            => Barcode\Code93::class,
        'barcodeean12'             => Barcode\Ean12::class,
        'barcodeEan12'             => Barcode\Ean12::class,
        'BarcodeEan12'             => Barcode\Ean12::class,
        'barcodeean13'             => Barcode\Ean13::class,
        'barcodeEan13'             => Barcode\Ean13::class,
        'BarcodeEan13'             => Barcode\Ean13::class,
        'barcodeean14'             => Barcode\Ean14::class,
        'barcodeEan14'             => Barcode\Ean14::class,
        'BarcodeEan14'             => Barcode\Ean14::class,
        'barcodeean18'             => Barcode\Ean18::class,
        'barcodeEan18'             => Barcode\Ean18::class,
        'BarcodeEan18'             => Barcode\Ean18::class,
        'barcodeean2'              => Barcode\Ean2::class,
        'barcodeEan2'              => Barcode\Ean2::class,
        'BarcodeEan2'              => Barcode\Ean2::class,
        'barcodeean5'              => Barcode\Ean5::class,
        'barcodeEan5'              => Barcode\Ean5::class,
        'BarcodeEan5'              => Barcode\Ean5::class,
        'barcodeean8'              => Barcode\Ean8::class,
        'barcodeEan8'              => Barcode\Ean8::class,
        'BarcodeEan8'              => Barcode\Ean8::class,
        'barcodegtin12'            => Barcode\Gtin12::class,
        'barcodeGtin12'            => Barcode\Gtin12::class,
        'BarcodeGtin12'            => Barcode\Gtin12::class,
        'barcodegtin13'            => Barcode\Gtin13::class,
        'barcodeGtin13'            => Barcode\Gtin13::class,
        'BarcodeGtin13'            => Barcode\Gtin13::class,
        'barcodegtin14'            => Barcode\Gtin14::class,
        'barcodeGtin14'            => Barcode\Gtin14::class,
        'BarcodeGtin14'            => Barcode\Gtin14::class,
        'barcodeidentcode'         => Barcode\Identcode::class,
        'barcodeIdentcode'         => Barcode\Identcode::class,
        'BarcodeIdentcode'         => Barcode\Identcode::class,
        'barcodeintelligentmail'   => Barcode\Intelligentmail::class,
        'barcodeIntelligentmail'   => Barcode\Intelligentmail::class,
        'BarcodeIntelligentmail'   => Barcode\Intelligentmail::class,
        'barcodeissn'              => Barcode\Issn::class,
        'barcodeIssn'              => Barcode\Issn::class,
        'BarcodeIssn'              => Barcode\Issn::class,
        'barcodeitf14'             => Barcode\Itf14::class,
        'barcodeItf14'             => Barcode\Itf14::class,
        'BarcodeItf14'             => Barcode\Itf14::class,
        'barcodeleitcode'          => Barcode\Leitcode::class,
        'barcodeleItcode'          => Barcode\Leitcode::class,
        'BarcodeleItcode'          => Barcode\Leitcode::class,
        'barcodeplanet'            => Barcode\Planet::class,
        'barcodePlanet'            => Barcode\Planet::class,
        'BarcodePlanet'            => Barcode\Planet::class,
        'barcodepostnet'           => Barcode\Postnet::class,
        'barcodePostnet'           => Barcode\Postnet::class,
        'BarcodePostnet'           => Barcode\Postnet::class,
        'barcoderoyalmail'         => Barcode\Royalmail::class,
        'barcodeRoyalmail'         => Barcode\Royalmail::class,
        'BarcodeRoyalmail'         => Barcode\Royalmail::class,
        'barcodesscc'              => Barcode\Sscc::class,
        'barcodeSscc'              => Barcode\Sscc::class,
        'BarcodeSscc'              => Barcode\Sscc::class,
        'barcodeupca'              => Barcode\Upca::class,
        'barcodeUpca'              => Barcode\Upca::class,
        'BarcodeUpca'              => Barcode\Upca::class,
        'barcodeupce'              => Barcode\Upce::class,
        'barcodeUpce'              => Barcode\Upce::class,
        'BarcodeUpce'              => Barcode\Upce::class,
        'barcode'                  => Barcode::class,
        'Barcode'                  => Barcode::class,
        'between'                  => Between::class,
        'Between'                  => Between::class,
        'bitwise'                  => Bitwise::class,
        'Bitwise'                  => Bitwise::class,
        'callback'                 => Callback::class,
        'Callback'                 => Callback::class,
        'creditcard'               => CreditCard::class,
        'creditCard'               => CreditCard::class,
        'CreditCard'               => CreditCard::class,
        'csrf'                     => Csrf::class,
        'Csrf'                     => Csrf::class,
        'date'                     => Date::class,
        'Date'                     => Date::class,
        'datestep'                 => DateStep::class,
        'dateStep'                 => DateStep::class,
        'DateStep'                 => DateStep::class,
        'datetime'                 => I18nValidator\DateTime::class,
        'dateTime'                 => I18nValidator\DateTime::class,
        'DateTime'                 => I18nValidator\DateTime::class,
        'dbnorecordexists'         => Db\NoRecordExists::class,
        'dbNoRecordExists'         => Db\NoRecordExists::class,
        'DbNoRecordExists'         => Db\NoRecordExists::class,
        'dbrecordexists'           => Db\RecordExists::class,
        'dbRecordExists'           => Db\RecordExists::class,
        'DbRecordExists'           => Db\RecordExists::class,
        'digits'                   => Digits::class,
        'Digits'                   => Digits::class,
        'emailaddress'             => EmailAddress::class,
        'emailAddress'             => EmailAddress::class,
        'EmailAddress'             => EmailAddress::class,
        'explode'                  => Explode::class,
        'Explode'                  => Explode::class,
        'filecount'                => File\Count::class,
        'fileCount'                => File\Count::class,
        'FileCount'                => File\Count::class,
        'filecrc32'                => File\Crc32::class,
        'fileCrc32'                => File\Crc32::class,
        'FileCrc32'                => File\Crc32::class,
        'fileexcludeextension'     => File\ExcludeExtension::class,
        'fileExcludeExtension'     => File\ExcludeExtension::class,
        'FileExcludeExtension'     => File\ExcludeExtension::class,
        'fileexcludemimetype'      => File\ExcludeMimeType::class,
        'fileExcludeMimeType'      => File\ExcludeMimeType::class,
        'FileExcludeMimeType'      => File\ExcludeMimeType::class,
        'fileexists'               => File\Exists::class,
        'fileExists'               => File\Exists::class,
        'FileExists'               => File\Exists::class,
        'fileextension'            => File\Extension::class,
        'fileExtension'            => File\Extension::class,
        'FileExtension'            => File\Extension::class,
        'filefilessize'            => File\FilesSize::class,
        'fileFilesSize'            => File\FilesSize::class,
        'FileFilesSize'            => File\FilesSize::class,
        'filehash'                 => File\Hash::class,
        'fileHash'                 => File\Hash::class,
        'FileHash'                 => File\Hash::class,
        'fileimagesize'            => File\ImageSize::class,
        'fileImageSize'            => File\ImageSize::class,
        'FileImageSize'            => File\ImageSize::class,
        'fileiscompressed'         => File\IsCompressed::class,
        'fileIsCompressed'         => File\IsCompressed::class,
        'FileIsCompressed'         => File\IsCompressed::class,
        'fileisimage'              => File\IsImage::class,
        'fileIsImage'              => File\IsImage::class,
        'FileIsImage'              => File\IsImage::class,
        'filemd5'                  => File\Md5::class,
        'fileMd5'                  => File\Md5::class,
        'FileMd5'                  => File\Md5::class,
        'filemimetype'             => File\MimeType::class,
        'fileMimeType'             => File\MimeType::class,
        'FileMimeType'             => File\MimeType::class,
        'filenotexists'            => File\NotExists::class,
        'fileNotExists'            => File\NotExists::class,
        'FileNotExists'            => File\NotExists::class,
        'filesha1'                 => File\Sha1::class,
        'fileSha1'                 => File\Sha1::class,
        'FileSha1'                 => File\Sha1::class,
        'filesize'                 => File\Size::class,
        'fileSize'                 => File\Size::class,
        'FileSize'                 => File\Size::class,
        'fileupload'               => File\Upload::class,
        'fileUpload'               => File\Upload::class,
        'FileUpload'               => File\Upload::class,
        'fileuploadfile'           => File\UploadFile::class,
        'fileUploadFile'           => File\UploadFile::class,
        'FileUploadFile'           => File\UploadFile::class,
        'filewordcount'            => File\WordCount::class,
        'fileWordCount'            => File\WordCount::class,
        'FileWordCount'            => File\WordCount::class,
        'float'                    => I18nValidator\IsFloat::class,
        'Float'                    => I18nValidator\IsFloat::class,
        'greaterthan'              => GreaterThan::class,
        'greaterThan'              => GreaterThan::class,
        'GreaterThan'              => GreaterThan::class,
        'hex'                      => Hex::class,
        'Hex'                      => Hex::class,
        'hostname'                 => Hostname::class,
        'Hostname'                 => Hostname::class,
        'iban'                     => Iban::class,
        'Iban'                     => Iban::class,
        'identical'                => Identical::class,
        'Identical'                => Identical::class,
        'inarray'                  => InArray::class,
        'inArray'                  => InArray::class,
        'InArray'                  => InArray::class,
        'int'                      => I18nValidator\IsInt::class,
        'Int'                      => I18nValidator\IsInt::class,
        'ip'                       => Ip::class,
        'Ip'                       => Ip::class,
        'isbn'                     => Isbn::class,
        'Isbn'                     => Isbn::class,
        'isfloat'                  => I18nValidator\IsFloat::class,
        'isFloat'                  => I18nValidator\IsFloat::class,
        'IsFloat'                  => I18nValidator\IsFloat::class,
        'isinstanceof'             => IsInstanceOf::class,
        'isInstanceOf'             => IsInstanceOf::class,
        'IsInstanceOf'             => IsInstanceOf::class,
        'isint'                    => I18nValidator\IsInt::class,
        'isInt'                    => I18nValidator\IsInt::class,
        'IsInt'                    => I18nValidator\IsInt::class,
        'lessthan'                 => LessThan::class,
        'lessThan'                 => LessThan::class,
        'LessThan'                 => LessThan::class,
        'notempty'                 => NotEmpty::class,
        'notEmpty'                 => NotEmpty::class,
        'NotEmpty'                 => NotEmpty::class,
        'phonenumber'              => I18nValidator\PhoneNumber::class,
        'phoneNumber'              => I18nValidator\PhoneNumber::class,
        'PhoneNumber'              => I18nValidator\PhoneNumber::class,
        'postcode'                 => I18nValidator\PostCode::class,
        'postCode'                 => I18nValidator\PostCode::class,
        'PostCode'                 => I18nValidator\PostCode::class,
        'regex'                    => Regex::class,
        'Regex'                    => Regex::class,
        'sitemapchangefreq'        => Sitemap\Changefreq::class,
        'sitemapChangefreq'        => Sitemap\Changefreq::class,
        'SitemapChangefreq'        => Sitemap\Changefreq::class,
        'sitemaplastmod'           => Sitemap\Lastmod::class,
        'sitemapLastmod'           => Sitemap\Lastmod::class,
        'SitemapLastmod'           => Sitemap\Lastmod::class,
        'sitemaploc'               => Sitemap\Loc::class,
        'sitemapLoc'               => Sitemap\Loc::class,
        'SitemapLoc'               => Sitemap\Loc::class,
        'sitemappriority'          => Sitemap\Priority::class,
        'sitemapPriority'          => Sitemap\Priority::class,
        'SitemapPriority'          => Sitemap\Priority::class,
        'stringlength'             => StringLength::class,
        'stringLength'             => StringLength::class,
        'StringLength'             => StringLength::class,
        'step'                     => Step::class,
        'Step'                     => Step::class,
        'timezone'                 => Timezone::class,
        'Timezone'                 => Timezone::class,
        'uri'                      => Uri::class,
        'Uri'                      => Uri::class,
    ];

    /**
     * Default set of factories
     *
     * @var array
     */
    protected $factories = [
        I18nValidator\Alnum::class             => InvokableFactory::class,
        I18nValidator\Alpha::class             => InvokableFactory::class,
        Barcode\Code25interleaved::class       => InvokableFactory::class,
        Barcode\Code25::class                  => InvokableFactory::class,
        Barcode\Code39ext::class               => InvokableFactory::class,
        Barcode\Code39::class                  => InvokableFactory::class,
        Barcode\Code93ext::class               => InvokableFactory::class,
        Barcode\Code93::class                  => InvokableFactory::class,
        Barcode\Ean12::class                   => InvokableFactory::class,
        Barcode\Ean13::class                   => InvokableFactory::class,
        Barcode\Ean14::class                   => InvokableFactory::class,
        Barcode\Ean18::class                   => InvokableFactory::class,
        Barcode\Ean2::class                    => InvokableFactory::class,
        Barcode\Ean5::class                    => InvokableFactory::class,
        Barcode\Ean8::class                    => InvokableFactory::class,
        Barcode\Gtin12::class                  => InvokableFactory::class,
        Barcode\Gtin13::class                  => InvokableFactory::class,
        Barcode\Gtin14::class                  => InvokableFactory::class,
        Barcode\Identcode::class               => InvokableFactory::class,
        Barcode\Intelligentmail::class         => InvokableFactory::class,
        Barcode\Issn::class                    => InvokableFactory::class,
        Barcode\Itf14::class                   => InvokableFactory::class,
        Barcode\Leitcode::class                => InvokableFactory::class,
        Barcode\Planet::class                  => InvokableFactory::class,
        Barcode\Postnet::class                 => InvokableFactory::class,
        Barcode\Royalmail::class               => InvokableFactory::class,
        Barcode\Sscc::class                    => InvokableFactory::class,
        Barcode\Upca::class                    => InvokableFactory::class,
        Barcode\Upce::class                    => InvokableFactory::class,
        Barcode::class                         => InvokableFactory::class,
        Between::class                         => InvokableFactory::class,
        Bitwise::class                         => InvokableFactory::class,
        Callback::class                        => InvokableFactory::class,
        CreditCard::class                      => InvokableFactory::class,
        Csrf::class                            => InvokableFactory::class,
        DateStep::class                        => InvokableFactory::class,
        Date::class                            => InvokableFactory::class,
        I18nValidator\DateTime::class          => InvokableFactory::class,
        Db\NoRecordExists::class               => InvokableFactory::class,
        Db\RecordExists::class                 => InvokableFactory::class,
        Digits::class                          => InvokableFactory::class,
        EmailAddress::class                    => InvokableFactory::class,
        Explode::class                         => InvokableFactory::class,
        File\Count::class                      => InvokableFactory::class,
        File\Crc32::class                      => InvokableFactory::class,
        File\ExcludeExtension::class           => InvokableFactory::class,
        File\ExcludeMimeType::class            => InvokableFactory::class,
        File\Exists::class                     => InvokableFactory::class,
        File\Extension::class                  => InvokableFactory::class,
        File\FilesSize::class                  => InvokableFactory::class,
        File\Hash::class                       => InvokableFactory::class,
        File\ImageSize::class                  => InvokableFactory::class,
        File\IsCompressed::class               => InvokableFactory::class,
        File\IsImage::class                    => InvokableFactory::class,
        File\Md5::class                        => InvokableFactory::class,
        File\MimeType::class                   => InvokableFactory::class,
        File\NotExists::class                  => InvokableFactory::class,
        File\Sha1::class                       => InvokableFactory::class,
        File\Size::class                       => InvokableFactory::class,
        File\Upload::class                     => InvokableFactory::class,
        File\UploadFile::class                 => InvokableFactory::class,
        File\WordCount::class                  => InvokableFactory::class,
        I18nValidator\IsFloat::class           => InvokableFactory::class,
        GreaterThan::class                     => InvokableFactory::class,
        Hex::class                             => InvokableFactory::class,
        Hostname::class                        => InvokableFactory::class,
        Iban::class                            => InvokableFactory::class,
        Identical::class                       => InvokableFactory::class,
        InArray::class                         => InvokableFactory::class,
        I18nValidator\IsInt::class             => InvokableFactory::class,
        Ip::class                              => InvokableFactory::class,
        Isbn::class                            => InvokableFactory::class,
        I18nValidator\IsFloat::class           => InvokableFactory::class,
        IsInstanceOf::class                    => InvokableFactory::class,
        I18nValidator\IsInt::class             => InvokableFactory::class,
        LessThan::class                        => InvokableFactory::class,
        NotEmpty::class                        => InvokableFactory::class,
        I18nValidator\PhoneNumber::class       => InvokableFactory::class,
        I18nValidator\PostCode::class          => InvokableFactory::class,
        Regex::class                           => InvokableFactory::class,
        Sitemap\Changefreq::class              => InvokableFactory::class,
        Sitemap\Lastmod::class                 => InvokableFactory::class,
        Sitemap\Loc::class                     => InvokableFactory::class,
        Sitemap\Priority::class                => InvokableFactory::class,
        StringLength::class                    => InvokableFactory::class,
        Step::class                            => InvokableFactory::class,
        Timezone::class                        => InvokableFactory::class,
        Uri::class                             => InvokableFactory::class,
    ];

    /**
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * Default instance type
     *
     * @var string
     */
    protected $instanceOf = ValidatorInterface::class;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * attached translator, if any, to the currently requested helper.
     *
     * @param  ContainerInterface $parentLocator
     * @param  array $config
     */
    public function __construct(ContainerInterface $parentLocator, array $config = [])
    {
        $config['initializers'][] = [$this, 'injectTranslator'];
        $config['initializers'][] = [$this, 'injectValidatorPluginManager'];
        parent::__construct($parentLocator, $config);
    }

    /**
     * Validate plugin instance
     *
     * {@inheritDoc}
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s expects only to create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($instance) ? get_class($instance) : gettype($instance))
            ));
        }
    }

    /**
     * For v2 compatibility: validate plugin instance.
     *
     * Proxies to `validate()`.
     *
     * @param mixed $instance
     * @throws InvalidServiceException
     */
    public function validatePlugin($instance)
    {
        $this->validate($instance);
    }

    /**
     * Inject a validator instance with the registered translator
     *
     * @param  ContainerInterface|object $first
     * @param  ContainerInterface|object $second
     * @return void
     */
    public function injectTranslator($first, $second)
    {
        if ($first instanceof ContainerInterface) {
            $locator   = $first;
            $validator = $second;
        } else {
            $locator   = $second;
            $validator = $first;
        }
        if ($validator instanceof Translator\TranslatorAwareInterface) {
            if ($locator && $locator->has('MvcTranslator')) {
                $validator->setTranslator($locator->get('MvcTranslator'));
            }
        }
    }

    /**
     * Inject a validator plugin manager
     *
     * @param  ContainerInterface|object $first
     * @param  ContainerInterface|object $second
     * @return void
     */
    public function injectValidatorPluginManager($first, $second)
    {
        if ($first instanceof ContainerInterface) {
            $locator   = $first;
            $validator = $second;
        } else {
            $locator   = $second;
            $validator = $first;
        }
        if ($validator instanceof ValidatorPluginManagerAwareInterface) {
            $validator->setValidatorPluginManager($this);
        }
    }
}
