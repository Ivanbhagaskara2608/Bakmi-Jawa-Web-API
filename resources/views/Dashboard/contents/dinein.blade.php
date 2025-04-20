@extends('dashboard.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pesanan Dine In</h1>
                </div>
                <div class="col-sm-6">
                    {{-- Optional Content --}}
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
                    <div class="card card-outline card-warning">
                        <div class="card-header row d-flex justify-content-between">
                            <h3 class="card-title">Daftar Pesanan</h3>
                        </div>
                        <div class="card-body">
                            <div id="dt-container" class="table-responsive">
                                <table class="table table-bordered table-hover" id="dt-data" style="width:100%">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Jenis</th>
                                            <th>Pesanan</th>
                                            <th>Catatan</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded dynamically by DataTables -->
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
<!-- Edit Modal -->
<div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Status</h5>
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
        // Initialize the DataTable when document is ready
        dataTable();
    });

    function dataTable() {
        $('#dt-data').DataTable({
            processing: true,       // Show processing indicator
            serverSide: true,       // Enable server-side processing
            ajax: {
                url: "{{ route('pesanan.data.dinein') }}",  // Route to fetch dine-in data
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,  // Disable ordering for row index
                    searchable: false, // Disable searching for row index
                    className: 'text-center' // Center the content
                },
                {
                    data: 'jenis',
                    name: 'jenis',
                    className: 'text-center' // Center the content
                },
                {
                    data: 'pesanan',
                    name: 'pesanan',
                    orderable: false,  // Disable ordering for pesanan
                    className: 'text-center', // Center the content
                    render: function(data, type, full, meta) {
                        // Render as list for each item
                        return `<ul>${data}</ul>`;
                    }
                },
                {
                    data: 'catatan',
                    name: 'catatan',
                    className: 'text-center'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal',
                    className: 'text-center'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center',
                    render: function(data, type, row) {
                        let badgeClass = '';

                        if (row.status == 'Diproses') {
                            badgeClass = 'warning';
                        } else if (row.status == 'Selesai') {
                            badgeClass = 'success';
                        } else if (row.status == 'Dibatalkan') {
                            badgeClass = 'danger';
                        } else {
                            badgeClass = 'secondary'; // Default case if status is unknown
                        }

                        return `<span class="badge badge-${badgeClass}">${row.status}</span>`;
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    orderable: false,   // Disable ordering for action
                    searchable: false,  // Disable searching for action
                    className: 'text-center', // Center the content
                    render: function(data, type, full, meta) {
                        // Render the action buttons with edit and delete options
                        return `
                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal" onclick="editPesanan(${data})"><i class="fas fa-edit"></i></button>
                        `;
                    }
                }
            ],
        });
    }

    function editPesanan(id) {
    $('#editModalBody').html(''); // Kosongkan modal sebelum memuat konten baru
    $.ajax({
        url: `${id}`,
        method: 'GET',
        success: function(response) {
            console.log(response); // Debug untuk memeriksa response yang diterima
            $('#editModalBody').html(`
                <form action="update/${id}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="status">Status :</label>
                        <select name="status" id="status" class="form-control select2bs4">
                            <option value="pending" ${response.status === 'pending' ? 'selected' : ''}>Diproses</option>
                            <option value="completed" ${response.status === 'completed' ? 'selected' : ''}>Selesai</option>
                            <option value="cancelled" ${response.status === 'cancelled' ? 'selected' : ''}>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Simpan</button>
                </form>
            `);

            // Inisialisasi select2 setelah elemen di-load
            $('#status').select2({
                theme: 'bootstrap4'
            });
        },
        error: function(error) {
            console.error(error); // Tampilkan error di console untuk debugging
            alert('Gagal memuat data!');
        }
    });

}

</script>
@endsection
