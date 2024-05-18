@extends('dashboard.app')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Menu</h1>
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
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Menu</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" id="dt-container">
                                    <table class="table table-bordered table-striped" id="dt-data">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama</th>
                                                <th>Kategori</th>
                                                <th>Harga</th>
                                                <th>Status</th>
                                                <th>Gambar</th>
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
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Menu</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('menu.store') }}" method="post" enctype="multipart/form-data" id="form-add-menu">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="nama">Nama :</label>
                                        <input type="text" name="nama" id="nama" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="kategori">Kategori :</label>
                                        <select name="kategori" id="kategori" class="form-control select2bs4">
                                            <option selected disabled>-- Please select --</option>
                                            <option value="makanan">Makanan</option>
                                            <option value="minuman">Minuman</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="harga">Harga :</label>
                                        <input type="text" name="harga" id="harga" class="form-control" oninput="formatCurrency(this)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="deskripsi">Deskripsi :</label>
                                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="gambar">Gambar :</label>
                                            </div>
                                        </div>
                                        <div id="images-container">
                                            <div class="custom-file mb-3">
                                                <input type="file" class="custom-file-input" name="gambar" id="gambar" accept="image/*">
                                                <label class="custom-file-label" for="gambar">Pilih file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button id="submitAdd" type="submit" class="btn btn-success"><i class="fas fa-plus mr-1"></i>Tambah Menu</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Menu</h5>
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
    <script>
        
    </script>
@endsection
