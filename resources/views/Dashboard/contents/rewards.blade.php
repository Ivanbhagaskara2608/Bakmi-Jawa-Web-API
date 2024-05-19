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
                        <div class="card card-outline card-primary">
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
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Reward</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('reward.store') }}" method="post"
                                id="form-add-reward">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="menu_id">Menu :</label>
                                        <select name="menu_id" id="menu_id" class="form-control select2bs4">
                                            <option selected disabled>-- Please select --</option>
                                            @foreach (\App\Models\Menu::all() as $menu)
                                                @if (!\App\Models\Reward::where('menu_id', $menu->id)->exists())
                                                    <option value="{{ $menu->id }}">{{ $menu->nama }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="point_required">Poin :</label>
                                        <input type="number" name="point_required" id="point_required" class="form-control">
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
    <script>
        $(document).ready(function() {
            dataTable();
        })

        function dataTable() {
            $('#dt-data').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('reward.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'menu',
                        name: 'menu'
                    },
                    {
                        data: 'point_required',
                        name: 'point_required'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal" onclick="editReward(${data})"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal" onclick="deleteReward(${data})"><i class="fas fa-trash"></i></button>
                                </div>
                            `;
                        }
                    }
                ]
            });
        }

        function editReward(id) {
            $('#editModalBody').html('');
            $.ajax({
                url: `reward/${id}`,
                method: 'GET',
                success: function(response) {
                    $('#editModalBody').html(`
                        <div class="text-center">
                            <h5 class="mb-3 font-weight-bold">${response.nama_menu}</h5>
                            <img id="editImagePreview" src="{{ asset('/images/menu/${response.gambar}') }}" class="img-fluid mb-3" alt="${response.nama}">
                        </div>
                        <form action="reward/update/${id}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="point_required">Poin :</label>
                                <input type="number" name="point_required" id="point_required" class="form-control" value="${response.reward.point_required}">
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
                        </form>
                    `);
                }
            })
        }

        function deleteReward(id) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `reward/delete/${id}`,
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(() => {
                                window.location.href = '{{ route('reward.index') }}';
                            });
                    
                        }
                    })
                }
            })
        }

    </script>
@endsection
