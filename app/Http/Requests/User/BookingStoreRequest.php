<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'required|exists:time_slots,id',
            'therapist_id' => 'nullable|exists:therapists,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => ['required', 'regex:/^(0[689]\d{8}|0[2-7]\d{7})$/', 'digits_between:9,10'],
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:500',
            'pdpa_accepted' => 'required|accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'กรุณาเลือกบริการที่ต้องการ',
            'service_id.exists' => 'ไม่พบบริการที่เลือก',
            'booking_date.required' => 'กรุณาเลือกวันที่ต้องการนัดหมาย',
            'booking_date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
            'booking_date.after_or_equal' => 'วันที่นัดหมายต้องไม่เป็นวันที่ผ่านมาแล้ว',
            'time_slot_id.required' => 'กรุณาเลือกรอบเวลาที่ต้องการ',
            'time_slot_id.exists' => 'ไม่พบรอบเวลาที่เลือก',
            'therapist_id.exists' => 'ไม่พบแพทย์ผู้ให้บริการที่เลือก',
            'first_name.required' => 'กรุณากรอกชื่อ',
            'first_name.max' => 'ชื่อมีความยาวเกิน 100 ตัวอักษร',
            'last_name.required' => 'กรุณากรอกนามสกุล',
            'last_name.max' => 'นามสกุลมีความยาวเกิน 100 ตัวอักษร',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'phone.regex' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง (เช่น 0812345678 หรือ 021234567)',
            'phone.digits_between' => 'เบอร์โทรศัพท์ต้องมีความยาว 9-10 หลัก',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.max' => 'อีเมลมีความยาวเกิน 255 ตัวอักษร',
            'notes.max' => 'หมายเหตุต้องมีความยาวไม่เกิน 500 ตัวอักษร',
            'pdpa_accepted.required' => 'กรุณายินยอมนโยบายความเป็นส่วนตัวก่อนดำเนินการ',
            'pdpa_accepted.accepted' => 'กรุณายินยอมนโยบายความเป็นส่วนตัวก่อนดำเนินการ',
        ];
    }
}
