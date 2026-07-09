<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class AppModel extends Model
{
   //protected $connection = 'sqlsrv2' ;    
   //protected $guarded = [];

   public static function get_month_name($value)
   {
      switch ($value) {
         case 1:
            return "Jan";
            break;
         case 2:
            return "Feb";
            break;
         case 3:
            return "Mar";
            break;
         case 4:
            return "Apr";
            break;
         case 5:
            return "May";
            break;
         case 6:
            return "Jun";
            break;
         case 7:
            return "Jul";
            break;
         case 8:
            return "Aug";
            break;
         case 9:
            return "Sep";
            break;
         case 10:
            return "Oct";
            break;
         case 11:
            return "Nov";
            break;
         case 12:
            return "Dec";
            break;
      }
   }

   public static function local_date_formate($value)
   {
      $exp = explode('-', $value);
      if (count($exp) == 3) {
         $result = $exp[2] . '/' . $exp[1] . '/' . $exp[0];
      } else {
         $result = NULL;
      }
      return $result;
   }

   public static function convert_sql_date_formate($value)
   {
      $exp = explode('-', $value);
      if (count($exp) == 3) {
         $result = $exp[2] . '-' . $exp[1] . '-' . $exp[0];
      }
      return $result;
   }

   public static function local_date_formate_name($value)
   {
      $time = substr($value, 11, 5);
      $value = substr($value, 0, 10);
      $day = substr($value, 8, 2);
      $month = self::get_month_name(substr($value, 5, 2));
      $year = substr($value, 0, 4);
      return $day . ' ' . $month . ' ' . $year . ' ' . $time;
   }


   public static function find_company_profile($key)
   {
      $t = DB::table('t100_company_profiles')->where('status_id', '=', 1)->get();
      if ($t->count() > 0) {
         foreach ($t as $h) {
            $result = $h->company_code . '^' . $h->company_name . '^' . $h->company_address . '^' . $h->company_city . '^' . $h->company_province . '^' . $h->company_state . '^' . $h->company_postal_code . '^' . $h->company_phone . '^' . $h->company_phone . '^' . $h->company_website . '^' . $h->company_email . '^' . $h->company_logo . '^' . $h->company_logo_draft;
         }
      } else {
         $result = '0^1^2^3^4^5^6^7^8^9^10^11^12';
      }

      $company_profile_array = explode("^", $result);
      $company_profile = '';
      if ($key == 'code') {
         $company_profile = $company_profile_array[0];
      }
      if ($key == 'name') {
         $company_profile = $company_profile_array[1];
      }
      if ($key == 'address') {
         $company_profile = $company_profile_array[2];
      }
      if ($key == 'city') {
         $company_profile = $company_profile_array[3];
      }
      if ($key == 'province') {
         $company_profile = $company_profile_array[4];
      }
      if ($key == 'state') {
         $company_profile = $company_profile_array[5];
      }
      if ($key == 'postal_code') {
         $company_profile = $company_profile_array[6];
      }
      if ($key == 'phone') {
         $company_profile = $company_profile_array[7];
      }
      if ($key == 'fax') {
         $company_profile = $company_profile_array[8];
      }
      if ($key == 'website') {
         $company_profile = $company_profile_array[9];
      }
      if ($key == 'email') {
         $company_profile = $company_profile_array[10];
      }
      if ($key == 'logo') {
         $company_profile = $company_profile_array[11];
      }
      if ($key == 'logodraft') {
         $company_profile = $company_profile_array[12];
      }
      if ($key == 'all') {
         $company_profile = $result;
      }
      return $company_profile;
   }

   public static function find_doc_reg_num($key)
   {
      $t = DB::table('t100_transaction_type')->where('trc_type_id', '=', $key)->select('t100_transaction_type.*')->get();
      if ($t->count() > 0) {
         foreach ($t as $h) {
            $result = $h->reg_num;
         }
      } else {
         $result = '';
      }
      return $result;
   }

   public static function find_doc_sign($key)
   {
      $t = DB::table('users')->where('id', '=', $key)->select('users.*')->get();
      if ($t->count() > 0) {
         foreach ($t as $h) {
            $result = $h->signature;
         }
      } else {
         $result = '';
      }
      return $result;
   }

   public static function post_date_formate($value)
   {
      $exp = explode('/', $value);
      if (count($exp) == 3) {
         $result = $exp[2] . '-' . $exp[1] . '-' . $exp[0];
      } else {
         $result = NULL;
      }
      return $result;
   }
}
