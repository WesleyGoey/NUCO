@extends('layouts.mainlayout')

@section('title', 'Edit Profile')

@section('content')
<section style="position:relative; left:50%; right:50%; margin-left:-50vw; margin-right:-50vw; width:100vw; background: linear-gradient(180deg, #FFF8F0 0%, #F9F6F0 100%); min-height:100vh; padding:3.0rem 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card" style="border-radius:16px; overflow:hidden; border:1px solid rgba(164,130,59,0.06); box-shadow:0 12px 30px rgba(0,0,0,0.06);">
                    <div style="background: linear-gradient(90deg,#A4823B 0%, #D6BB7F 100%); padding:18px 20px;">
                        <h2 class="m-0 fw-bold text-center" style="color:#F5F0E5; font-size:1.25rem;">Edit Profile</h2>
                    </div>

                    <div class="p-4" style="background:transparent;">
                        {{-- include only the form partial; partial renders the "Profile Information" header/description --}}
                        @include('profile.partials.update-profile-information-form', ['user' => $user ?? auth()->user()])
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- modal change password (kept in this view for the partial) --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content" style="border-radius:10px; overflow:hidden; border:1px solid rgba(164,130,59,0.06);">
      <div class="modal-header" style="background:#A4823B;">
        <h5 class="modal-title" id="changePasswordModalLabel" style="color:#F5F0E5;">Change password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1)"></button>
      </div>
      <div class="modal-body" style="background:#fff;">
        @includeIf('profile.partials.update-password-form')
      </div>
    </div>
  </div>
</div>
@endsection
