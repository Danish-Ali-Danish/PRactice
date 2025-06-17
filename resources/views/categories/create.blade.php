@extends('layout.app')
@section('content')
<div class="container">
    <h2>Create category</h2>
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
       
        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection
