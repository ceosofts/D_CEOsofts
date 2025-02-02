@extends('layouts.app')

@section('title', 'เพิ่มบริษัท')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มบริษัท</h1>

    <form action="{{ route('admin.companies.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="company_name" class="form-label">ชื่อบริษัท:</label>
            <input type="text" name="company_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">ที่อยู่:</label>
            <textarea name="address" class="form-control"></textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label">เบอร์โทร:</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="mobile" class="form-label">เบอร์มือถือ:</label>
                <input type="text" name="mobile" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="fax" class="form-label">แฟกซ์:</label>
                <input type="text" name="fax" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
            <label for="website" class="form-label">เว็บไซต์:</label>
            <input type="text" name="website" class="form-control">
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">โลโก้บริษัท (URL รูปภาพ):</label>
            <input type="text" name="logo" class="form-control">
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="twitter" class="form-label">Twitter:</label>
                <input type="text" name="twitter" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="instagram" class="form-label">Instagram:</label>
                <input type="text" name="instagram" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="linkedin" class="form-label">LinkedIn:</label>
                <input type="text" name="linkedin" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="youtube" class="form-label">YouTube:</label>
                <input type="text" name="youtube" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="tiktok" class="form-label">TikTok:</label>
                <input type="text" name="tiktok" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="facebook" class="form-label">Facebook:</label>
                <input type="text" name="facebook" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label for="line" class="form-label">Line:</label>
            <input type="text" name="line" class="form-control">
        </div>

        <div class="mb-3">
            <label for="tax_id" class="form-label">เลขประจำตัวผู้เสียภาษี:</label>
            <input type="text" name="tax_id" class="form-control" maxlength="13">
        </div>

        <div class="mb-3">
            <label for="contact_person" class="form-label">ชื่อผู้ติดต่อ:</label>
            <input type="text" name="contact_person" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> บันทึก</button>
    </form>
</div>
@endsection
