@push('script')
<script>
$(document).ready(function () {
    const DB_LOCATIONS = @json($districts ?? []);

    const DISTRICTS_MAP = [
        { name: "Dhaka", aliases: ["dhaka", "ঢাকা", "dhk"] },
        { name: "Gazipur", aliases: ["gazipur", "গাজীপুর", "joydebpur", "জয়দেবপুর"] },
        { name: "Narayanganj", aliases: ["narayanganj", "নারায়ণগঞ্জ", "নারায়নগঞ্জ", "fatullah", "ফতুল্লা", "sonargaon", "সোনারগাঁও"] },
        { name: "Tangail", aliases: ["tangail", "টাঙ্গাইল", "কালিহাতী", "মধুপুর"] },
        { name: "Faridpur", aliases: ["faridpur", "ফরিদপুর", "ভাঙ্গা", "bhanga"] },
        { name: "Gopalganj", aliases: ["gopalganj", "গোপালগঞ্জ", "টুঙ্গিপাড়া", "tungipara"] },
        { name: "Kishoreganj", aliases: ["kishoreganj", "কিশোরগঞ্জ", "bhairab", "ভৈরব"] },
        { name: "Madaripur", aliases: ["madaripur", "মাদারীপুর", "শিবচর", "shibchar"] },
        { name: "Manikganj", aliases: ["manikganj", "মানিকগঞ্জ", "সিংগাইর"] },
        { name: "Munshiganj", aliases: ["munshiganj", "মুন্সিগঞ্জ", "মুন্সীগঞ্জ", "শ্রীনগর"] },
        { name: "Narsingdi", aliases: ["narsingdi", "নরসিংদী", "রায়পুরা", "পলাশ"] },
        { name: "Rajbari", aliases: ["rajbari", "রাজবাড়ী", "রাজবাড়ি", "পাংশা"] },
        { name: "Shariatpur", aliases: ["shariatpur", "শরীয়তপুর", "শরিয়তপুর", "জাজিরা", "নড়িয়া", "naria"] },
        { name: "Chattogram", aliases: ["chattogram", "chittagong", "চট্টগ্রাম", "চিটাগাং", "ctg"] },
        { name: "Cox's Bazar", aliases: ["cox's bazar", "coxs bazar", "coxsbazar", "কক্সবাজার", "কক্স বাজার", "টেকনাফ", "teknaf"] },
        { name: "Cumilla", aliases: ["cumilla", "comilla", "কুমিল্লা"] },
        { name: "Brahmanbaria", aliases: ["brahmanbaria", "b.baria", "ব্রাহ্মণবাড়িয়া", "ব্রাহ্মনবাড়িয়া"] },
        { name: "Chandpur", aliases: ["chandpur", "চাঁদপুর", "চাদপুর", "হাজীগঞ্জ"] },
        { name: "Feni", aliases: ["feni", "ফেনী", "ফেনি"] },
        { name: "Lakshmipur", aliases: ["lakshmipur", "লক্ষ্মীপুর", "লক্ষীপুর", "রায়পুর"] },
        { name: "Noakhali", aliases: ["noakhali", "নোয়াখালী", "নোয়াখালী", "মাইজদী", "চৌমুহনী"] },
        { name: "Bandarban", aliases: ["bandarban", "বান্দরবান"] },
        { name: "Khagrachhari", aliases: ["khagrachhari", "খাগড়াছড়ি", "খাগড়াছড়ি"] },
        { name: "Rangamati", aliases: ["rangamati", "রাঙ্গামাটি", "রাঙামাটি"] },
        { name: "Rajshahi", aliases: ["rajshahi", "রাজশাহী"] },
        { name: "Bogura", aliases: ["bogura", "bogra", "বগুড়া", "বগুড়া"] },
        { name: "Joypurhat", aliases: ["joypurhat", "জয়পুরহাট", "জয়পুরহাট"] },
        { name: "Naogaon", aliases: ["naogaon", "নওগাঁ", "নওগা"] },
        { name: "Natore", aliases: ["natore", "নাটোর"] },
        { name: "Chapainawabganj", aliases: ["chapainawabganj", "chapai", "চাঁপাইনবাবগঞ্জ", "চাঁপাই"] },
        { name: "Pabna", aliases: ["pabna", "পাবনা", "ঈশ্বরদী", "ishwardi"] },
        { name: "Sirajganj", aliases: ["sirajganj", "সিরাজগঞ্জ"] },
        { name: "Khulna", aliases: ["khulna", "খুলনা"] },
        { name: "Bagerhat", aliases: ["bagerhat", "বাগেরহাট", "মংলা", "mongla"] },
        { name: "Chuadanga", aliases: ["chuadanga", "চুয়াডাঙ্গা", "চুয়াডাঙ্গা"] },
        { name: "Jashore", aliases: ["jashore", "jessore", "যশোর", "বেনাপোল", "benapole"] },
        { name: "Jhenaidah", aliases: ["jhenaidah", "ঝিনাইদহ"] },
        { name: "Kushtia", aliases: ["kushtia", "কুষ্টিয়া", "কুষ্টিয়া"] },
        { name: "Magura", aliases: ["magura", "মাগুরা"] },
        { name: "Meherpur", aliases: ["meherpur", "মেহেরপুর"] },
        { name: "Narail", aliases: ["narail", "নড়াইল", "নড়াইল"] },
        { name: "Satkhira", aliases: ["satkhira", "সাতক্ষীরা"] },
        { name: "Barishal", aliases: ["barishal", "barisal", "বরিশাল"] },
        { name: "Barguna", aliases: ["barguna", "বরগুনা"] },
        { name: "Bhola", aliases: ["bhola", "ভোলা", "বোরহানউদ্দিন", "চরফ্যাশন"] },
        { name: "Jhalokathi", aliases: ["jhalokathi", "ঝালকাঠি"] },
        { name: "Patuakhali", aliases: ["patuakhali", "পটুয়াখালী", "পটুয়াখালী", "কুয়াকাটা"] },
        { name: "Pirojpur", aliases: ["pirojpur", "পিরোজপুর"] },
        { name: "Sylhet", aliases: ["sylhet", "সিলেট"] },
        { name: "Habiganj", aliases: ["habiganj", "হবিগঞ্জ", "মাধবপুর"] },
        { name: "Moulvibazar", aliases: ["moulvibazar", "মৌলভীবাজার", "শ্রীমঙ্গল", "sreemangal"] },
        { name: "Sunamganj", aliases: ["sunamganj", "সুনামগঞ্জ", "ছাতক"] },
        { name: "Rangpur", aliases: ["rangpur", "রংপুর"] },
        { name: "Dinajpur", aliases: ["dinajpur", "দিনাজপুর"] },
        { name: "Gaibandha", aliases: ["gaibandha", "গাইবান্ধা"] },
        { name: "Kurigram", aliases: ["kurigram", "কুড়িগ্রাম", "কুড়িগ্রাম"] },
        { name: "Lalmonirhat", aliases: ["lalmonirhat", "লালমনিরহাট"] },
        { name: "Nilphamari", aliases: ["nilphamari", "নীলফামারী", "সৈয়দপুর", "saidpur"] },
        { name: "Panchagarh", aliases: ["panchagarh", "পঞ্চগড়", "পঞ্চগড়", "তেঁতুলিয়া"] },
        { name: "Thakurgaon", aliases: ["thakurgaon", "ঠাকুরগাঁও", "ঠাকুরগাও"] },
        { name: "Mymensingh", aliases: ["mymensingh", "ময়মনসিংহ", "ময়মনসিংহ"] },
        { name: "Jamalpur", aliases: ["jamalpur", "জামালপুর"] },
        { name: "Netrokona", aliases: ["netrokona", "নেত্রকোণা", "নেত্রকোনা"] },
        { name: "Sherpur", aliases: ["sherpur", "শেরপুর"] }
    ];

    const FAMOUS_THANAS = [
        { name: "Banani", aliases: ["banani", "বনানী"], district: "Dhaka" },
        { name: "Gulshan", aliases: ["gulshan", "গুলশান"], district: "Dhaka" },
        { name: "Mirpur", aliases: ["mirpur", "মিরপুর"], district: "Dhaka" },
        { name: "Dhanmondi", aliases: ["dhanmondi", "ধানমন্ডি", "ধানমণ্ডি"], district: "Dhaka" },
        { name: "Uttara", aliases: ["uttara", "উত্তরা"], district: "Dhaka" },
        { name: "Mohammadpur", aliases: ["mohammadpur", "মোহাম্মদপুর"], district: "Dhaka" },
        { name: "Khilkhet", aliases: ["khilkhet", "খিলক্ষেত", "খিলখেত"], district: "Dhaka" },
        { name: "Badda", aliases: ["badda", "বাড্ডা"], district: "Dhaka" },
        { name: "Rampura", aliases: ["rampura", "রামপুরা"], district: "Dhaka" },
        { name: "Khilgaon", aliases: ["khilgaon", "খিলগাঁও", "খিলগাও"], district: "Dhaka" },
        { name: "Motijheel", aliases: ["motijheel", "মতিঝিল"], district: "Dhaka" },
        { name: "Jatrabari", aliases: ["jatrabari", "যাত্রাবাড়ী", "যাত্রাবাড়ি"], district: "Dhaka" },
        { name: "Demra", aliases: ["demra", "ডেমরা"], district: "Dhaka" },
        { name: "Lalbagh", aliases: ["lalbagh", "লালবাগ"], district: "Dhaka" },
        { name: "New Market", aliases: ["new market", "নিউ মার্কেট", "নিউমার্কেট"], district: "Dhaka" },
        { name: "Shahbagh", aliases: ["shahbagh", "শাহবাগ"], district: "Dhaka" },
        { name: "Paltan", aliases: ["paltan", "পল্টন"], district: "Dhaka" },
        { name: "Ramna", aliases: ["ramna", "রমনা"], district: "Dhaka" },
        { name: "Tejgaon", aliases: ["tejgaon", "তেজগাঁও", "তেজগাও"], district: "Dhaka" },
        { name: "Mohakhali", aliases: ["mohakhali", "মহাখালী"], district: "Dhaka" },
        { name: "Banasree", aliases: ["banasree", "বনশ্রী"], district: "Dhaka" },
        { name: "Basabo", aliases: ["basabo", "বাসাবো"], district: "Dhaka" },
        { name: "Bashundhara", aliases: ["bashundhara", "বসুন্ধরা"], district: "Dhaka" },
        { name: "Malibagh", aliases: ["malibagh", "মালিবাগ"], district: "Dhaka" },
        { name: "Moghbazar", aliases: ["moghbazar", "mogbazar", "মগবাজার"], district: "Dhaka" },
        { name: "Shantinagar", aliases: ["shantinagar", "শান্তিনগর"], district: "Dhaka" },
        { name: "Panthapath", aliases: ["panthapath", "পান্থপথ"], district: "Dhaka" },
        { name: "Elephant Road", aliases: ["elephant road", "এলিফ্যান্ট রোড"], district: "Dhaka" },
        { name: "Farmgate", aliases: ["farmgate", "ফার্মগেট"], district: "Dhaka" },
        { name: "Wari", aliases: ["wari", "ওয়ারী", "ওয়ারী"], district: "Dhaka" },
        { name: "Adabor", aliases: ["adabor", "আদাবর"], district: "Dhaka" },
        { name: "Kafrul", aliases: ["kafrul", "কাফরুল"], district: "Dhaka" },
        { name: "Kalabagan", aliases: ["kalabagan", "কলাবাগান"], district: "Dhaka" },
        { name: "Cantonment", aliases: ["cantonment", "ক্যান্টনমেন্ট"], district: "Dhaka" },
        { name: "Chawkbazar", aliases: ["chawkbazar", "চকবাজার"], district: "Dhaka" },
        { name: "Kamrangirchar", aliases: ["kamrangirchar", "কামরাঙ্গীরচর"], district: "Dhaka" },
        { name: "Hazaribagh", aliases: ["hazaribagh", "হাজারীবাগ"], district: "Dhaka" },
        { name: "Sutrapur", aliases: ["sutrapur", "সূত্রাপুর"], district: "Dhaka" },
        { name: "Kotwali", aliases: ["kotwali", "কোতোয়ালী", "কোতোয়ালী"] },
        { name: "Savar", aliases: ["savar", "সাভার"], district: "Dhaka", is_sub_city: true },
        { name: "Keraniganj", aliases: ["keraniganj", "কেরানীগঞ্জ", "কেরানিগঞ্জ"], district: "Dhaka", is_sub_city: true },
        { name: "Dhamrai", aliases: ["dhamrai", "ধামরাই"], district: "Dhaka", is_sub_city: true },
        { name: "Tongi", aliases: ["tongi", "টঙ্গী", "টঙ্গি"], district: "Gazipur", is_sub_city: true },
        { name: "Panchlaish", aliases: ["panchlaish", "পাঁচলাইশ"], district: "Chattogram" },
        { name: "Halishahar", aliases: ["halishahar", "হালিশহর"], district: "Chattogram" },
        { name: "Agrabad", aliases: ["agrabad", "আগ্রাবাদ"], district: "Chattogram" },
        { name: "Chandgaon", aliases: ["chandgaon", "চান্দগাঁও"], district: "Chattogram" }
    ];

    let parseTimeout = null;
    let pendingThanaName = null;

    // Listen for thanas loaded from charge-script's AJAX
    $('#thana_to_area').on('thanas_loaded', function () {
        if (pendingThanaName) {
            selectMatchingThana(pendingThanaName);
            pendingThanaName = null;
        } else {
            const addr = ($('#customer_address').val() || '').trim().toLowerCase();
            $('#thana_to_area option').each(function () {
                const val = $(this).val();
                const text = $(this).text().trim().toLowerCase();
                if (val && text && text !== 'select thana') {
                    if (addr.includes(text)) {
                        selectMatchingThana(text);
                        return false;
                    }
                }
            });
        }
    });

    $('#customer_address').on('input paste keyup change keydown', function () {
        clearTimeout(parseTimeout);
        parseTimeout = setTimeout(detectLocationFromAddress, 300);
    });

    function findDistrictOption(districtNameOrId) {
        if (!districtNameOrId) return null;
        let matched = null;
        const target = districtNameOrId.toString().trim().toLowerCase();
        $('#city_to_thana option').each(function () {
            const val = $(this).val();
            if (!val) return;
            const text = $(this).text().trim().toLowerCase();
            const dataName = ($(this).data('name') || '').toString().toLowerCase();
            if (val.toString() === target || text === target || dataName === target) {
                matched = { id: val, name: $(this).text().trim() };
                return false;
            }
        });
        return matched;
    }

    function selectMatchingThana(thanaNameOrId) {
        if (!thanaNameOrId) return null;
        const target = thanaNameOrId.toString().trim().toLowerCase();
        let matchedVal = null;
        let matchedText = '';

        // 1. Match by Option Value
        if ($('#thana_to_area option[value="' + thanaNameOrId + '"]').length) {
            matchedVal = thanaNameOrId;
            matchedText = $('#thana_to_area option[value="' + thanaNameOrId + '"]').text().trim();
        }

        // 2. Exact match by Text
        if (!matchedVal) {
            $('#thana_to_area option').each(function () {
                const val = $(this).val();
                if (!val) return;
                const text = $(this).text().trim().toLowerCase();
                if (text === target) {
                    matchedVal = val;
                    matchedText = $(this).text().trim();
                    return false;
                }
            });
        }

        // 3. Substring match by Text
        if (!matchedVal) {
            $('#thana_to_area option').each(function () {
                const val = $(this).val();
                if (!val) return;
                const text = $(this).text().trim().toLowerCase();
                if (text.length >= 3 && (text.includes(target) || target.includes(text))) {
                    matchedVal = val;
                    matchedText = $(this).text().trim();
                    return false;
                }
            });
        }

        if (matchedVal) {
            $('#thana_to_area').val(matchedVal).trigger('change');

            const distText = $('#city_to_thana option:selected').text().trim();
            if (distText && distText !== 'Select District') {
                showValidBadge(distText, matchedText);
            }
            return { id: matchedVal, name: matchedText };
        }
        return null;
    }

    function showValidBadge(distName, thanaName) {
        let badgeText = '📍 সনাক্ত হয়েছে: ' + distName;
        if (thanaName) {
            badgeText += ' > ' + thanaName;
        }
        $('#address_auto_detect_badge')
            .removeClass('d-none bg-soft-danger text-danger')
            .addClass('bg-soft-success text-success')
            .css({ 'border': '1px solid #28a745', 'border-radius': '4px', 'display': 'inline-block' })
            .html('<i class="las la-magic me-1"></i> <span>' + badgeText + '</span>');
        
        $('#customer_address').removeClass('is-invalid');
        $('#address_error_msg').addClass('d-none');
    }

    function showInvalidBadge(msg) {
        $('#address_auto_detect_badge')
            .removeClass('d-none bg-soft-success text-success')
            .addClass('bg-soft-danger text-danger')
            .css({ 'border': '1px solid #dc3545', 'border-radius': '4px', 'display': 'inline-block' })
            .html('<i class="las la-exclamation-triangle me-1"></i> <span>' + (msg || 'অনুগ্রহ করে সঠিক ঠিকানা লিখুন (জেলা ও থানা আবশ্যক)') + '</span>');
        
        $('#customer_address').addClass('is-invalid');
        $('#address_error_msg').text(msg || 'Please enter a valid address containing district and thana.').removeClass('d-none');
    }

    function detectLocationFromAddress() {
        const address = ($('#customer_address').val() || '').trim().toLowerCase();
        if (address.length < 3) {
            $('#address_auto_detect_badge').addClass('d-none');
            $('#address_error_msg').addClass('d-none');
            $('#customer_address').removeClass('is-invalid');

            if ($('#city_to_thana').val() !== '') {
                $('#city_to_thana').val('').trigger('change');
            }
            if ($('#thana_to_area').val() !== '') {
                $('#thana_to_area').val('').trigger('change');
            }
            return { valid: false };
        }

        let detectedDistrict = null;
        let detectedThana = null;

        // 1. Check famous thanas first (with custom aliases / sub_city flags)
        for (const t of FAMOUS_THANAS) {
            for (const alias of t.aliases) {
                if (address.includes(alias.toLowerCase())) {
                    detectedThana = { name: t.name, is_sub_city: t.is_sub_city };
                    if (t.district) {
                        detectedDistrict = DB_LOCATIONS.find(d => d.name.toLowerCase() === t.district.toLowerCase());
                    }
                    break;
                }
            }
            if (detectedThana) break;
        }

        // 2. Check 64 districts map (Bangla & English aliases)
        if (!detectedDistrict) {
            for (const d of DISTRICTS_MAP) {
                for (const alias of d.aliases) {
                    if (address.includes(alias.toLowerCase())) {
                        detectedDistrict = DB_LOCATIONS.find(item => item.name.toLowerCase() === d.name.toLowerCase());
                        break;
                    }
                }
                if (detectedDistrict) break;
            }
        }

        // 3. Search ALL 64 districts' thanas in DB_LOCATIONS
        if (!detectedThana) {
            // If district is already found, check this district's thanas first
            if (detectedDistrict && detectedDistrict.thanas) {
                for (const th of detectedDistrict.thanas) {
                    const thName = th.name.toLowerCase();
                    if (address.includes(thName)) {
                        detectedThana = th;
                        break;
                    }
                    const cleaned = thName.replace(/district$/i, '').trim();
                    if (cleaned.length > 2 && address.includes(cleaned)) {
                        detectedThana = th;
                        break;
                    }
                }
            }

            // If thana not yet found, scan across ALL districts
            if (!detectedThana) {
                outerDistrictLoop:
                for (const dist of DB_LOCATIONS) {
                    if (!dist.thanas) continue;
                    for (const th of dist.thanas) {
                        const thName = th.name.toLowerCase();
                        if (thName === 'sadar' || thName.length < 3) continue;

                        if (address.includes(thName)) {
                            detectedThana = th;
                            if (!detectedDistrict) {
                                detectedDistrict = dist;
                            }
                            break outerDistrictLoop;
                        }
                    }
                }
            }
        }

        // 4. Verification: BOTH District AND Thana must be matched from Database
        if (detectedDistrict && detectedThana) {
            const matchedOption = findDistrictOption(detectedDistrict.id || detectedDistrict.name);
            if (matchedOption) {
                const currentVal = $('#city_to_thana').val();
                const thanaToSelect = detectedThana.id || detectedThana.name;

                if (currentVal != matchedOption.id) {
                    pendingThanaName = thanaToSelect;
                    $('#city_to_thana').val(matchedOption.id).trigger('change');
                } else if (thanaToSelect) {
                    selectMatchingThana(thanaToSelect);
                }

                // Auto-update delivery area (parcel_type)
                if (detectedThana.is_sub_city) {
                    $('select[name="parcel_type"]').val('sub_city').trigger('change');
                } else if (matchedOption.name.toLowerCase() === 'dhaka') {
                    const currentType = $('select[name="parcel_type"]').val();
                    if (!currentType || currentType === 'outside_city') {
                        $('select[name="parcel_type"]').val('same_day').trigger('change');
                    }
                } else if (matchedOption.name.toLowerCase() === 'gazipur' || matchedOption.name.toLowerCase() === 'narayanganj') {
                    $('select[name="parcel_type"]').val('sub_city').trigger('change');
                } else {
                    $('select[name="parcel_type"]').val('outside_city').trigger('change');
                }

                showValidBadge(matchedOption.name, detectedThana.name);
                return { valid: true, district: detectedDistrict, thana: detectedThana };
            }
        }

        // If either District or Thana is NOT detected in database from the address
        showInvalidBadge('Please enter a valid address with district and thana');
        if ($('#city_to_thana').val() !== '') {
            $('#city_to_thana').val('').trigger('change');
        }
        if ($('#thana_to_area').val() !== '') {
            $('#thana_to_area').val('').trigger('change');
        }
        return { valid: false };
    }

    // ==========================================
    // Customer Phone Number Real-Time Validation
    // ==========================================
    const bdPhoneRegex = /^(?:\+?88|88)?01[3-9]\d{8}$/;

    function validateCustomerPhone(showErrorImmediately = false) {
        const phoneInput = $('#customer_phone_number');
        if (!phoneInput.length) return true;

        const rawVal = phoneInput.val() || '';
        const cleanVal = rawVal.trim().replace(/[\s\-]/g, '');
        const errorEl = $('#customer_phone_error');

        if (!cleanVal) {
            if (showErrorImmediately) {
                phoneInput.addClass('is-invalid').removeClass('is-valid');
                if (errorEl.length) errorEl.removeClass('d-none').show();
            } else {
                phoneInput.removeClass('is-invalid is-valid');
                if (errorEl.length) errorEl.addClass('d-none').hide();
            }
            return false;
        }

        if (bdPhoneRegex.test(cleanVal)) {
            phoneInput.removeClass('is-invalid').addClass('is-valid');
            if (errorEl.length) errorEl.addClass('d-none').hide();
            return true;
        } else {
            // If user has typed at least 3 digits or on blur/submit, show warning
            if (showErrorImmediately || cleanVal.length >= 3) {
                phoneInput.addClass('is-invalid').removeClass('is-valid');
                if (errorEl.length) errorEl.removeClass('d-none').show();
            }
            return false;
        }
    }

    $(document).on('input keyup', '#customer_phone_number', function () {
        validateCustomerPhone(false);
    });

    $(document).on('blur', '#customer_phone_number', function () {
        validateCustomerPhone(true);
    });

    // Block Form Submission if Phone or Address is Invalid
    $('form').has('#customer_address, #customer_phone_number').on('submit', function (e) {
        // 1. Phone validation
        const phoneInput = $('#customer_phone_number');
        if (phoneInput.length) {
            const isPhoneValid = validateCustomerPhone(true);
            if (!isPhoneValid) {
                e.preventDefault();
                e.stopImmediatePropagation();

                if (typeof toastr !== 'undefined') {
                    toastr.error('Please enter a valid 11-digit Bangladeshi mobile number (e.g. 017XXXXXXXX).');
                }

                $('html, body').animate({
                    scrollTop: phoneInput.offset().top - 120
                }, 300);

                phoneInput.focus();
                return false;
            }
        }

        // 2. Address validation
        const address = ($('#customer_address').val() || '').trim();
        const cityVal = $('#city_to_thana').val();
        const thanaVal = $('#thana_to_area').val();

        if (!address || address.length < 3 || !cityVal || !thanaVal) {
            e.preventDefault();
            e.stopImmediatePropagation();

            showInvalidBadge('Please enter a valid address with district and thana');

            if (typeof toastr !== 'undefined') {
                toastr.error('Please enter a valid address containing district and thana.');
            }

            $('html, body').animate({
                scrollTop: $('#customer_address').offset().top - 120
            }, 300);

            $('#customer_address').focus();
            return false;
        }
    });

    // Page Load check: if District and Thana are already selected (e.g. Edit Form)
    setTimeout(function() {
        const initialCity = $('#city_to_thana').val();
        const initialThana = $('#thana_to_area').val();
        const initialAddress = ($('#customer_address').val() || '').trim();

        if (initialCity && initialThana) {
            const distText = $('#city_to_thana option:selected').text().trim();
            const thanaText = $('#thana_to_area option:selected').text().trim();
            if (distText && thanaText && distText !== 'Select District' && thanaText !== 'Select Thana') {
                showValidBadge(distText, thanaText);
            }
        } else if (initialAddress.length >= 3) {
            detectLocationFromAddress();
        }

        // Check initial phone if present
        if ($('#customer_phone_number').val()) {
            validateCustomerPhone(false);
        }
    }, 200);

});
</script>
@endpush
