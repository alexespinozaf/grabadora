@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Characters</h3>
                            <a href="{{ route('characters.create') }}" class="btn btn-outline-primary">Create Character</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="characters-table"  class="table table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Rol Order</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($characters as $character)
                                <tr>
                                    <td>{{ $character->name }}</td>
                                    <td>{{ $character->description }}</td>
                                    <td>{{ $character->rol_order }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('characters.edit', $character->id) }}" class="btn btn-outline-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" style="border-top-right-radius: 5px!important; border-bottom-right-radius: 5px!important;">
                                                <i class="fa fa-edit  me-2"></i> Edit
                                            </a>
                                            <form action="{{ route('characters.destroy', $character->id) }}" method="POST" style="display: inline;">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-outline-danger"  data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                    <i class="fa fa-trash  me-2"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
{{--                        <a href="{{ route('characters.create') }}" class="btn btn-success">Create Character</a>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $("body").tooltip({ selector: '[data-toggle=tooltip]' });
            $('#characters-table').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }
            });
        });
    </script>
@endsection
