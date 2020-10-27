<?php
if(!defined("INCLUDED")) die("Access forbidden.");

$_DATA["page_name"] = "Meet The Team";
$Team = [
    [
        "name" => "Developers",
        "icon" => "code-tags",
        "members" =>
        [
            [
                // Moonly Days
                "steamid" => "76561198188657895",
                "name" => "Moonly Days",
                "bio" => "The original author of the idea of this project, which ultimately coincided with the idea of Tyler McVicker. The main developer of everything related to this project.",
                "links" => [
                    "twitter" => 'https://twitter.com/moonlysdays',
                    'email' => 'mailto:moonlydays@creators.tf'
                ]
            ],
            [
                // Jota
                "steamid" => "76561198233791672",
                "name" => "Jota",
                "bio" => "Frontend website developer. He makes landing pages for our major events. Coffee lover ☕. If you want to get in touch with him, the best way is by sending an email.",
                "links" => [
                    "email" => "mailto:jota@creators.tf"
                ]
            ],
            [
                // rob5300
                "steamid" => "76561198044581863",
                "name" => "rob5300",
                "bio" => "Web programmer. Creator and maintainer of the 3D loadout preview. Game developer based in the UK. ☕.",
                "links" => [
                    "twitter" => 'https://twitter.com/robb5300',
                ]
            ],
            [
                // nanochip
                "steamid" => "76561198042248451",
                "name" => "nanochip",
                "bio" => "Server Developer with a thorough appreciation of the little things. ☕.",
                "links" => [
                    "twitter" => 'https://twitter.com/xnanochip',
					'email' => 'mailto:nanochip@creators.tf'
                ]
            ],
            [
                // Gamecube762
                "steamid" => "76561198001607989",
                "name" => "Gamecube762",
                "bio" => "Server Developer. He also owns some useful Twitter bots that provide information about events in the game.",
                "links" => [
                    "twitter" => 'https://twitter.com/gamecube762',
                    'email' => 'mailto:gamecube762@hotmail.com'
                ]
            ],
            [
                // stephanie
                "steamid" => "76561198208622111",
                "name" => "stephanie",
                "bio" => "Server Developer and backend person 🌌",
                "links" => [
                    "twitter" => 'https://twitter.com/sapphonie',
                    'web' => 'https://steph.anie.dev'
                ]
            ],
            [
                // HiGPS
                "steamid" => "76561197963998743",
                "name" => "HiGPS",
                "bio" => "Weapon balancer. Creator of BalanceMod servers.",
                "links" => [
                    "twitter" => 'https://twitter.com/Higps',
                    'web' => 'https://balancemod.tf'
                ]
            ],
            [
                // Ivory
                "steamid" => "76561198056366195",
                "name" => "Ivory",
                "bio" => "Server Developer. Enjoys experimenting with custom game modes and custom bot logic."
            ]
        ]
    ],
    [
        "name" => "Artists",
        "icon" => "brush",
        "members" => [
            [
                // Dr. Dze
                "steamid" => "76561198070719072",
                "name" => "Dr. Dze",
                "bio" => "Source Filmmaker animator and artist. Russian YouTuber."
            ],
            [
                // ElkTF2
                "steamid" => "76561198125404376",
                "name" => "ElkTF2",
                "bio" => "Source Filmmaker artist."
            ],
            [
                // N-cognito
                "steamid" => "76561198028539550",
                "bio" => "Team Fortress 2 Modeller."
            ],
            [
                // MrModez
                "steamid" => "76561198029219422",
                "bio" => "Pineapple composer. Writes music in different styles including TF2 style. Likes being lazy.",
                "links" => [
                    "youtube" => "https://youtube.com/mrmodez",
                    "twitter" => "https://twitter.com/MrModez"
                ]
            ],
            [
                // Vipes
                "steamid" => "76561197995003316",
                "name" => "Vipes"
            ],
            [
                // TurnTwister
                "steamid" => "76561198091077647",
                "bio" => "SFM artist."
            ],
            [
                // Manndarinchik
                "steamid" => "76561198200838625",
                "bio" => "SFM artist, Blender enthusiast. Tries to dip his toes in everything 3D related.",
                "links" => [
                    "twitter" => "https://twitter.com/ManndarinSFM"
                ]
            ],
            [
                // UmKlaiDet
                "steamid" => "76561198435422255",
                "bio" => "3D modeller."
            ],
            [
                // Alaxe
                "steamid" => "76561198179600693",
                "bio" => "SFM artist."
            ],
            [
                // Leia
                "steamid" => "76561198089450432",
                "bio" => "Writer. Do some misc stuff on the team as well. A French sociology student, they also have a love-hate relationship with Hammer."
            ],
            [
                // Cheddzy
                "steamid" => "76561198093409895",
                "bio" => "I do UX and design. The coolest teacher you'd ever have."
            ]
        ]
    ],
    [
        "name" => "Social Media",
        "icon" => "bullhorn",
        "members" => [
            [
                // Tyler
                "steamid" => "76561198025749276",
                "name" => "Tyler McVicker",
                "bio" => "Co-creator of Creators.TF, creator of Valve News Network and related channels and projects.",
                "links" => [
                    "twitter" => 'https://twitter.com/valvenewsnetwor'
                ]
            ],
            [
                // PeaseMaker
                "steamid" => "76561198012898744",
                "name" => "PeaseMaker",
                "bio" => "Media guy, russian YouTuber, connoisseur of dad jokes.",
                "links" => [
                    "twitter" => 'https://twitter.com/Ivan_PeaseMaker'
                ]
            ]
        ]
    ]
];

// Config Part Ended.
$SteamIDs = [];
foreach ($Team as $Group) {
    foreach ($Group["members"] as $Member) {
        array_push($SteamIDs, $Member["steamid"]);
    }
}
$MemberData = $Core->db->getAllRows("SELECT steamid, alias, avatar, name FROM tf_users WHERE ".join(" OR ", array_map(function($s){return "steamid = '".$s."'";},$SteamIDs)));
$Members = "";
foreach ($Team as $Group) {
    $Elements = "";
    foreach ($Group["members"] as $Member) {
        foreach ($MemberData as $MemberInfo) {
            if($MemberInfo["steamid"] == $Member["steamid"])
            {
                $Elements.=render('user-mini',[
                    'avatar' => $MemberInfo["avatar"],
                    'steamid' => $MemberInfo["alias"] ?? $MemberInfo["steamid"],
                    'name' => $Member["name"] ?? $MemberInfo["name"],
                    'text' => "<div>".($Member["bio"] ?? NULL)."</div><div class='peopleContactBox'>".join("",array_map(function($k,$v){return "<a class='peopleContactBoxLinks' target='_blank' href='".$v."'><i style='color:;' class='mdi mdi-".$k." peopleContactLinks'></i></a>";},array_keys($Member["links"] ?? []),($Member["links"] ?? [])))."</div>"
                ]);
            }
        }
    }
    $Members.=render('showcase/generic',[
        'name' => (isset($Group["icon"])?'<i class="mdi mdi-'.$Group["icon"].'"></i> ':"").$Group["name"],
        'content' => $Elements
    ]);
}
$Content = render('page', [
    'title' => "Meet the Team",
    'content' => $Members
]);
?>
