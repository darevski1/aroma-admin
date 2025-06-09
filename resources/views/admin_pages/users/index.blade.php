@extends('layouts.master')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
      <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
        <p class="mb-4">
          Check the sales, value and bounce rate by country.
        </p>
      </div>
 
    </div>
 
    <div class="row mb-4 mt-5">
      <div class="col-lg-12  mb-4">
        <div class="card">
          <div class="card-header pb-0">
        
          </div>
          <div class="card-body px-0 pb-2">
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Username</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Edit</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Edit</th>
                  </tr>
                </thead>
                <tbody id="usersTable">
 
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
 
    </div>
 
  </div>
@endsection

<script>
        window.addEventListener('DOMContentLoaded', () => {
            if (window.getUsers) {
                window.getUsers();
            } else {
                console.error("getUsers function not found on window.");
            }
        })
</script>