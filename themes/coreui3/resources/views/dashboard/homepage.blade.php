@extends('dashboard.base')

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            {{ __('Users') }}
                            <div class="card-header-actions"><a class=" btn btn-primary " href="#"
                                >Novo</a></div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-outline">
                                <thead class="thead-light">
                                <tr>
                                    <th>Username</th>
                                    <th>E-mail</th>
                                    <th>Roles</th>
                                    <th>Email verified at</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(\App\Models\User::all() as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->menuroles }}</td>
                                        <td>{{ $user->email_verified_at }}</td>
                                        <td>
                                            <a href="{{ url('/users/' . $user->id) }}"
                                               class="btn btn-block btn-primary">View</a>
                                        </td>
                                        <td>
                                            <a href="{{ url('/users/' . $user->id . '/edit') }}"
                                               class="btn btn-block btn-primary">Edit</a>
                                        </td>
                                        <td>
                                            @if( auth()->user()->id !== $user->id )
                                                <form action="" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button class="btn btn-block btn-danger">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('javascript')

    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/coreui-chartjs.bundle.js') }}"></script>
    <script src="{{ asset('js/main.js') }}" defer></script>
@endsection
