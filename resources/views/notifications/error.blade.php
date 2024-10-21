@if ($errors->any())
    <ul class="alert alert-danger alert-dismissible fade show" role="alert">
        @foreach($errors->all() as $error)
            <li style="margin-left: 15px">{{ $error }}</li>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </ul>
@endif


