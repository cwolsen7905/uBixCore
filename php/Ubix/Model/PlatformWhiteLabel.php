<?php

declare(strict_types=1);

namespace Ubix\Model;

use DateTimeInterface;
use Ubix\Model\AbstractModel as Model;

/**
 * Model of a platform white label
 *
 * @see \Ubix\Tests\Model\PlatformWhiteLabelTest PHPUnit test case
 */
final class PlatformWhiteLabel extends Model
{
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_CLOSED   = 'closed';

    /**
     * Constructor
     *
     * @param ?string            $domain                  Placeholder for domain // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $affiliateId             Placeholder for affiliateId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $mpCode                  Placeholder for mpCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $templateSet             Placeholder for templateSet // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $title                   Placeholder for title // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $shortTitle              Placeholder for shortTitle // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $metaKeywords            Placeholder for metaKeywords // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $metaDescription         Placeholder for metaDescription // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $customText              Placeholder for customText // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $serviceDefault          Placeholder for serviceDefault // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $serviceGirls            Placeholder for serviceGirls // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $serviceGuys             Placeholder for serviceGuys // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $serviceTrans            Placeholder for serviceTrans // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $logoMpCode              Placeholder for logoMpCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $backgroundUrl           Placeholder for backgroundUrl // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalCssUrl          Placeholder for externalCssUrl // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalFaviconUrl      Placeholder for externalFaviconUrl // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color1Bg                Placeholder for color1Bg // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color1Text              Placeholder for color1Text // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color1Link              Placeholder for color1Link // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color2Bg                Placeholder for color2Bg // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color2Text              Placeholder for color2Text // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color2Link              Placeholder for color2Link // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color3Bg                Placeholder for color3Bg // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color3Text              Placeholder for color3Text // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color3Link              Placeholder for color3Link // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color4Bg                Placeholder for color4Bg // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color4Text              Placeholder for color4Text // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color4Link              Placeholder for color4Link // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color5Bg                Placeholder for color5Bg // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color5Text              Placeholder for color5Text // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color5Link              Placeholder for color5Link // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color6Bg                Placeholder for color6Bg // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color6Text              Placeholder for color6Text // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $color6Link              Placeholder for color6Link // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $googleAnalytics         Placeholder for googleAnalytics // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $metaGoogleVerify        Placeholder for metaGoogleVerify // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $metaBingVerify          Placeholder for metaBingVerify // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $canonicalAllowed        Placeholder for canonicalAllowed // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalUrl             Placeholder for externalUrl // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalTitle           Placeholder for externalTitle // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalUrl2            Placeholder for externalUrl2 // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalTitle2          Placeholder for externalTitle2 // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalUrl3            Placeholder for externalUrl3 // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $externalTitle3          Placeholder for externalTitle3 // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $featureModelsStudioCode Placeholder for featureModelsStudioCode // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $studioAdminId           Placeholder for studioAdminId // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $recruiterAdminId        Placeholder for recruiterAdminId // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $referralIdType          Placeholder for referralIdType // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $authorizedDomainId      Placeholder for authorizedDomainId // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $dateCreated             Placeholder for dateCreated // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $dateLastUpdated         Placeholder for dateLastUpdated // NOT_IMPLEMENTED: replace placeholder
     * @param ?DateTimeInterface $whoisLastVerified       Placeholder for whoisLastVerified // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $whoisType               Placeholder for whoisType // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $recentTraffic           Placeholder for recentTraffic // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $spamBlacklist           Placeholder for spamBlacklist // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $showDating              Placeholder for showDating // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $status                  Placeholder for status // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $sslStatus               Placeholder for sslStatus // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $isHouse                 Placeholder for isHouse // NOT_IMPLEMENTED: replace placeholder
     * @param ?int               $certProcessed           Placeholder for certProcessed // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $merchant                Placeholder for merchant // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $sitekey                 Placeholder for sitekey // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $domainReview            Placeholder for domainReview // NOT_IMPLEMENTED: replace placeholder
     * @param ?string            $platform                Placeholder for platform // NOT_IMPLEMENTED: replace placeholder
     */
    public function __construct(
        private ?string $domain = null,
        private ?int $affiliateId = null,
        private ?string $mpCode = null,
        private ?string $templateSet = null,
        private ?string $title = null,
        private ?string $shortTitle = null,
        private ?string $metaKeywords = null,
        private ?string $metaDescription = null,
        private ?string $customText = null,
        private ?string $serviceDefault = null,
        private ?string $serviceGirls = null,
        private ?string $serviceGuys = null,
        private ?string $serviceTrans = null,
        private ?string $logoMpCode = null,
        private ?string $backgroundUrl = null,
        private ?string $externalCssUrl = null,
        private ?string $externalFaviconUrl = null,
        private ?string $color1Bg = null,
        private ?string $color1Text = null,
        private ?string $color1Link = null,
        private ?string $color2Bg = null,
        private ?string $color2Text = null,
        private ?string $color2Link = null,
        private ?string $color3Bg = null,
        private ?string $color3Text = null,
        private ?string $color3Link = null,
        private ?string $color4Bg = null,
        private ?string $color4Text = null,
        private ?string $color4Link = null,
        private ?string $color5Bg = null,
        private ?string $color5Text = null,
        private ?string $color5Link = null,
        private ?string $color6Bg = null,
        private ?string $color6Text = null,
        private ?string $color6Link = null,
        private ?string $googleAnalytics = null,
        private ?string $metaGoogleVerify = null,
        private ?string $metaBingVerify = null,
        private ?string $canonicalAllowed = null,
        private ?string $externalUrl = null,
        private ?string $externalTitle = null,
        private ?string $externalUrl2 = null,
        private ?string $externalTitle2 = null,
        private ?string $externalUrl3 = null,
        private ?string $externalTitle3 = null,
        private ?string $featureModelsStudioCode = null,
        private ?int $studioAdminId = null,
        private ?int $recruiterAdminId = null,
        private ?string $referralIdType = null,
        private ?int $authorizedDomainId = null,
        private ?DateTimeInterface $dateCreated = null,
        private ?DateTimeInterface $dateLastUpdated = null,
        private ?DateTimeInterface $whoisLastVerified = null,
        private ?string $whoisType = null,
        private ?string $recentTraffic = null,
        private ?string $spamBlacklist = null,
        private ?string $showDating = null,
        private ?string $status = null,
        private ?string $sslStatus = null,
        private ?string $isHouse = null,
        private ?int $certProcessed = null,
        private ?string $merchant = null,
        private ?string $sitekey = null,
        private ?string $domainReview = null,
        private ?string $platform = null,
    ) {
    }

    /**
     * Get the value of domain
     *
     * @return ?string The value of domain
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Set the value of domain
     *
     * @param ?string $domain The value for domain
     *
     * @return void
     */
    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * Get the value of affiliateId
     *
     * @return ?int The value of affiliateId
     */
    public function getAffiliateId(): ?int
    {
        return $this->affiliateId;
    }

    /**
     * Set the value of affiliateId
     *
     * @param ?int $affiliateId The value for affiliateId
     *
     * @return void
     */
    public function setAffiliateId(?int $affiliateId): void
    {
        $this->affiliateId = $affiliateId;
    }

    /**
     * Get the value of mpCode
     *
     * @return ?string The value of mpCode
     */
    public function getMpCode(): ?string
    {
        return $this->mpCode;
    }

    /**
     * Set the value of mpCode
     *
     * @param ?string $mpCode The value for mpCode
     *
     * @return void
     */
    public function setMpCode(?string $mpCode): void
    {
        $this->mpCode = $mpCode;
    }

    /**
     * Get the value of templateSet
     *
     * @return ?string The value of templateSet
     */
    public function getTemplateSet(): ?string
    {
        return $this->templateSet;
    }

    /**
     * Set the value of templateSet
     *
     * @param ?string $templateSet The value for templateSet
     *
     * @return void
     */
    public function setTemplateSet(?string $templateSet): void
    {
        $this->templateSet = $templateSet;
    }

    /**
     * Get the value of title
     *
     * @return ?string The value of title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param ?string $title The value for title
     *
     * @return void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the value of shortTitle
     *
     * @return ?string The value of shortTitle
     */
    public function getShortTitle(): ?string
    {
        return $this->shortTitle;
    }

    /**
     * Set the value of shortTitle
     *
     * @param ?string $shortTitle The value for shortTitle
     *
     * @return void
     */
    public function setShortTitle(?string $shortTitle): void
    {
        $this->shortTitle = $shortTitle;
    }

    /**
     * Get the value of metaKeywords
     *
     * @return ?string The value of metaKeywords
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * Set the value of metaKeywords
     *
     * @param ?string $metaKeywords The value for metaKeywords
     *
     * @return void
     */
    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * Get the value of metaDescription
     *
     * @return ?string The value of metaDescription
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * Set the value of metaDescription
     *
     * @param ?string $metaDescription The value for metaDescription
     *
     * @return void
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Get the value of customText
     *
     * @return ?string The value of customText
     */
    public function getCustomText(): ?string
    {
        return $this->customText;
    }

    /**
     * Set the value of customText
     *
     * @param ?string $customText The value for customText
     *
     * @return void
     */
    public function setCustomText(?string $customText): void
    {
        $this->customText = $customText;
    }

    /**
     * Get the value of serviceDefault
     *
     * @return ?string The value of serviceDefault
     */
    public function getServiceDefault(): ?string
    {
        return $this->serviceDefault;
    }

    /**
     * Set the value of serviceDefault
     *
     * @param ?string $serviceDefault The value for serviceDefault
     *
     * @return void
     */
    public function setServiceDefault(?string $serviceDefault): void
    {
        $this->serviceDefault = $serviceDefault;
    }

    /**
     * Get the value of serviceGirls
     *
     * @return ?string The value of serviceGirls
     */
    public function getServiceGirls(): ?string
    {
        return $this->serviceGirls;
    }

    /**
     * Set the value of serviceGirls
     *
     * @param ?string $serviceGirls The value for serviceGirls
     *
     * @return void
     */
    public function setServiceGirls(?string $serviceGirls): void
    {
        $this->serviceGirls = $serviceGirls;
    }

    /**
     * Get the value of serviceGuys
     *
     * @return ?string The value of serviceGuys
     */
    public function getServiceGuys(): ?string
    {
        return $this->serviceGuys;
    }

    /**
     * Set the value of serviceGuys
     *
     * @param ?string $serviceGuys The value for serviceGuys
     *
     * @return void
     */
    public function setServiceGuys(?string $serviceGuys): void
    {
        $this->serviceGuys = $serviceGuys;
    }

    /**
     * Get the value of serviceTrans
     *
     * @return ?string The value of serviceTrans
     */
    public function getServiceTrans(): ?string
    {
        return $this->serviceTrans;
    }

    /**
     * Set the value of serviceTrans
     *
     * @param ?string $serviceTrans The value for serviceTrans
     *
     * @return void
     */
    public function setServiceTrans(?string $serviceTrans): void
    {
        $this->serviceTrans = $serviceTrans;
    }

    /**
     * Get the value of logoMpCode
     *
     * @return ?string The value of logoMpCode
     */
    public function getLogoMpCode(): ?string
    {
        return $this->logoMpCode;
    }

    /**
     * Set the value of logoMpCode
     *
     * @param ?string $logoMpCode The value for logoMpCode
     *
     * @return void
     */
    public function setLogoMpCode(?string $logoMpCode): void
    {
        $this->logoMpCode = $logoMpCode;
    }

    /**
     * Get the value of backgroundUrl
     *
     * @return ?string The value of backgroundUrl
     */
    public function getBackgroundUrl(): ?string
    {
        return $this->backgroundUrl;
    }

    /**
     * Set the value of backgroundUrl
     *
     * @param ?string $backgroundUrl The value for backgroundUrl
     *
     * @return void
     */
    public function setBackgroundUrl(?string $backgroundUrl): void
    {
        $this->backgroundUrl = $backgroundUrl;
    }

    /**
     * Get the value of externalCssUrl
     *
     * @return ?string The value of externalCssUrl
     */
    public function getExternalCssUrl(): ?string
    {
        return $this->externalCssUrl;
    }

    /**
     * Set the value of externalCssUrl
     *
     * @param ?string $externalCssUrl The value for externalCssUrl
     *
     * @return void
     */
    public function setExternalCssUrl(?string $externalCssUrl): void
    {
        $this->externalCssUrl = $externalCssUrl;
    }

    /**
     * Get the value of externalFaviconUrl
     *
     * @return ?string The value of externalFaviconUrl
     */
    public function getExternalFaviconUrl(): ?string
    {
        return $this->externalFaviconUrl;
    }

    /**
     * Set the value of externalFaviconUrl
     *
     * @param ?string $externalFaviconUrl The value for externalFaviconUrl
     *
     * @return void
     */
    public function setExternalFaviconUrl(?string $externalFaviconUrl): void
    {
        $this->externalFaviconUrl = $externalFaviconUrl;
    }

    /**
     * Get the value of color1Bg
     *
     * @return ?string The value of color1Bg
     */
    public function getColor1Bg(): ?string
    {
        return $this->color1Bg;
    }

    /**
     * Set the value of color1Bg
     *
     * @param ?string $color1Bg The value for color1Bg
     *
     * @return void
     */
    public function setColor1Bg(?string $color1Bg): void
    {
        $this->color1Bg = $color1Bg;
    }

    /**
     * Get the value of color1Text
     *
     * @return ?string The value of color1Text
     */
    public function getColor1Text(): ?string
    {
        return $this->color1Text;
    }

    /**
     * Set the value of color1Text
     *
     * @param ?string $color1Text The value for color1Text
     *
     * @return void
     */
    public function setColor1Text(?string $color1Text): void
    {
        $this->color1Text = $color1Text;
    }

    /**
     * Get the value of color1Link
     *
     * @return ?string The value of color1Link
     */
    public function getColor1Link(): ?string
    {
        return $this->color1Link;
    }

    /**
     * Set the value of color1Link
     *
     * @param ?string $color1Link The value for color1Link
     *
     * @return void
     */
    public function setColor1Link(?string $color1Link): void
    {
        $this->color1Link = $color1Link;
    }

    /**
     * Get the value of color2Bg
     *
     * @return ?string The value of color2Bg
     */
    public function getColor2Bg(): ?string
    {
        return $this->color2Bg;
    }

    /**
     * Set the value of color2Bg
     *
     * @param ?string $color2Bg The value for color2Bg
     *
     * @return void
     */
    public function setColor2Bg(?string $color2Bg): void
    {
        $this->color2Bg = $color2Bg;
    }

    /**
     * Get the value of color2Text
     *
     * @return ?string The value of color2Text
     */
    public function getColor2Text(): ?string
    {
        return $this->color2Text;
    }

    /**
     * Set the value of color2Text
     *
     * @param ?string $color2Text The value for color2Text
     *
     * @return void
     */
    public function setColor2Text(?string $color2Text): void
    {
        $this->color2Text = $color2Text;
    }

    /**
     * Get the value of color2Link
     *
     * @return ?string The value of color2Link
     */
    public function getColor2Link(): ?string
    {
        return $this->color2Link;
    }

    /**
     * Set the value of color2Link
     *
     * @param ?string $color2Link The value for color2Link
     *
     * @return void
     */
    public function setColor2Link(?string $color2Link): void
    {
        $this->color2Link = $color2Link;
    }

    /**
     * Get the value of color3Bg
     *
     * @return ?string The value of color3Bg
     */
    public function getColor3Bg(): ?string
    {
        return $this->color3Bg;
    }

    /**
     * Set the value of color3Bg
     *
     * @param ?string $color3Bg The value for color3Bg
     *
     * @return void
     */
    public function setColor3Bg(?string $color3Bg): void
    {
        $this->color3Bg = $color3Bg;
    }

    /**
     * Get the value of color3Text
     *
     * @return ?string The value of color3Text
     */
    public function getColor3Text(): ?string
    {
        return $this->color3Text;
    }

    /**
     * Set the value of color3Text
     *
     * @param ?string $color3Text The value for color3Text
     *
     * @return void
     */
    public function setColor3Text(?string $color3Text): void
    {
        $this->color3Text = $color3Text;
    }

    /**
     * Get the value of color3Link
     *
     * @return ?string The value of color3Link
     */
    public function getColor3Link(): ?string
    {
        return $this->color3Link;
    }

    /**
     * Set the value of color3Link
     *
     * @param ?string $color3Link The value for color3Link
     *
     * @return void
     */
    public function setColor3Link(?string $color3Link): void
    {
        $this->color3Link = $color3Link;
    }

    /**
     * Get the value of color4Bg
     *
     * @return ?string The value of color4Bg
     */
    public function getColor4Bg(): ?string
    {
        return $this->color4Bg;
    }

    /**
     * Set the value of color4Bg
     *
     * @param ?string $color4Bg The value for color4Bg
     *
     * @return void
     */
    public function setColor4Bg(?string $color4Bg): void
    {
        $this->color4Bg = $color4Bg;
    }

    /**
     * Get the value of color4Text
     *
     * @return ?string The value of color4Text
     */
    public function getColor4Text(): ?string
    {
        return $this->color4Text;
    }

    /**
     * Set the value of color4Text
     *
     * @param ?string $color4Text The value for color4Text
     *
     * @return void
     */
    public function setColor4Text(?string $color4Text): void
    {
        $this->color4Text = $color4Text;
    }

    /**
     * Get the value of color4Link
     *
     * @return ?string The value of color4Link
     */
    public function getColor4Link(): ?string
    {
        return $this->color4Link;
    }

    /**
     * Set the value of color4Link
     *
     * @param ?string $color4Link The value for color4Link
     *
     * @return void
     */
    public function setColor4Link(?string $color4Link): void
    {
        $this->color4Link = $color4Link;
    }

    /**
     * Get the value of color5Bg
     *
     * @return ?string The value of color5Bg
     */
    public function getColor5Bg(): ?string
    {
        return $this->color5Bg;
    }

    /**
     * Set the value of color5Bg
     *
     * @param ?string $color5Bg The value for color5Bg
     *
     * @return void
     */
    public function setColor5Bg(?string $color5Bg): void
    {
        $this->color5Bg = $color5Bg;
    }

    /**
     * Get the value of color5Text
     *
     * @return ?string The value of color5Text
     */
    public function getColor5Text(): ?string
    {
        return $this->color5Text;
    }

    /**
     * Set the value of color5Text
     *
     * @param ?string $color5Text The value for color5Text
     *
     * @return void
     */
    public function setColor5Text(?string $color5Text): void
    {
        $this->color5Text = $color5Text;
    }

    /**
     * Get the value of color5Link
     *
     * @return ?string The value of color5Link
     */
    public function getColor5Link(): ?string
    {
        return $this->color5Link;
    }

    /**
     * Set the value of color5Link
     *
     * @param ?string $color5Link The value for color5Link
     *
     * @return void
     */
    public function setColor5Link(?string $color5Link): void
    {
        $this->color5Link = $color5Link;
    }

    /**
     * Get the value of color6Bg
     *
     * @return ?string The value of color6Bg
     */
    public function getColor6Bg(): ?string
    {
        return $this->color6Bg;
    }

    /**
     * Set the value of color6Bg
     *
     * @param ?string $color6Bg The value for color6Bg
     *
     * @return void
     */
    public function setColor6Bg(?string $color6Bg): void
    {
        $this->color6Bg = $color6Bg;
    }

    /**
     * Get the value of color6Text
     *
     * @return ?string The value of color6Text
     */
    public function getColor6Text(): ?string
    {
        return $this->color6Text;
    }

    /**
     * Set the value of color6Text
     *
     * @param ?string $color6Text The value for color6Text
     *
     * @return void
     */
    public function setColor6Text(?string $color6Text): void
    {
        $this->color6Text = $color6Text;
    }

    /**
     * Get the value of color6Link
     *
     * @return ?string The value of color6Link
     */
    public function getColor6Link(): ?string
    {
        return $this->color6Link;
    }

    /**
     * Set the value of color6Link
     *
     * @param ?string $color6Link The value for color6Link
     *
     * @return void
     */
    public function setColor6Link(?string $color6Link): void
    {
        $this->color6Link = $color6Link;
    }

    /**
     * Get the value of googleAnalytics
     *
     * @return ?string The value of googleAnalytics
     */
    public function getGoogleAnalytics(): ?string
    {
        return $this->googleAnalytics;
    }

    /**
     * Set the value of googleAnalytics
     *
     * @param ?string $googleAnalytics The value for googleAnalytics
     *
     * @return void
     */
    public function setGoogleAnalytics(?string $googleAnalytics): void
    {
        $this->googleAnalytics = $googleAnalytics;
    }

    /**
     * Get the value of metaGoogleVerify
     *
     * @return ?string The value of metaGoogleVerify
     */
    public function getMetaGoogleVerify(): ?string
    {
        return $this->metaGoogleVerify;
    }

    /**
     * Set the value of metaGoogleVerify
     *
     * @param ?string $metaGoogleVerify The value for metaGoogleVerify
     *
     * @return void
     */
    public function setMetaGoogleVerify(?string $metaGoogleVerify): void
    {
        $this->metaGoogleVerify = $metaGoogleVerify;
    }

    /**
     * Get the value of metaBingVerify
     *
     * @return ?string The value of metaBingVerify
     */
    public function getMetaBingVerify(): ?string
    {
        return $this->metaBingVerify;
    }

    /**
     * Set the value of metaBingVerify
     *
     * @param ?string $metaBingVerify The value for metaBingVerify
     *
     * @return void
     */
    public function setMetaBingVerify(?string $metaBingVerify): void
    {
        $this->metaBingVerify = $metaBingVerify;
    }

    /**
     * Get the value of canonicalAllowed
     *
     * @return ?string The value of canonicalAllowed
     */
    public function getCanonicalAllowed(): ?string
    {
        return $this->canonicalAllowed;
    }

    /**
     * Set the value of canonicalAllowed
     *
     * @param ?string $canonicalAllowed The value for canonicalAllowed
     *
     * @return void
     */
    public function setCanonicalAllowed(?string $canonicalAllowed): void
    {
        $this->canonicalAllowed = $canonicalAllowed;
    }

    /**
     * Get the value of externalUrl
     *
     * @return ?string The value of externalUrl
     */
    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    /**
     * Set the value of externalUrl
     *
     * @param ?string $externalUrl The value for externalUrl
     *
     * @return void
     */
    public function setExternalUrl(?string $externalUrl): void
    {
        $this->externalUrl = $externalUrl;
    }

    /**
     * Get the value of externalTitle
     *
     * @return ?string The value of externalTitle
     */
    public function getExternalTitle(): ?string
    {
        return $this->externalTitle;
    }

    /**
     * Set the value of externalTitle
     *
     * @param ?string $externalTitle The value for externalTitle
     *
     * @return void
     */
    public function setExternalTitle(?string $externalTitle): void
    {
        $this->externalTitle = $externalTitle;
    }

    /**
     * Get the value of externalUrl2
     *
     * @return ?string The value of externalUrl2
     */
    public function getExternalUrl2(): ?string
    {
        return $this->externalUrl2;
    }

    /**
     * Set the value of externalUrl2
     *
     * @param ?string $externalUrl2 The value for externalUrl2
     *
     * @return void
     */
    public function setExternalUrl2(?string $externalUrl2): void
    {
        $this->externalUrl2 = $externalUrl2;
    }

    /**
     * Get the value of externalTitle2
     *
     * @return ?string The value of externalTitle2
     */
    public function getExternalTitle2(): ?string
    {
        return $this->externalTitle2;
    }

    /**
     * Set the value of externalTitle2
     *
     * @param ?string $externalTitle2 The value for externalTitle2
     *
     * @return void
     */
    public function setExternalTitle2(?string $externalTitle2): void
    {
        $this->externalTitle2 = $externalTitle2;
    }

    /**
     * Get the value of externalUrl3
     *
     * @return ?string The value of externalUrl3
     */
    public function getExternalUrl3(): ?string
    {
        return $this->externalUrl3;
    }

    /**
     * Set the value of externalUrl3
     *
     * @param ?string $externalUrl3 The value for externalUrl3
     *
     * @return void
     */
    public function setExternalUrl3(?string $externalUrl3): void
    {
        $this->externalUrl3 = $externalUrl3;
    }

    /**
     * Get the value of externalTitle3
     *
     * @return ?string The value of externalTitle3
     */
    public function getExternalTitle3(): ?string
    {
        return $this->externalTitle3;
    }

    /**
     * Set the value of externalTitle3
     *
     * @param ?string $externalTitle3 The value for externalTitle3
     *
     * @return void
     */
    public function setExternalTitle3(?string $externalTitle3): void
    {
        $this->externalTitle3 = $externalTitle3;
    }

    /**
     * Get the value of featureModelsStudioCode
     *
     * @return ?string The value of featureModelsStudioCode
     */
    public function getFeatureModelsStudioCode(): ?string
    {
        return $this->featureModelsStudioCode;
    }

    /**
     * Set the value of featureModelsStudioCode
     *
     * @param ?string $featureModelsStudioCode The value for featureModelsStudioCode
     *
     * @return void
     */
    public function setFeatureModelsStudioCode(?string $featureModelsStudioCode): void
    {
        $this->featureModelsStudioCode = $featureModelsStudioCode;
    }

    /**
     * Get the value of studioAdminId
     *
     * @return ?int The value of studioAdminId
     */
    public function getStudioAdminId(): ?int
    {
        return $this->studioAdminId;
    }

    /**
     * Set the value of studioAdminId
     *
     * @param ?int $studioAdminId The value for studioAdminId
     *
     * @return void
     */
    public function setStudioAdminId(?int $studioAdminId): void
    {
        $this->studioAdminId = $studioAdminId;
    }

    /**
     * Get the value of recruiterAdminId
     *
     * @return ?int The value of recruiterAdminId
     */
    public function getRecruiterAdminId(): ?int
    {
        return $this->recruiterAdminId;
    }

    /**
     * Set the value of recruiterAdminId
     *
     * @param ?int $recruiterAdminId The value for recruiterAdminId
     *
     * @return void
     */
    public function setRecruiterAdminId(?int $recruiterAdminId): void
    {
        $this->recruiterAdminId = $recruiterAdminId;
    }

    /**
     * Get the value of referralIdType
     *
     * @return ?string The value of referralIdType
     */
    public function getReferralIdType(): ?string
    {
        return $this->referralIdType;
    }

    /**
     * Set the value of referralIdType
     *
     * @param ?string $referralIdType The value for referralIdType
     *
     * @return void
     */
    public function setReferralIdType(?string $referralIdType): void
    {
        $this->referralIdType = $referralIdType;
    }

    /**
     * Get the value of authorizedDomainId
     *
     * @return ?int The value of authorizedDomainId
     */
    public function getAuthorizedDomainId(): ?int
    {
        return $this->authorizedDomainId;
    }

    /**
     * Set the value of authorizedDomainId
     *
     * @param ?int $authorizedDomainId The value for authorizedDomainId
     *
     * @return void
     */
    public function setAuthorizedDomainId(?int $authorizedDomainId): void
    {
        $this->authorizedDomainId = $authorizedDomainId;
    }

    /**
     * Get the value of dateCreated
     *
     * @return ?DateTimeInterface The value of dateCreated
     */
    public function getDateCreated(): ?DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @param ?DateTimeInterface $dateCreated The value for dateCreated
     *
     * @return void
     */
    public function setDateCreated(?DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get the value of dateLastUpdated
     *
     * @return ?DateTimeInterface The value of dateLastUpdated
     */
    public function getDateLastUpdated(): ?DateTimeInterface
    {
        return $this->dateLastUpdated;
    }

    /**
     * Set the value of dateLastUpdated
     *
     * @param ?DateTimeInterface $dateLastUpdated The value for dateLastUpdated
     *
     * @return void
     */
    public function setDateLastUpdated(?DateTimeInterface $dateLastUpdated): void
    {
        $this->dateLastUpdated = $dateLastUpdated;
    }

    /**
     * Get the value of whoisLastVerified
     *
     * @return ?DateTimeInterface The value of whoisLastVerified
     */
    public function getWhoisLastVerified(): ?DateTimeInterface
    {
        return $this->whoisLastVerified;
    }

    /**
     * Set the value of whoisLastVerified
     *
     * @param ?DateTimeInterface $whoisLastVerified The value for whoisLastVerified
     *
     * @return void
     */
    public function setWhoisLastVerified(?DateTimeInterface $whoisLastVerified): void
    {
        $this->whoisLastVerified = $whoisLastVerified;
    }

    /**
     * Get the value of whoisType
     *
     * @return ?string The value of whoisType
     */
    public function getWhoisType(): ?string
    {
        return $this->whoisType;
    }

    /**
     * Set the value of whoisType
     *
     * @param ?string $whoisType The value for whoisType
     *
     * @return void
     */
    public function setWhoisType(?string $whoisType): void
    {
        $this->whoisType = $whoisType;
    }

    /**
     * Get the value of recentTraffic
     *
     * @return ?string The value of recentTraffic
     */
    public function getRecentTraffic(): ?string
    {
        return $this->recentTraffic;
    }

    /**
     * Set the value of recentTraffic
     *
     * @param ?string $recentTraffic The value for recentTraffic
     *
     * @return void
     */
    public function setRecentTraffic(?string $recentTraffic): void
    {
        $this->recentTraffic = $recentTraffic;
    }

    /**
     * Get the value of spamBlacklist
     *
     * @return ?string The value of spamBlacklist
     */
    public function getSpamBlacklist(): ?string
    {
        return $this->spamBlacklist;
    }

    /**
     * Set the value of spamBlacklist
     *
     * @param ?string $spamBlacklist The value for spamBlacklist
     *
     * @return void
     */
    public function setSpamBlacklist(?string $spamBlacklist): void
    {
        $this->spamBlacklist = $spamBlacklist;
    }

    /**
     * Get the value of showDating
     *
     * @return ?string The value of showDating
     */
    public function getShowDating(): ?string
    {
        return $this->showDating;
    }

    /**
     * Set the value of showDating
     *
     * @param ?string $showDating The value for showDating
     *
     * @return void
     */
    public function setShowDating(?string $showDating): void
    {
        $this->showDating = $showDating;
    }

    /**
     * Get the value of status
     *
     * @return ?string The value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param ?string $status The value for status
     *
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the value of sslStatus
     *
     * @return ?string The value of sslStatus
     */
    public function getSslStatus(): ?string
    {
        return $this->sslStatus;
    }

    /**
     * Set the value of sslStatus
     *
     * @param ?string $sslStatus The value for sslStatus
     *
     * @return void
     */
    public function setSslStatus(?string $sslStatus): void
    {
        $this->sslStatus = $sslStatus;
    }

    /**
     * Get the value of isHouse
     *
     * @return ?string The value of isHouse
     */
    public function getIsHouse(): ?string
    {
        return $this->isHouse;
    }

    /**
     * Set the value of isHouse
     *
     * @param ?string $isHouse The value for isHouse
     *
     * @return void
     */
    public function setIsHouse(?string $isHouse): void
    {
        $this->isHouse = $isHouse;
    }

    /**
     * Get the value of certProcessed
     *
     * @return ?int The value of certProcessed
     */
    public function getCertProcessed(): ?int
    {
        return $this->certProcessed;
    }

    /**
     * Set the value of certProcessed
     *
     * @param ?int $certProcessed The value for certProcessed
     *
     * @return void
     */
    public function setCertProcessed(?int $certProcessed): void
    {
        $this->certProcessed = $certProcessed;
    }

    /**
     * Get the value of merchant
     *
     * @return ?string The value of merchant
     */
    public function getMerchant(): ?string
    {
        return $this->merchant;
    }

    /**
     * Set the value of merchant
     *
     * @param ?string $merchant The value for merchant
     *
     * @return void
     */
    public function setMerchant(?string $merchant): void
    {
        $this->merchant = $merchant;
    }

    /**
     * Get the value of sitekey
     *
     * @return ?string The value of sitekey
     */
    public function getSitekey(): ?string
    {
        return $this->sitekey;
    }

    /**
     * Set the value of sitekey
     *
     * @param ?string $sitekey The value for sitekey
     *
     * @return void
     */
    public function setSitekey(?string $sitekey): void
    {
        $this->sitekey = $sitekey;
    }

    /**
     * Get the value of domainReview
     *
     * @return ?string The value of domainReview
     */
    public function getDomainReview(): ?string
    {
        return $this->domainReview;
    }

    /**
     * Set the value of domainReview
     *
     * @param ?string $domainReview The value for domainReview
     *
     * @return void
     */
    public function setDomainReview(?string $domainReview): void
    {
        $this->domainReview = $domainReview;
    }

    /**
     * Get the value of platform
     *
     * @return ?string The value of platform
     */
    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    /**
     * Set the value of platform
     *
     * @param ?string $platform The value for platform
     *
     * @return void
     */
    public function setPlatform(?string $platform): void
    {
        $this->platform = $platform;
    }
}
