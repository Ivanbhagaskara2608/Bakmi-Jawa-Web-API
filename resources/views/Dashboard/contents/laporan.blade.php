@extends('dashboard.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Laporan Penjualan</h1>
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
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Laporan Penjualan</h3>
                            <div class="input-group col-3">
                                        <select name="month-selector" class="form-control">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i === Carbon\Carbon::now()->month ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                                </option>
                                            @endfor
                                        </select>
                                        <select name="year-selector" class="form-control">
                                            @for ($i = 2021; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i === Carbon\Carbon::now()->year ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                            </div>
                            
                        </div>
                        <div class="card-body">
                            <div id="dt-container" class="table-responsive">
                                <table class="table table-bordered table-hover" id="dt-data" style="width:100%">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Tanggal</th>
                                            <th>Pemasukan</th>
                                            <th>Item Terjual</th>
                                            <th>Item Terlaris</th>
                                            <th>Jumlah Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded dynamically by DataTables -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button class="btn btn-warning" id="print"><i class="fas fa-print me-2"></i>
                                Print</button>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        dataTable('{{ Carbon\Carbon::now()->month }}', '{{ Carbon\Carbon::now()->year }}');

        $('select[name="month-selector"], select[name="year-selector"]').on('change', function() {
            $('#dt-data').DataTable().destroy();
            dataTable($('select[name="month-selector"]').val(), $('select[name="year-selector"]')
                .val());
        });

        $('#print').on('click', function() {
            const month = $('select[name="month-selector"]').val();
            const year = $('select[name="year-selector"]').val();
            window.open("{{ route('laporan.print') }}?bulan=" + month + "&tahun=" + year, '_blank');
        })
    });

    function dataTable(bulan, tahun) {
        $('#dt-data').DataTable({
            processing: true,       // Show processing indicator
            serverSide: true,       // Enable server-side processing
            ajax: {
                url: "{{ route('laporan.data') }}", 
                type: 'GET',
                data: {
                    bulan: bulan,
                    tahun: tahun
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,  // Disable ordering for row index
                    searchable: false, // Disable searching for row index
                    className: 'text-center' // Center the content
                },
                { data: 'tanggal', name: 'tanggal', className: 'text-center' },
                { data: 'pemasukan', name: 'pemasukan', className: 'text-center' },
                { data: 'item_terjual', name: 'item_terjual', className: 'text-center' },
                { data: 'item_terlaris', name: 'item_terlaris', className: 'text-center' },
                { data: 'jumlah_transaksi', name: 'jumlah_transaksi', className: 'text-center' }
            ],
            order: [[1, 'asc']]  // Default sorting by the "Tanggal" column (4th column)
        });
    }

</script>
@endsection
