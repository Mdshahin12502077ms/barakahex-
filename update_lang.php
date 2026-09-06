<?php
$enPath = __DIR__ . '/lang/en.json';
$bnPath = __DIR__ . '/lang/bn.json';

$enData = json_decode(file_get_contents($enPath), true);
$enAdditions = [
    'bags' => 'Bags',
    'bag' => 'Bag',
    'add_bag' => 'Add Bag',
    'edit_bag' => 'Edit Bag',
    'bag_no' => 'Bag No',
    'total_parcels' => 'Total Parcels',
    'max_capacity' => 'Max Capacity',
    'bag_capacity_full' => 'Bag capacity is full!',
    'bag_created_successfully' => 'Bag created successfully',
    'bag_updated_successfully' => 'Bag updated successfully',
    'bag_deleted_successfully' => 'Bag deleted successfully',
    'bag_must_be_open' => 'Bag must be open',
    'parcel_already_in_another_active_bag' => 'Parcel is already in another active bag',
    'close_bag' => 'Close Bag',
    'dispatch_bag' => 'Dispatch Bag',
    'receive_bag' => 'Receive Bag',
    'manifest' => 'Manifest',
    'print_manifest' => 'Print Manifest',
    'parcels_in_bag' => 'Parcels in Bag',
    'bag_details' => 'Bag Details',
    'bag_info' => 'Bag Information',
    'scan_barcode/enter_percel_no' => 'Scan Barcode / Enter Parcel No',
    'scan_here' => 'Scan Here...',
    'no_parcels_found' => 'No parcels found',
    'dispatched_by' => 'Dispatched By',
    'received_by' => 'Received By'
];
$enData = array_merge($enData, $enAdditions);
file_put_contents($enPath, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$bnData = json_decode(file_get_contents($bnPath), true);
$bnAdditions = [
    'bags' => 'ব্যাগসমূহ',
    'bag' => 'ব্যাগ',
    'add_bag' => 'নতুন ব্যাগ',
    'edit_bag' => 'ব্যাগ এডিট',
    'bag_no' => 'ব্যাগ নং',
    'total_parcels' => 'মোট পার্সেল',
    'max_capacity' => 'সর্বোচ্চ পার্সেল ধারণক্ষমতা',
    'bag_capacity_full' => 'ব্যাগে আর পার্সেল ধরবে না!',
    'bag_created_successfully' => 'ব্যাগ সফলভাবে তৈরি হয়েছে',
    'bag_updated_successfully' => 'ব্যাগ আপডেট হয়েছে',
    'bag_deleted_successfully' => 'ব্যাগ ডিলিট হয়েছে',
    'bag_must_be_open' => 'ব্যাগ ওপেন অবস্থায় থাকতে হবে',
    'parcel_already_in_another_active_bag' => 'পার্সেলটি ইতিমধ্যে অন্য একটি একটিভ ব্যাগে আছে',
    'close_bag' => 'ব্যাগ ক্লোজ করুন',
    'dispatch_bag' => 'ব্যাগ ডিসপ্যাচ করুন',
    'receive_bag' => 'ব্যাগ রিসিভ করুন',
    'manifest' => 'ম্যানিফেস্ট',
    'print_manifest' => 'ম্যানিফেস্ট প্রিন্ট',
    'parcels_in_bag' => 'ব্যাগের ভেতরের পার্সেল',
    'bag_details' => 'ব্যাগের বিস্তারিত',
    'bag_info' => 'ব্যাগের তথ্য',
    'scan_barcode/enter_percel_no' => 'বারকোড স্ক্যান করুন / পার্সেল নাম্বার দিন',
    'scan_here' => 'এখানে স্ক্যান করুন...',
    'no_parcels_found' => 'কোনো পার্সেল পাওয়া যায়নি',
    'dispatched_by' => 'ডিসপ্যাচ করেছেন',
    'received_by' => 'রিসিভ করেছেন'
];
$bnData = array_merge($bnData, $bnAdditions);
file_put_contents($bnPath, json_encode($bnData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Language files updated successfully!";
