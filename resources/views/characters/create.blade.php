@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-3">
{{--                inside card--}}
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Create Character</h5></div>
                    <div class="card-body">
                        <div class="panel panel-default">
                            <div class="panel-heading"></div>
                            <div class="panel-body">
                                <form action="{{ route('characters.store') }}" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="2" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="genre">Genre</label>
                                        <select name="genre" id="genre" class="form-control custom-select" data-placeholder="Select Genre" required>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="neutral">Neutral</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="rol_order">Rol Order</label>
                                        <select name="rol_order" id="rol_order" class="form-control custom-select" data-placeholder="Select the character rol order" required>
                                            <option value="">Select Rol Order</option>
                                            <option value="r1">Rol 1</option>
                                            <option value="r2">Rol 2</option>
                                            <option value="r3">Rol 3</option>
                                    <input type="submit" class="btn btn-outline-primary mt-3" value="Create"/>
                                </form>
                            </div>
                        </div>
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

