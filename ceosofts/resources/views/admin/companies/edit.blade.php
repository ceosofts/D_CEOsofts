@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลบริษัท')

@section('content')
<div class="container">
    <h1 class="mb-4">แก้ไขข้อมูลบริษัท</h1>

    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="company_name" class="form-label">ชื่อบริษัท:</label>
            <input type="text" name="company_name" class="form-control" value="{{ $company->company_name }}" required>
        </div>

        <div class="mb-3">
            <label for="branch" class="form-label">รหัสสาขา:</label>
            <input type="number" name="branch" class="form-control" value="{{ $company->branch }}" required>
        </div>
        
        <div class="mb-3">
            <label for="branch_description" class="form-label">รายละเอียดสาขา:</label>
            <input type="text" name="branch_description" class="form-control" value="{{ $company->branch_description }}">
        </div>

        

        <div class="mb-3">
            <label for="address" class="form-label">ที่อยู่:</label>
            <textarea name="address" class="form-control">{{ $company->address }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label">เบอร์โทร:</label>
                <input type="text" name="phone" class="form-control" value="{{ $company->phone }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="mobile" class="form-label">เบอร์มือถือ:</label>
                <input type="text" name="mobile" class="form-control" value="{{ $company->mobile }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="fax" class="form-label">แฟกซ์:</label>
                <input type="text" name="fax" class="form-control" value="{{ $company->fax }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" value="{{ $company->email }}">
        </div>

        <div class="mb-3">
            <label for="website" class="form-label">เว็บไซต์:</label>
            <input type="text" name="website" class="form-control" value="{{ $company->website }}">
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">โลโก้บริษัท (URL รูปภาพ):</label>
            <input type="text" name="logo" class="form-control" value="{{ $company->logo }}">
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="twitter" class="form-label">Twitter:</label>
                <input type="text" name="twitter" class="form-control" value="{{ $company->twitter }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="instagram" class="form-label">Instagram:</label>
                <input type="text" name="instagram" class="form-control" value="{{ $company->instagram }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="linkedin" class="form-label">LinkedIn:</label>
                <input type="text" name="linkedin" class="form-control" value="{{ $company->linkedin }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="youtube" class="form-label">YouTube:</label>
                <input type="text" name="youtube" class="form-control" value="{{ $company->youtube }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="tiktok" class="form-label">TikTok:</label>
                <input type="text" name="tiktok" class="form-control" value="{{ $company->tiktok }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="facebook" class="form-label">Facebook:</label>
                <input type="text" name="facebook" class="form-control" value="{{ $company->facebook }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="line" class="form-label">Line:</label>
            <input type="text" name="line" class="form-control" value="{{ $company->line }}">
        </div>

        <div class="mb-3">
            <label for="tax_id" class="form-label">เลขประจำตัวผู้เสียภาษี:</label>
            <input type="text" name="tax_id" class="form-control" value="{{ $company->tax_id }}" maxlength="13">
        </div>

        <div class="mb-3">
            <label for="contact_person" class="form-label">ชื่อผู้ติดต่อ:</label>
            <input type="text" name="contact_person" class="form-control" value="{{ $company->contact_person }}">
        </div>


        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> บันทึกการแก้ไข</button>
        {{-- <p>Action URL: {{ route('admin.companies.update', $company->id) }}</p> --}}

    </form>
</div>
@endsection
