@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-3">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Edit Character</h5></div>
                    <div class="card-body">
                        <form action="{{ route('characters.update', $character->id) }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $character->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" cols="30" rows="2" class="form-control" required>{{ $character->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="genre">Genre</label>
                                <select name="genre" id="genre" class="form-control custom-select" data-placeholder="Select Genre" required>
                                    <option value="male" {{ $character->genre == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $character->genre == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="neutral" {{ $character->genre == 'neutral' ? 'selected' : '' }}>Neutral</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rol_order">Rol Order</label>
                                <select name="rol_order" id="rol_order" class="form-control custom-select" data-placeholder="Select the character rol order" required>
                                    <option value="r1" {{ $character->rol_order == 'r1' ? 'selected' : '' }}>Rol 1</option>
                                    <option value="r2" {{ $character->rol_order == 'r2' ? 'selected' : '' }}>Rol 2</option>
                                    <option value="r3" {{ $character->rol_order == 'r3' ? 'selected' : '' }}>Rol 3</option>
                                </select>
                            </div>
                            <input type="submit" class="btn btn-outline-primary mt-3" value="Edit"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>

        $(document).ready(function(){

            $( '.custom-select' ).select2( {
                placeholder: $( this ).data( 'placeholder' ),
                closeOnSelect: false,
            } );

        });
    </script>
@endsection

