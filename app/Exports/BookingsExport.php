<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BookingsExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly array $filters = []) {}

    public function collection(): Collection
    {
        return Booking::with(['customer', 'service', 'therapist', 'timeSlot'])
            ->when($this->filters['date_from'] ?? $this->filters['date'] ?? null, fn ($q, $date) => $q->whereDate('booking_date', '>=', $date))
            ->when($this->filters['date_to'] ?? null, fn ($q, $date) => $q->whereDate('booking_date', '<=', $date))
            ->when($this->filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($this->filters['service_id'] ?? null, fn ($q, $id) => $q->where('service_id', $id))
            ->when($this->filters['therapist_id'] ?? null, fn ($q, $id) => $q->where('therapist_id', $id))
            ->get()->map(fn (Booking $b) => [$b->booking_no, $b->booking_date?->format('Y-m-d'), $b->timeSlot?->label_th, $b->customer?->full_name, $b->customer?->phone, $b->service?->name_th, $b->therapist?->displayName, Booking::STATUSES[$b->status] ?? $b->status]);
    }

    public function headings(): array
    {
        return ['รหัสการจอง', 'วันที่', 'รอบเวลา', 'ผู้รับบริการ', 'เบอร์โทร', 'หัตถการ', 'ผู้ให้บริการ', 'สถานะ'];
    }
}
