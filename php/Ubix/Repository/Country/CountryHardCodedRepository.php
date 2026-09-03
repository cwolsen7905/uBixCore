<?php

declare(strict_types=1);

namespace Ubix\Repository\Country;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\Country;
use Ubix\Repository\Country\CountryReaderInterface as CountryReader;

/**
 * Hard coded repository for reading country data
 *
 * @see \Ubix\Tests\Repository\Country\CountryHardCodedRepositoryTest PHPUnit test case
 */
final class CountryHardCodedRepository implements CountryReader
{
    /**
     * @var Country[] $countriesSingleton
     */
    private static ?array $countriesSingleton = null;

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
        //
        //  If the singleton is not yet populated, populate it now
        //
        if (self::$countriesSingleton === null) {
            //
            //  Source: https://en.wikipedia.org/wiki/ISO_3166-1
            //
            $this->addCountry('Afghanistan', 'AF', 'AFG');
            $this->addCountry('Aland Islands', 'AX', 'ALA');
            $this->addCountry('Albania', 'AL', 'ALB');
            $this->addCountry('Algeria', 'DZ', 'DZA');
            $this->addCountry('American Samoa', 'AS', 'ASM');
            $this->addCountry('Andorra', 'AD', 'AND');
            $this->addCountry('Angola', 'AO', 'AGO');
            $this->addCountry('Anguilla', 'AI', 'AIA');
            $this->addCountry('Antarctica', 'AQ', 'ATA');
            $this->addCountry('Antigua And Barbuda', 'AG', 'ATG');
            $this->addCountry('Argentina', 'AR', 'ARG');
            $this->addCountry('Armenia', 'AM', 'ARM');
            $this->addCountry('Aruba', 'AW', 'ABW');
            $this->addCountry('Australia', 'AU', 'AUS');
            $this->addCountry('Austria', 'AT', 'AUT');
            $this->addCountry('Azerbaijan', 'AZ', 'AZE');
            $this->addCountry('Bahamas', 'BS', 'BHS');
            $this->addCountry('Bahrain', 'BH', 'BHR');
            $this->addCountry('Bangladesh', 'BD', 'BGD');
            $this->addCountry('Barbados', 'BB', 'BRB');
            $this->addCountry('Belarus', 'BY', 'BLR');
            $this->addCountry('Belgium', 'BE', 'BEL');
            $this->addCountry('Belize', 'BZ', 'BLZ');
            $this->addCountry('Benin', 'BJ', 'BEN');
            $this->addCountry('Bermuda', 'BM', 'BMU');
            $this->addCountry('Bhutan', 'BT', 'BTN');
            $this->addCountry('Bolivia', 'BO', 'BOL');
            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This country is not being supported by VSM but exists on the ISO 3166-1 list
            // $this->addCountry('Bonaire, Sint Eustatius and Saba', 'BQ', 'BES');
            $this->addCountry('Bosnia And Herzegovina', 'BA', 'BIH');
            $this->addCountry('Botswana', 'BW', 'BWA');
            $this->addCountry('Bouvet Island', 'BV', 'BVT');
            $this->addCountry('Brazil', 'BR', 'BRA');
            $this->addCountry('British Indian Ocean Territory', 'IO', 'IOT');
            $this->addCountry('Brunei Darussalam', 'BN', 'BRN');
            $this->addCountry('Bulgaria', 'BG', 'BGR');
            $this->addCountry('Burkina Faso', 'BF', 'BFA');
            $this->addCountry('Burundi', 'BI', 'BDI');
            $this->addCountry('Cape Verde', 'CV', 'CPV');
            $this->addCountry('Cambodia', 'KH', 'KHM');
            $this->addCountry('Cameroon', 'CM', 'CMR');
            $this->addCountry('Canada', 'CA', 'CAN');
            $this->addCountry('Cayman Islands', 'KY', 'CYM');
            $this->addCountry('Central African Republic', 'CF', 'CAF');
            $this->addCountry('Chad', 'TD', 'TCD');
            $this->addCountry('Chile', 'CL', 'CHL');
            $this->addCountry('China', 'CN', 'CHN');
            $this->addCountry('Christmas Island', 'CX', 'CXR');
            $this->addCountry('Cocos (Keeling) Islands', 'CC', 'CCK');
            $this->addCountry('Colombia', 'CO', 'COL');
            $this->addCountry('Comoros', 'KM', 'COM');
            $this->addCountry('Congo', 'CG', 'COG');
            $this->addCountry('Congo, Democratic Republic', 'CD', 'COD');
            $this->addCountry('Cook Islands', 'CK', 'COK');
            $this->addCountry('Costa Rica', 'CR', 'CRI');
            $this->addCountry('Croatia', 'HR', 'HRV');
            $this->addCountry('Cuba', 'CU', 'CUB');
            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This country is not being supported by VSM but exists on the ISO 3166-1 list
            // $this->addCountry('Curaçao', 'CW', 'CUW');
            $this->addCountry('Cyprus', 'CY', 'CYP');
            $this->addCountry('Czech Republic', 'CZ', 'CZE');
            $this->addCountry('Cote D\'Ivoire', 'CI', 'CIV');
            $this->addCountry('Denmark', 'DK', 'DNK');
            $this->addCountry('Djibouti', 'DJ', 'DJI');
            $this->addCountry('Dominica', 'DM', 'DMA');
            $this->addCountry('Dominican Republic', 'DO', 'DOM');
            $this->addCountry('Ecuador', 'EC', 'ECU');
            $this->addCountry('Egypt', 'EG', 'EGY');
            $this->addCountry('El Salvador', 'SV', 'SLV');
            $this->addCountry('Equatorial Guinea', 'GQ', 'GNQ');
            $this->addCountry('Eritrea', 'ER', 'ERI');
            $this->addCountry('Estonia', 'EE', 'EST');
            $this->addCountry('Swaziland', 'SZ', 'SWZ');
            $this->addCountry('Ethiopia', 'ET', 'ETH');
            $this->addCountry('Falkland Islands (Malvinas)', 'FK', 'FLK');
            $this->addCountry('Faroe Islands', 'FO', 'FRO');
            $this->addCountry('Fiji', 'FJ', 'FJI');
            $this->addCountry('Finland', 'FI', 'FIN');
            $this->addCountry('France', 'FR', 'FRA');
            $this->addCountry('French Guiana', 'GF', 'GUF');
            $this->addCountry('French Polynesia', 'PF', 'PYF');
            $this->addCountry('French Southern Territories', 'TF', 'ATF');
            $this->addCountry('Gabon', 'GA', 'GAB');
            $this->addCountry('Gambia', 'GM', 'GMB');
            $this->addCountry('Georgia', 'GE', 'GEO');
            $this->addCountry('Germany', 'DE', 'DEU');
            $this->addCountry('Ghana', 'GH', 'GHA');
            $this->addCountry('Gibraltar', 'GI', 'GIB');
            $this->addCountry('Greece', 'GR', 'GRC');
            $this->addCountry('Greenland', 'GL', 'GRL');
            $this->addCountry('Grenada', 'GD', 'GRD');
            $this->addCountry('Guadeloupe', 'GP', 'GLP');
            $this->addCountry('Guam', 'GU', 'GUM');
            $this->addCountry('Guatemala', 'GT', 'GTM');
            $this->addCountry('Guernsey', 'GG', 'GGY');
            $this->addCountry('Guinea', 'GN', 'GIN');
            $this->addCountry('Guinea-Bissau', 'GW', 'GNB');
            $this->addCountry('Guyana', 'GY', 'GUY');
            $this->addCountry('Haiti', 'HT', 'HTI');
            $this->addCountry('Heard Island & Mcdonald Islands', 'HM', 'HMD');
            $this->addCountry('Holy See (Vatican City State)', 'VA', 'VAT');
            $this->addCountry('Honduras', 'HN', 'HND');
            $this->addCountry('Hong Kong', 'HK', 'HKG');
            $this->addCountry('Hungary', 'HU', 'HUN');
            $this->addCountry('Iceland', 'IS', 'ISL');
            $this->addCountry('India', 'IN', 'IND');
            $this->addCountry('Indonesia', 'ID', 'IDN');
            $this->addCountry('Iran, Islamic Republic Of', 'IR', 'IRN');
            $this->addCountry('Iraq', 'IQ', 'IRQ');
            $this->addCountry('Ireland', 'IE', 'IRL');
            $this->addCountry('Isle Of Man', 'IM', 'IMN');
            $this->addCountry('Israel', 'IL', 'ISR');
            $this->addCountry('Italy', 'IT', 'ITA');
            $this->addCountry('Jamaica', 'JM', 'JAM');
            $this->addCountry('Japan', 'JP', 'JPN');
            $this->addCountry('Jersey', 'JE', 'JEY');
            $this->addCountry('Jordan', 'JO', 'JOR');
            $this->addCountry('Kazakhstan', 'KZ', 'KAZ');
            $this->addCountry('Kenya', 'KE', 'KEN');
            $this->addCountry('Kiribati', 'KI', 'KIR');
            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This country is not being supported by VSM but exists on the ISO 3166-1 list
            // $this->addCountry('Korea (Democratic People\'s Republic of)', 'KP', 'PRK');
            $this->addCountry('Korea', 'KR', 'KOR');
            $this->addCountry('Kuwait', 'KW', 'KWT');
            $this->addCountry('Kyrgyzstan', 'KG', 'KGZ');
            $this->addCountry('Lao People\'s Democratic Republic', 'LA', 'LAO');
            $this->addCountry('Latvia', 'LV', 'LVA');
            $this->addCountry('Lebanon', 'LB', 'LBN');
            $this->addCountry('Lesotho', 'LS', 'LSO');
            $this->addCountry('Liberia', 'LR', 'LBR');
            $this->addCountry('Libyan Arab Jamahiriya', 'LY', 'LBY');
            $this->addCountry('Liechtenstein', 'LI', 'LIE');
            $this->addCountry('Lithuania', 'LT', 'LTU');
            $this->addCountry('Luxembourg', 'LU', 'LUX');
            $this->addCountry('Macao', 'MO', 'MAC');
            $this->addCountry('Madagascar', 'MG', 'MDG');
            $this->addCountry('Malawi', 'MW', 'MWI');
            $this->addCountry('Malaysia', 'MY', 'MYS');
            $this->addCountry('Maldives', 'MV', 'MDV');
            $this->addCountry('Mali', 'ML', 'MLI');
            $this->addCountry('Malta', 'MT', 'MLT');
            $this->addCountry('Marshall Islands', 'MH', 'MHL');
            $this->addCountry('Martinique', 'MQ', 'MTQ');
            $this->addCountry('Mauritania', 'MR', 'MRT');
            $this->addCountry('Mauritius', 'MU', 'MUS');
            $this->addCountry('Mayotte', 'YT', 'MYT');
            $this->addCountry('Mexico', 'MX', 'MEX');
            $this->addCountry('Micronesia, Federated States Of', 'FM', 'FSM');
            $this->addCountry('Moldova', 'MD', 'MDA');
            $this->addCountry('Monaco', 'MC', 'MCO');
            $this->addCountry('Mongolia', 'MN', 'MNG');
            $this->addCountry('Montenegro', 'ME', 'MNE');
            $this->addCountry('Montserrat', 'MS', 'MSR');
            $this->addCountry('Morocco', 'MA', 'MAR');
            $this->addCountry('Mozambique', 'MZ', 'MOZ');
            $this->addCountry('Myanmar', 'MM', 'MMR');
            $this->addCountry('Namibia', 'NA', 'NAM');
            $this->addCountry('Nauru', 'NR', 'NRU');
            $this->addCountry('Nepal', 'NP', 'NPL');
            $this->addCountry('Netherlands', 'NL', 'NLD');
            $this->addCountry('New Caledonia', 'NC', 'NCL');
            $this->addCountry('New Zealand', 'NZ', 'NZL');
            $this->addCountry('Nicaragua', 'NI', 'NIC');
            $this->addCountry('Niger', 'NE', 'NER');
            $this->addCountry('Nigeria', 'NG', 'NGA');
            $this->addCountry('Niue', 'NU', 'NIU');
            $this->addCountry('Norfolk Island', 'NF', 'NFK');
            $this->addCountry('Macedonia', 'MK', 'MKD');
            $this->addCountry('Northern Mariana Islands', 'MP', 'MNP');
            $this->addCountry('Norway', 'NO', 'NOR');
            $this->addCountry('Oman', 'OM', 'OMN');
            $this->addCountry('Pakistan', 'PK', 'PAK');
            $this->addCountry('Palau', 'PW', 'PLW');
            $this->addCountry('Palestinian Territory, Occupied', 'PS', 'PSE');
            $this->addCountry('Panama', 'PA', 'PAN');
            $this->addCountry('Papua New Guinea', 'PG', 'PNG');
            $this->addCountry('Paraguay', 'PY', 'PRY');
            $this->addCountry('Peru', 'PE', 'PER');
            $this->addCountry('Philippines', 'PH', 'PHL');
            $this->addCountry('Pitcairn', 'PN', 'PCN');
            $this->addCountry('Poland', 'PL', 'POL');
            $this->addCountry('Portugal', 'PT', 'PRT');
            $this->addCountry('Puerto Rico', 'PR', 'PRI');
            $this->addCountry('Qatar', 'QA', 'QAT');
            $this->addCountry('Reunion', 'RE', 'REU');
            $this->addCountry('Romania', 'RO', 'ROU');
            $this->addCountry('Russian Federation', 'RU', 'RUS');
            $this->addCountry('Rwanda', 'RW', 'RWA');
            $this->addCountry('Saint Barthelemy', 'BL', 'BLM');
            $this->addCountry('Saint Helena', 'SH', 'SHN');
            $this->addCountry('Saint Kitts And Nevis', 'KN', 'KNA');
            $this->addCountry('Saint Lucia', 'LC', 'LCA');
            $this->addCountry('Saint Martin', 'MF', 'MAF');
            $this->addCountry('Saint Pierre And Miquelon', 'PM', 'SPM');
            $this->addCountry('Saint Vincent And Grenadines', 'VC', 'VCT');
            $this->addCountry('Samoa', 'WS', 'WSM');
            $this->addCountry('San Marino', 'SM', 'SMR');
            $this->addCountry('Sao Tome And Principe', 'ST', 'STP');
            $this->addCountry('Saudi Arabia', 'SA', 'SAU');
            $this->addCountry('Senegal', 'SN', 'SEN');
            $this->addCountry('Serbia', 'RS', 'SRB');
            $this->addCountry('Seychelles', 'SC', 'SYC');
            $this->addCountry('Sierra Leone', 'SL', 'SLE');
            $this->addCountry('Singapore', 'SG', 'SGP');
            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This country is not being supported by VSM but exists on the ISO 3166-1 list
            // $this->addCountry('Sint Maarten (Dutch part)', 'SX', 'SXM');
            $this->addCountry('Slovakia', 'SK', 'SVK');
            $this->addCountry('Slovenia', 'SI', 'SVN');
            $this->addCountry('Solomon Islands', 'SB', 'SLB');
            $this->addCountry('Somalia', 'SO', 'SOM');
            $this->addCountry('South Africa', 'ZA', 'ZAF');
            $this->addCountry('South Georgia And Sandwich Isl.', 'GS', 'SGS');
            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This country is not being supported by VSM but exists on the ISO 3166-1 list
            // $this->addCountry('South Sudan', 'SS', 'SSD');
            $this->addCountry('Spain', 'ES', 'ESP');
            $this->addCountry('Sri Lanka', 'LK', 'LKA');
            $this->addCountry('Sudan', 'SD', 'SDN');
            $this->addCountry('Suriname', 'SR', 'SUR');
            $this->addCountry('Svalbard And Jan Mayen', 'SJ', 'SJM');
            $this->addCountry('Sweden', 'SE', 'SWE');
            $this->addCountry('Switzerland', 'CH', 'CHE');
            $this->addCountry('Syrian Arab Republic', 'SY', 'SYR');
            $this->addCountry('Taiwan', 'TW', 'TWN');
            $this->addCountry('Tajikistan', 'TJ', 'TJK');
            $this->addCountry('Tanzania', 'TZ', 'TZA');
            $this->addCountry('Thailand', 'TH', 'THA');
            $this->addCountry('Timor-Leste', 'TL', 'TLS');
            $this->addCountry('Togo', 'TG', 'TGO');
            $this->addCountry('Tokelau', 'TK', 'TKL');
            $this->addCountry('Tonga', 'TO', 'TON');
            $this->addCountry('Trinidad And Tobago', 'TT', 'TTO');
            $this->addCountry('Tunisia', 'TN', 'TUN');
            $this->addCountry('Turkey', 'TR', 'TUR');
            $this->addCountry('Turkmenistan', 'TM', 'TKM');
            $this->addCountry('Turks And Caicos Islands', 'TC', 'TCA');
            $this->addCountry('Tuvalu', 'TV', 'TUV');
            $this->addCountry('Uganda', 'UG', 'UGA');
            $this->addCountry('Ukraine', 'UA', 'UKR');
            $this->addCountry('United Arab Emirates', 'AE', 'ARE');
            $this->addCountry('United Kingdom', 'GB', 'GBR');
            $this->addCountry('United States Outlying Islands', 'UM', 'UMI');
            $this->addCountry('United States', 'US', 'USA');
            $this->addCountry('Uruguay', 'UY', 'URY');
            $this->addCountry('Uzbekistan', 'UZ', 'UZB');
            $this->addCountry('Vanuatu', 'VU', 'VUT');
            $this->addCountry('Venezuela', 'VE', 'VEN');
            $this->addCountry('Viet Nam', 'VN', 'VNM');
            $this->addCountry('Virgin Islands, British', 'VG', 'VGB');
            $this->addCountry('Virgin Islands, U.S.', 'VI', 'VIR');
            $this->addCountry('Wallis And Futuna', 'WF', 'WLF');
            $this->addCountry('Western Sahara', 'EH', 'ESH');
            $this->addCountry('Yemen', 'YE', 'YEM');
            $this->addCountry('Zambia', 'ZM', 'ZMB');
            $this->addCountry('Zimbabwe', 'ZW', 'ZWE');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getByName(string $name): array
    {
        //
        //  Search the singleton array for a matching country name
        //
        foreach (self::$countriesSingleton ?? [] as $country) {
            if ($country->getName() === $name) {
                return [$country];
            }
        }

        //
        //  If no results are found then return empty array
        //
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getByIso31661Alpha2(string $iso31661Alpha2): array
    {
        //
        //  Search the singleton array for a matching ISO 3166-1 alpha-2 code (two letter code)
        //
        foreach (self::$countriesSingleton ?? [] as $country) {
            if ($country->getIso31661Alpha2() === $iso31661Alpha2) {
                return [$country];
            }
        }

        //
        //  If no results are found then return empty array
        //
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getByIso31661Alpha3(string $iso31661Alpha3): array
    {
        //
        //  Search the singleton array for a matching ISO 3166-1 alpha-3 code (three letter code)
        //
        foreach (self::$countriesSingleton ?? [] as $country) {
            if ($country->getIso31661Alpha3() === $iso31661Alpha3) {
                return [$country];
            }
        }

        //
        //  If no results are found then return empty array
        //
        return [];
    }

    /**
     * Add a country to the singleton array
     *
     * @param string $name           The country's name
     * @param string $iso31661Alpha2 The country's ISO 3166-1 alpha-2 code (two letter code)
     * @param string $iso31661Alpha3 The country's ISO 3166-1 alpha-3 code (three letter code)
     *
     * @return void
     */
    private function addCountry(string $name, string $iso31661Alpha2, string $iso31661Alpha3): void
    {
        self::$countriesSingleton[] = new Country(
            name:           $name,
            iso31661Alpha2: $iso31661Alpha2,
            iso31661Alpha3: $iso31661Alpha3,
        );
    }
}
