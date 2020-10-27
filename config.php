<?php
$Core->config = [
    "database" => [
        "hostname" => "188.165.203.123",
        "username" => "webmaster",
        "password" => "FqCVVXAMA4etRVUXB6TJrRr3rL3mWmVj",
        "database" => "website"
    ],
    "salts" => [
        "cache" => "f4ChPxefbLGZN8jp"
    ],
    "website" => [
        "cookie_domain" => ".creators.tf",
        "template" => "default",
        "server_rcon" => "UwqetgeD5ChShcDXgh6huH2z67Ne7MtE",
        "separator" => "::",
        "postsPerPage" => 10,
        "fastdl" => "https://fastdl.creators.tf/tf"
    ],
    "social" => [
        "comments_max_length" => 1000
    ],
    "webhooks" => [
        "DISCORD_JOINUS" => "https://discordapp.com/api/webhooks/643915211941871617/Ab_CzbnGSC66muG8pgla711X0dQJUGa-RU8RvTGGS2d0Bc90wbPMWqQCsIgd_5nnaA4X",
        "DISCORD_ERROR_REPORT" => "https://discordapp.com/api/webhooks/701022055813873694/KV7M9lVr2rZhyw9yxWgOVF1Cr3qV4hZa_2EfphEip6VsFVN7HU4wT4wHTwr1giJHPA5J",
        "DISCORD_SUBMISSION_MODERATION" => "https://discordapp.com/api/webhooks/715980710111870976/sUwBJfwurX0MaLcy7ls2o0isfBcmzscVWjC2mFOB9RkA4fjTTRC7QD3xsmdvcNnIy25e"
    ],
    "submissions" => [
        "min_rating" => 150,
        "min_stars" => 4,
        "page" => 15,
        "types" => [
            "Map",
            "Cosmetic",
            "Unusual Effect",
            "War Paint",
            "Taunt",
            "Weapon",
            "Client Mod"
        ],
        "themes" => [
            "Halloween",
            "Smissmas",
            "Night",
            "Snow",
            "Jungle",
            "Industrial",
            "Desert",
            "Alpine",
            "Japanese",
            "City",
            "Construction",
            "Egyptian",
            "Farmland",
            "Spytech",
            "Robotic",
            "Pyroland",
            "Medieval",
            "Other"
        ],
        "classes" => [
            "Scout",
            "Soldier",
            "Pyro",
            "Demoman",
            "Heavy",
            "Engineer",
            "Medic",
            "Sniper",
            "Spy"
        ],
        "gamemodes" => [
            "Capture The Flag",
            "Control Point",
            "Payload",
            "Payload Race",
            "Arena",
            "King Of The Hill",
            "Attack / Defense",
            "Special Delivery",
            "Robot Destruction",
            "Mann Vs. Machine",
            "Mannpower",
            "Medieval",
            "PASS Time",
            "Specialty",
            "Other"
        ]
    ],
    "economy" => [
        "items_per_page" => 48,
        "default_pages" => 5,
        "max_pages" => 25,
        "max_checkout_items" => 50,

        "classes" => [
            "scout",
            "soldier",
            "pyro",
            "demo",
            "heavy",
            "engineer",
            "medic",
            "sniper",
            "spy"
        ],
        "slots" => [
            "PRIMARY" => [
                'name' => "Primary",
                'url' => 'primary'
            ],
            "SECONDARY" => [
                'name' => "Secondary",
                'url' => 'secondary'
            ],
            "MELEE" => [
                'name' => "Melee",
                'url' => 'melee'
            ],
            "PDA" => [
                'name' => "PDA",
                'url' => 'pda'
            ],
            "WEAR_1" => [
                'name' => "Wearable",
                'url' => 'wearable/1'
            ],
            "WEAR_2" => [
                'name' => "Wearable",
                'url' => 'wearable/2'
            ],
            "WEAR_3" => [
                'name' => "Wearable",
                'url' => 'wearable/3'
            ],
            "ACTION" => [
                'name' => "Action",
                'url' => 'action'
            ],
            "TAUNT_1" => [
                'name' => "Taunt",
                'url' => 'taunt/1'
            ],
            "TAUNT_2" => [
                'name' => "Taunt",
                'url' => 'taunt/2'
            ],
            "TAUNT_3" => [
                'name' => "Taunt",
                'url' => 'taunt/3'
            ],
            "TAUNT_4" => [
                'name' => "Taunt",
                'url' => 'taunt/4'
            ],
            "TAUNT_5" => [
                'name' => "Taunt",
                'url' => 'taunt/5'
            ],
            "TAUNT_6" => [
                'name' => "Taunt",
                'url' => 'taunt/6'
            ],
            "TAUNT_7" => [
                'name' => "Taunt",
                'url' => 'taunt/7'
            ],
            "TAUNT_8" => [
                'name' => "Taunt",
                'url' => 'taunt/8'
            ]
        ]
    ],
    "api" => [
        "steam" => "0AA765E4CD1A1A712EF52F6B1B20906D",
    ],
    "templator" => [
        "brackets" => [ // [TAG] [/TAG]
            "HIDE" => false,
            "DEV" => $ENVIRONMENT == ENVIRONMENT_LOCAL,
            "REMOTE" => $ENVIRONMENT == ENVIRONMENT_REMOTE,
            "PROD" => $ENVIRONMENT == ENVIRONMENT_PRODUCTION,
            "BETA" => $ENVIRONMENT == ENVIRONMENT_BETA
        ],
        "tags" => [ // {TAG}
            "CDN" => $ENVIRONMENT == ENVIRONMENT_PRODUCTION ? "https://creators.tf/cdn" : "/cdn",
            "CDN_FASTDL" => "https://fastdl.creators.tf",
            "Language" => $Core->Language,
            "Version" => VERSION,
            "min" => $ENVIRONMENT == ENVIRONMENT_PRODUCTION ? ".min" : ""
        ]
    ]
];
?>
