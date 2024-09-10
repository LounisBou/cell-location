<?php

/**
 * Mobile Country Code
 * @see https://en.wikipedia.org/wiki/Mobile_country_code
 * @see https://www.mcc-mnc.com/
 */

declare(strict_types=1);

namespace Lounisbou\CellLocation\Enums;

enum MobileCountryCode: int
{
    // A
    case AFGHANISTAN = 412;
    case ALBANIA = 276;
    case ALGERIA = 603;
    case AMERICAN_SAMOA = 544;
    case ANDORRA = 213;
    case ANGOLA = 631;
    case ANGUILLA = 365;
    case ANTARCTICA = 672;
    case ANTIGUA_AND_BARBUDA = 344;
    case ARGENTINA = 722;
    case ARMENIA = 283;
    case ARUBA = 363;
    case AUSTRALIA = 505;
    case AUSTRIA = 232;
    case AZERBAIJAN = 400;

    // B
    case BAHAMAS = 364;
    case BAHRAIN = 426;
    case BANGLADESH = 470;
    case BARBADOS = 342;
    case BELARUS = 257;
    case BELGIUM = 206;
    case BELIZE = 702;
    case BENIN = 616;
    case BERMUDA = 350;
    case BHUTAN = 402;
    case BOLIVIA = 736;
    case BOSNIA_AND_HERZEGOVINA = 218;
    case BOTSWANA = 652;
    case BRAZIL = 724;
    case BRITISH_VIRGIN_ISLANDS = 348;
    case BRUNEI = 528;
    case BULGARIA = 284;
    case BURKINA_FASO = 613;
    case BURUNDI = 642;

    // C
    case CAMBODIA = 456;
    case CAMEROON = 624;
    case CANADA = 302;
    case CAPE_VERDE = 625;
    case CAYMAN_ISLANDS = 346;
    case CENTRAL_AFRICAN_REPUBLIC = 623;
    case CHAD = 622;
    case CHILE = 730;
    case CHINA = 460;
    case COLOMBIA = 732;
    case COMOROS = 654;
    case CONGO = 629;
    case CONGO_DEMOCRATIC_REPUBLIC = 630;
    case COOK_ISLANDS = 548;
    case COSTA_RICA = 712;
    case CROATIA = 219;
    case CUBA = 368;
    case CURACAO = 362;
    case CYPRUS = 280;
    case CZECH_REPUBLIC = 230;

    // D
    case DENMARK = 238;
    case DJIBOUTI = 638;
    case DOMINICA = 366;
    case DOMINICAN_REPUBLIC = 370;

    // E
    case ECUADOR = 740;
    case EGYPT = 602;
    case EL_SALVADOR = 706;
    case EQUATORIAL_GUINEA = 627;
    case ERITREA = 657;
    case ESTONIA = 248;
    case ESWATINI = 653;
    case ETHIOPIA = 636;

    // F
    case FAROE_ISLANDS = 288;
    case FIJI = 542;
    case FINLAND = 244;
    case FRANCE = 208;
    case FRENCH_GUIANA = 742;
    case FRENCH_POLYNESIA = 547;
    case FRENCH_SOUTHERN_TERRITORIES = 655;

    // G
    case GABON = 628;
    case GAMBIA = 607;
    case GEORGIA = 282;
    case GERMANY = 262;
    case GHANA = 620;
    case GIBRALTAR = 266;
    case GREECE = 202;
    case GREENLAND = 290;
    case GRENADA = 352;
    case GUADELOUPE = 340;
    case GUAM = 535;
    case GUATEMALA = 704;
    case GUINEA = 611;
    case GUINEA_BISSAU = 632;
    case GUYANA = 738;

    // H
    case HAITI = 372;
    case HONDURAS = 708;
    case HONG_KONG = 454;
    case HUNGARY = 216;

    // I
    case ICELAND = 274;
    case INDIA = 404;
    case INDONESIA = 510;
    case IRAN = 432;
    case IRAQ = 418;
    case IRELAND = 272;
    case ISRAEL = 425;
    case ITALY = 222;
    case IVORY_COAST = 612;

    // J
    case JAMAICA = 338;
    case JAPAN = 440;
    case JORDAN = 416;

    // K
    case KAZAKHSTAN = 401;
    case KENYA = 639;
    case KIRIBATI = 545;
    case KOREA_SOUTH = 450;
    case KUWAIT = 419;
    case KYRGYZSTAN = 437;

    // L
    case LAOS = 457;
    case LATVIA = 247;
    case LEBANON = 415;
    case LESOTHO = 651;
    case LIBERIA = 618;
    case LIBYA = 606;
    case LIECHTENSTEIN = 295;
    case LITHUANIA = 246;
    case LUXEMBOURG = 270;

    // M
    case MACAO = 455;
    case MADAGASCAR = 646;
    case MALAWI = 650;
    case MALAYSIA = 502;
    case MALDIVES = 472;
    case MALI = 610;
    case MALTA = 278;
    case MARTINIQUE = 340;
    case MAURITANIA = 609;
    case MAURITIUS = 617;
    case MAYOTTE = 663;
    case MEXICO = 334;
    case MOLDOVA = 259;
    case MONACO = 212;
    case MONGOLIA = 428;
    case MONTENEGRO = 297;
    case MONTSERRAT = 354;
    case MOROCCO = 604;
    case MOZAMBIQUE = 643;
    case MYANMAR = 414;

    // N
    case NAMIBIA = 649;
    case NAURU = 536;
    case NEPAL = 429;
    case NETHERLANDS = 204;
    case NETHERLANDS_ANTILLES = 362;
    case NEW_CALEDONIA = 546;
    case NEW_ZEALAND = 530;
    case NICARAGUA = 710;
    case NIGER = 614;
    case NIGERIA = 621;
    case NORFOLK_ISLAND = 505;
    case NORTH_MACEDONIA = 294;
    case NORWAY = 242;

    // O
    case OMAN = 422;

    // P
    case PAKISTAN = 410;
    case PALAU = 552;
    case PANAMA = 714;
    case PAPUA_NEW_GUINEA = 537;
    case PARAGUAY = 744;
    case PERU = 716;
    case PHILIPPINES = 515;
    case POLAND = 260;
    case PORTUGAL = 268;
    case PUERTO_RICO = 330;

    // Q
    case QATAR = 427;

    // R
    case ROMANIA = 226;
    case RUSSIA = 250;
    case RWANDA = 635;

    // S
    case SAINT_KITTS_AND_NEVIS = 356;
    case SAINT_LUCIA = 358;
    case SAINT_VINCENT_AND_THE_GRENADINES = 360;
    case SAMOA = 549;
    case SAN_MARINO = 292;
    case SAO_TOME_AND_PRINCIPE = 626;
    case SAUDI_ARABIA = 420;
    case SENEGAL = 608;
    case SERBIA = 220;
    case SEYCHELLES = 633;
    case SIERRA_LEONE = 619;
    case SINGAPORE = 525;
    case SLOVAKIA = 231;
    case SLOVENIA = 293;
    case SOLOMON_ISLANDS = 540;
    case SOMALIA = 637;
    case SOUTH_AFRICA = 655;
    case SOUTH_SUDAN = 659;
    case SPAIN = 214;
    case SRI_LANKA = 413;
    case SUDAN = 634;
    case SURINAME = 746;
    case SWEDEN = 240;
    case SWITZERLAND = 228;
    case SYRIA = 417;

    // T
    case TAIWAN = 466;
    case TAJIKISTAN = 436;
    case TANZANIA = 640;
    case THAILAND = 520;
    case TOGO = 615;
    case TOKELAU = 553;
    case TONGA = 539;
    case TRINIDAD_AND_TOBAGO = 374;
    case TUNISIA = 605;
    case TURKEY = 286;
    case TURKMENISTAN = 438;
    case TURKS_AND_CAICOS = 376;
    case TUVALU = 553;

    // U
    case UGANDA = 641;
    case UKRAINE = 255;
    case UNITED_ARAB_EMIRATES = 424;
    case UNITED_KINGDOM = 234;
    case UNITED_STATES = 310;
    case URUGUAY = 748;
    case UZBEKISTAN = 434;

    // V
    case VANUATU = 541;
    case VATICAN_CITY = 225;
    case VENEZUELA = 734;
    case VIETNAM = 452;
    case VIRGIN_ISLANDS_US = 332;

    // Y
    case YEMEN = 421;

    // Z
    case ZAMBIA = 645;
    case ZIMBABWE = 648;
}
