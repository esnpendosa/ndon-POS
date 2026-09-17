<!-- Page content -->
<div class="row">
    <div class="col-sm-12">
        <h4><i class="fa fa-shopping-cart mr-2"></i> Order Barang</h4>
        <br>
        <?php echo alert_swal();?>
        <div class="row">
            <div class="col-sm-6">
                <div class="card mb-3">
                    <!-- Card header -->
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-search mr-1"></i>
                            Cari Barang
                        </h5>
                    </div>
                    <div class="card-body pb-0">
                        <div class="form-group">
                            <input type="text" name="cari_barang" id="cari_barang" class="form-control"
                                placeholder="Masukan Kode Barang [ENTER]">
                        </div>
                        <div id="hasil_cari"></div>
                        <div id="tunggu"></div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body p-0 pt-3 pb-3">
                        <!-- Light table -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Semua</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Limit</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped table-sm text-sm table-bordered w-100"
                                        id="table-artikel-query">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>ID</th>
                                                <!-- <th>Gambar</th> -->
                                                <th>Nama_Barang</th>
                                                <!-- <th>Merk</th> -->
                                                <th>Harga</th>
                                                <th>Stok (Satuan)</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped table-sm text-sm table-bordered w-100"
                                        id="table-artikel-query-limit">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>ID</th>
                                                <!-- <th>Gambar</th> -->
                                                <th>Nama_Barang</th>
                                                <!-- <th>Merk</th> -->
                                                <th>Harga</th>
                                                <th>Stok (Satuan)</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="float-right">
                            <!-- Button trigger modal -->
                            <!-- <button type="button" class="btn btn-primary btn-md mt-1 mb-2" data-toggle="modal"
                                data-target="#modelId">
                                <i class="fa fa-search mr-1"></i> Daftar Barang
                            </button> -->
                            <button type="button" class="btn btn-danger btn-md mt-1 mb-2" data-toggle="modal"
                                data-target="#modelIdReset">
                                <i class="fa fa-trash mr-1"></i> Reset Keranjang
                            </button>
                            <input type="hidden" id="rupiah1">
                            <input type="hidden" id="rupiah2">
                        </div>
                        <h5 class="mt-2"><i class="fa fa-shopping-cart mr-1"></i> Keranjang</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="keranjangTransaksi"></div>
                    </div>
                    <hr>
                    <div class="card-body text-sm">
                        <div id="formTransaksi"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Barang -->
<!-- <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-search mr-1"></i> Daftar Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->

<!-- Modal  Reset -->
<div class="modal fade" id="modelIdReset" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-shopping-cart mr-1"></i> Reset Keranjang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda ingin mereset keranjang ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="proses.php?aksi=reset" class="btn btn-danger">Reset</a>
            </div>
        </div>
    </div>
</div>

<script>
// AJAX call for autocomplete 
$(document).ready(function() {
    $('#keranjangTransaksi').load('<?= $baseURL.'helper/transaksi_beli/keranjang.php';?>');
    $('#formTransaksi').load('<?= $baseURL.'helper/transaksi_beli/formTransaksi.php';?>');
});
</script>
<script>
// AJAX call for autocomplete 
$(document).ready(function() {
    $('#cari_barang').focus();
    
    // Filter both DataTables dynamically as user types
    $("#cari_barang").on("keyup input", function() {
        var val = $(this).val();
        if (typeof tabel !== 'undefined' && tabel !== null) {
            tabel.search(val).draw();
        }
        if (typeof tabel1 !== 'undefined' && tabel1 !== null) {
            tabel1.search(val).draw();
        }
    });

    // Handle Enter key for barcode scanning / adding to cart
    $("#cari_barang").on("keypress", function(e) {
        if (e.which == 13) { // Enter key
            e.preventDefault();
            var val = $(this).val().trim();
            if (val !== "") {
                $.ajax({
                    type: "POST",
                    url: "<?= $baseURL.'helper/cari.php?sortir=beli';?>",
                    data: 'keyword=' + val,
                    beforeSend: function() {
                        $("#hasil_cari").hide();
                        $("#tunggu").html(
                            '<p style="color:green"><blink><i class="fa fa-sync fa-spin mr-1"></i> tunggu sebentar</blink></p>'
                        );
                    },
                    success: function(html) {
                        $("#tunggu").html('');
                        $("#hasil_cari").show();
                        $("#hasil_cari").html(html);
                    }
                });
            }
        }
    });
});
</script>
<script>
var tabel = null;
$(document).ready(function() {
    tabel = $('#table-artikel-query').DataTable({
        "dom": "lrtip",
        "processing": true,
        "responsive": false,
        "serverSide": true,
        "ordering": true, // Set true agar bisa di sorting
        "order": [
            [0, 'DESC']
        ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
        "ajax": {
            "url": "<?= $baseURL.'/helper/data.php?aksi=barang';?>", // URL file untuk proses select datanya
            "type": "POST"
        },
        "deferRender": true,
        "aLengthMenu": [
            [10, 25, 50],
            [10, 25, 50]
        ], // Combobox Limit
        "columns": [{
                "data": 'id',
                "sortable": false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                "data": "id_barang"
            },
            // {
            //     "data": "gambar",
            //     "render": function(data, type, row, meta) {
            //         if (row.gambar !== null) {
            //             return `<center>
            //                             <a href="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" data-toggle="lightbox" 
            //                                 data-title="${row.nama_barang}" data-gallery="gallery">
            //                                 <img src="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" 
            //                                     alt="${row.nama_barang}" class="img-fluid" width="80"/>
            //                             </a>
            //                         </center>`;
            //         } else {
            //             return `<center>
            //                             <a href="<?= $baseURL;?>assets/dist/img/no-image.jpg" data-toggle="lightbox" 
            //                                 data-title="${row.nama_barang}" data-gallery="gallery">
            //                                 <img src="<?= $baseURL;?>assets/dist/img/no-image.jpg" 
            //                                     alt="${row.nama_barang}" class="img-fluid" width="80"/>
            //                             </a>
            //                         </center>`;
            //         }
            //     }
            // },
            {
                "data": "nama_barang",
                "render": function(data, type, row, meta) {
                    return `<div><strong>${row.nama_barang}</strong></div>
                            <div class="text-muted" style="font-size: 10.5px; margin-top: 1px;">(${row.nama_kategori})</div>`;
                }
            },
            // {
            //     "data": "merk"
            // },
            {
                data: 'harga_beli',
                render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp')
            },
            {
                "data": "stok",
                "render": function(data, type, row, meta) {
                    return `${row.stok} ${row.satuan_barang}`;
                }
            },
            {
                "data": "id",
                "render": function(data, type, row, meta) {
                    return `<form method="post" action="proses.php?aksi=keranjang" style="margin: 0;">  
                                <input type="hidden" value="1" name="qty">
                                <input type="hidden" value="${row.id}" name="id">
                                <button type="submit" class="btn btn-success btn-sm" title="Tambahkan ke keranjang" style="padding: 0.25rem 0.6rem;">
                                    <i class="fa fa-shopping-cart"></i>
                                </button>
                            </form>`;
                }
            },
        ],
        "drawCallback": function(settings) {
            $('.dataTables_length').addClass("pr-3 pl-3");
            $('.dataTables_filter').addClass("pr-3 pl-3");
            $('.dataTables_info').addClass("pr-3 pl-3 mb-2");
            $('.dataTables_paginate').addClass("pr-3 pl-3");
        }
    });
});
</script>

<script>
var tabel1 = null;
$(document).ready(function() {
    tabel1 = $('#table-artikel-query-limit').DataTable({
        "dom": "lrtip",
        "processing": true,
        "responsive": false,
        "serverSide": true,
        "ordering": true, // Set true agar bisa di sorting
        "order": [
            [0, 'DESC']
        ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
        "ajax": {
            "url": "<?= $baseURL.'/helper/data.php?aksi=barang&stok=yes';?>", // URL file untuk proses select datanya
            "type": "POST"
        },
        "deferRender": true,
        "aLengthMenu": [
            [10, 25, 50],
            [10, 25, 50]
        ], // Combobox Limit
        "columns": [{
                "data": 'id',
                "sortable": false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                "data": "id_barang"
            },
            // {
            //     "data": "gambar",
            //     "render": function(data, type, row, meta) {
            //         if (row.gambar !== null) {
            //             return `<center>
            //                             <a href="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" data-toggle="lightbox" 
            //                                 data-title="${row.nama_barang}" data-gallery="gallery">
            //                                 <img src="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" 
            //                                     alt="${row.nama_barang}" class="img-fluid" width="80"/>
            //                             </a>
            //                         </center>`;
            //         } else {
            //             return `<center>
            //                             <a href="<?= $baseURL;?>assets/dist/img/no-image.jpg" data-toggle="lightbox" 
            //                                 data-title="${row.nama_barang}" data-gallery="gallery">
            //                                 <img src="<?= $baseURL;?>assets/dist/img/no-image.jpg" 
            //                                     alt="${row.nama_barang}" class="img-fluid" width="80"/>
            //                             </a>
            //                         </center>`;
            //         }
            //     }
            // },
            {
                "data": "nama_barang",
                "render": function(data, type, row, meta) {
                    return `<div><strong>${row.nama_barang}</strong></div>
                            <div class="text-muted" style="font-size: 10.5px; margin-top: 1px;">(${row.nama_kategori})</div>`;
                }
            },
            // {
            //     "data": "merk"
            // },
            {
                data: 'harga_beli',
                render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp')
            },
            {
                "data": "stok",
                "render": function(data, type, row, meta) {
                    return `${row.stok} ${row.satuan_barang}`;
                }
            },
            {
                "data": "id",
                "render": function(data, type, row, meta) {
                    return `<form method="post" action="proses.php?aksi=keranjang" style="margin: 0;">  
                                <input type="hidden" value="1" name="qty">
                                <input type="hidden" value="${row.id}" name="id">
                                <button type="submit" class="btn btn-success btn-sm" title="Tambahkan ke keranjang" style="padding: 0.25rem 0.6rem;">
                                    <i class="fa fa-shopping-cart"></i>
                                </button>
                            </form>`;
                }
            },
        ],
        "drawCallback": function(settings) {
            $('.dataTables_length').addClass("pr-3 pl-3");
            $('.dataTables_filter').addClass("pr-3 pl-3");
            $('.dataTables_info').addClass("pr-3 pl-3 mb-2");
            $('.dataTables_paginate').addClass("pr-3 pl-3");
        }
    });
});
</script>