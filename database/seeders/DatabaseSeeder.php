<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pw = Hash::make('Password123!');
        $now = now();
        $school = 'sch_demo0001';
        $trial = now()->addDays(14);

        DB::table('subscription_plans')->upsert([
            ['id'=>'plan_free','name'=>'Free','code'=>'free','price_monthly'=>0,'currency'=>'NGN','max_students'=>50,'max_teachers'=>5,'storage_mb'=>500,'features'=>json_encode(['academics','attendance','announcements']),'is_custom'=>0,'is_active'=>1,'sort_order'=>0,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'plan_starter','name'=>'Starter','code'=>'starter','price_monthly'=>15000,'currency'=>'NGN','max_students'=>300,'max_teachers'=>20,'storage_mb'=>5000,'features'=>json_encode(['academics','attendance','results','fees','announcements','cbt']),'is_custom'=>0,'is_active'=>1,'sort_order'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'plan_professional','name'=>'Professional','code'=>'professional','price_monthly'=>35000,'currency'=>'NGN','max_students'=>1500,'max_teachers'=>100,'storage_mb'=>20000,'features'=>json_encode(['academics','attendance','results','fees','accounting','cbt','inventory','pos','reports']),'is_custom'=>0,'is_active'=>1,'sort_order'=>2,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'plan_enterprise','name'=>'Enterprise','code'=>'enterprise','price_monthly'=>0,'currency'=>'NGN','max_students'=>null,'max_teachers'=>null,'storage_mb'=>null,'features'=>json_encode(['all']),'is_custom'=>1,'is_active'=>1,'sort_order'=>3,'created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('schools')->upsert([[
            'id'=>$school,'name'=>'Greenfield Academy','short_name'=>'Greenfield','slug'=>'greenfield',
            'email'=>'info@greenfield.edu','phone'=>'+234 800 000 0000','address'=>'12 Palm Avenue, Lagos',
            'primary_color'=>'#16a34a','secondary_color'=>'#15803d','status'=>'active','created_at'=>$now,'updated_at'=>$now,
        ]], 'id');

        DB::table('subscriptions')->upsert([[
            'id'=>'sub_demo1','school_id'=>$school,'plan_id'=>'plan_professional','status'=>'active',
            'billing_cycle'=>'monthly','trial_ends_at'=>$trial,'current_period_end'=>$trial,'created_at'=>$now,'updated_at'=>$now,
        ]], 'id');

        DB::table('users')->upsert([
            ['id'=>'usr_super','school_id'=>null,'role'=>'super_admin','first_name'=>'Platform','last_name'=>'Owner','email'=>'super@sas.app','password'=>$pw,'is_active'=>1,'email_verified'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'usr_admin','school_id'=>$school,'role'=>'school_admin','first_name'=>'Grace','last_name'=>'Adeyemi','email'=>'admin@greenfield.edu','password'=>$pw,'is_active'=>1,'email_verified'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'usr_teacher','school_id'=>$school,'role'=>'teacher','first_name'=>'John','last_name'=>'Okafor','email'=>'teacher@greenfield.edu','password'=>$pw,'is_active'=>1,'email_verified'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'usr_student','school_id'=>$school,'role'=>'student','first_name'=>'Mary','last_name'=>'Bello','email'=>'student@greenfield.edu','password'=>$pw,'is_active'=>1,'email_verified'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'usr_sales','school_id'=>$school,'role'=>'sales_staff','first_name'=>'Peter','last_name'=>'Nwosu','email'=>'sales@greenfield.edu','password'=>$pw,'is_active'=>1,'email_verified'=>1,'created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('sessions_academic')->upsert([['id'=>'ses_2025','school_id'=>$school,'name'=>'2025/2026','is_current'=>1,'created_at'=>$now,'updated_at'=>$now]], 'id');
        DB::table('terms')->upsert([
            ['id'=>'trm_1','school_id'=>$school,'session_id'=>'ses_2025','name'=>'First Term','is_current'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'trm_2','school_id'=>$school,'session_id'=>'ses_2025','name'=>'Second Term','is_current'=>0,'created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('classes')->upsert([
            ['id'=>'cls_jss1','school_id'=>$school,'name'=>'JSS 1','level'=>'Junior','teacher_id'=>'tch_1','capacity'=>40,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'cls_jss2','school_id'=>$school,'name'=>'JSS 2','level'=>'Junior','teacher_id'=>null,'capacity'=>40,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'cls_ss1','school_id'=>$school,'name'=>'SS 1','level'=>'Senior','teacher_id'=>null,'capacity'=>40,'created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('subjects')->upsert([
            ['id'=>'subj_math','school_id'=>$school,'name'=>'Mathematics','code'=>'MTH','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'subj_eng','school_id'=>$school,'name'=>'English','code'=>'ENG','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'subj_sci','school_id'=>$school,'name'=>'Basic Science','code'=>'SCI','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'subj_soc','school_id'=>$school,'name'=>'Social Studies','code'=>'SOC','created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('students')->upsert([
            ['id'=>'std_1','school_id'=>$school,'user_id'=>'usr_student','admission_no'=>'GA/001','first_name'=>'Mary','last_name'=>'Bello','gender'=>'F','class_id'=>'cls_jss1','guardian_name'=>'Mr Bello','guardian_phone'=>'+234800111222','status'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'std_2','school_id'=>$school,'user_id'=>null,'admission_no'=>'GA/002','first_name'=>'Samuel','last_name'=>'Eze','gender'=>'M','class_id'=>'cls_jss1','guardian_name'=>'Mrs Eze','guardian_phone'=>'+234800111333','status'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'std_3','school_id'=>$school,'user_id'=>null,'admission_no'=>'GA/003','first_name'=>'Aisha','last_name'=>'Yusuf','gender'=>'F','class_id'=>'cls_jss2','guardian_name'=>'Mr Yusuf','guardian_phone'=>'+234800111444','status'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'std_4','school_id'=>$school,'user_id'=>null,'admission_no'=>'GA/004','first_name'=>'David','last_name'=>'Okon','gender'=>'M','class_id'=>'cls_ss1','guardian_name'=>'Mrs Okon','guardian_phone'=>'+234800111555','status'=>'active','created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('teachers')->upsert([
            ['id'=>'tch_1','school_id'=>$school,'user_id'=>'usr_teacher','first_name'=>'John','last_name'=>'Okafor','email'=>'teacher@greenfield.edu','subject'=>'Mathematics','employee_no'=>'EMP/001','status'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'tch_2','school_id'=>$school,'user_id'=>null,'first_name'=>'Ngozi','last_name'=>'Ibe','email'=>'ngozi@greenfield.edu','subject'=>'English','employee_no'=>'EMP/002','status'=>'active','created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('fee_structures')->upsert([
            ['id'=>'fee_tuition','school_id'=>$school,'name'=>'Tuition','category'=>'tuition','amount'=>50000,'term_id'=>'trm_1','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'fee_exam','school_id'=>$school,'name'=>'Examination','category'=>'examination','amount'=>5000,'term_id'=>'trm_1','created_at'=>$now,'updated_at'=>$now],
        ], 'id');
        DB::table('student_fees')->upsert([
            ['id'=>'sf_1','school_id'=>$school,'student_id'=>'std_1','fee_id'=>'fee_tuition','amount'=>50000,'amount_paid'=>30000,'status'=>'partial','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'sf_2','school_id'=>$school,'student_id'=>'std_1','fee_id'=>'fee_exam','amount'=>5000,'amount_paid'=>5000,'status'=>'paid','created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('products')->upsert([
            ['id'=>'prod_1','school_id'=>$school,'name'=>'Exercise Book','category'=>'Stationery','purchase_price'=>150,'selling_price'=>250,'stock_qty'=>200,'low_stock'=>10,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'prod_2','school_id'=>$school,'name'=>'School Uniform','category'=>'Uniform','purchase_price'=>3500,'selling_price'=>5000,'stock_qty'=>60,'low_stock'=>10,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'prod_3','school_id'=>$school,'name'=>'School Bag','category'=>'Accessories','purchase_price'=>2500,'selling_price'=>4000,'stock_qty'=>8,'low_stock'=>10,'created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('announcements')->upsert([
            ['id'=>'ann_1','school_id'=>$school,'title'=>'Resumption Date','body'=>'School resumes Monday, September 15th for the new term.','audience'=>'all','created_by'=>'usr_admin','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'ann_2','school_id'=>$school,'title'=>'PTA Meeting','body'=>'The termly PTA meeting holds this Saturday by 10am.','audience'=>'parents','created_by'=>'usr_admin','created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('transactions')->upsert([
            ['id'=>'txn_1','school_id'=>$school,'type'=>'income','category'=>'fees','description'=>'Opening fee collections','amount'=>450000,'date'=>now()->toDateString(),'created_at'=>$now,'updated_at'=>$now],
            ['id'=>'txn_2','school_id'=>$school,'type'=>'expense','category'=>'salaries','description'=>'Staff salaries','amount'=>180000,'date'=>now()->toDateString(),'created_at'=>$now,'updated_at'=>$now],
        ], 'id');

        DB::table('results')->upsert([
            ['id'=>'res_1','school_id'=>$school,'student_id'=>'std_1','subject_id'=>'subj_math','class_id'=>'cls_jss1','term_id'=>'trm_1','ca_score'=>28,'exam_score'=>55,'total_score'=>83,'grade'=>'A','remark'=>'Excellent','status'=>'approved','entered_by'=>'usr_teacher','created_at'=>$now,'updated_at'=>$now],
            ['id'=>'res_2','school_id'=>$school,'student_id'=>'std_1','subject_id'=>'subj_eng','class_id'=>'cls_jss1','term_id'=>'trm_1','ca_score'=>22,'exam_score'=>45,'total_score'=>67,'grade'=>'B','remark'=>'Very Good','status'=>'approved','entered_by'=>'usr_teacher','created_at'=>$now,'updated_at'=>$now],
        ], 'id');
    }
}
