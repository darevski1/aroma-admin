@extends('layouts.master')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
      <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Dashboard - create new campaign</h3>
        <p class="mb-4">
          Create new campaign.
        </p>
      </div>
      @if(session('success'))
          <div class="alert alert-success">
              {{ session('success') }}
          </div>
      @endif
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
          <div class="card-body">
            <form role="form" class="text-start" method="POST" action="{{ route('campaign.store') }}">
              @csrf
              <div class="input-group input-group-outline my-3">
                <label class="form-label">Campaign Name</label>
                <input type="text" class="form-control" name="campaign_name"  required>
              </div>
              <div class="text-center">
                <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Create</button>
              </div>
     
            </form>
          </div>
        </div>
      </div>
 
    </div>
 
 
  </div>
@endsection