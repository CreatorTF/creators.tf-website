/*
TF2 Realtime Cosmetic Preview system using THREE.JS
By Robert Straub, robertstraub.co.uk, rob5300.
For use on Creators.TF.
Custom modifications to THREE.js are required for this to operate. Do not replace the library without replicating the changes.

All TF2 Assets are owned by Valve Software and as such they maintain the rights to these assets.
If by request they should be removed they will be, please send an email to us and we will handle this request.

Custom code is owned by Robert and the Creators.TF team.
No reuse or redistribution may be done without explicit written permission by Robert and/or Creators.TF team member.
We reserve the write to revoke permission at any time.
*/


import * as THREE from '../external/three.module.js'; //This is modified and cannot be replaced by a fresh three.js copy.
import { GLTFLoader } from './GLTFLoader.js';
import { OrbitControls } from "./OrbitControls.js";

const ROOT_GLB_PATH = "/cdn/assets/glbs/"

const WeaponType = {
    PRIMARY: "primary",
    SECONDARY: "secondary",
    MELEE: "melee"
}

var container, controls;
var camera, scene, renderer;
var mixer;
var clock;

const ENABLE_LOADING_INDICATOR = true;
var loadingIndicator;
var loadingIndicatorMaterial;
var loadingCount = 0;
var idle;
var currentClass;
var currentClassID = -1;
var currentCosmetics = [];
//Current weapon data.
var currentWeaponSkeleton = null;
var classLoadedCallback = null;

//These cannot change as the text is set in items.json.
//New ones can be added but follow similar scheme.
const hatKey = "HAT";
const headphonesKEY = "HEADPHONES_S";
const pyroBackpack = "BACKPACK_P";
const head = "HEAD";
const shoesSocks = "SHOES_SOCKS_S";
const dogtags = "DOGTAGS_S";
const gammaCorrectionAmount = 2.2;

//Classes
//Populate classes with nulls for now for spaces for each class.
//Classes from 0 to 8.
//Scout, Soldier, Pyro, Demoman, Heavy, Engineer, Medic, Sniper, Spy
class TF2ClassData {
    constructor(path, skeletonName, modelNames, stringName){
        //Path to where the glb is stored, relative to the root dir.
        this.path = path;

        //Name of the skeleton inside the glb. Be warned that . characters are removed soo scout.qc_skeleton becomes scoutqc_skeleton.
        this.skeleton = skeletonName;

        //Array of KVPair to connect keys to model names that can be hidden.
        this.models = modelNames;

        //Human readable name for the class. MUST match the directory for its cosmetics.
        this.name = stringName;
    }
}

class KVPair {
    constructor(key, value){
        this.key = key;
        this.value = value;
    }
}

//Storage of class glb paths
//Follow TF2ClassData constructor args for each, if there are no models for 'modelNames' then put null.
//Storage path is set to be relative to root ( ./ )
const classesData = [
    new TF2ClassData("classes/scout.glb", "scoutqc_skeleton", [new KVPair(hatKey, "scout_hat_bodygroup"), new KVPair(headphonesKEY, "scout_headphones_bodygroup"), new KVPair(shoesSocks, "scout_shoes_socks_bodygroup"), new KVPair(dogtags, "scout_dogtags_bodygroup")], "Scout"),
    new TF2ClassData("classes/soldier.glb", "soldierqc_skeleton", [new KVPair(hatKey, "soldier_hat_bodygroup")], "Soldier"),
    new TF2ClassData("classes/pyro.glb", "pyroqc_skeleton", [new KVPair(pyroBackpack, "pyro_backpack_bodygroup"), new KVPair(head, "pyro_head_bodygroup")], "Pyro"),
    new TF2ClassData("classes/demoman.glb", "demoqc_skeleton", null, "Demoman"),
    new TF2ClassData("classes/heavy.glb", "heavyqc_skeleton", null, "Heavy"),
    new TF2ClassData("classes/engineer.glb", "engineerqc_skeleton", [new KVPair(hatKey, "engineer_hat_bodygroup")], "Engineer"),
    new TF2ClassData("classes/medic.glb", "medicqc_skeleton", null, "Medic"),
    new TF2ClassData("classes/sniper.glb", "sniperqc_skeleton", [new KVPair(hatKey, "sniper_hat_bodygroup")], "Sniper"),
    new TF2ClassData("classes/spy.glb", "spyqc_skeleton", null, "Spy")
];

const secondaryAnimationPath = "animations/{class}/secondary.glb";
const meleeAnimationPath = "animations/{class}/melee.glb";
const customAnimationPath = "animations/{class}/";

//Rotations are in Radians.
//These are rotations to add to the model when this animation is used to fix differences.
//Index corrisponds to class index.
const classsecondaryAniminationRotations = [
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0)
]

const classmeleeAnimationRotations = [
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(Math.PI, 0, 0),
    new THREE.Vector3(Math.PI, 0, 0)
];

const classdefaultAnimationRotations = [
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(0,0,0),
    new THREE.Vector3(Math.PI,0,0)
];

const defaultClassWeapons = [
    {
        "path": "weapons/Scattergun.glb",
        "type": "primary",
        "skeletonName": "w_scattergunqc_skeleton",
        "classIDs": [
            0
        ],
        "rotationFix": [
            3.141592653589793,
            0,
            0
        ]
    },
    {
        "path": "weapons/rocketlauncher.glb",
        "type": "primary",
        "skeletonName": "w_rocketlauncherqc_skeleton",
        "classIDs": [
            1
        ]
    },
    {
        "path": "weapons/Flamethrower.glb",
        "type": "primary",
        "skeletonName": "c_flamethrowerqc_skeleton",
        "classIDs": [
            2
        ]
    },
    {
        "path": "weapons/Grenade_Launcher.glb",
        "type": "primary",
        "skeletonName": "c_grenadelauncherqc_skeleton",
        "classIDs": [
            3
        ]
    },
    {
        "path": "weapons/minigun.glb",
        "type": "primary",
        "skeletonName": "c_minigunqc_skeleton",
        "classIDs": [
            4
        ]
    },
    {
        "path": "weapons/shotgun.glb",
        "type": "primary",
        "skeletonName": "c_shotgunqc_skeleton",
        "classIDs": [
            5
        ]
    },
    {
        "path": "weapons/medigun.glb",
        "type": "secondary",
        "skeletonName": "c_medigunqc_skeleton",
        "classIDs": [
            6
        ]
    },
    {
        "path": "weapons/sniperrifle.glb",
        "type": "primary",
        "skeletonName": "w_sniperrifleqc_skeleton",
        "classIDs": [
            7
        ]
    },
    {
        "path": "weapons/revolver.glb",
        "type": "primary",
        "skeletonName": "c_revolverqc_skeleton",
        "classIDs": [
            8
        ]
    }
]

class BonePair {
    constructor(name) {
      this.name = name;
      this.master = null;
      this.children = [];
      this.update = true;
    }
}

//Master bones.
var classMasterSkeleton;
var allBones = [];

//All the bones that we look for and find on cosmetics to child to the master skeleton.
//If a bone isn't here it WONT get updated/animated on cosmetics.
const allBoneNames = [
    "bip_pelvis",
    "bip_spine_0",
    "bip_spine_1",
    "bip_hip_L",
    "bip_hip_R",
    "prp_pack",
    "bip_knee_L",
    "bip_foot_L",
    "bip_knee_L",
    "bip_toe_L",
    "bip_knee_R",
    "bip_foot_R",
    "bip_knee_R",
    "bip_toe_R",
    "bip_spine_2",
    "bip_spine_3",
    "bip_neck",
    "bip_collar_L",
    "bip_head",
    "bip_upperArm_L",
    "bip_lowerArm_L",
    "bip_hand_L",
    "bip_collar_R",
    "bip_upperArm_R",
    "bip_lowerArm_R",
    "bip_hand_R",
    "prp_legPouch",
    "prp_coat_front_L",
    "prp_coat_front_1_L",
    "prp_coat_back_L",
    "prp_coat_back_1_L",
    "prp_coat_front_R",
    "prp_coat_front_1_R",
    "prp_coat_back_R",
    "prp_coat_back_1_R",
    "prp_pack_L",
    "prp_pack_R",
    "prp_pack_back",
    "prop_bone",
    "prop_bone_1",
    "prop_bone_2",
    "prop_bone_3",
    "prop_bone_4",
    "prop_bone_5",
    "prop_bone_6",
    "weapon_bone_L",
    "weapon_bone_R",
    "bip_thumb_0_L",
    "bip_thumb_1_L",
    "bip_thumb_2_L",
    "bip_index_0_L",
    "bip_index_1_L",
    "bip_index_2_L",
    "bip_middle_0_L",
    "bip_middle_1_L",
    "bip_middle_2_L",
    "bip_ring_0_L",
    "bip_ring_1_L",
    "bip_ring_2_L",
    "bip_pinky_0_L",
    "bip_pinky_1_L",
    "bip_pinky_2_L",
    "bip_thumb_0_R",
    "bip_thumb_1_R",
    "bip_thumb_2_R",
    "bip_index_0_R",
    "bip_index_1_R",
    "bip_index_2_R",
    "bip_middle_0_R",
    "bip_middle_1_R",
    "bip_middle_2_R",
    "bip_ring_0_R",
    "bip_ring_1_R",
    "bip_ring_2_R",
    "bip_pinky_0_R",
    "bip_pinky_1_R",
    "bip_pinky_2_R",
    "weapon_bone",
    "bip_crotchflap_0",
    "joint_hose01",
    "joint_hose02",
    "joint_hose03",
    "joint_hose04",
    "joint_hose05"
];

//Setup all default tint values for materials that support tinting.
//Key: vmt material name | Value: RGB Hex value as string.
const defaultTints = {
    "2020_breadcap": "717c1d",
    "amphibeanie": "e8a5ae",
    "amphibeanie_blue": "7cacce",
    "antifreeze_ulster_1": "615841",
    "antifreeze_ulster_1_blue": "615841",
    "badlands_sunblock": "6c251f",
    "badlands_sunblock_blue": "345d79",
    "batters_beak": "70868a",
    "batters_beak_blue": "70868a",
    "beater_cop": "2a2a2a",
    "beater_cop_blue": "2a2a2a",
    "benefactors_bowl": "e13030",
    "benefactors_bowl_blue": "30e1c7",
    "beretstack": "b8383b",
    "beretstack_blue": "5885a2",
    "bigger_mann_on_campus": "c8bbb1",
    "bigger_mann_on_campus_blue": "acbcca",
    "bloodsoaked_brim_paintable_band": "442429",
    "bloodsoaked_brim_paintable_band_blue": "242b44",
    "bombinomicon_hat": "433e3e",
    "bombinomicon_hat_blue": "433e3e",
    "boston_bling_1": "b73f3f",
    "boston_bling_1_blue": "3c708d",
    "brave_boots": "e6e6e6",
    "brave_boots_blue": "e6e6e6",
    "bubonic_bedizen_no_hood": "2a2220",
    "bubonic_bedizen_no_hood_blue": "2a2220",
    "buckshot_bandolier_1": "78231e",
    "buckshot_bandolier_1_blue": "28556e",
    "cadavers_coat": "b8383b",
    "cadavers_coat_blue": "5885a2",
    "charlatans_cordobs": "c53238",
    "charlatans_cordobs_blue": "6a9db3",
    "churchill_hat": "a89276",
    "coleader_cap_style1": "40372b",
    "coleader_cap_style1_blue": "40372b",
    "comrade_communicator": "c36c2d",
    "comrade_communicator_blue": "b88035",
    "coxswain_coat": "7d7864",
    "coxswain_coat_blue": "7d7864",
    "dancers_dress_1": "c53238",
    "dancers_dress_1_blue": "6a9db3",
    "deathadder": "be383b",
    "deathadder_1": "dc4649",
    "deathadder_1_blue": "649cc8",
    "deathadder_blue": "5885a2",
    "dugout_scratchers": "373737",
    "dugout_scratchers_blue": "232323",
    "dustbowler_style_1": "974734",
    "dustbowler_style_1_blue": "3f7590",
    "dutiful_do": "45301e",
    "engie_winter_hat_1": "463c37",
    "engie_winter_hat_1_blue": "3c464d",
    "family_doctor": "3b3937",
    "family_doctor_blue": "37393b",
    "flame_kindler": "a03c28",
    "flame_kindler_blue": "286e8c",
    "foxhound_style_1": "3e3936",
    "gallon_o_grog": "2d7819",
    "googly_aye_1": "",
    "gravel_blooded_mercenaries_style_1": "654740",
    "gravel_blooded_mercenaries_style_1_blue": "28394d",
    "gravel_blooded_mercenaries_style_2": "654740",
    "gravel_blooded_mercenaries_style_2_blue": "28394d",
    "grave_diggers_goatee": "3b2a20",
    "henshin_helmet": "f08149",
    "henshin_helmet_blue": "ef9849",
    "hightech_haircut": "513e31",
    "hippocratic_growth": "c3c3ba",
    "hot_tropic_well_shaded_style": "a5a5a0",
    "hot_tropic_well_shaded_style_blue": "a5a5a0",
    "invisible_igniter": "625147",
    "josuke": "58413b",
    "keelhaul_kapitan_1": "2d2823",
    "keelhaul_kapitan_1_blue": "2d2823",
    "lanai_lounger_mai_tai_style": "553232",
    "lanai_lounger_mai_tai_style_blue": "324655",
    "lil_garbage_gaurdians": "424f3b",
    "lucky_laces": "6c5845",
    "madame_hootsabunch": "e68c53",
    "medic_general_coat": "1e1e1e",
    "medic_general_coat_blue": "1e1e1e",
    "nightwalkers_necktie": "4d2927",
    "nightwalkers_necktie_blue": "29274d",
    "peak_precision_parka": "9d312f",
    "peak_precision_parka_blue": "395c78",
    "pestilent_profession": "2a2220",
    "present_from_pyro": "be3228",
    "present_from_pyro_blue": "5091be",
    "pyrovision_visors": "fc77",
    "pyrovision_visors_blue": "a963ff",
    "saviors_suit": "b83c40",
    "saviors_suit_blue": "3d7193",
    "scientist_head_3": "524848",
    "shady_business_uptown_style": "373232",
    "spectral_specs": "7eba7c",
    "stealthy_spaniard": "c53238",
    "stealthy_spaniard_blue": "6a9db3",
    "suit_couture1": "3b1f23",
    "suit_couture1_blue": "18233d",
    "survivors_kit": "922c20",
    "survivors_kit_blue": "345d79",
    "tainted_tome_cover": "3e5a25",
    "tainted_tome_cover_lvl2": "33491f",
    "tainted_tome_cover_lvl3": "30522e",
    "tainted_tome_cover_lvl4": "3f4c19",
    "tainted_tome_spine": "453729",
    "tainted_tome_spine_lvl2": "8e4f19",
    "tainted_tome_spine_lvl3": "848d8d",
    "tainted_tome_spine_lvl4": "e9b63c",
    "tiny_supplier": "fad87a",
    "tiny_supplier_blue": "fad87a",
    "uncanny_undertaker": "202020",
    "undercover_usurper": "5a4c40",
    "undercover_usurper_blue": "5a4c40",
    "usual_locked_style": "ffab4a",
    "usual_locked_style_1": "ffab4a",
    "usual_locked_style_1_blue": "ffc94a",
    "usual_locked_style_blue": "ffc94a",
    "vampires_vestments": "d0c2b4",
    "vampires_vestments_blue": "abc7cb",
    "veterans_vail": "a8a496",
    "veterans_vail_blue": "a8a496",
    "winter_western": "582d2d",
    "winter_western_blue": "374355"
}

init();

//Initializes the major objects and scene.
function init() {
    clock = new THREE.Clock();
    clock.start();

    let width, height;

    //What three js will make its canvas inside and fill up.
    container = document.getElementById("loadout-preview");

    //Lets get the width and height from the containing element for later use.
    width = container.offsetWidth;
    height = container.offsetHeight;

    camera = new THREE.PerspectiveCamera(40, width / height, 1, 300);
    camera.position.set(0, 60, 160);

    scene = new THREE.Scene();
    scene.add(camera);

    if(ENABLE_LOADING_INDICATOR){
        var spriteMap = new THREE.TextureLoader().load( "/cdn/assets/images/tf_logo_white_square.png" );
        loadingIndicatorMaterial  = new THREE.SpriteMaterial( { map: spriteMap } );
        loadingIndicator = new THREE.Sprite( loadingIndicatorMaterial );
        loadingIndicator.position.set(0,0,-10);
        loadingIndicator.scale.set(4,4,1);

        let loadingIndicatorBack = new THREE.Sprite( new THREE.SpriteMaterial( { color: new THREE.Color(0x2b2826).convertGammaToLinear(1.865), opacity: 0.75, transparent: true} ) );
        loadingIndicator.add(loadingIndicatorBack);
        loadingIndicatorBack.position.set(0,0,-1);
        loadingIndicatorBack.scale.set(5,5,1);
        camera.add(loadingIndicator);
    }

    var directionalLight = new THREE.DirectionalLight(0xffffff, 1.5);
    directionalLight.position.set(0, 10, 5);
    scene.add(directionalLight);

    var amlight = new THREE.AmbientLight( 0xa6a6a6 ); // soft white light
    scene.add( amlight );

    try {
        renderer = new THREE.WebGLRenderer({ antialias: true , alpha: true });
        renderer.setClearColor( 0x000000, 0 );
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(width, height);
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.outputEncoding = THREE.sRGBEncoding;
    }
    catch {
        Creators.Actions.Modals.alert({
            name: "Loadout Preview WebGL Error",
            innerText: "The Loadout preview failed to initialize the WebGL renderer.<br> Please check if your browser supports WebGL or if it needs to be enabled <a href=\"https://get.webgl.org/\" target=\"_blank\">at this page.</a>"
        });
        return;
    }

    container.appendChild(renderer.domElement);
    renderer.domElement.style.outline = "none";

    controls = new OrbitControls( camera, renderer.domElement );
    controls.enablePan = false;
    controls.minDistance = 80;
    controls.maxDistance = 200;
    controls.maxPolarAngle = Math.PI / 2;
    controls.minPolarAngle = Math.PI / 5;

    //controls.maxAzimuthAngle = Math.PI;
    controls.target = new THREE.Vector3(0, 35, 0);
    controls.update();

    //Setup all the bones using the master name list
    allBoneNames.forEach(name => {
        allBones.push(new BonePair(name));
    });

    try{
        Setup(GetSetupDataObject());
    }
    catch (error) {
        Creators.Actions.Modals.alert({
            name: "Loadout Preview Setup Error",
            innerText: `The Loadout Preview failed to load. Please report this error:<br>${error}`
        });
        return;
    }

    animate();
}

//Performs the setup process using the data in the meta element.
function Setup(setupDataObject){
    if(setupDataObject.cosmeticonly){
        //COSMETIC ONLY
        //Cheat and set the current class object directly.
        currentClass = classesData[setupDataObject.class_id];

        if(setupDataObject.cosmetics.length > 0){
            LoadCosmeticFromGLTF(setupDataObject.cosmetics[0], function(skel){

                controls.target = GetCenter(setupDataObject.cosmetics[0], skel);
                controls.update();
                if(setupDataObject.cosmetictints[0] != null && setupDataObject.cosmetictints[0] != "" && setupDataObject.cosmetictints[0] != "0"){
                    SetChildrenColourTints(skel, setupDataObject.cosmetictints[0]);
                }

            }
            , false);
        }

        controls.minDistance = 60;
        controls.maxDistance = 100;
        //Reset these to defaults.
        controls.maxPolarAngle = Math.PI;
        controls.minPolarAngle = 0;
        controls.update();
    }
    else if(setupDataObject.weapononly){
        //WEAPON ONLY DISPLAY
        let weapondata = setupDataObject.weapon;
        let finishedFunc = function(weaponOb){
            //Called when weapon loading is complete
            controls.target = GetCenter(weapondata, weaponOb);
            controls.minDistance = 70;
            controls.maxDistance = 90;
            //Reset these to defaults.
            controls.maxPolarAngle = Math.PI;
            controls.minPolarAngle = 0;
            controls.update();
            weaponOb.rotation.setFromVector3(ArrayToVector(weapondata.rotationFix));
        };
        LoadWeaponFromGLTF(weapondata, false, finishedFunc, false);
    }
    else{
        //NORMAL DISPLAY MODE
        //Assign a function to this var for us to get a callback when the class model is loaded.
        //This is executed later after SetClass().
        classLoadedCallback = function(){

            for(let i = 0; i < setupDataObject.cosmetics.length; i++){
                AddCosmetic(setupDataObject.cosmetics[i], setupDataObject.cosmetictints[i]);
            }

            //If the weapon field is missing or if its some other empty garbage then we dont have a weapon. Use the default weapon.
            if(setupDataObject.weapon != null && setupDataObject.weapon != {} && !Array.isArray(setupDataObject.weapon)){
                LoadWeaponFromGLTF(setupDataObject.weapon);
            }
            else{
                //Get the default weapon for this class and use that.
                LoadWeaponFromGLTF(defaultClassWeapons[setupDataObject.class_id]);
            }

            //Unassign ourselves when this is done.
            classLoadedCallback = null;
        }

        SetClass(setupDataObject.class_id);
    }
}

function GetSetupDataObject(){
    return loadoutpreview_setupdata;
}

function GetCenter(cosmeticData, skeleton){
    let center = new THREE.Vector3(0,0,0);

    //Some of the objects are actually rotated but the skeleton hides this making the calculated center be wrong.
    //We make a matrix to apply to the position to rotate it back to where it should be, fixing it.
    //Not all need this soo its set per cosmetic.
    let fixMatrix = new THREE.Matrix4();
    let quat = new THREE.Quaternion();
    //A positive rotation on x of 90 degrees.
    quat.setFromEuler(new THREE.Euler(Math.PI/2, 0, 0, 'XYZ'));
    fixMatrix.compose(new THREE.Vector3(0,0,0), quat, new THREE.Vector3(1,1,1));

    if(cosmeticData.centerPos != null){
        return cosmeticData.centerPos;
    }
    else{
        for(let i = 0; i < skeleton.children.length; i++){
            if(skeleton.children[i].type == "SkinnedMesh"){
                skeleton.children[i].geometry.computeBoundingBox();
                skeleton.children[i].geometry.boundingBox.getCenter(center);
                if(cosmeticData.rotFix) center.applyMatrix4(fixMatrix);
                break;
            }
            for(let j = 0; j < skeleton.children[i].children.length; j++){
                if(skeleton.children[i].children[j].type == "SkinnedMesh"){
                    skeleton.children[i].children[j].geometry.computeBoundingBox();
                    skeleton.children[i].children[j].geometry.boundingBox.getCenter(center);
                    if(cosmeticData.rotFix) center.applyMatrix4(fixMatrix);
                    break;
                }
            }
        }
    }

    center = skeleton.localToWorld(center);

    return center;
}

//Goes through the bones and childs all children objects to the master of that bone group.
function ChildAllBones(){
    allBones.forEach(element => {
        if(element.master != null && element.children.length > 0 && element.update)
            ChildBonesToMaster(element.master, element.children);
    });
}

//Updates the renderer and animations at a consistent rate.
//We have no control and the browser calls this for us.
function animate() {
    requestAnimationFrame(animate);
    var delta = clock.getDelta();
    if(mixer != null) mixer.update(delta);

    if(ENABLE_LOADING_INDICATOR && loadingIndicator.visible) loadingIndicatorMaterial.rotation += (Math.PI * 0.65) * delta;

    renderer.render(scene, camera);
}

//Check this skeleton and get all bones that match and make them the master bones.
function FindMasterBones(skeleton){
    allBones.forEach(element => {
        var bone = skeleton.getObjectByName(element.name);
        if(bone != null) element.master = bone;
    });
}

//Child these bones to the master given.
function ChildBonesToMaster(master, children){
    for(var i = 0; i < children.length; i++){
        children[i].position.set(0,0,0);
        children[i].rotation.set(0,0,0);
        master.add(children[i]);
    }
}

function SetClass(index){
    var classObj = classesData[index];
    if(classObj.path == "") return;

    //We need to remove the old skeleton.
    //We also need to see if we should remove any other cosmetics as they wont be right for this class.
    if(classMasterSkeleton != null){
        classMasterSkeleton.parent.remove(classMasterSkeleton);
        //skelMaster.dispose();
    }

    //Clear out all bone children as they will be from old invalid objects
    allBones.forEach(element => {
        element.children = [];
    });

    currentClass = classObj;
    LoadClassFromGLTF(classObj.path, classObj.skeleton, index);
}

function AddCosmetic(cosmeticDataObject, tint, callback){
    if(cosmeticDataObject != null){
        let cosmeticCallback = function(skel){
            currentCosmetics.push(skel);
            if(callback != null) callback();
            if(tint != null && tint != "" && tint != "0"){
                SetChildrenColourTints(skel, tint, cosmeticDataObject);
            }
        }

        LoadCosmeticFromGLTF(cosmeticDataObject, cosmeticCallback);
        HideObjectsForCosmetic(cosmeticDataObject);
    }
}

function HideObjectsForCosmetic(cosmetic){
    //If there are models to disable on the class, do that now.
    if(cosmetic.disables != null){
        cosmetic.disables.forEach(element => {
            HideObjectOnClassSkelViaName(GetModelNameFromKey_CurrentClass(element));
        });
    }
}

function SetAllVisble_CurrentClass(){
    classMasterSkeleton.children.forEach(child => {
        child.visible = true;
    });
}

function GetModelNameFromKey_CurrentClass(key){
    var toReturn = "";
    if(currentClass.models == null) return "";
    currentClass.models.forEach(element => {
        if(element.key == key) toReturn = element.value;
    });
    return toReturn;
}

function HideObjectOnClassSkelViaName(name){
    var objToHide = classMasterSkeleton.getObjectByName(name);
    if(objToHide != null) objToHide.visible = false;
}

function LoadWeaponFromGLTF(weapondata, childBones = true, finishedCallback = null, shouldPlayAnimation = true){
    UpdateLoadingCount(1);

    let loader = new GLTFLoader().setPath(ROOT_GLB_PATH);
    try{
        loader.load(weapondata.path, function (gltf) {
            scene.add(gltf.scene);
            gltf.scene.position.set(0, 0, 0);
            gltf.scene.scale.set(1,1,1);

            if(weapondata.hasOwnProperty("skeletonNamea") && weapondata.skeletonName != ""){
                currentWeaponSkeleton = gltf.scene.getObjectByName(weapondata.skeletonName);
            }
            
            //Backup code to find skeleton object for cosmetics that do not specify.
            if(currentWeaponSkeleton == null){
                for(var i = 0; i < gltf.scene.children.length; i++){
                    //Object probably has the word skeleton in its name or its types match what we expect.
                    if(gltf.scene.children[i].name.includes("skeleton") || gltf.scene.children[i].type == "Bone" || gltf.scene.children[i].type == "SkinnedMesh"){
                        currentWeaponSkeleton = gltf.scene.children[i];
                        break;
                    }
                    for(var x = 0; x < gltf.scene.children[i].children.length; x++){
                        //Probably will have bones or a skinned mesh as its children soo this works too.
                        if(gltf.scene.children[i].children[x].type == "Bone" || gltf.scene.children[i].children[x].type == "SkinnedMesh"){
                            currentWeaponSkeleton = gltf.scene.children[i];
                            break;
                        }
                    }
                    if(currentWeaponSkeleton != null) break;
                }
            }

            if(weapondata.rotationFix != null){
                currentWeaponSkeleton.rotation.setFromVector3(ArrayToVector(weapondata.rotationFix));
            }

            //Fix parts randomly being culled yet being visible.
            //Probably bad bounding boxes.
            currentWeaponSkeleton.children.forEach(element => {
                element.frustumCulled = false;
                //Try on direct children too.
                element.children.forEach(element => {
                    element.frustumCulled = false;
                });
            });
            currentWeaponSkeleton.frustumCulled = false;

            if(childBones){
                GetAllCosmeticBones(currentWeaponSkeleton);
                ChildAllBones();
            }

            if(weapondata.type != WeaponType.PRIMARY && shouldPlayAnimation){
                let animPath;
                let isCustom = false;

                //If no animation override data is here in the object, use the type to find the anim path.
                if(weapondata.animationOverride == null || weapondata.animationOverride[currentClassID] == ""){
                    animPath = weapondata.type == WeaponType.SECONDARY ? secondaryAnimationPath : meleeAnimationPath;
                }
                else {
                    //The animation path is from the override array as we have an entry.
                    animPath = `${customAnimationPath}${weapondata.animationOverride[currentClassID]}`;
                    isCustom = true;
                }
                //Define callback function for us to handle the result of the animation load.
                let resultFunction = function(result){
                    if(!result){
                        //If false, we should remove this new weapon as the animation didnt load correctly.
                        if(currentWeaponSkeleton != null){
                            currentWeaponSkeleton = null;
                        }
                    }
                };
                if(isCustom) LoadAndPlayAnimationFromGLTF(animPath, "custom", resultFunction);
                else LoadAndPlayAnimationFromGLTF(animPath, weapondata.type, resultFunction);
            }

            if(finishedCallback != null) finishedCallback(currentWeaponSkeleton);

            UpdateLoadingCount(-1);
        },
        ProgressTextSet,
        function(e){
            UpdateLoadingCount(-1);
            console.log("Error: Failed to load Weapon: " + weapondata.path + ". Error: " + e);}
        );
    }
    catch(e){
        console.print("[LoadoutPreview] Error: Failed to load weapon at path: " + weapondata.path);
        UpdateLoadingCount(-1);
    }
}

//Load a class model from a file and setup its skeleton to be used for cosmetics.
function LoadClassFromGLTF(path, skeletonname, classIndex){
    UpdateLoadingCount(1);
    var loader = new GLTFLoader().setPath(ROOT_GLB_PATH);
    try{
        loader.load(path, function (gltf) {
        try{
            scene.add(gltf.scene);
            gltf.scene.position.set(0, 0, 0);
            gltf.scene.scale.set(1,1,1);

            //Get the skeleton from the class and store its bones as the master bones.
            classMasterSkeleton = gltf.scene.getObjectByName(skeletonname);

            classMasterSkeleton.rotation.setFromVector3(classdefaultAnimationRotations[classIndex]);

            //Fix parts randomly being culled yet being visible.
            //Probably bad bounding boxes.
            classMasterSkeleton.children.forEach(element => {
                element.frustumCulled = false;
                //Try on direct children too.
                element.children.forEach(element => {
                    element.frustumCulled = false;
                });
            });

            classMasterSkeleton.frustumCulled = false;
            FindMasterBones(classMasterSkeleton);

            //Enable shadows and get the idle anim.
            //Shadows are off anyways but left here for future.
            mixer = new THREE.AnimationMixer(classMasterSkeleton);
            /*
            gltf.scene.children.forEach(element => {
                element.castShadow = true;
                element.receiveShadow = true;
            }); */
            var clips = gltf.animations;

            //Find the animations and begin playing idle.
            //Idle animations should have the same name for all classes.
            idle = THREE.AnimationClip.findByName(clips, 'a_reference');
            var idleAction = mixer.clipAction(idle);
            idleAction.play();

            ChildAllBones();

            currentClassID = Number.parseInt(classIndex);
        }
        catch (e) {
            console.error(`Exception during class load gltf handling: ${e}`);
        }

        SetAllVisble_CurrentClass();

        UpdateLoadingCount(-1);
        if(classLoadedCallback != null) classLoadedCallback();

        }, ProgressTextSet ,function(){ UpdateLoadingCount(-1); });

    }
    catch(e){
        UpdateLoadingCount(-1);
        console.error(`Exception during attepting to load in class gltf: ${e}`);
    }
}

//Load a cosmetic from a file and attach it.
function LoadCosmeticFromGLTF(cosmeticData, finishedLoadingFunction, childBones = true){
    var path = cosmeticData.path.replace("{class}", currentClass.name);
    UpdateLoadingCount(1);
    var loader = new GLTFLoader().setPath(ROOT_GLB_PATH);
    var doneFunc = function(gltfScene){
        var skel = null
        if(cosmeticData.hasOwnProperty("skeleton") && cosmeticData.skeleton != ""){
            skel = gltfScene.getObjectByName(cosmeticData.skeleton);
        }
        
        //Backup code to find skeleton object for cosmetics that do not specify.
        if(skel == null){
            for(var i = 0; i < gltfScene.children.length; i++){
                //Object probably has the word skeleton in its name or its types match what we expect.
                if(gltfScene.children[i].name.includes("skeleton") || gltfScene.children[i].type == "Bone" || gltfScene.children[i].type == "SkinnedMesh"){
                    skel = gltfScene.children[i];
                    break;
                }
                for(var x = 0; x < gltfScene.children[i].children.length; x++){                    
                    //Probably will have bones or a skinned mesh as its children soo this works too.
                    if(gltfScene.children[i].children[x].type == "Bone" || gltfScene.children[i].children[x].type == "SkinnedMesh"){
                        skel = gltfScene.children[i];
                        break;
                    }
                }
                if(skel != null) break;
            }
        }

        if(childBones){
            GetAllCosmeticBones(skel);
            ChildAllBones();
        }

        skel.frustumCulled = false;
        //Do some strange searching of children and childrens children to stop the bad bad frustum culling.
        skel.children.forEach(ch => {
            ch.frustumCulled = false;

            ch.children.forEach(ch1 => {
                ch1.frustumCulled = false;
            });
        });

        

        var allFoundMaterials = [];
        var obNames = [];

        //Find all the materials and store then to make searching more efficient.
        skel.children.forEach(el => {
            if(el.material != null){
                allFoundMaterials.push(el.material);
                obNames.push(el.material.name);
            }

            //Do the children of this child too.
            el.children.forEach(el_ => {
                if(el_.material != null){
                    allFoundMaterials.push(el_.material);
                    obNames.push(el_.material.name);
                }
            });
        });

        //Check all materials and apply default tint colours using the data from the json object.
        for(let i = 0; i < obNames.length; i++){
            if(defaultTints[obNames[i]] != null){
                let defaultColour = new THREE.Color(parseInt(defaultTints[obNames[i]], 16));
                defaultColour.convertGammaToLinear(gammaCorrectionAmount);
                allFoundMaterials[i].color = defaultColour;
            }
        }

        for(let i = 0; i < allFoundMaterials.length; i++){
            allFoundMaterials[i].side = THREE.FrontSide;
        }

        UpdateLoadingCount(-1);

        //Setup alpha mask texture if that property exists.
        if(cosmeticData.alphaMask != null && cosmeticData.alphaMask != ""){
            var l = new THREE.TextureLoader();

            if(!Array.isArray(cosmeticData.alphaMask)){
                //Only ONE alpha mask to use.
                var path = cosmeticData.alphaMask.replace("{class}", currentClass.name);
                UpdateLoadingCount(1);
                l.load(ROOT_GLB_PATH + path, function(img){
                    img.flipY = false;
                    img.needsUpdate = true;

                    skel.children.forEach(el => {
                        if(el.material != null){
                            el.material.alphaMap = img;
                            el.material.transparent = true;
                            //Custom for Spectral specs
                            el.material.blending = THREE.CustomBlending;
                            el.material.blending = THREE.AdditiveBlending;
                            
                            el.material.needsUpdate = true;

                            //el.material = new THREE.MeshStandardMaterial
                            console.log("Alpha stuff done");
                        }

                        //Do the children of this child too.
                        el.children.forEach(el_ => {
                            if(el_.material != null){
                                el_.material.alphaMap = img;
                                el_.material.transparent = true;
                                el_.material.needsUpdate = true;
                            }
                        });
                    });
                    UpdateLoadingCount(-1);
                });
            }
            else {
                cosmeticData.alphaMask.forEach(element => {
                    if(element.value != ""){
                        UpdateLoadingCount(1);
                        l.load(ROOT_GLB_PATH + element.value, function(img2){
                            img2.flipY = false;
                            img2.needsUpdate = true;

                            for(var i = 0; i < obNames.length; i++){
                                if(obNames[i] == element.key){
                                    allFoundMaterials[i].alphaMap = img2;
                                    allFoundMaterials[i].transparent = true;
                                    allFoundMaterials[i].needsUpdate = true;
                                }
                            }
                            UpdateLoadingCount(-1);
                        });
                    }
                });
            }
        }
        
        //Lets try to load a map for the colour mask.
        //If there is no mask then this wont be used on the shader and tints will be for the whole cosmetic.
        if(cosmeticData.colourMask != null && cosmeticData.colourMask != ""){
            //Load the image and apply it to all materials that we can find.
            var l = new THREE.TextureLoader();

            if(!Array.isArray(cosmeticData.colourMask)){
                //Only ONE mask to use.
                var path = cosmeticData.colourMask.replace("{class}", currentClass.name);
                UpdateLoadingCount(1);
                l.load(ROOT_GLB_PATH + path, function(img){
                    //THREE JS DOES THIS BY DEFAULT???
                    img.flipY = false;
                    img.needsUpdate = true;

                    skel.children.forEach(el => {
                        if(el.material != null){
                            el.material.colourmaskMap = img;
                            el.material.additiveColourBlend = cosmeticData.additive;
                            el.material.needsUpdate = true;
                        }

                        //Do the children of this child too.
                        el.children.forEach(el_ => {
                            if(el_.material != null){
                                el_.material.colourmaskMap = img;
                                el_.material.additiveColourBlend = cosmeticData.additive;
                                el_.material.needsUpdate = true;
                            }
                        });
                    });
                    UpdateLoadingCount(-1);
                });
            }
            else {
                //We have more than one mask to use or objects to ignore.
                //Go through each mask and find its matching material
                cosmeticData.colourMask.forEach(element => {
                    if(element.value != "IGNORE_COLOR_TINT"){
                        UpdateLoadingCount(1);
                        l.load(ROOT_GLB_PATH + element.value, function(img2){
                            img2.flipY = false;
                            img2.needsUpdate = true;

                            for(var i = 0; i < obNames.length; i++){
                                if(obNames[i] == element.key){
                                    allFoundMaterials[i].colourmaskMap = img2;
                                    allFoundMaterials[i].additiveColourBlend = cosmeticData.additive;
                                    allFoundMaterials[i].needsUpdate = true;
                                }
                            }
                            UpdateLoadingCount(-1);
                        });
                    }
                });

                FixIgnoreColourMaterials(cosmeticData, allFoundMaterials, obNames);
            }
        }
        else if(cosmeticData.additive){
            skel.children.forEach(el => {
                if(el.material != null){
                    el.material.additiveColourBlend = true;
                    el.material.needsUpdate = true;
                }

                el.children.forEach(el_ => {
                    if(el_.material != null){
                        el_.material.additiveColourBlend = true;
                        el_.material.needsUpdate = true;
                    }
                });
            });
        }

        finishedLoadingFunction(skel);
        console.log("Finished loading : " + cosmeticData.skeleton);
    }
    try{
        loader.load(path,
            function(gltf){
                CosmeticSceneLoad(gltf, doneFunc); },
                ProgressTextSet,
            function(e){
                UpdateLoadingCount(-1);
                console.log("Error: Failed to load Cosmetic: " + path + ". Error: " + e);}
        );
    }
    catch (e) {
        UpdateLoadingCount(-1);
        console.log("Error: Failed to load Cosmetic: " + path);
    }
}

function LoadAndPlayAnimationFromGLTF(path, animationName, resultFunc){
    let loader = new GLTFLoader().setPath(ROOT_GLB_PATH);
    path = path.replace("{class}", currentClass.name);

    UpdateLoadingCount(1);
    loader.load(path, function(gltf){
        var anims = gltf.animations;

        var clip = THREE.AnimationClip.findByName(anims, animationName);

        //We MUST stop all actions before changing as it may try to play both?
        mixer.stopAllAction();
        var action = mixer.clipAction(clip);
        action.play();

        if(animationName == WeaponType.MELEE){
            classMasterSkeleton.rotation.setFromVector3(classmeleeAnimationRotations[currentClassID]);
        }
        else if(animationName == WeaponType.SECONDARY){
            classMasterSkeleton.rotation.setFromVector3(classsecondaryAniminationRotations[currentClassID]);
        }
        
        setTimeout(() => {
            UpdateLoadingCount(-1);
        }, 150);

        resultFunc(true);
    }
    /*
    ,
    null,
    function(e){
        console.log("[LoadoutPreview] Error when trying to play animation: " + e); resultFunc(false);
    }
    */
    );
}

//Callback for scene load.
function CosmeticSceneLoad(gltf, LoadedCallback){
    scene.add(gltf.scene);
    gltf.scene.position.set(0, 0, 0);
    gltf.scene.scale.set(1,1,1);

    LoadedCallback(gltf.scene);
}

function GetAllCosmeticBones(skeleton){
    allBones.forEach(element => {
        var bone = skeleton.getObjectByName(element.name);
        if(bone != null) element.children.push(bone);
    });
}

function SetChildrenColourTints(skel, colour, cosmeticData){
    colour = parseInt(colour, 16);

    var gammaColour = new THREE.Color(colour);
    gammaColour.convertGammaToLinear(gammaCorrectionAmount);

    var allFoundMaterials = [];
    var obNames = [];

    //Find all the materials and store them
    //Also perform colour application on materials now.
    skel.children.forEach(el => {
        if(el.material != null){
            allFoundMaterials.push(el.material);
            obNames.push(el.material.name);
            el.material.color = gammaColour;
        }

        el.children.forEach(el_ => {
            if(el_.material != null){
                allFoundMaterials.push(el_.material);
                obNames.push(el_.material.name);
                el_.material.color = gammaColour;
            }
        });
    });

    if(Array.isArray(cosmeticData.colourMask)){
        FixIgnoreColourMaterials(cosmeticData, allFoundMaterials, obNames);
    }
}

function FixIgnoreColourMaterials(cosmeticData, materials, materialNames){
    cosmeticData.colourMask.forEach(element => {
        if(element.value == "IGNORE_COLOR_TINT"){
            //This material has the IGNORE_COLOR_TINT value meaning it actually wants to not have any tinting.
            //Lets go and reverse the colour tint for this material.
            for(var i = 0; i < materialNames.length; i++){
                if(materialNames[i] == element.key){
                    //Set the materials colour to white.
                    materials[i].color = new THREE.Color(0xffffff);
                }
            }
        }
    });
}

function UpdateLoadingCount(change){
    if(!ENABLE_LOADING_INDICATOR) return;
    loadingCount += change;
    loadingIndicator.visible = loadingCount > 0;
}

//Callback to set the current loading progress.
function ProgressTextSet(progress){
    // loadingpercent.innerHTML = Math.ceil(progress.loaded / progress.total * 100);
}

function ArrayToVector(array){
    return new THREE.Vector3(array[0], array[1], array[2]);
}
