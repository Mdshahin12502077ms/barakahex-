<?php

namespace App\Services;

use App\Models\District;
use App\Models\Thana;

class AddressLocationDetector
{
    protected static $cachedDistricts = null;

    protected static $districtMap = [
        ["name" => "Dhaka", "aliases" => ["dhaka", "ঢাকা", "dhk"]],
        ["name" => "Gazipur", "aliases" => ["gazipur", "গাজীপুর", "joydebpur", "জয়দেবপুর"]],
        ["name" => "Narayanganj", "aliases" => ["narayanganj", "নারায়ণগঞ্জ", "নারায়নগঞ্জ", "fatullah", "ফতুল্লা", "sonargaon", "সোনারগাঁও"]],
        ["name" => "Tangail", "aliases" => ["tangail", "টাঙ্গাইল", "কালিহাতী", "মধুপুর"]],
        ["name" => "Faridpur", "aliases" => ["faridpur", "ফরিদপুর", "ভাঙ্গা", "bhanga"]],
        ["name" => "Gopalganj", "aliases" => ["gopalganj", "গোপালগঞ্জ", "টুঙ্গিপাড়া", "tungipara"]],
        ["name" => "Kishoreganj", "aliases" => ["kishoreganj", "কিশোরগঞ্জ", "bhairab", "ভৈরব"]],
        ["name" => "Madaripur", "aliases" => ["madaripur", "মাদারীপুর", "শিবচর", "shibchar"]],
        ["name" => "Manikganj", "aliases" => ["manikganj", "মানিকগঞ্জ", "সিংগাইর"]],
        ["name" => "Munshiganj", "aliases" => ["munshiganj", "মুন্সিগঞ্জ", "মুন্সীগঞ্জ", "শ্রীনগর"]],
        ["name" => "Narsingdi", "aliases" => ["narsingdi", "নরসিংদী", "রায়পুরা", "পলাশ"]],
        ["name" => "Rajbari", "aliases" => ["rajbari", "রাজবাড়ী", "রাজবাড়ি", "পাংশা"]],
        ["name" => "Shariatpur", "aliases" => ["shariatpur", "শরীয়তপুর", "শরিয়তপুর", "জাজিরা", "নড়িয়া", "naria"]],
        ["name" => "Chattogram", "aliases" => ["chattogram", "chittagong", "চট্টগ্রাম", "চিটাগাং", "ctg"]],
        ["name" => "Cox's Bazar", "aliases" => ["cox's bazar", "coxs bazar", "coxsbazar", "কক্সবাজার", "কক্স বাজার", "টেকনাফ", "teknaf"]],
        ["name" => "Cumilla", "aliases" => ["cumilla", "comilla", "কুমিল্লা"]],
        ["name" => "Brahmanbaria", "aliases" => ["brahmanbaria", "b.baria", "ব্রাহ্মণবাড়িয়া", "ব্রাহ্মনবাড়িয়া"]],
        ["name" => "Chandpur", "aliases" => ["chandpur", "চাঁদপুর", "চাদপুর", "হাজীগঞ্জ"]],
        ["name" => "Feni", "aliases" => ["feni", "ফেনী", "ফেনি"]],
        ["name" => "Lakshmipur", "aliases" => ["lakshmipur", "লক্ষ্মীপুর", "লক্ষীপুর", "রায়পুর"]],
        ["name" => "Noakhali", "aliases" => ["noakhali", "নোয়াখালী", "নোয়াখালী", "মাইজদী", "চৌমুহনী"]],
        ["name" => "Bandarban", "aliases" => ["bandarban", "বান্দরবান"]],
        ["name" => "Khagrachhari", "aliases" => ["khagrachhari", "খাগড়াছড়ি", "খাগড়াছড়ি"]],
        ["name" => "Rangamati", "aliases" => ["rangamati", "রাঙ্গামাটি", "রাঙামাটি"]],
        ["name" => "Rajshahi", "aliases" => ["rajshahi", "রাজশাহী"]],
        ["name" => "Bogura", "aliases" => ["bogura", "bogra", "বগুড়া", "বগুড়া"]],
        ["name" => "Joypurhat", "aliases" => ["joypurhat", "জয়পুরহাট", "জয়পুরহাট"]],
        ["name" => "Naogaon", "aliases" => ["naogaon", "নওগাঁ", "নওগা"]],
        ["name" => "Natore", "aliases" => ["natore", "নাটোর"]],
        ["name" => "Chapainawabganj", "aliases" => ["chapainawabganj", "chapai", "চাঁপাইনবাবগঞ্জ", "চাঁপাই"]],
        ["name" => "Pabna", "aliases" => ["pabna", "পাবনা", "ঈশ্বরদী", "ishwardi"]],
        ["name" => "Sirajganj", "aliases" => ["sirajganj", "সিরাজগঞ্জ"]],
        ["name" => "Khulna", "aliases" => ["khulna", "খুলনা"]],
        ["name" => "Bagerhat", "aliases" => ["bagerhat", "বাগেরহাট", "মংলা", "mongla"]],
        ["name" => "Chuadanga", "aliases" => ["chuadanga", "চুয়াডাঙ্গা", "চুয়াডাঙ্গা"]],
        ["name" => "Jashore", "aliases" => ["jashore", "jessore", "যশোর", "বেনাপোল", "benapole"]],
        ["name" => "Jhenaidah", "aliases" => ["jhenaidah", "ঝিনাইদহ"]],
        ["name" => "Kushtia", "aliases" => ["kushtia", "কুষ্টিয়া", "কুষ্টিয়া"]],
        ["name" => "Magura", "aliases" => ["magura", "মাগুরা"]],
        ["name" => "Meherpur", "aliases" => ["meherpur", "মেহেরপুর"]],
        ["name" => "Narail", "aliases" => ["narail", "নড়াইল", "নড়াইল"]],
        ["name" => "Satkhira", "aliases" => ["satkhira", "সাতক্ষীরা"]],
        ["name" => "Barishal", "aliases" => ["barishal", "barisal", "বরিশাল"]],
        ["name" => "Barguna", "aliases" => ["barguna", "বরগুনা"]],
        ["name" => "Bhola", "aliases" => ["bhola", "ভোলা", "বোরহানউদ্দিন", "চরফ্যাশন"]],
        ["name" => "Jhalokathi", "aliases" => ["jhalokathi", "ঝালকাঠি"]],
        ["name" => "Patuakhali", "aliases" => ["patuakhali", "পটুয়াখালী", "পটুয়াখালী", "কুয়াকাটা"]],
        ["name" => "Pirojpur", "aliases" => ["pirojpur", "পিরোজপুর"]],
        ["name" => "Sylhet", "aliases" => ["sylhet", "সিলেট"]],
        ["name" => "Habiganj", "aliases" => ["habiganj", "হবিগঞ্জ", "মাধবপুর"]],
        ["name" => "Moulvibazar", "aliases" => ["moulvibazar", "মৌলভীবাজার", "শ্রীমঙ্গল", "sreemangal"]],
        ["name" => "Sunamganj", "aliases" => ["sunamganj", "সুনামগঞ্জ", "ছাতক"]],
        ["name" => "Rangpur", "aliases" => ["rangpur", "রংপুর"]],
        ["name" => "Dinajpur", "aliases" => ["dinajpur", "দিনাজপুর"]],
        ["name" => "Gaibandha", "aliases" => ["gaibandha", "গাইবান্ধা"]],
        ["name" => "Kurigram", "aliases" => ["kurigram", "কুড়িগ্রাম", "কুড়িগ্রাম"]],
        ["name" => "Lalmonirhat", "aliases" => ["lalmonirhat", "লালমনিরহাট"]],
        ["name" => "Nilphamari", "aliases" => ["nilphamari", "নীলফামারী", "সৈয়দপুর", "saidpur"]],
        ["name" => "Panchagarh", "aliases" => ["panchagarh", "পঞ্চগড়", "পঞ্চগড়", "তেঁতুলিয়া"]],
        ["name" => "Thakurgaon", "aliases" => ["thakurgaon", "ঠাকুরগাঁও", "ঠাকুরগাও"]],
        ["name" => "Mymensingh", "aliases" => ["mymensingh", "ময়মনসিংহ", "ময়মনসিংহ"]],
        ["name" => "Jamalpur", "aliases" => ["jamalpur", "জামালপুর"]],
        ["name" => "Netrokona", "aliases" => ["netrokona", "নেত্রকোণা", "নেত্রকোনা"]],
        ["name" => "Sherpur", "aliases" => ["sherpur", "শেরপুর"]]
    ];

    protected static $famousThanas = [
        ["name" => "Banani", "aliases" => ["banani", "বনানী"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Gulshan", "aliases" => ["gulshan", "গুলশান"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Mirpur", "aliases" => ["mirpur", "মিরপুর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Dhanmondi", "aliases" => ["dhanmondi", "ধানমন্ডি", "ধানমণ্ডি"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Uttara", "aliases" => ["uttara", "উত্তরা"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Mohammadpur", "aliases" => ["mohammadpur", "মোহাম্মদপুর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Khilkhet", "aliases" => ["khilkhet", "খিলক্ষেত", "খিলখেত"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Badda", "aliases" => ["badda", "বাড্ডা"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Rampura", "aliases" => ["rampura", "রামপুরা"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Khilgaon", "aliases" => ["khilgaon", "খিলগাঁও", "খিলগাও"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Motijheel", "aliases" => ["motijheel", "মতিঝিল"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Jatrabari", "aliases" => ["jatrabari", "যাত্রাবাড়ী", "যাত্রাবাড়ি"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Demra", "aliases" => ["demra", "ডেমরা"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Lalbagh", "aliases" => ["lalbagh", "লালবাগ"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "New Market", "aliases" => ["new market", "নিউ মার্কেট", "নিউমার্কেট"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Shahbagh", "aliases" => ["shahbagh", "শাহবাগ"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Paltan", "aliases" => ["paltan", "পল্টন"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Ramna", "aliases" => ["ramna", "রমনা"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Tejgaon", "aliases" => ["tejgaon", "তেজগাঁও", "তেজগাও"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Mohakhali", "aliases" => ["mohakhali", "মহাখালী"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Banasree", "aliases" => ["banasree", "বনশ্রী"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Basabo", "aliases" => ["basabo", "বাসাবো"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Bashundhara", "aliases" => ["bashundhara", "বসুন্ধরা"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Malibagh", "aliases" => ["malibagh", "মালিবাগ"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Moghbazar", "aliases" => ["moghbazar", "mogbazar", "মগবাজার"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Shantinagar", "aliases" => ["shantinagar", "শান্তিনগর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Panthapath", "aliases" => ["panthapath", "পান্থপথ"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Farmgate", "aliases" => ["farmgate", "ফার্মগেট"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Kafrul", "aliases" => ["kafrul", "কাফরুল"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Pallabi", "aliases" => ["pallabi", "পল্লবী"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Cantonment", "aliases" => ["cantonment", "সেনানিবাস"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Hazaribagh", "aliases" => ["hazaribagh", "হাজারীবাগ"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Kamrangirchar", "aliases" => ["kamrangirchar", "কামরাঙ্গীরচর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Kotwali", "aliases" => ["kotwali", "কোতোয়ালী"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Sutrapur", "aliases" => ["sutrapur", "সূত্রাপুর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Wari", "aliases" => ["wari", "ওয়ারী"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Gendaria", "aliases" => ["gendaria", "গেন্ডারিয়া"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Shyampur", "aliases" => ["shyampur", "শ্যামপুর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Kadamtali", "aliases" => ["kadamtali", "কদমতলী"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Chawkbazar", "aliases" => ["chawkbazar", "চকবাজার"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Bongshal", "aliases" => ["bongshal", "বংশাল"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Vatara", "aliases" => ["vatara", "ভাটারা"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Dakshinkhan", "aliases" => ["dakshinkhan", "দক্ষিণখান"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Uttarkhan", "aliases" => ["uttarkhan", "উত্তরখান"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Turag", "aliases" => ["turag", "তুরাগ"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Adabor", "aliases" => ["adabor", "আদাবর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Sher-e-Bangla Nagar", "aliases" => ["sher-e-bangla nagar", "শেরেবাংলা নগর"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Kalabagan", "aliases" => ["kalabagan", "কলাবাগান"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Hatirjheel", "aliases" => ["hatirjheel", "হাতিরঝিল"], "district" => "Dhaka", "is_sub_city" => false],
        ["name" => "Savar", "aliases" => ["savar", "সাভার"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "Dhamrai", "aliases" => ["dhamrai", "ধামরাই"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "Keraniganj", "aliases" => ["keraniganj", "কেরানীগঞ্জ", "কেরানিগঞ্জ"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "Keraniganj Model", "aliases" => ["keraniganj model", "কেরানীগঞ্জ মডেল"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "South Keraniganj", "aliases" => ["south keraniganj", "দক্ষিণ কেরানীগঞ্জ"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "Nawabganj", "aliases" => ["nawabganj", "নবাবগঞ্জ"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "Dohar", "aliases" => ["dohar", "দোহার"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "Ashulia", "aliases" => ["ashulia", "আশুলিয়া", "আশুলিয়া"], "district" => "Dhaka", "is_sub_city" => true],
        ["name" => "Gazipur Sadar", "aliases" => ["gazipur sadar", "গাজীপুর সদর"], "district" => "Gazipur", "is_sub_city" => true],
        ["name" => "Tongi", "aliases" => ["tongi", "টঙ্গী", "টঙ্গি"], "district" => "Gazipur", "is_sub_city" => true],
        ["name" => "Kaliakair", "aliases" => ["kaliakair", "কালিয়াকৈর"], "district" => "Gazipur", "is_sub_city" => true],
        ["name" => "Kaliganj", "aliases" => ["kaliganj", "কালীগঞ্জ"], "district" => "Gazipur", "is_sub_city" => true],
        ["name" => "Kapasia", "aliases" => ["kapasia", "কাপাসিয়া"], "district" => "Gazipur", "is_sub_city" => true],
        ["name" => "Sreepur", "aliases" => ["sreepur", "শ্রীপুর"], "district" => "Gazipur", "is_sub_city" => true],
        ["name" => "Narayanganj Sadar", "aliases" => ["narayanganj sadar", "নারায়ণগঞ্জ সদর"], "district" => "Narayanganj", "is_sub_city" => true],
        ["name" => "Fatullah", "aliases" => ["fatullah", "ফতুল্লা"], "district" => "Narayanganj", "is_sub_city" => true],
        ["name" => "Siddhirganj", "aliases" => ["siddhirganj", "সিদ্ধিরগঞ্জ"], "district" => "Narayanganj", "is_sub_city" => true],
        ["name" => "Bandar", "aliases" => ["bandar", "বন্দর"], "district" => "Narayanganj", "is_sub_city" => true],
        ["name" => "Araihazar", "aliases" => ["araihazar", "আড়াইহাজার"], "district" => "Narayanganj", "is_sub_city" => true],
        ["name" => "Sonargaon", "aliases" => ["sonargaon", "সোনারগাঁও"], "district" => "Narayanganj", "is_sub_city" => true],
        ["name" => "Rupganj", "aliases" => ["rupganj", "রূপগঞ্জ"], "district" => "Narayanganj", "is_sub_city" => true]
    ];

    public static function getDistricts()
    {
        if (self::$cachedDistricts === null) {
            self::$cachedDistricts = District::with(['thanas' => function ($q) {
                $q->where('status', 'active')->orderBy('name');
            }])->where('status', 'active')->orderBy('name')->get();
        }
        return self::$cachedDistricts;
    }

    /**
     * Detect or validate District and Thana from address and explicit fields
     *
     * @param string $address
     * @param string|int|null $explicitDistrict
     * @param string|int|null $explicitThana
     * @return array
     */
    public static function detectLocation($address, $explicitDistrict = null, $explicitThana = null)
    {
        $districts = self::getDistricts();
        $cleanAddress = mb_strtolower(trim((string)$address));

        $matchedDistrict = null;
        $matchedThana = null;

        // 1. If explicit District provided (by ID or Name)
        if (!empty($explicitDistrict)) {
            $cleanExpDist = mb_strtolower(trim((string)$explicitDistrict));
            $matchedDistrict = $districts->first(function ($d) use ($cleanExpDist, $explicitDistrict) {
                return (string)$d->id === (string)$explicitDistrict || mb_strtolower($d->name) === $cleanExpDist;
            });

            // If not directly matched, check aliases
            if (!$matchedDistrict) {
                foreach (self::$districtMap as $dm) {
                    if (in_array($cleanExpDist, $dm['aliases'])) {
                        $matchedDistrict = $districts->first(function ($d) use ($dm) {
                            return mb_strtolower($d->name) === mb_strtolower($dm['name']);
                        });
                        if ($matchedDistrict) break;
                    }
                }
            }
        }

        // 2. If explicit Thana provided (by ID or Name)
        if (!empty($explicitThana)) {
            $cleanExpThana = mb_strtolower(trim((string)$explicitThana));
            if ($matchedDistrict) {
                $matchedThana = $matchedDistrict->thanas->first(function ($t) use ($cleanExpThana, $explicitThana) {
                    return (string)$t->id === (string)$explicitThana || mb_strtolower($t->name) === $cleanExpThana;
                });
            } else {
                // Search across all districts
                foreach ($districts as $d) {
                    $t = $d->thanas->first(function ($th) use ($cleanExpThana, $explicitThana) {
                        return (string)$th->id === (string)$explicitThana || mb_strtolower($th->name) === $cleanExpThana;
                    });
                    if ($t) {
                        $matchedThana = $t;
                        $matchedDistrict = $d;
                        break;
                    }
                }
            }
        }

        // 3. If either district or thana is missing, detect from address text
        if (!$matchedDistrict || !$matchedThana) {
            // A. Check 64 districts map first to see if a district is explicitly named in the address
            if (!$matchedDistrict) {
                foreach (self::$districtMap as $dm) {
                    foreach ($dm['aliases'] as $alias) {
                        if (mb_stripos($cleanAddress, $alias) !== false) {
                            $targetDistName = mb_strtolower($dm['name']);
                            $matchedDistrict = $districts->first(function ($d) use ($targetDistName) {
                                return mb_strtolower($d->name) === $targetDistName;
                            });
                            if ($matchedDistrict) break 2;
                        }
                    }
                }
            }

            // B. If district is known, search thanas within that district first
            if ($matchedDistrict && !$matchedThana) {
                if ($matchedDistrict->thanas) {
                    foreach ($matchedDistrict->thanas as $th) {
                        $tName = mb_strtolower($th->name);
                        if (mb_stripos($cleanAddress, $tName) !== false) {
                            $matchedThana = $th;
                            break;
                        }
                        $cleanT = preg_replace('/district$/i', '', $tName);
                        $cleanT = trim($cleanT);
                        if (strlen($cleanT) > 2 && mb_stripos($cleanAddress, $cleanT) !== false) {
                            $matchedThana = $th;
                            break;
                        }
                    }
                }
            }

            // C. If thana still not found, check famous thanas
            if (!$matchedThana) {
                foreach (self::$famousThanas as $ft) {
                    foreach ($ft['aliases'] as $alias) {
                        if (mb_stripos($cleanAddress, $alias) !== false) {
                            $targetDistName = mb_strtolower($ft['district']);
                            $distCandidate = $matchedDistrict ?: $districts->first(function ($d) use ($targetDistName) {
                                return mb_strtolower($d->name) === $targetDistName;
                            });

                            if ($distCandidate) {
                                $thanaCandidate = $distCandidate->thanas->first(function ($t) use ($ft) {
                                    return mb_stripos($t->name, $ft['name']) !== false || mb_stripos($ft['name'], $t->name) !== false;
                                });

                                if ($thanaCandidate) {
                                    $matchedThana = $thanaCandidate;
                                    if (!$matchedDistrict) $matchedDistrict = $distCandidate;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }

            // C. Search thanas in address
            if (!$matchedThana) {
                if ($matchedDistrict && $matchedDistrict->thanas) {
                    foreach ($matchedDistrict->thanas as $th) {
                        $tName = mb_strtolower($th->name);
                        if (mb_stripos($cleanAddress, $tName) !== false) {
                            $matchedThana = $th;
                            break;
                        }
                        $cleanT = preg_replace('/district$/i', '', $tName);
                        $cleanT = trim($cleanT);
                        if (strlen($cleanT) > 2 && mb_stripos($cleanAddress, $cleanT) !== false) {
                            $matchedThana = $th;
                            break;
                        }
                    }
                }

                // If still no thana, scan across ALL districts in DB
                if (!$matchedThana) {
                    foreach ($districts as $d) {
                        if (!$d->thanas) continue;
                        foreach ($d->thanas as $th) {
                            $tName = mb_strtolower($th->name);
                            if ($tName === 'sadar' || strlen($tName) < 3) continue;

                            if (mb_stripos($cleanAddress, $tName) !== false) {
                                $matchedThana = $th;
                                if (!$matchedDistrict) {
                                    $matchedDistrict = $d;
                                }
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        // If district matched and thana not matched, check if there's a Sadar thana in that district
        if ($matchedDistrict && !$matchedThana) {
            if (mb_stripos($cleanAddress, 'sadar') !== false || mb_stripos($cleanAddress, 'সদর') !== false) {
                $matchedThana = $matchedDistrict->thanas->first(function ($t) {
                    return mb_stripos($t->name, 'sadar') !== false;
                });
            }
        }

        // Validate result
        if ($matchedDistrict && $matchedThana) {
            // Determine suggested parcel type
            $distNameLower = mb_strtolower($matchedDistrict->name);
            $thanaNameLower = mb_strtolower($matchedThana->name);

            $isSubCity = in_array($distNameLower, ['gazipur', 'narayanganj'])
                || ($distNameLower === 'dhaka' && in_array($thanaNameLower, ['savar', 'dhamrai', 'keraniganj', 'keraniganj model', 'south keraniganj', 'nawabganj', 'dohar', 'ashulia']));

            $suggestedParcelType = 'outside_city';
            if ($distNameLower === 'dhaka' && !$isSubCity) {
                $suggestedParcelType = 'same_day';
            } elseif ($isSubCity) {
                $suggestedParcelType = 'sub_city';
            }

            return [
                'is_valid' => true,
                'district_id' => $matchedDistrict->id,
                'district_name' => $matchedDistrict->name,
                'thana_id' => $matchedThana->id,
                'thana_name' => $matchedThana->name,
                'suggested_parcel_type' => $suggestedParcelType,
                'error' => null,
            ];
        }

        // Invalid Address
        $errorMsg = 'Please enter a valid address containing district and thana.';
        if ($matchedDistrict && !$matchedThana) {
            $errorMsg = 'Valid district identified (' . $matchedDistrict->name . '), but valid thana not found in address.';
        } elseif (!$matchedDistrict && $matchedThana) {
            $errorMsg = 'Thana identified (' . $matchedThana->name . '), but district not found in address.';
        }

        return [
            'is_valid' => false,
            'district_id' => $matchedDistrict ? $matchedDistrict->id : null,
            'district_name' => $matchedDistrict ? $matchedDistrict->name : null,
            'thana_id' => $matchedThana ? $matchedThana->id : null,
            'thana_name' => $matchedThana ? $matchedThana->name : null,
            'suggested_parcel_type' => null,
            'error' => $errorMsg,
        ];
    }
}
