@extends('layouts.app')

@section('title', 'Quotation List')

@section('content')
<div class="container">
    <h1>Quotation List</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('quotations.create') }}" class="btn btn-primary mb-3">Create Quotation</a>

    <!-- ใส่ .table-responsive เพื่อให้เลื่อนดูตารางในมือถือได้สะดวก -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Quotation No.</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Status</th>  <!-- เพิ่มคอลัมน์ Status -->
                    <th class="text-end">Total (THB)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $q)
                    <tr>
                        <td>{{ $q->quotation_number }}</td>
                        <!-- หากต้องการปรับรูปแบบวันที่ (กรณี quotation_date เป็น date/datetime) -->
                        <td>{{ \Carbon\Carbon::parse($q->quotation_date)->format('Y-m-d') }}</td>

                        <td>
                            {{ $q->customer_company }}
                            @if(!empty($q->customer_contact_name))
                                <br><small>{{ $q->customer_contact_name }}</small>
                            @endif
                        </td>

                        <td>
                            @if($q->status)
                                <span class="badge" style="background-color: {{ $q->status->color }}">
                                    {{ $q->status->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary">No Status</span>
                            @endif
                        </td>

                        <!-- จัดตัวเลขรวมชิดขวาเพื่อให้อ่านง่าย -->
                        <td class="text-end">{{ number_format($q->total_amount, 2) }}</td>

                        <td>
                            <a href="{{ route('quotations.show', $q->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('quotations.edit', $q->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('quotations.destroy', $q->id) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                            @if($q->status && $q->status->name === 'ลูกค้าอนุมัติใบเสนอราคา')
                                <a href="{{ route('quotations.create-invoice', $q) }}" 
                                   class="btn btn-success">
                                    <i class="bi bi-file-earmark-text"></i> Create Invoice
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- แสดง pagination -->
    {{ $quotations->links() }}
</div>
@endsection