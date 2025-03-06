@extends('layouts.app')

@section('title', 'เพิ่มบริษัท')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มบริษัท</h1>

    <!-- แสดงข้อความ Error หากมี -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.companies.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="company_name" class="form-label">ชื่อบริษัท:</label>
            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="branch" class="form-label">รหัสสาขา:</label>
            <input type="number" name="branch" id="branch" class="form-control" value="{{ old('branch') }}" required>
        </div>
        
        <div class="mb-3">
            <label for="branch_description" class="form-label">รายละเอียดสาขา:</label>
            <input type="text" name="branch_description" id="branch_description" class="form-control" value="{{ old('branch_description') }}">
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">ที่อยู่:</label>
            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address') }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label">เบอร์โทร:</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="mobile" class="form-label">เบอร์มือถือ:</label>
                <input type="text" name="mobile" id="mobile" class="form-control" value="{{ old('mobile') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="fax" class="form-label">แฟกซ์:</label>
                <input type="text" name="fax" id="fax" class="form-control" value="{{ old('fax') }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="website" class="form-label">เว็บไซต์:</label>
            <input type="text" name="website" id="website" class="form-control" value="{{ old('website') }}">
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">โลโก้บริษัท (URL รูปภาพ):</label>
            <input type="text" name="logo" id="logo" class="form-control" value="{{ old('logo') }}">
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="twitter" class="form-label">Twitter:</label>
                <input type="text" name="twitter" id="twitter" class="form-control" value="{{ old('twitter') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="instagram" class="form-label">Instagram:</label>
                <input type="text" name="instagram" id="instagram" class="form-control" value="{{ old('instagram') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="linkedin" class="form-label">LinkedIn:</label>
                <input type="text" name="linkedin" id="linkedin" class="form-control" value="{{ old('linkedin') }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="youtube" class="form-label">YouTube:</label>
                <input type="text" name="youtube" id="youtube" class="form-control" value="{{ old('youtube') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="tiktok" class="form-label">TikTok:</label>
                <input type="text" name="tiktok" id="tiktok" class="form-control" value="{{ old('tiktok') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="facebook" class="form-label">Facebook:</label>
                <input type="text" name="facebook" id="facebook" class="form-control" value="{{ old('facebook') }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="line" class="form-label">Line:</label>
            <input type="text" name="line" id="line" class="form-control" value="{{ old('line') }}">
        </div>

        <div class="mb-3">
            <label for="tax_id" class="form-label">เลขประจำตัวผู้เสียภาษี:</label>
            <input type="text" name="tax_id" id="tax_id" class="form-control" maxlength="13" value="{{ old('tax_id') }}">
        </div>

        <div class="mb-3">
            <label for="contact_person" class="form-label">ชื่อผู้ติดต่อ:</label>
            <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person') }}">
        </div>

        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> บันทึก</button>
    </form>
</div>
@endsection