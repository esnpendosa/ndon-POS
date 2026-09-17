<!-- Page content -->
<div class="row">
    <div class="col-md-12">
        <h5 class="mb-2"><i class="fa fa-shopping-cart mr-2"></i> Kasir</h5>
        <?php echo alert_swal();?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mt-1 mb-2">
                    <!-- Card header -->
                    <div class="card-header py-2">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fa fa-search mr-1"></i>
                            Cari Barang
                        </h6>
                    </div>
                    <div class="card-body pb-0 pt-2">
                        <div class="form-group mb-2">
                            <input type="text" name="cari_barang" id="cari_barang" class="form-control"
                                placeholder="Masukan Kode Barang [ENTER]">
                            <input type="hidden" id="rupiah1">
                            <input type="hidden" id="rupiah2">
                        </div>
                        <div id="hasil_cari"></div>
                        <div id="tunggu"></div>
                    </div>
                </div>
                <div class="card mt-1 mb-2">
                    <div class="card-body p-0 pt-2 pb-2">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm text-sm table-bordered w-100"
                                id="table-artikel-query">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <!-- <th>Gambar</th> -->
                                        <th>Barang</th>
                                        <!-- <th>Merk</th> -->
                                        <th>Harga_Retail</th>
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
            <div class="col-md-6">
                <div class="card mt-1 mb-2">
                    <div class="card-header py-2">
                        <div class="float-right">
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                data-target="#modelIdReset">
                                <i class="fa fa-trash mr-1"></i> Reset Keranjang
                            </button>
                        </div>
                        <h6 class="mt-1 mb-0 font-weight-bold"><i class="fa fa-shopping-cart mr-1"></i> Keranjang</h6>
                    </div>
                    <div class="card-body p-0">
                        <div id="keranjangTransaksi"></div>
                    </div>
                    <hr class="my-2">
                    <div class="card-body py-2 text-sm">
                        <div id="formTransaksi"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $sqlp = "SELECT * FROM pelanggan ORDER BY id DESC LIMIT 1";
    $rowp = $connectdb->prepare($sqlp);
    $rowp->execute();
    $editp = $rowp->fetch(PDO::FETCH_OBJ);
    if(isset($editp->id)){
        $cekidp = $editp->id + 1;
    }else{
        $cekidp = 1;
    }
    $kodep = 'PL00'.$cekidp;
?>
<div class="modal fade" id="modelIdPelanggan" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Pelanggan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="proses.php?aksi=pelanggan" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Kode</label>
                        <input type="text" class="form-control" required value="<?= $kodep;?>" readonly
                            name="kode_pelanggan" id="kode_pelanggan" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Nama</label>
                        <input type="text" class="form-control" required name="nama_pelanggan" id="nama_pelanggan"
                            placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Telepon</label>
                        <input type="number" class="form-control" required name="telepon_pelanggan"
                            id="telepon_pelanggan" placeholder="">
                    </div>

                    <div class="form-group">
                        <label for="">E-mail</label>
                        <input type="email" class="form-control" required name="email_pelanggan" id="email_pelanggan"
                            placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Alamat</label>
                        <textarea class="form-control" required name="alamat_pelanggan" id="alamat_pelanggan"
                            placeholder=""></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-md">Save</button>
                </div>
            </form>
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
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered w-100" id="table-artikel-query">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Barang</th>
                                <th>Gambar</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th>Harga</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
    $('#cari_barang').focus();
    $('#keranjangTransaksi').load('<?= $baseURL.'helper/transaksi/keranjang.php';?>');
    $('#formTransaksi').load('<?= $baseURL.'helper/transaksi/formTransaksi.php';?>');
    
    // Filter DataTable dynamically as user types
    $("#cari_barang").on("keyup input", function() {
        var val = $(this).val();
        if (typeof tabel !== 'undefined' && tabel !== null) {
            tabel.search(val).draw();
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
                    url: "<?= $baseURL.'helper/cari.php';?>",
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
            <?php if(isset($_GET['stok'])){?> "url": "<?= $baseURL.'/helper/data.php?aksi=barang&stok=yes';?>", // URL file untuk proses select datanya
            <?php }else{?> "url": "<?= $baseURL.'/helper/data.php?aksi=barang';?>", // URL file untuk proses select datanya
            <?php }?> "type": "POST"
        },
        "deferRender": true,
        "aLengthMenu": [
            [5, 10, 25, 50],
            [5, 10, 25, 50]
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
                data: 'harga_jual',
                render: function(data, type, row, meta) {
                    var out = 'Rp' + Number(row.harga_jual || 0).toLocaleString('id-ID');
                    if (row.min_grosir > 0 && Number(row.harga_grosir || 0) > 0) {
                        out += `<div class="text-success font-weight-bold" style="font-size: 10px; margin-top: 1px;">
                                    Grosir &ge; ${row.min_grosir}: Rp${Number(row.harga_grosir).toLocaleString('id-ID')}
                                </div>`;
                    }
                    return out;
                }
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