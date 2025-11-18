<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformWhiteLabel;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\PlatformWhiteLabelOptions;
use Ubix\Model\PlatformWhiteLabel;
use Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelReaderInterface as PlatformWhiteLabelReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading platform white label data
 *
 * @see \Ubix\Tests\Repository\PlatformWhiteLabel\PlatformWhiteLabelSqlRepositoryTest PHPUnit test case
 */
final class PlatformWhiteLabelSqlRepository implements PlatformWhiteLabelReader
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getByDomain(string $domain): array
    {
        return $this->query(new PlatformWhiteLabelOptions(
            domain: $domain,
            limit:  1,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param PlatformWhiteLabelOptions $options DTO of options to generate the query
     *
     * @return PlatformWhiteLabel[] An array of platform white label object(s)
     */
    private function query(PlatformWhiteLabelOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					wl.domain,
					wl.affiliate_id,
					wl.mp_code,
					wl.template_set,
					wl.title,
					wl.short_title,
					wl.meta_keywords,
					wl.meta_description,
					wl.custom_text,
					wl.service_default,
					wl.service_girls,
					wl.service_guys,
					wl.service_trans,
					wl.logo_mp_code,
					wl.background_url,
					wl.external_css_url,
					wl.external_favicon_url,
					wl.color_1_bg,
					wl.color_1_text,
					wl.color_1_link,
					wl.color_2_bg,
					wl.color_2_text,
					wl.color_2_link,
					wl.color_3_bg,
					wl.color_3_text,
					wl.color_3_link,
					wl.color_4_bg,
					wl.color_4_text,
					wl.color_4_link,
					wl.color_5_bg,
					wl.color_5_text,
					wl.color_5_link,
					wl.color_6_bg,
					wl.color_6_text,
					wl.color_6_link,
					wl.google_analytics,
					wl.meta_google_verify,
					wl.meta_bing_verify,
					wl.canonical_allowed,
					wl.external_url,
					wl.external_title,
					wl.external_url_2,
					wl.external_title_2,
					wl.external_url_3,
					wl.external_title_3,
					wl.feature_models_studio_code,
					wl.studio_admin_id,
					wl.recruiter_admin_id,
					wl.referral_id_type,
					wl.authorized_domain_id,
					wl.date_created,
					wl.date_last_updated,
					wl.whois_last_verified,
					wl.whois_type,
					wl.recent_traffic,
					wl.spam_blacklist,
					wl.show_dating,
					wl.status,
					wl.ssl_status,
					wl.is_house,
					wl.cert_processed,
					wl.merchant,
					wl.sitekey,
					wl.domain_review,
					wl.platform
				FROM VSCASH.White_Label wl
				WHERE 1 = 1';

        if ($options->domain !== null) {
            $sql .= ' AND wl.domain = :domain';

            $parameters['domain'] = $options->domain;
        }

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     domain: ?string,
         *     affiliate_id: ?int,
         *     mp_code: ?string,
         *     template_set: ?string,
         *     title: ?string,
         *     short_title: ?string,
         *     meta_keywords: ?string,
         *     meta_description: ?string,
         *     custom_text: ?string,
         *     service_default: ?string,
         *     service_girls: ?string,
         *     service_guys: ?string,
         *     service_trans: ?string,
         *     logo_mp_code: ?string,
         *     background_url: ?string,
         *     external_css_url: ?string,
         *     external_favicon_url: ?string,
         *     color_1_bg: ?string,
         *     color_1_text: ?string,
         *     color_1_link: ?string,
         *     color_2_bg: ?string,
         *     color_2_text: ?string,
         *     color_2_link: ?string,
         *     color_3_bg: ?string,
         *     color_3_text: ?string,
         *     color_3_link: ?string,
         *     color_4_bg: ?string,
         *     color_4_text: ?string,
         *     color_4_link: ?string,
         *     color_5_bg: ?string,
         *     color_5_text: ?string,
         *     color_5_link: ?string,
         *     color_6_bg: ?string,
         *     color_6_text: ?string,
         *     color_6_link: ?string,
         *     google_analytics: ?string,
         *     meta_google_verify: ?string,
         *     meta_bing_verify: ?string,
         *     canonical_allowed: ?string,
         *     external_url: ?string,
         *     external_title: ?string,
         *     external_url_2: ?string,
         *     external_title_2: ?string,
         *     external_url_3: ?string,
         *     external_title_3: ?string,
         *     feature_models_studio_code: ?string,
         *     studio_admin_id: ?int,
         *     recruiter_admin_id: ?int,
         *     referral_id_type: ?string,
         *     authorized_domain_id: ?int,
         *     date_created: ?string,
         *     date_last_updated: ?string,
         *     whois_last_verified: ?string,
         *     whois_type: ?string,
         *     recent_traffic: ?string,
         *     spam_blacklist: ?string,
         *     show_dating: ?string,
         *     status: ?string,
         *     ssl_status: ?string,
         *     is_house: ?string,
         *     cert_processed: ?int,
         *     merchant: ?string,
         *     sitekey: ?string,
         *     domain_review: ?string,
         *     platform: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters, true) as $row) {
            $objects[] = new PlatformWhiteLabel(
                domain:                  $row['domain'],
                affiliateId:             $row['affiliate_id'],
                mpCode:                  $row['mp_code'],
                templateSet:             $row['template_set'],
                title:                   $row['title'],
                shortTitle:              $row['short_title'],
                metaKeywords:            $row['meta_keywords'],
                metaDescription:         $row['meta_description'],
                customText:              $row['custom_text'],
                serviceDefault:          $row['service_default'],
                serviceGirls:            $row['service_girls'],
                serviceGuys:             $row['service_guys'],
                serviceTrans:            $row['service_trans'],
                logoMpCode:              $row['logo_mp_code'],
                backgroundUrl:           $row['background_url'],
                externalCssUrl:          $row['external_css_url'],
                externalFaviconUrl:      $row['external_favicon_url'],
                color1Bg:                $row['color_1_bg'],
                color1Text:              $row['color_1_text'],
                color1Link:              $row['color_1_link'],
                color2Bg:                $row['color_2_bg'],
                color2Text:              $row['color_2_text'],
                color2Link:              $row['color_2_link'],
                color3Bg:                $row['color_3_bg'],
                color3Text:              $row['color_3_text'],
                color3Link:              $row['color_3_link'],
                color4Bg:                $row['color_4_bg'],
                color4Text:              $row['color_4_text'],
                color4Link:              $row['color_4_link'],
                color5Bg:                $row['color_5_bg'],
                color5Text:              $row['color_5_text'],
                color5Link:              $row['color_5_link'],
                color6Bg:                $row['color_6_bg'],
                color6Text:              $row['color_6_text'],
                color6Link:              $row['color_6_link'],
                googleAnalytics:         $row['google_analytics'],
                metaGoogleVerify:        $row['meta_google_verify'],
                metaBingVerify:          $row['meta_bing_verify'],
                canonicalAllowed:        $row['canonical_allowed'],
                externalUrl:             $row['external_url'],
                externalTitle:           $row['external_title'],
                externalUrl2:            $row['external_url_2'],
                externalTitle2:          $row['external_title_2'],
                externalUrl3:            $row['external_url_3'],
                externalTitle3:          $row['external_title_3'],
                featureModelsStudioCode: $row['feature_models_studio_code'],
                studioAdminId:           $row['studio_admin_id'],
                recruiterAdminId:        $row['recruiter_admin_id'],
                referralIdType:          $row['referral_id_type'],
                authorizedDomainId:      $row['authorized_domain_id'],
                dateCreated:             $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
                dateLastUpdated:         $row['date_last_updated'] !== null ? new DateTime($row['date_last_updated']) : null,
                whoisLastVerified:       $row['whois_last_verified'] !== null ? new DateTime($row['whois_last_verified']) : null,
                whoisType:               $row['whois_type'],
                recentTraffic:           $row['recent_traffic'],
                spamBlacklist:           $row['spam_blacklist'],
                showDating:              $row['show_dating'],
                status:                  $row['status'],
                sslStatus:               $row['ssl_status'],
                isHouse:                 $row['is_house'],
                certProcessed:           $row['cert_processed'],
                merchant:                $row['merchant'],
                sitekey:                 $row['sitekey'],
                domainReview:            $row['domain_review'],
                platform:                $row['platform'],
            );
        }

        return $objects;
    }
}
