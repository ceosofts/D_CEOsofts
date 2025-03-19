@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">
                        <i class="fas fa-building mr-2"></i> รายละเอียดบริษัท
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-edit"></i> แก้ไข
                        </a>
                        <a href="{{ route('companies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <div class="border p-3">
                                @if($company->logo)
                                    <img src="{{ $company->logoUrl }}" class="img-fluid mb-3" alt="Company Logo" style="max-height: 150px;">
                                @else
                                    <div class="p-5 bg-light mb-3">
                                        <i class="fas fa-building fa-5x text-secondary"></i>
                                    </div>
                                @endif
                                <h4>{{ $company->company_name }}</h4>
                                @if($company->branch)
                                    <p class="text-muted">สาขาที่ {{ $company->branch }} {{ $company->branch_description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%"><i class="fas fa-map-marker-alt"></i> ที่อยู่</th>
                                            <td>{{ $company->fullAddress }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone"></i> โทรศัพท์</th>
                                            <td>{{ $company->phone ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-mobile-alt"></i> มือถือ</th>
                                            <td>{{ $company->mobile ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-fax"></i> โทรสาร</th>
                                            <td>{{ $company->fax ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-envelope"></i> อีเมล</th>
                                            <td>{{ $company->email ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-globe"></i> เว็บไซต์</th>
                                            <td>
                                                @if($company->website)
                                                    <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-id-card"></i> เลขประจำตัวผู้เสียภาษี</th>
                                            <td>{{ $company->tax_id ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-user"></i> บุคคลที่ติดต่อ</th>
                                            <td>{{ $company->contact_person ?: '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($company->hasSocialMedia())
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-share-alt"></i> ช่องทางโซเชียลมีเดีย</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap">
                                            @if($company->facebook)
                                                <a href="{{ $company->facebook }}" target="_blank" class="btn btn-primary mx-1 mb-2">
                                                    <i class="fab fa-facebook-f"></i> Facebook
                                                </a>
                                            @endif
                                            @if($company->twitter)
                                                <a href="{{ $company->twitter }}" target="_blank" class="btn btn-info mx-1 mb-2">
                                                    <i class="fab fa-twitter"></i> Twitter
                                                </a>
                                            @endif
                                            @if($company->instagram)
                                                <a href="{{ $company->instagram }}" target="_blank" class="btn btn-danger mx-1 mb-2">
                                                    <i class="fab fa-instagram"></i> Instagram
                                                </a>
                                            @endif
                                            @if($company->linkedin)
                                                <a href="{{ $company->linkedin }}" target="_blank" class="btn btn-primary mx-1 mb-2">
                                                    <i class="fab fa-linkedin"></i> LinkedIn
                                                </a>
                                            @endif
                                            @if($company->youtube)
                                                <a href="{{ $company->youtube }}" target="_blank" class="btn btn-danger mx-1 mb-2">
                                                    <i class="fab fa-youtube"></i> YouTube
                                                </a>
                                            @endif
                                            @if($company->tiktok)
                                                <a href="{{ $company->tiktok }}" target="_blank" class="btn btn-dark mx-1 mb-2">
                                                    <i class="fab fa-tiktok"></i> TikTok
                                                </a>
                                            @endif
                                            @if($company->line)
                                                <a href="{{ $company->line }}" target="_blank" class="btn btn-success mx-1 mb-2">
                                                    <i class="fab fa-line"></i> Line
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
