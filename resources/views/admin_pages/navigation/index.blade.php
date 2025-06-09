@extends('layouts.master')
@section('title', 'Navigation Builder')
@section('content')
<div class="container-fluid py-2">
    <div class="row">
      <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
        <p class="mb-4">
          Building main navigation for the admin panel is easy with our navigation builder. You can add, edit, and delete navigation items, and they will be reflected in the sidebar automatically.
        </p>
      </div>
    </div>
@if ($errors->any())

    <div class="alert alert-danger">

        <ul>

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif
    @if (session('success'))
 
        <div class="alert alert-success alert-dismissible text-white" role="alert">
                <span class="text-sm"> {{ session('success') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
    @endif
     <div class="row mt-5 mb-5">
      <div class="col-lg-12  mb-4">
   

          <form action="{{route('navigation.post')}}" method="POST">
            @csrf
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                  <h6>Navigation Builder</h6>

                  <div class="input-group input-group-outline my-3">
                    <label class="form-label">Navigation Name</label>
                    <input type="text" name="name" class="form-control"  required>
                  </div>
                  <div class="input-group input-group-outline my-3">
                    <label class="form-label">Navigation Slug</label>
                    <input type="text" name="slug" class="form-control"    >
                  </div>
                 <div class="input-group">
                  <button type="submit" class="btn btn-success">Save</button>
                  <button type="submit" class="btn btn-default"><span class="material-symbols-outlined">
add
</span> Create sub menu</button>
                 </div>
          </form>
         
      </div>
      <div class="card">
        <form action="{{route('navigation/submenu/create')}}" method="POST">
                 @csrf
                     <div class="input-group input-group-outline my-3">
                      <select name="navigation_id" id="" class="form-control" >
                          <option value="" >Select Parent Navigation</option>
                          @foreach ($navs as $item)
                              <option value="{{$item->id}}">{{$item->name}}</option>
                          @endforeach
                      </select>
                  </div>
                     <div class="input-group input-group-outline my-3">
                      <label class="form-label">Navigation Slug</label>
                    <input type="text" name="sub_name" class="form-control"    >
                  </div>
          <div class="input-group">
            <button type="submit" class="btn btn-info">Save</button>
          </div>

        </form>
      </div>
    </div>   

      <div class="row">
        <div class="col-md-12 mt-4">
          <div class="card">
            <div class="card-header pb-0 px-3">
              <h6 class="mb-0">Navigation</h6>
            </div>
            <div class="card-body pt-4 p-3">
               <div id="nav">
                
               </div>
            </div>
          </div>
        </div>
 
      </div>


 
 
  </div>
@endsection
<script>
 
      window.addEventListener('DOMContentLoaded', () => {
            if (window.getMenu) {
                window.getMenu();
            } else {
                console.error("getMenu function not found on window.");
            }
        });
</script>

 