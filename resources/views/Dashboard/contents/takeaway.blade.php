@extends('dashboard.app')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pesanan Take Away</h1>
                </div>
                <div class="col-sm-6">
                    {{--  --}}
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card card-outline card-primary">
                        <div class="card-header row d-flex justify-content-between">
                            <h3 class="card-title">Daftar Pesanan</h3>
                        </div>
                        <div class="card-body">
                            <div id="container" class="table-responsive">
                                <table class="table table-bordered table-hover" id="main_table">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Jenis</th>
                                            <th>Pesanan</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection