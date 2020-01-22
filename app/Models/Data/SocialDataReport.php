<?php

namespace App\Models\Data;

class SocialDataReport extends ModelData
{
    protected $dates = ['deleted_at', 'imported_at'];

    protected $guarded = [];

    protected $table = 'social_data_reports';

    // this list of countries id listed on https://gush.co/social-data/dictionaries
    const SD_COUNTRY_SG = 536780;
    const SD_COUNTRY_MY = 2108121;
    const SD_COUNTRY_ID = 304751;
    const SD_COUNTRY_TH = 2067731;
    const SD_COUNTRY_PH = 443174;
    const SD_COUNTRY_KR = 307756;
    const SD_COUNTRY_JP = 382313;
    const SD_COUNTRY_VN = 49915;
    const SD_COUNTRY_US = 148838;
    const SD_COUNTRY_CN = 270056;

    // this list of interests id listed on https://gush.co/social-data/dictionaries
    const INTEREST_TELEVISION = 1;
    const INTEREST_MUSIC = 3;
    const INTEREST_SHOPPING_AND_RETAIL = 7;
    const INTEREST_COFFEE_TEA_AND_BEVERAGES = 9;
    const INTEREST_CAMERA_AND_PHOTOGRAPHY = 11;
    const INTEREST_CLOTHES_SHOES_HANDBAGS_AND_ACCESSORIES = 13;
    const INTEREST_BEER_WINE_AND_SPIRITS = 19;
    const INTEREST_SPORTS = 21;
    const INTEREST_ELECTRONICS_AND_COMPUTERS = 25;
    const INTEREST_GAMING = 30;
    const INTEREST_ACTIVEWEAR = 33;
    const INTEREST_ART_AND_DESIGN = 36;
    const INTEREST_TRAVEL_TOURISM_AND_AVIATION = 43;
    const INTEREST_BUSINESS_AND_CAREERS = 58;
    const INTEREST_BEAUTY_AND_COSMETICS = 80;
    const INTEREST_HEALTHCARE_AND_MEDICINE = 100;
    const INTEREST_JEWELLERY_AND_WATCHES = 130;
    const INTEREST_RESTAURANT_FOOD_AND_GROCERY = 139;
    const INTEREST_TOYS_CHILDREN_AND_BABY = 190;
    const INTEREST_FITNESS_AND_YOGA = 196;
    const INTEREST_WEDDING = 291;
    const INTEREST_TOBACCO_AND_SMOKING = 405;
    const INTEREST_PETS = 673;
    const INTEREST_HEALHTY_LIFESTYLE = 798;
    const INTEREST_LUXURY_GOODS = 1500;
    const INTEREST_HOME_DECOR_FURNITURE_AND_GARDEN = 1560;
    const INTEREST_FRIENDS_FAMILY_AND_RELATIONSHIPS = 1708;
    const INTEREST_CARS_AND_MOTORBIKES = 1826;

    const FILTER_LIMIT = 3000;

    private const COUNTRIES_ENABLED = [
        self::SD_COUNTRY_SG,
        self::SD_COUNTRY_MY,
        self::SD_COUNTRY_ID,
        self::SD_COUNTRY_TH,
        self::SD_COUNTRY_PH,
        self::SD_COUNTRY_KR,
        self::SD_COUNTRY_JP,
        self::SD_COUNTRY_VN,
        self::SD_COUNTRY_US,
        self::SD_COUNTRY_CN,
    ];

    private const COUNTRIES_NAME = [
        self::SD_COUNTRY_SG => 'Singapore',
        self::SD_COUNTRY_MY => 'Malaysia',
        self::SD_COUNTRY_ID => 'Indonesia',
        self::SD_COUNTRY_TH => 'Thailand',
        self::SD_COUNTRY_PH => 'Philipine',
        self::SD_COUNTRY_KR => 'Korea',
        self::SD_COUNTRY_JP => 'Japan',
        self::SD_COUNTRY_VN => 'Vietnam',
        self::SD_COUNTRY_US => 'United States',
        self::SD_COUNTRY_CN => 'China',
    ];

    private const FOLLOWERS_LIMIT = [
        self::SD_COUNTRY_SG => 12500,
        self::SD_COUNTRY_MY => 75000,
        self::SD_COUNTRY_ID => 400000,
        self::SD_COUNTRY_TH => 100000,
        self::SD_COUNTRY_PH => 20000,
        self::SD_COUNTRY_KR => 60000,
        self::SD_COUNTRY_JP => 70000,
        self::SD_COUNTRY_VN => 20000,
        self::SD_COUNTRY_US => 900000,
        self::SD_COUNTRY_CN => 7500,
    ];

    public static function getCountryName($country_id)
    {
        return isset(self::COUNTRIES_NAME[$country_id]) ? self::COUNTRIES_NAME[$country_id] : null;
    }

    public static function getCountriesEnabled()
    {
        if (env('APP_ENV') === 'testing') {
            return [
                self::SD_COUNTRY_SG,
                self::SD_COUNTRY_MY,
                self::SD_COUNTRY_ID,
            ];
        }

        return self::COUNTRIES_ENABLED;
    }

    public static function getReportGenerationFiltersDryRun($country_id)
    {
        $filters = self::getReportGenerationFilters($country_id);
        $filters['dry_run'] = true;
        // unset($filters['filter']['list']); // put this if want to see the original query
        return $filters;
    }

    public static function getReportGenerationFilters($country_id)
    {
        $generalFilter = [
            'geo' => [
                ['id' => $country_id]
            ],
            // 'engagement_rate' => [
            //     'value' => 1,
            //     'operator' => 'gte'
            // ],
            'list' => [
                [
                    'id' => 172, // exclude list id 172 which are already exported
                    'action' => 'not',
                ]
            ]
        ];
        switch ($country_id) {
            case self::SD_COUNTRY_SG:
                $countryFilter = [
                    'brand_category' => [
                        // Beauty
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        // Parenting
                        self::INTEREST_TOYS_CHILDREN_AND_BABY,
                        // Lifestyle
                        self::INTEREST_CAMERA_AND_PHOTOGRAPHY,
                        self::INTEREST_FRIENDS_FAMILY_AND_RELATIONSHIPS,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_HOME_DECOR_FURNITURE_AND_GARDEN,
                        self::INTEREST_TRAVEL_TOURISM_AND_AVIATION,
                        self::INTEREST_WEDDING,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_SG]
                    ]
                ];
                break;
            case self::SD_COUNTRY_MY:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        self::INTEREST_CAMERA_AND_PHOTOGRAPHY,
                        self::INTEREST_FRIENDS_FAMILY_AND_RELATIONSHIPS,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_HOME_DECOR_FURNITURE_AND_GARDEN,
                        self::INTEREST_TRAVEL_TOURISM_AND_AVIATION,
                        self::INTEREST_WEDDING,
                        // Entertainment
                        self::INTEREST_TELEVISION,
                        self::INTEREST_MUSIC,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_MY]
                    ]
                ];
                break;
            case self::SD_COUNTRY_ID:
                $countryFilter = [
                    'brand_category' => [
                        // Gaming
                        self::INTEREST_GAMING,

                        self::INTEREST_TOYS_CHILDREN_AND_BABY,

                        // Fashion
                        self::INTEREST_JEWELLERY_AND_WATCHES,
                        self::INTEREST_LUXURY_GOODS,
                        self::INTEREST_SHOPPING_AND_RETAIL,
                        self::INTEREST_CLOTHES_SHOES_HANDBAGS_AND_ACCESSORIES,
                        self::INTEREST_BEAUTY_AND_COSMETICS,

                        // Travel
                        self::INTEREST_TRAVEL_TOURISM_AND_AVIATION,

                        // Health
                        self::INTEREST_HEALTHCARE_AND_MEDICINE,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_FITNESS_AND_YOGA,
                        self::INTEREST_SPORTS,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_ID]
                    ]
                ];
                break;
            case self::SD_COUNTRY_TH:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        self::INTEREST_TRAVEL_TOURISM_AND_AVIATION,
                        self::INTEREST_TOYS_CHILDREN_AND_BABY,
                        self::INTEREST_CAMERA_AND_PHOTOGRAPHY,
                        self::INTEREST_FRIENDS_FAMILY_AND_RELATIONSHIPS,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_HOME_DECOR_FURNITURE_AND_GARDEN,
                        self::INTEREST_WEDDING,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_TH]
                    ]
                ];
                break;
            case self::SD_COUNTRY_PH:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_JEWELLERY_AND_WATCHES,
                        self::INTEREST_LUXURY_GOODS,
                        self::INTEREST_SHOPPING_AND_RETAIL,
                        self::INTEREST_CLOTHES_SHOES_HANDBAGS_AND_ACCESSORIES,
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        self::INTEREST_ELECTRONICS_AND_COMPUTERS,
                        self::INTEREST_SPORTS,
                        self::INTEREST_ACTIVEWEAR,
                        self::INTEREST_HEALTHCARE_AND_MEDICINE,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_FITNESS_AND_YOGA,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_PH]
                    ]
                ];
                break;
            case self::SD_COUNTRY_KR:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        self::INTEREST_JEWELLERY_AND_WATCHES,
                        self::INTEREST_LUXURY_GOODS,
                        self::INTEREST_SHOPPING_AND_RETAIL,
                        self::INTEREST_CLOTHES_SHOES_HANDBAGS_AND_ACCESSORIES,
                        self::INTEREST_TOYS_CHILDREN_AND_BABY,
                        self::INTEREST_CAMERA_AND_PHOTOGRAPHY,
                        self::INTEREST_FRIENDS_FAMILY_AND_RELATIONSHIPS,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_HOME_DECOR_FURNITURE_AND_GARDEN,
                        self::INTEREST_WEDDING,
                        self::INTEREST_ELECTRONICS_AND_COMPUTERS,
                        self::INTEREST_TRAVEL_TOURISM_AND_AVIATION,
                        self::INTEREST_PETS,
                        self::INTEREST_ACTIVEWEAR,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_KR]
                    ]
                ];
                break;
            case self::SD_COUNTRY_JP:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_BEER_WINE_AND_SPIRITS,
                        self::INTEREST_COFFEE_TEA_AND_BEVERAGES,
                        self::INTEREST_RESTAURANT_FOOD_AND_GROCERY,
                        self::INTEREST_JEWELLERY_AND_WATCHES,
                        self::INTEREST_LUXURY_GOODS,
                        self::INTEREST_SHOPPING_AND_RETAIL,
                        self::INTEREST_CLOTHES_SHOES_HANDBAGS_AND_ACCESSORIES,
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        self::INTEREST_TRAVEL_TOURISM_AND_AVIATION,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_JP]
                    ]
                ];
                break;
            case self::SD_COUNTRY_VN:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_GAMING,
                        self::INTEREST_CLOTHES_SHOES_HANDBAGS_AND_ACCESSORIES,
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        self::INTEREST_TELEVISION,
                        self::INTEREST_MUSIC,
                        self::INTEREST_ELECTRONICS_AND_COMPUTERS,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_VN]
                    ]
                ];
                break;
            case self::SD_COUNTRY_US:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_TOYS_CHILDREN_AND_BABY,
                        self::INTEREST_FRIENDS_FAMILY_AND_RELATIONSHIPS,
                        self::INTEREST_HEALTHCARE_AND_MEDICINE,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_FITNESS_AND_YOGA,
                        self::INTEREST_SPORTS,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_US]
                    ]
                ];
                break;
            case self::SD_COUNTRY_CN:
                $countryFilter = [
                    'brand_category' => [
                        self::INTEREST_BEAUTY_AND_COSMETICS,
                        self::INTEREST_GAMING,
                        self::INTEREST_ELECTRONICS_AND_COMPUTERS,
                        self::INTEREST_CAMERA_AND_PHOTOGRAPHY,
                        self::INTEREST_FRIENDS_FAMILY_AND_RELATIONSHIPS,
                        self::INTEREST_HEALHTY_LIFESTYLE,
                        self::INTEREST_HOME_DECOR_FURNITURE_AND_GARDEN,
                        self::INTEREST_TRAVEL_TOURISM_AND_AVIATION,
                        self::INTEREST_WEDDING,
                    ],
                    'followers' => [
                        'left_number' => self::FOLLOWERS_LIMIT[self::SD_COUNTRY_CN]
                    ]
                ];
                break;
            default:
                $countryFilter = [];
        }

        return [
            'filter' => array_merge($generalFilter, $countryFilter),
            'sort' => [
                'field' => 'followers'
            ],
            'paging' => [
                'limit' => self::FILTER_LIMIT
            ],
            'dry_run' => false,
            'export_type' => 'FULL',
        ];
    }
}
