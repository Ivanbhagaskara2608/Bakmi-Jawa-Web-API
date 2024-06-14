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
                                            <option value="Makanan">Makanan</option>
                                            <option value="Minuman">Minuman</option>
                                            <option value="Cemilan">Cemilan</option>
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
        $(document).ready(function() {
            dataTable();

            $('#form-add-menu').on('submit', function(e) {
                var priceInput = $('#harga');
                priceInput.val(accounting.unformat(priceInput.val()));
            })

            $('#form-edit-menu').on('submit', function(e) {
                var priceInput = $('#editPrice');
                priceInput.val(accounting.unformat(priceInput.val()));
            })
        })

        function dataTable() {
            $('#dt-container').html('');
            $('#dt-container').html(`
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
                </table>
            `)

            $('#dt-data').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('menu.data') }}",
                columnDefs: [{
                    className: 'text-center',
                    targets: [0, 2, 3, 5, 6]
                }],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'nama', name: 'nama' },
                    { data: 'kategori', name: 'kategori' },
                    { data: 'harga', name: 'harga' },
                    { data: 'status', name: 'status',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<span class="badge badge-${row.status == 'TERSEDIA' ? 'success' : 'secondary'}">${row.status}</span>`
                        }
                     },
                    { data: 'gambar', name: 'gambar' },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal" onclick="editMenu(${data})"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal" onclick="deleteMenu(${data})"><i class="fas fa-trash"></i></button>
                                </div>
                            `;
                        }
                    }
                ]
            });
        }

        function previewEditImage(event) {
            var editImage = document.getElementById('editImagePreview');
            editImage.src = URL.createObjectURL(event.target.files[0]);
        }

        function editMenu(id) {
            $('#editModalBody').html('');
            $.ajax({
                url: `menu/${id}`,
                type: 'GET',
                success: function(response) {
                    $('#editModalBody').html(`
                        <img id="editImagePreview" src="{{ asset('/images/menu/${response.gambar}') }}" class="img-fluid mb-3" alt="${response.nama}">
                        <form action="menu/update/${id}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="nama">Nama :</label>
                                <input type="text" name="nama" id="nama" class="form-control" value="${response.nama}">
                            </div>
                            <div class="mb-3">
                                <label for="status">Status :</label>
                                    <select name="status" id="status" class="form-control select2bs4">
                                        <option value="TERSEDIA" ${response.status === 'TERSEDIA' ? 'selected' : ''}>TERSEDIA</option>
                                        <option value="HABIS" ${response.status === 'HABIS' ? 'selected' : ''}>HABIS</option>
                                    </select>
                            </div>
                            <div class="mb-3">
                                <label for="kategori">Kategori :</label>
                                    <select name="kategori" id="kategori" class="form-control select2bs4">
                                        <option value="Makanan" ${response.kategori === 'Makanan' ? 'selected' : ''}>Makanan</option>
                                        <option value="Minuman" ${response.kategori === 'Minuman' ? 'selected' : ''}>Minuman</option>
                                        <option value="Cemilan" ${response.kategori === 'Cemilan' ? 'selected' : ''}>Cemilan</option>
                                    </select>
                            </div>
                            <div class="mb-3">
                                <label for="harga">Harga :</label>
                                <input type="text" name="harga" id="harga" class="form-control" oninput="formatCurrency(this)" value="${response.harga}">
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi">Deskripsi :</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3">${response.deskripsi}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="gambar">Image :</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="gambar" id="gambar" accept="image/*" onchange="previewEditImage(event)">
                                    <label class="custom-file-label" for="gambar">Pilih file</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
                        </form>
                    `);
                    bsCustomFileInput.init();
                }
            })
        }

        function deleteMenu(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `menu/delete/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Data berhasil dihapus.',
                                'success'
                            )
                            dataTable();
                        }
                    })
                }
            })
        }
    </script>
@endsection
