@extends('layouts.mainlayout')

@section('title', 'Tables')

@section('content')
<div class="container-xl py-4">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-3">Select Table</h3>
            {{-- TODO: isi UI pemilihan meja di sini --}}
            {{-- contoh placeholder --}}
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action">Table 1</a>
                <a href="#" class="list-group-item list-group-item-action">Table 2</a>
                <a href="#" class="list-group-item list-group-item-action">Table 3</a>
            </div>
        </div>
    </div>
</div>
@endsection