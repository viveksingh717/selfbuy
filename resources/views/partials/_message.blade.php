@if (session()->has('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> {{session()->get('error')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>    
@endif

@if (session()->has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> {{session()->get('success')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>    
@endif

@if (session()->has('alert'))
  <div class="alert alert-primary alert-dismissible fade show" role="alert">
    <strong>Alert!</strong> {{session()->get('alert')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>    
@endif

@if (session()->has('note'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Alert!</strong> {{session()->get('note')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>    
@endif

<div id="global_error" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
    <strong>Error!</strong>
    <span class="global_error_text"></span>

    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>