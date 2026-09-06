<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BangladeshLocationSeeder extends Seeder
{
    public function run()
    {
        $countryId = 19; // Bangladesh in countries table

        $divisionsData = [
            'Dhaka' => [
                'Dhaka' => ['Adabor', 'Badda', 'Banani', 'Banasree', 'Bangshal', 'Basabo', 'Bashundhara', 'Bimanbandar', 'Cantonment', 'Chowkbazar', 'Dakshinkhan', 'Darus Salam', 'Demra', 'Dhanmondi', 'Elephant Road', 'Farmgate', 'Gendaria', 'Gulshan', 'Hazaribagh', 'Jatrabari', 'Kadamtali', 'Kafrul', 'Kalabagan', 'Kamrangirchar', 'Khilgaon', 'Khilkhet', 'Kotwali', 'Lalbagh', 'Malibagh', 'Mirpur', 'Moghbazar', 'Mohakhali', 'Mohammadpur', 'Motijheel', 'New Market', 'Pallabi', 'Paltan', 'Panthapath', 'Ramna', 'Rampura', 'Sabujbagh', 'Shah Ali', 'Shahbagh', 'Shantinagar', 'Sher-e-Bangla Nagar', 'Shyampur', 'Sutrapur', 'Tejgaon', 'Tejgaon Industrial Area', 'Turag', 'Uttara', 'Uttara East', 'Uttara West', 'Uttar Khan', 'Vatara', 'Wari', 'Dhamrai', 'Dohar', 'Keraniganj', 'Nawabganj', 'Savar'],
                'Gazipur' => ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur', 'Tongi'],
                'Narayanganj' => ['Narayanganj Sadar', 'Araihazar', 'Bandar', 'Rupganj', 'Sonargaon', 'Fatullah', 'Siddhirganj'],
                'Tangail' => ['Tangail Sadar', 'Basail', 'Bhuapur', 'Delduar', 'Ghatail', 'Gopalpur', 'Kalihati', 'Madhupur', 'Mirzapur', 'Nagarpur', 'Sakhipur', 'Dhanbari'],
                'Faridpur' => ['Faridpur Sadar', 'Alfadanga', 'Bhanga', 'Boalmari', 'Charbhadrasan', 'Madhukhali', 'Nagarkanda', 'Sadarpur', 'Saltha'],
                'Gopalganj' => ['Gopalganj Sadar', 'Kashiani', 'Kotalipara', 'Muksudpur', 'Tungipara'],
                'Kishoreganj' => ['Kishoreganj Sadar', 'Austagram', 'Bajitpur', 'Bhairab', 'Hossainpur', 'Itna', 'Karimganj', 'Katiadi', 'Kuliarchar', 'Mithamain', 'Nikli', 'Pakundia', 'Tarail'],
                'Madaripur' => ['Madaripur Sadar', 'Kalkini', 'Rajoir', 'Shibchar', 'Dasar'],
                'Manikganj' => ['Manikganj Sadar', 'Daulatpur', 'Ghior', 'Harirampur', 'Saturia', 'Shivalaya', 'Singair'],
                'Munshiganj' => ['Munshiganj Sadar', 'Gazaria', 'Lohajang', 'Sirajdikhan', 'Sreenagar', 'Tongibari'],
                'Narsingdi' => ['Narsingdi Sadar', 'Belabo', 'Monohardi', 'Palash', 'Raipura', 'Shibpur'],
                'Rajbari' => ['Rajbari Sadar', 'Baliakandi', 'Goalandaghat', 'Pangsha', 'Kalukhali'],
                'Shariatpur' => ['Shariatpur Sadar', 'Bhedarganj', 'Damudya', 'Gosairhat', 'Naria', 'Zajira', 'Sakhipur'],
            ],
            'Chattogram' => [
                'Chattogram' => ['Kotwali', 'Panchlaish', 'Chandgaon', 'Double Mooring', 'Halishahar', 'Khulshi', 'Bakolia', 'Bayazid', 'Patenga', 'Pahartali', 'Bandar', 'Akbar Shah', 'Karnaphuli', 'Anwara', 'Banshkhali', 'Boalkhali', 'Chandanaish', 'Fatikchhari', 'Hathazari', 'Lohagara', 'Mirsharai', 'Patiya', 'Rangunia', 'Raozan', 'Sandwip', 'Satkania', 'Sitakunda'],
                'Cox\'s Bazar' => ['Cox\'s Bazar Sadar', 'Chakaria', 'Kutubdia', 'Maheshkhali', 'Ramu', 'Teknaf', 'Ukhia', 'Pekua', 'Eidgaon'],
                'Cumilla' => ['Cumilla Adarsha Sadar', 'Cumilla Sadar Dakshin', 'Barura', 'Brahmanpara', 'Burichang', 'Chandina', 'Chauddagram', 'Daudkandi', 'Debidwar', 'Homna', 'Laksam', 'Muradnagar', 'Nangalkot', 'Meghna', 'Titas', 'Monohargonj', 'Lalmai'],
                'Brahmanbaria' => ['Brahmanbaria Sadar', 'Ashuganj', 'Bancharampur', 'Bijoynagar', 'Kasba', 'Nabinagar', 'Nasirnagar', 'Sarail', 'Akhaura'],
                'Chandpur' => ['Chandpur Sadar', 'Faridganj', 'Haimchar', 'Haziganj', 'Kachua', 'Matlab Dakshin', 'Matlab Uttar', 'Shahrasti'],
                'Feni' => ['Feni Sadar', 'Chhagalnaiya', 'Daganbhuiyan', 'Parshuram', 'Fulgazi', 'Sonagazi'],
                'Lakshmipur' => ['Lakshmipur Sadar', 'Raipur', 'Ramganj', 'Ramgati', 'Kamalnagar'],
                'Noakhali' => ['Noakhali Sadar', 'Begumganj', 'Chatkhil', 'Companiganj', 'Hatiya', 'Senbagh', 'Sonaimuri', 'Subarnachar', 'Kabirhat'],
                'Bandarban' => ['Bandarban Sadar', 'Ali Kadam', 'Lama', 'Naikhongchhari', 'Rowangchhari', 'Ruma', 'Thanchi'],
                'Khagrachhari' => ['Khagrachhari Sadar', 'Dighinala', 'Lakshmichhari', 'Mahalchhari', 'Manikchhari', 'Matiranga', 'Panchhari', 'Ramgarh', 'Guimara'],
                'Rangamati' => ['Rangamati Sadar', 'Bagaichhari', 'Barkal', 'Belaichhari', 'Juraichhari', 'Kaptai', 'Kawkhali', 'Langadu', 'Naniarchar', 'Rajasthali'],
            ],
            'Rajshahi' => [
                'Rajshahi' => ['Boalia', 'Motihar', 'Rajpara', 'Shah Makhdum', 'Chandrima', 'Kasiadanga', 'Katakhali', 'Bagha', 'Bagmara', 'Charghat', 'Durgapur', 'Godagari', 'Mohanpur', 'Paba', 'Puthia', 'Tanore'],
                'Bogura' => ['Bogura Sadar', 'Adamdighi', 'Dhunat', 'Dhupchanchia', 'Gabtali', 'Kahaloo', 'Nandigram', 'Sariakandi', 'Sahajanpur', 'Sherpur', 'Shibganj', 'Sonatala'],
                'Joypurhat' => ['Joypurhat Sadar', 'Akkelpur', 'Kalai', 'Khetlal', 'Panchbibi'],
                'Naogaon' => ['Naogaon Sadar', 'Atrai', 'Badalgachhi', 'Dhamoirhat', 'Manda', 'Mohadevpur', 'Niamatpur', 'Patnitala', 'Porsha', 'Raninagar', 'Sapahar'],
                'Natore' => ['Natore Sadar', 'Bagatipara', 'Baraigram', 'Gurudaspur', 'Lalpur', 'Singra', 'Naldanga'],
                'Chapainawabganj' => ['Chapainawabganj Sadar', 'Bholahat', 'Gomastapur', 'Nachole', 'Shibganj'],
                'Pabna' => ['Pabna Sadar', 'Atgharia', 'Bera', 'Bhangura', 'Chatmohar', 'Faridpur', 'Ishwardi', 'Santhia', 'Sujanagar'],
                'Sirajganj' => ['Sirajganj Sadar', 'Belkuchi', 'Chauhali', 'Kamarkhanda', 'Kazipur', 'Raiganj', 'Shahjadpur', 'Tarash', 'Ullahpara'],
            ],
            'Khulna' => [
                'Khulna' => ['Khulna Sadar', 'Daulatpur', 'Khalishpur', 'Khan Jahan Ali', 'Kotwali', 'Sonadanga', 'Harintana', 'Aranghata', 'Batiaghata', 'Dacope', 'Dumuria', 'Dighalia', 'Koyra', 'Paikgachha', 'Phultala', 'Rupsha', 'Terokhada'],
                'Bagerhat' => ['Bagerhat Sadar', 'Chitalmari', 'Fakirhat', 'Kachua', 'Mollahat', 'Mongla', 'Morrelganj', 'Rampal', 'Sarankhola'],
                'Chuadanga' => ['Chuadanga Sadar', 'Alamdanga', 'Damurhuda', 'Jibannagar'],
                'Jashore' => ['Jashore Sadar', 'Abhaynagar', 'Bagherpara', 'Chaugachha', 'Jhikargachha', 'Keshabpur', 'Manirampur', 'Sharsha'],
                'Jhenaidah' => ['Jhenaidah Sadar', 'Harinakunda', 'Kaliganj', 'Kotchandpur', 'Maheshpur', 'Shailkupa'],
                'Kushtia' => ['Kushtia Sadar', 'Bheramara', 'Daulatpur', 'Khoksa', 'Kumarkhali', 'Mirpur'],
                'Magura' => ['Magura Sadar', 'Mohammadpur', 'Shalikha', 'Sreepur'],
                'Meherpur' => ['Meherpur Sadar', 'Gangni', 'Mujibnagar'],
                'Narail' => ['Narail Sadar', 'Kalia', 'Lohagara'],
                'Satkhira' => ['Satkhira Sadar', 'Assasuni', 'Debhata', 'Kalaroa', 'Kaliganj', 'Shyamnagar', 'Tala'],
            ],
            'Barishal' => [
                'Barishal' => ['Barishal Sadar', 'Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gaurnadi', 'Hizla', 'Mehendiganj', 'Muladi', 'Wazirpur'],
                'Barguna' => ['Barguna Sadar', 'Amtali', 'Bamna', 'Betagi', 'Patharghata', 'Taltali'],
                'Bhola' => ['Bhola Sadar', 'Burhanuddin', 'Char Fasson', 'Daulatkhan', 'Lalmohan', 'Manpura', 'Tazumuddin'],
                'Jhalokathi' => ['Jhalokathi Sadar', 'Kathalia', 'Nalchhiti', 'Rajapur'],
                'Patuakhali' => ['Patuakhali Sadar', 'Bauphal', 'Dashmina', 'Galachipa', 'Kalapara', 'Mirzaganj', 'Rangabali', 'Dumki'],
                'Pirojpur' => ['Pirojpur Sadar', 'Bhandaria', 'Kawkhali', 'Mathbaria', 'Nazirpur', 'Nesarabad', 'Indurkani'],
            ],
            'Sylhet' => [
                'Sylhet' => ['Sylhet Sadar', 'Balaganj', 'Beanibazar', 'Bishwanath', 'Companiganj', 'Dakshin Surma', 'Fenchuganj', 'Golapganj', 'Gowainghat', 'Jaintiapur', 'Kanaighat', 'Osmani Nagar', 'Zakiganj'],
                'Habiganj' => ['Habiganj Sadar', 'Ajmiriganj', 'Bahubal', 'Baniyachong', 'Chunarughat', 'Lakhai', 'Madhabpur', 'Nabiganj', 'Sayestaganj'],
                'Moulvibazar' => ['Moulvibazar Sadar', 'Barlekha', 'Juri', 'Kamalganj', 'Kulaura', 'Rajnagar', 'Sreemangal'],
                'Sunamganj' => ['Sunamganj Sadar', 'Bishwamvarpur', 'Chhatak', 'Derai', 'Dharampasha', 'Dowarabazar', 'Jagannathpur', 'Jamalganj', 'Shantiganj', 'Sullah', 'Tahirpur', 'Madhyanagar'],
            ],
            'Rangpur' => [
                'Rangpur' => ['Rangpur Sadar', 'Badarganj', 'Gangachhara', 'Kaunia', 'Mithapukur', 'Pirgachha', 'Pirganj', 'Taraganj'],
                'Dinajpur' => ['Dinajpur Sadar', 'Birampur', 'Birganj', 'Biral', 'Bochaganj', 'Chirirbandar', 'Fulbari', 'Ghoraghat', 'Hakimpur', 'Kaharole', 'Khansama', 'Nawabganj', 'Parbatipur'],
                'Gaibandha' => ['Gaibandha Sadar', 'Fulchhari', 'Gobindaganj', 'Palashbari', 'Sadullapur', 'Saghata', 'Sundarganj'],
                'Kurigram' => ['Kurigram Sadar', 'Bhurungamari', 'Char Rajibpur', 'Chilmari', 'Phulbari', 'Nageshwari', 'Rajarhat', 'Raomari', 'Ulipur'],
                'Lalmonirhat' => ['Lalmonirhat Sadar', 'Aditmari', 'Hatibandha', 'Kaliganj', 'Patgram'],
                'Nilphamari' => ['Nilphamari Sadar', 'Dimla', 'Domar', 'Jaldhaka', 'Kishoreganj', 'Saidpur'],
                'Panchagarh' => ['Panchagarh Sadar', 'Atwari', 'Boda', 'Debiganj', 'Tetulia'],
                'Thakurgaon' => ['Thakurgaon Sadar', 'Baliadangi', 'Haripur', 'Pirganj', 'Ranisankail', 'Ruhia'],
            ],
            'Mymensingh' => [
                'Mymensingh' => ['Mymensingh Sadar', 'Bhaluka', 'Dhobaura', 'Fulbaria', 'Gafargaon', 'Gauripur', 'Haluaghat', 'Ishwarganj', 'Muktagachha', 'Nandail', 'Phulpur', 'Trishal', 'Tara Khanda'],
                'Jamalpur' => ['Jamalpur Sadar', 'Bakshiganj', 'Dewanganj', 'Islampur', 'Madarganj', 'Melandaha', 'Sarishabari'],
                'Netrokona' => ['Netrokona Sadar', 'Atpara', 'Barhatta', 'Durgapur', 'Khaliajuri', 'Kalmakanda', 'Kendua', 'Madan', 'Mohanganj', 'Purbadhala'],
                'Sherpur' => ['Sherpur Sadar', 'Jhenaigati', 'Nakla', 'Nalitabari', 'Sreebardi'],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($divisionsData as $divisionName => $districts) {
                $division = DB::table('divisions')->where('name', $divisionName)->first();
                if (!$division) {
                    $divisionId = DB::table('divisions')->insertGetId([
                        'name'       => $divisionName,
                        'country_id' => $countryId,
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $divisionId = $division->id;
                }

                foreach ($districts as $districtName => $thanas) {
                    $district = DB::table('districts')->where('name', $districtName)->first();
                    if (!$district) {
                        $districtId = DB::table('districts')->insertGetId([
                            'name'        => $districtName,
                            'division_id' => $divisionId,
                            'status'      => 'active',
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    } else {
                        $districtId = $district->id;
                    }

                    foreach ($thanas as $thanaName) {
                        $exists = DB::table('thanas')
                            ->where('district_id', $districtId)
                            ->whereRaw('LOWER(name) = ?', [strtolower(trim($thanaName))])
                            ->exists();

                        if (!$exists) {
                            DB::table('thanas')->insert([
                                'name'        => $thanaName,
                                'district_id' => $districtId,
                                'status'      => 'active',
                                'created_at'  => now(),
                                'updated_at'  => now(),
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            $this->command->info('Bangladesh 8 divisions, 64 districts and all thanas seeded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeding failed: ' . $e->getMessage());
        }
    }
}
