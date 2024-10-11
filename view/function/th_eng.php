<?php

// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th';

$translations = [
    'th' => [
        'th_language' => 'ภาษาไทย',
        'en_language' => 'TH Language',
        'login' => 'เข้าสู่ระบบ',
        'register' => 'ลงทะเบียน',
        'question' => 'คำถามที่พบบ่อย',
        'contact' => 'ติดต่อเรา',
        'profile' => 'ดูโปรไฟล์',
        'logout' => 'ออกจากระบบ',
        'track' => 'กรอกหมายเลขติดตามของคุณที่นี่!',
        'ex' => 'ตัวอย่าง',
        'email' => 'อีเมลล์',
        'password' => 'รหัสผ่าน',
        'signup' => 'สมัครสมาชิก',
        'forgotpassword' => 'ลืมรหัสผ่าน',
        'donthaveaccount' => 'หากยังไม่มีรหัสผ่านกด-->',
        'fristname' => 'ชื่อ',
        'lastname' => 'นามสกุล',
        'address' => 'ที่อยู่',
        'addressph' => 'ที่อยู่ (ไม่ต้องระบุตำบล อำเภอ จังหวัด)',
        'tel' => 'เบอร์โทร',
        'telph' => '(0XXXXXXXXX) ไม่ต้องใส่ขีด',
        'provinces' => 'จังหวัด',
        'provincesph' => 'โปรดเลือกจังหวัด',
        'amphures' => 'อำเภอ',
        'amphuresph' => 'โปรดเลือกอำเภอ',
        'districts' => 'ตำบล',
        'districtsph' => 'โปรดเลือกตำบล',
        'zipcode' => 'รหัสไปรษณีย์',
        'confirmPassword' => 'ยืนยันรหัสผ่าน',
        'haveaccount' => 'มีสมาชิกแล้วกด?? -->',
        'facebook' => 'เฟสบุ๊ค',
        'website' => 'เว็บไซต์',
        'call' => 'โทร',
        'askquestion' => 'ถามคำถาม หรือแจ้งปัญหาในการใช้งาน',
        'submit' => 'ยืนยัน',
        'yourname' => 'ชื่อผู้ส่ง',
        'youremail' => 'ใส่อีเมล์',
        'description' => 'ใส่รายละเอียด',
        'freq' => 'คำถามที่พบบ่อย',
        'fullname' => 'ชื่อ-นามสกุล',
        'mobile' => 'เบอร์โทรศัพท์',
        'success' =>'สำเร็จ',
        'notsuccess' =>'ไม่สำเร็จ',

    ],
    'en' => [
        'th_language' => 'TH Language',
        'en_language' => 'ENG Language',
        'login' => 'Login',
        'register' => 'Register',
        'question' => 'Frequency Asked Questions',
        'contact' => 'Contact Us',
        'profile' => 'View Profile',
        'logout' => 'Logout',
        'track' => 'Enter your tracking number here!',
        'ex' => 'Ex.',
        'email' => 'Email',
        'password' => 'Password',
        'signup' => 'Sign up',
        'forgotpassword' => 'Forgot password?',
        'donthaveaccount' => 'Don\'t have an account?',
        'fristname' => 'Firstname',
        'lastname' => 'Lastname',
        'address' => 'Address',
        'addressph' => 'Address (Do not specify sub-district, district, province)',
        'tel' => 'Telephone Number',
        'telph' => '(0XXXXXXXXX) Do not use hyphens',
        'provinces' => 'Province',
        'provincesph' => 'Please choose Province',
        'amphures' => 'District',
        'amphuresph' => 'Please choose District',
        'districts' => 'Subdistrict',
        'districtsph' => 'Please choose Subdistrict',
        'zipcode' => 'Zip Code',
        'confirmPassword' => 'Confirm Password',
        'haveaccount' => 'Already have an account?',
        'facebook' => 'Facebook',
        'website' => 'Website',
        'call' => 'Call',
        'askquestion' => 'Ask a Question',
        'submit' => 'Submit',
        'yourname' => 'Yourname',
        'youremail' => 'YourEmail',
        'description' => 'Description',
        'freq' => 'Frequently Questions',
        'fullname' => 'Fullname',
        'mobile' => 'Mobile',
        'success' =>'success',
        
    ]
];

$lang_th_language = $translations[$lang]['th_language'];
$lang_en_language = $translations[$lang]['en_language'];
$lang_login = $translations[$lang]['login'];
$lang_register = $translations[$lang]['register'];
$lang_question = $translations[$lang]['question'];
$lang_contact = $translations[$lang]['contact'];
$lang_profile = $translations[$lang]['profile'];
$lang_logout = $translations[$lang]['logout'];
$lang_track = $translations[$lang]['track'];
$lang_ex = $translations[$lang]['ex'];
$lang_email = $translations[$lang]['email'];
$lang_password = $translations[$lang]['password'];
$lang_signup = $translations[$lang]['signup'];
$lang_forgotpassword = $translations[$lang]['forgotpassword'];
$lang_donthaveaccount = $translations[$lang]['donthaveaccount'];
$lang_fristname = $translations[$lang]['fristname'];
$lang_lastname = $translations[$lang]['lastname'];
$lang_address = $translations[$lang]['address'];
$lang_addressph = $translations[$lang]['addressph'];
$lang_tel = $translations[$lang]['tel'];
$lang_telph = $translations[$lang]['telph'];
$lang_provinces = $translations[$lang]['provinces'];
$lang_provincesph = $translations[$lang]['provincesph'];
$lang_amphures = $translations[$lang]['amphures'];
$lang_amphuresph = $translations[$lang]['amphuresph'];
$lang_districts = $translations[$lang]['districts'];
$lang_districtsph = $translations[$lang]['districtsph'];
$lang_zipcode = $translations[$lang]['zipcode'];
$lang_confirmPassword = $translations[$lang]['confirmPassword'];
$lang_haveaccount = $translations[$lang]['haveaccount'];
$lang_contact = $translations[$lang]['contact'];
$lang_facebook = $translations[$lang]['facebook'];
$lang_website = $translations[$lang]['website'];
$lang_call = $translations[$lang]['call'];
$lang_askquestion = $translations[$lang]['askquestion'];
$lang_submit = $translations[$lang]['submit'];
$lang_yourname = $translations[$lang]['yourname'];
$lang_youremail = $translations[$lang]['youremail'];
$lang_description = $translations[$lang]['description'];
$lang_freq = $translations[$lang]['freq'];
$lang_fullname = $translations[$lang]['fullname'];
$lang_mobile = $translations[$lang]['mobile'];
$lang_success = $translations[$lang]['success'];


?>
