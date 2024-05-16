@extends('dashboard.app')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Rewards</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-8">
                        <!-- Default box -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Rewards</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" id="dt-container">
                                    <table class="table table-bordered table-striped" id="dt-data">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Menu</th>
                                                <th>Poin</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Reward</h3>
                            </div>
                            <div class="card-body">
                                <form action="" method="post"
                                id="form-add-menu">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="menu">Menu :</label>
                                        <input type="text" name="menu" id="menu" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="point">Poin :</label>
                                        <input type="number" name="point" id="point" class="form-control">
                                    </div>
                                    <button id="submitAdd" type="submit" class="btn btn-success"><i
                                            class="fas fa-plus mr-1"></i>Tambah Reward</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Reward</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="editModalBody">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
